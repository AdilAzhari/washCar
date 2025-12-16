<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { Head, Link } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Card, CardContent, Button } from '@/Components/ui'
import { ArrowLeft, Download, Printer } from 'lucide-vue-next'
import QRCode from 'qrcode'

interface Branch {
  id: number
  name: string
  code: string
}

const props = defineProps<{
  branch: Branch
  appUrl: string
}>()

const qrCodeUrl = ref('')
const canvasRef = ref<HTMLCanvasElement | null>(null)

const generateQRCode = async () => {
  const joinUrl = `${props.appUrl}/queue/join/${props.branch.code}`

  if (canvasRef.value) {
    try {
      await QRCode.toCanvas(canvasRef.value, joinUrl, {
        width: 400,
        margin: 2,
        color: {
          dark: '#000000',
          light: '#FFFFFF'
        }
      })
      qrCodeUrl.value = canvasRef.value.toDataURL('image/png')
    } catch (error) {
      console.error('Error generating QR code:', error)
    }
  }
}

const downloadQRCode = () => {
  if (qrCodeUrl.value) {
    const link = document.createElement('a')
    link.download = `${props.branch.code}-qr-code.png`
    link.href = qrCodeUrl.value
    link.click()
  }
}

const printQRCode = () => {
  window.print()
}

onMounted(() => {
  generateQRCode()
})
</script>

<template>
  <Head :title="`QR Code - ${branch.name}`" />
  <AuthenticatedLayout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">Branch QR Code</h2>
    </template>
    <div class="py-12 print:py-4">
      <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="space-y-6 animate-fade-in">
          <!-- Header -->
          <div class="flex items-center justify-between print:hidden">
            <div class="flex items-center gap-4">
              <Link :href="route('branches.show', branch.id)">
                <Button variant="outline" size="sm">
                  <ArrowLeft class="w-4 h-4 mr-2" />
                  Back
                </Button>
              </Link>
              <div>
                <h1 class="text-2xl font-bold mb-2">QR Code</h1>
                <p class="text-muted-foreground">{{ branch.name }}</p>
              </div>
            </div>
            <div class="flex gap-2">
              <Button variant="outline" @click="downloadQRCode">
                <Download class="w-4 h-4 mr-2" />
                Download
              </Button>
              <Button variant="outline" @click="printQRCode">
                <Printer class="w-4 h-4 mr-2" />
                Print
              </Button>
            </div>
          </div>

          <!-- QR Code Display -->
          <Card class="print:shadow-none print:border-0">
            <CardContent class="p-12 text-center space-y-6">
              <div>
                <h2 class="text-3xl font-bold mb-2">{{ branch.name }}</h2>
                <p class="text-muted-foreground text-lg">Branch Code: {{ branch.code }}</p>
              </div>

              <div class="flex justify-center">
                <div class="bg-white p-8 rounded-2xl shadow-lg inline-block">
                  <canvas ref="canvasRef" class="mx-auto"></canvas>
                </div>
              </div>

              <div class="space-y-2">
                <p class="text-xl font-semibold">Scan to Join the Queue</p>
                <p class="text-sm text-muted-foreground">Join queue at: /queue/join/{{ branch.code }}</p>
              </div>
            </CardContent>
          </Card>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<style>
@media print {
  body * {
    visibility: hidden;
  }
  .print\\:shadow-none,
  .print\\:shadow-none * {
    visibility: visible;
  }
  .print\\:shadow-none {
    position: absolute;
    left: 50%;
    top: 50%;
    transform: translate(-50%, -50%);
  }
}
</style>
