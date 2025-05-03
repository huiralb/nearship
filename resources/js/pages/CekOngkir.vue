<template>
  <Card class="max-w-xl mx-auto mt-8">
    <CardHeader>
      <CardTitle>Cek Ongkir</CardTitle>
    </CardHeader>
    <CardContent>
      <form @submit.prevent="submit">
        <div class="mb-6">
          <h2 class="text-lg font-semibold mb-4">Data Paket</h2>
          <div class="mb-4 relative">
            <Label for="asal">Asal <span class="text-red-500">*</span></Label>
            <Input id="asal" v-model="form.asal" @input="onAsalInput" placeholder="Cijerrah, Bandung Kulon, Bandung, Kota, Jawa Barat" class="mt-1" required autocomplete="off" />
            <div v-if="showAsalDropdown && asalSuggestions.length" class="absolute z-10 w-full bg-white border rounded shadow mt-1 max-h-48 overflow-auto">
              <div v-for="(suggestion, idx) in asalSuggestions" :key="idx" @mousedown.prevent="selectAsalSuggestion(suggestion)" class="px-3 py-2 cursor-pointer hover:bg-gray-100">
                {{ suggestion.alt_name }}
              </div>
            </div>
          </div>
          <div class="mb-4 relative">
            <Label for="tujuan">Tujuan <span class="text-red-500">*</span></Label>
            <Input id="tujuan" v-model="form.tujuan" @input="onTujuanInput" placeholder="Paminggir, Garut Kota, Garut, Jawa Barat" class="mt-1" required autocomplete="off" />
            <div v-if="showTujuanDropdown && tujuanSuggestions.length" class="absolute z-10 w-full bg-white border rounded shadow mt-1 max-h-48 overflow-auto">
              <div v-for="(suggestion, idx) in tujuanSuggestions" :key="idx" @mousedown.prevent="selectTujuanSuggestion(suggestion)" class="px-3 py-2 cursor-pointer hover:bg-gray-100">
                {{ suggestion.alt_name }}
              </div>
            </div>
          </div>
          <div class="mb-4 flex items-end gap-2">
            <div class="flex-1">
              <Label for="berat">Berat <span class="text-red-500">*</span></Label>
              <Input id="berat" v-model="form.berat" type="number" min="1" class="mt-1" required />
            </div>
            <select v-model="form.satuan" class="h-9 border rounded-md px-2">
              <option value="kg">kg</option>
              <option value="g">g</option>
            </select>
          </div>
          <div class="text-sm text-muted-foreground mb-2">Estimasi berat terhitung: 0 - 0 kg</div>
        </div>
        <Button class="w-full" type="submit">Lihat Estimasi Ongkir</Button>
      </form>
    </CardContent>
  </Card>
</template>

<script setup>
import Card from '@/components/ui/card/Card.vue'
import CardHeader from '@/components/ui/card/CardHeader.vue'
import CardTitle from '@/components/ui/card/CardTitle.vue'
import CardContent from '@/components/ui/card/CardContent.vue'
import Input from '@/components/ui/input/Input.vue'
import Label from '@/components/ui/label/Label.vue'
import Button from '@/components/ui/button/Button.vue'
import { useForm } from '@inertiajs/vue3'
import { ref, watch } from 'vue'

const form = useForm({
  asal: '',
  tujuan: '',
  berat: 1,
  satuan: 'kg',
})

const asalSuggestions = ref([])
const showAsalDropdown = ref(false)
let asalFetchTimeout = null

const tujuanSuggestions = ref([])
const showTujuanDropdown = ref(false)
let tujuanFetchTimeout = null

function onAsalInput() {
  showAsalDropdown.value = !!form.asal
  if (asalFetchTimeout) clearTimeout(asalFetchTimeout)
  if (!form.asal) {
    asalSuggestions.value = []
    return
  }
  asalFetchTimeout = setTimeout(async () => {
    const res = await fetch(`/api/districts/search?q=${encodeURIComponent(form.asal)}`)
    console.log('res', res)
    if (res.ok) {
      asalSuggestions.value = await res.json()
    } else {
      asalSuggestions.value = []
    }
  }, 300)
}

function selectAsalSuggestion(suggestion) {
  form.asal = suggestion.alt_name
  showAsalDropdown.value = false
}

function onTujuanInput() {
  showTujuanDropdown.value = !!form.tujuan
  if (tujuanFetchTimeout) clearTimeout(tujuanFetchTimeout)
  if (!form.tujuan) {
    tujuanSuggestions.value = []
    return
  }
  tujuanFetchTimeout = setTimeout(async () => {
    const res = await fetch(`/api/districts/search?q=${encodeURIComponent(form.tujuan)}`)
    console.log('res', res)
    if (res.ok) {
      tujuanSuggestions.value = await res.json()
    } else {
      tujuanSuggestions.value = []
    }
  }, 300)
}

function selectTujuanSuggestion(suggestion) {
  form.tujuan = suggestion.alt_name
  showTujuanDropdown.value = false
}

// Hide dropdown on blur
watch(() => form.asal, (val) => {
  if (!val) showAsalDropdown.value = false
})
watch(() => form.tujuan, (val) => {
  if (!val) showTujuanDropdown.value = false
})

const submit = () => {
  // Placeholder for form submission logic
  // e.g., form.post(route('cek-ongkir'))
}
</script>
