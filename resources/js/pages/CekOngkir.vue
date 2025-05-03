<template>
  <Card class="max-w-xl mx-auto mt-8">
    <CardHeader>
      <CardTitle>Cek Ongkir</CardTitle>
    </CardHeader>
    <CardContent>
      <form @submit.prevent>
        <div class="mb-6">
          <h2 class="text-lg font-semibold mb-4">Data Paket</h2>
          <div class="mb-4">
            <Label for="asal">Asal <span class="text-red-500">*</span></Label>
            <Input id="asal" v-model="asal" placeholder="Cijerrah, Bandung Kulon, Bandung, Kota, Jawa Barat" class="mt-1" required />
          </div>
          <div class="mb-4">
            <Label for="tujuan">Tujuan <span class="text-red-500">*</span></Label>
            <Input id="tujuan" v-model="tujuan" placeholder="Paminggir, Garut Kota, Garut, Jawa Barat" class="mt-1" required />
          </div>
          <div class="mb-4 flex items-end gap-2">
            <div class="flex-1">
              <Label for="berat">Berat <span class="text-red-500">*</span></Label>
              <Input id="berat" v-model="berat" type="number" min="1" class="mt-1" required />
            </div>
            <select v-model="satuan" class="h-9 border rounded-md px-2">
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

<script setup lang="ts">
import Card from '@/components/ui/card/Card.vue'
import CardHeader from '@/components/ui/card/CardHeader.vue'
import CardTitle from '@/components/ui/card/CardTitle.vue'
import CardContent from '@/components/ui/card/CardContent.vue'
import Input from '@/components/ui/input/Input.vue'
import Label from '@/components/ui/label/Label.vue'
import Button from '@/components/ui/button/Button.vue'
import { ref } from 'vue'

const asal = ref('')
const tujuan = ref('')
const berat = ref(1)
const satuan = ref('kg')
</script>
