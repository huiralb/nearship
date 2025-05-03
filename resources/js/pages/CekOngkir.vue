<template>
    <Head title="Cek Ongkir" />
    <Card class="max-w-xl mx-auto mt-8">
        <CardHeader>
            <h1 class="text-4xl font-bold">Cek Ongkir</h1>
        </CardHeader>
        <CardContent>
            <form @submit.prevent="submit">
                <div class="mb-6">
                    <h2 class="text-lg font-semibold mb-4">Data Paket</h2>
                    <div class="mb-4 relative">
                        <Label for="asal">Asal <span class="text-red-500">*</span></Label>
                        <Input id="asal" v-model="asal" @input="onAsalInput"
                            placeholder="Cijerrah, Bandung Kulon, Bandung, Kota, Jawa Barat" class="mt-1" required
                            autocomplete="off" />
                        <div v-if="showAsalDropdown && asalSuggestions.length"
                            class="absolute z-10 w-full bg-gray-700 text-white border rounded shadow mt-1 max-h-48 overflow-auto">
                            <div v-for="(suggestion, idx) in asalSuggestions" :key="idx"
                                @mousedown.prevent="selectAsalSuggestion(suggestion)"
                                class="px-3 py-2 cursor-pointer hover:bg-blue-400">
                                {{ suggestion.address }}
                            </div>
                        </div>
                        <InputError :message="props.errors?.asal?.subdistrict_id" />
                    </div>
                    <div class="mb-4 relative">
                        <Label for="tujuan">Tujuan <span class="text-red-500">*</span></Label>
                        <Input id="tujuan" v-model="tujuan" @input="onTujuanInput"
                            placeholder="Paminggir, Garut Kota, Garut, Jawa Barat" class="mt-1" required
                            autocomplete="off" />
                        <div v-if="showTujuanDropdown && tujuanSuggestions.length"
                            class="absolute z-10 w-full bg-gray-700 text-white border rounded shadow mt-1 max-h-48 overflow-auto">
                            <div v-for="(suggestion, idx) in tujuanSuggestions" :key="idx"
                                @mousedown.prevent="selectTujuanSuggestion(suggestion)"
                                class="px-3 py-2 cursor-pointer hover:bg-blue-400">
                                {{ suggestion.address }}
                            </div>
                        </div>
                        <InputError :message="props.errors?.tujuan?.subdistrict_id" />
                    </div>
                    <div class="mb-4 relative">
                        <Label for="agen">Pengiriman (Agen) <span class="text-red-500">*</span></Label>
                        <Input id="agen" v-model="agen" placeholder="Pilih Agen" readonly class="mt-1" />
                        <InputError :message="props.errors?.agen?.subdistrict_id" />
                        <!-- Table Agen terdekat (nearby) -->
                        <div v-if="loadingAgents" class="mt-4 shadow text-center text-gray-500">Memuat agen terdekat...</div>
                        <div v-if="!loadingAgents" class="mt-0">
                            <div v-if="nearbyAgents.length" class="max-h-[300px] shadow overflow-x-auto">
                                <table class="min-w-full border text-sm">
                                    <thead class="bg-gray-100">
                                        <tr>
                                            <th class="border px-3 py-2">Nama Agen</th>
                                            <th class="border px-3 py-2">Alamat</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(item, idx) in nearbyAgents" :key="idx" class="cursor-pointer hover:bg-blue-200" :class="{'bg-blue-200': item.address == agen}" @click="selectAgen(item)">
                                            <td class="border px-3 py-2 font-semibold">{{ item.name }}</td>
                                            <td class="border px-3 py-2">{{ item.address }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div v-else class="min-h-[50px] flex flex-column items-center justify-center"><em class="text-sm text-gray-700  ">Tidak ada agen tersedia</em></div>
                        </div>
                    </div>
                    <div class="mb-4">
                        <div class="flex items-end gap-2">
                            <div class="flex-1">
                                <Label for="berat">Berat <span class="text-red-500">*</span></Label>
                                <Input id="berat" v-model="form.berat" type="number" min="1" class="mt-1" required />
                            </div>
                            <select v-model="form.satuan" class="h-9 border rounded-md px-2">
                                <option value="kg">kg</option>
                                <option value="g">g</option>
                            </select>
                        </div>
                        <InputError :message="props.errors.berat" />
                    </div>

                </div>
                <div v-if="errorMessage" class="mb-4 p-4 bg-red-100 text-red-700 rounded-md">
                    {{ errorMessage }}
                </div>
                <Button class="w-full" type="submit" :disabled="loadingSubmit">
                    {{ loadingSubmit ? 'Memuat...' : 'Lihat Estimasi Ongkir' }}
                </Button>
            </form>

            <!-- Shipping Services Result -->
            <div class="mt-8">
                <h2 class="text-lg font-semibold mb-4">Hasil Pencarian</h2>
                <template v-if="shippingServices && shippingServices.length">
                    <ShippingServiceTabs :services="shippingServices" />
                </template>
            </div>
        </CardContent>
    </Card>
</template>

<script setup>
import { ref, watch } from 'vue'
import { Head, useForm } from '@inertiajs/vue3'
import Card from '@/components/ui/card/Card.vue'
import CardHeader from '@/components/ui/card/CardHeader.vue'
import CardTitle from '@/components/ui/card/CardTitle.vue'
import CardContent from '@/components/ui/card/CardContent.vue'
import Input from '@/components/ui/input/Input.vue'
import Label from '@/components/ui/label/Label.vue'
import Button from '@/components/ui/button/Button.vue'
import InputError from '@/components/InputError.vue'
import ShippingServiceTabs from '@/components/ShippingServiceTabs.vue'

const props = defineProps({
    errors: {
        type: Object,
        default: () => ({})
    }
})

const form = useForm({
    asal: null,
    tujuan: null,
    agen: null,
    berat: 1,
    satuan: 'kg',
})

const tujuan = ref('')
const asal = ref('')
const agen = ref('')
const loadingSubmit = ref(false)
const shippingServices = ref([])
const rajaongkirResponse = ref(null)

const asalSuggestions = ref([])
const showAsalDropdown = ref(false)
let asalFetchTimeout = null

const tujuanSuggestions = ref([])
const showTujuanDropdown = ref(false)
let tujuanFetchTimeout = null

const nearbyAgents = ref([])
const loadingAgents = ref(false)

const errorMessage = ref('')

function onAsalInput() {
    showAsalDropdown.value = !!asal.value
    if (asalFetchTimeout) clearTimeout(asalFetchTimeout)
    if (!asal.value) {
        asalSuggestions.value = []
        return
    }
    asalFetchTimeout = setTimeout(async () => {
        const res = await fetch(`/api/districts/search?q=${encodeURIComponent(asal.value)}`)
        if (res.ok) {
            asalSuggestions.value = await res.json()
        } else {
            asalSuggestions.value = []
        }
    }, 300)
}

function selectAsalSuggestion(suggestion) {
    asal.value = suggestion.address
    form.asal = suggestion
    showAsalDropdown.value = false
}

function onTujuanInput() {
    showTujuanDropdown.value = !!tujuan.value
    if (tujuanFetchTimeout) clearTimeout(tujuanFetchTimeout)
    if (!tujuan.value) {
        tujuanSuggestions.value = []
        agen.value = ''
        return
    }
    tujuanFetchTimeout = setTimeout(async () => {
        const res = await fetch(`/api/districts/search?q=${encodeURIComponent(tujuan.value)}`)
        if (res.ok) {
            tujuanSuggestions.value = await res.json()
            nearbyAgents.value = []
        } else {
            tujuanSuggestions.value = []
            agen.value = ''
            form.agen = null
        }
    }, 300)
}

async function selectTujuanSuggestion(suggestion) {
    tujuan.value = suggestion.address
    form.tujuan = suggestion
    showTujuanDropdown.value = false
    // Fetch nearby agents
    loadingAgents.value = true
    nearbyAgents.value = []
    try {
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        const res = await fetch('/api/agen/nearby', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token
            },
            body: JSON.stringify({ tujuan: form.tujuan }),
        })
        if (res.ok) {
            nearbyAgents.value = await res.json()
            if(nearbyAgents.value.length == 1) {
                form.agen = nearbyAgents.value[0]
                agen.value = form.agen.address
            }
        }
    } finally {
        loadingAgents.value = false
    }
}

async function selectAgen(item) {
    agen.value = item.address
    form.agen = item
    shippingServices.value = []
    rajaongkirResponse.value = null
}

function convertBerat(satuan) {
    return satuan == 'g' ? form.berat / 1000 : form.berat * 1000;
}

// Hide dropdown on blur
watch(() => form, (val) => {
    if (!val) showAsalDropdown.value = false
})
watch(() => form.tujuan, (val) => {
    if (!val) showTujuanDropdown.value = false
})
watch(() => form.satuan, (val, old) => {
    form.berat = convertBerat(old)
})

function transform(data) {
    return {
        originType: 'subdistrict',
        origin: data.asal.subdistrict_id,
        destinationType: 'subdistrict',
        destination: data.agen.subdistrict_id,
        weight: data.satuan == 'kg' ? data.satuan * 1000 : data.satuan,
        courier: 'jne'
    }
}

const submit = () => {
    shippingServices.value = []
    rajaongkirResponse.value = null

    form.post('/cek-ongkir', {
        onProgress() {
            loadingSubmit.value = true
        },
        onSuccess(page) {
            if (page.props.success) {
                shippingServices.value = page.props.data?.rajaongkir?.results[0].costs
                rajaongkirResponse.value = page.props.data.rajaongkir
            } else {
                console.error('Error:', error)
                errorMessage.value = error.message || 'Terjadi kesalahan saat mengambil data ongkos kirim.'
            }
        },
        onError(errors) {
            console.error(errors)
        },
        onFinish() {
            loadingSubmit.value = false
        }
    })

}
</script>
