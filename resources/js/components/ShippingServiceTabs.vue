<script setup>
import { ref, computed } from 'vue'

const props = defineProps({
    services: {
        type: Array,
        required: true,
        default: () => []
    }
})

const tabs = [
    { id: 'semua', label: 'Semua' },
    { id: 'reguler', label: 'Reguler' },
    { id: 'express', label: 'Express' },
    { id: 'cargo', label: 'Cargo' }
]

const activeTab = ref('semua')

const filteredServices = computed(() => {
    if (activeTab.value === 'semua') return props.services

    const categoryMap = {
        'reguler': ['REG'],
        'express': ['YES'],
        'cargo': ['JTR']
    }

    return props.services.filter(service =>
        categoryMap[activeTab.value]?.includes(service.service)
    )
})

const formatPrice = (price) => {
    return new Intl.NumberFormat('id-ID').format(price)
}

const formatEtd = (etd) => {
    const [min, max] = etd.split('-')
    return max ? `${min}-${max} Hari` : `${min} Hari`
}
</script>

<template>
    <div class="w-full">
        <!-- Tabs -->
        <div class="flex border-b mb-4">
            <button
                v-for="tab in tabs"
                :key="tab.id"
                @click="activeTab = tab.id"
                class="px-4 py-2 -mb-px"
                :class="[
                    activeTab === tab.id
                        ? 'text-blue-500 border-b-2 border-blue-500 font-medium'
                        : 'text-gray-500 hover:text-gray-700'
                ]"
            >
                {{ tab.label }}
            </button>
        </div>

        <!-- Service Cards -->
        <div class="space-y-4">
            <div v-for="service in filteredServices" :key="service.service"
                class="border rounded-lg p-4 hover:border-blue-500 cursor-pointer">
                <div class="flex items-start justify-between">
                    <div>
                        <div class="flex items-center gap-2">
                            <img src="/images/jne-logo.svg" alt="JNE" class="h-8 w-auto" />
                            <div>
                                <h3 class="font-medium">{{ service.description }}</h3>
                                <p class="text-sm text-gray-500">{{ formatEtd(service.cost[0].etd) }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="text-blue-500 text-sm">Cashback Rp 1.200</div>
                        <div class="font-medium">Rp {{ formatPrice(service.cost[0].value) }}</div>
                    </div>
                </div>
                <div class="mt-2">
                    <div class="flex items-center gap-2 text-gray-600">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="ml-1">88% Pengiriman sukses</span>
                        </div>
                        <div class="flex items-center ml-4">
                            <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span class="ml-1">100% Ketepatan waktu</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.border-blue-500 {
    border-color: rgb(59, 130, 246);
}
.text-blue-500 {
    color: rgb(59, 130, 246);
}
.text-gray-500 {
    color: rgb(107, 114, 128);
}
.text-gray-600 {
    color: rgb(75, 85, 99);
}
.text-gray-700 {
    color: rgb(55, 65, 81);
}
.text-green-500 {
    color: rgb(34, 197, 94);
}
</style>
