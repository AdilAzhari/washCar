<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3'
import { Button, Input, Label, Textarea, Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/Components/ui'
import ApplicationLogo from '@/Components/ApplicationLogo.vue'

interface Branch {
  id: number
  name: string
  code: string
  address: string
}

interface Package {
  id: number
  name: string
  description: string
  price: number
  duration: number
  color: string
}

const props = defineProps<{
  branch: Branch
  packages: Package[]
}>()

const form = useForm({
  name: '',
  phone: '',
  plate_number: '',
  vehicle_type: '',
  package_id: null as number | null,
  special_requests: ''
})

const submit = () => {
  form.post(route('queue.submit', props.branch.code))
}

const getPackageColor = (color: string) => {
  const colors: Record<string, string> = {
    blue: 'bg-blue-100 border-blue-300 hover:bg-blue-200',
    green: 'bg-green-100 border-green-300 hover:bg-green-200',
    purple: 'bg-purple-100 border-purple-300 hover:bg-purple-200',
    orange: 'bg-orange-100 border-orange-300 hover:bg-orange-200',
    pink: 'bg-pink-100 border-pink-300 hover:bg-pink-200'
  }
  return colors[color] || 'bg-gray-100 border-gray-300 hover:bg-gray-200'
}
</script>

<template>
  <Head title="Join Queue" />

  <div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-green-50">
    <!-- Header -->
    <div class="bg-white border-b border-gray-200 shadow-sm">
      <div class="max-w-4xl mx-auto px-4 py-6">
        <div class="flex items-center gap-4">
          <ApplicationLogo class="h-12 w-auto" />
          <div>
            <h1 class="text-2xl font-bold text-gray-900">WashyWashy Pro</h1>
            <p class="text-sm text-gray-600">Car Wash Management</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-4xl mx-auto px-4 py-8">
      <!-- Branch Info -->
      <Card class="mb-6 bg-gradient-to-r from-blue-500 to-green-500 text-white border-0">
        <CardHeader>
          <CardTitle class="text-2xl text-white">{{ branch.name }}</CardTitle>
          <CardDescription class="text-blue-100">{{ branch.address }}</CardDescription>
        </CardHeader>
      </Card>

      <!-- Queue Form -->
      <Card>
        <CardHeader>
          <CardTitle>Join the Queue</CardTitle>
          <CardDescription>Fill in your details to join the car wash queue at this branch</CardDescription>
        </CardHeader>
        <CardContent>
          <form @submit.prevent="submit" class="space-y-6">
            <!-- Personal Information -->
            <div class="space-y-4">
              <h3 class="text-lg font-semibold text-gray-900">Personal Information</h3>

              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="space-y-2">
                  <Label for="name">Full Name *</Label>
                  <Input
                    id="name"
                    v-model="form.name"
                    type="text"
                    placeholder="Enter your name"
                    :class="{ 'border-red-500': form.errors.name }"
                    required
                  />
                  <p v-if="form.errors.name" class="text-sm text-red-600">{{ form.errors.name }}</p>
                </div>

                <div class="space-y-2">
                  <Label for="phone">Phone Number *</Label>
                  <Input
                    id="phone"
                    v-model="form.phone"
                    type="tel"
                    placeholder="Enter your phone number"
                    :class="{ 'border-red-500': form.errors.phone }"
                    required
                  />
                  <p v-if="form.errors.phone" class="text-sm text-red-600">{{ form.errors.phone }}</p>
                </div>
              </div>
            </div>

            <!-- Vehicle Information -->
            <div class="space-y-4">
              <h3 class="text-lg font-semibold text-gray-900">Vehicle Information</h3>

              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="space-y-2">
                  <Label for="plate_number">Plate Number *</Label>
                  <Input
                    id="plate_number"
                    v-model="form.plate_number"
                    type="text"
                    placeholder="e.g., ABC-1234"
                    :class="{ 'border-red-500': form.errors.plate_number }"
                    required
                  />
                  <p v-if="form.errors.plate_number" class="text-sm text-red-600">{{ form.errors.plate_number }}</p>
                </div>

                <div class="space-y-2">
                  <Label for="vehicle_type">Vehicle Type (Optional)</Label>
                  <Input
                    id="vehicle_type"
                    v-model="form.vehicle_type"
                    type="text"
                    placeholder="e.g., Sedan, SUV, Truck"
                    :class="{ 'border-red-500': form.errors.vehicle_type }"
                  />
                  <p v-if="form.errors.vehicle_type" class="text-sm text-red-600">{{ form.errors.vehicle_type }}</p>
                </div>
              </div>
            </div>

            <!-- Package Selection -->
            <div class="space-y-4">
              <h3 class="text-lg font-semibold text-gray-900">Select Package (Optional)</h3>

              <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <button
                  v-for="pkg in packages"
                  :key="pkg.id"
                  type="button"
                  @click="form.package_id = pkg.id"
                  :class="[
                    'p-4 rounded-lg border-2 transition-all text-left',
                    form.package_id === pkg.id
                      ? 'ring-2 ring-blue-500 border-blue-500'
                      : getPackageColor(pkg.color)
                  ]"
                >
                  <div class="font-semibold text-gray-900">{{ pkg.name }}</div>
                  <div class="text-sm text-gray-600 mt-1">{{ pkg.description }}</div>
                  <div class="mt-2 flex items-center justify-between">
                    <span class="text-lg font-bold text-blue-600">${{ pkg.price }}</span>
                    <span class="text-xs text-gray-500">{{ pkg.duration }} min</span>
                  </div>
                </button>
              </div>
            </div>

            <!-- Special Requests -->
            <div class="space-y-2">
              <Label for="special_requests">Special Requests (Optional)</Label>
              <Textarea
                id="special_requests"
                v-model="form.special_requests"
                placeholder="Any special instructions or requests?"
                rows="3"
              />
            </div>

            <!-- Submit Button -->
            <div class="flex gap-4">
              <Button
                type="submit"
                :disabled="form.processing"
                class="flex-1 bg-gradient-to-r from-blue-500 to-green-500 hover:from-blue-600 hover:to-green-600"
              >
                <span v-if="form.processing">Joining Queue...</span>
                <span v-else>Join Queue</span>
              </Button>
            </div>
          </form>
        </CardContent>
      </Card>

      <!-- Info Card -->
      <Card class="mt-6 bg-blue-50 border-blue-200">
        <CardContent class="pt-6">
          <div class="flex items-start gap-3">
            <svg class="w-6 h-6 text-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <div class="text-sm text-blue-800">
              <p class="font-semibold mb-1">What happens next?</p>
              <ul class="list-disc list-inside space-y-1 text-blue-700">
                <li>You'll receive a queue number and position</li>
                <li>Track your queue status in real-time</li>
                <li>Get notified when it's your turn</li>
                <li>Drive to the designated bay when called</li>
              </ul>
            </div>
          </div>
        </CardContent>
      </Card>
    </div>
  </div>
</template>
