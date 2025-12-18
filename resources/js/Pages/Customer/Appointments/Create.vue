<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3'
import CustomerLayout from '@/Layouts/CustomerLayout.vue'
import { Calendar, MapPin, Package, Car, FileText, ArrowRight } from 'lucide-vue-next'
import { ref } from 'vue'

const props = defineProps<{
  branches: Array<{
    id: number
    name: string
    code: string
    address: string
  }>
  packages: Array<{
    id: number
    name: string
    description: string
    price: number
    estimated_duration: number
    loyalty_points: number
  }>
}>()

const form = useForm({
  branch_id: '',
  package_id: '',
  scheduled_at: '',
  plate_number: '',
  vehicle_type: '',
  special_requests: '',
})

const step = ref(1)

const selectedBranch = ref<any>(null)
const selectedPackage = ref<any>(null)

const selectBranch = (branch: any) => {
  form.branch_id = branch.id
  selectedBranch.value = branch
  step.value = 2
}

const selectPackage = (pkg: any) => {
  form.package_id = pkg.id
  selectedPackage.value = pkg
  step.value = 3
}

const submit = () => {
  form.post(route('customer.appointments.store'), {
    onSuccess: () => {
      // Redirect handled by controller
    },
  })
}

// Get minimum date (today + 1 hour)
const getMinDateTime = () => {
  const now = new Date()
  now.setHours(now.getHours() + 1)
  return now.toISOString().slice(0, 16)
}
</script>

<template>
  <Head title="Book Appointment" />

  <CustomerLayout>
    <div class="p-6 max-w-4xl mx-auto space-y-6">
      <!-- Header -->
      <div>
        <h1 class="text-3xl font-bold">Book an Appointment</h1>
        <p class="text-muted-foreground mt-1">Schedule your car wash and earn bonus points</p>
      </div>

      <!-- Progress Steps -->
      <div class="flex items-center justify-between mb-8">
        <div
          v-for="(stepInfo, index) in [
            { number: 1, label: 'Location' },
            { number: 2, label: 'Package' },
            { number: 3, label: 'Details' },
          ]"
          :key="index"
          class="flex-1 flex items-center"
        >
          <div class="flex items-center gap-2 flex-1">
            <div
              :class="[
                'flex items-center justify-center w-8 h-8 rounded-full font-bold text-sm transition-colors',
                step >= stepInfo.number
                  ? 'bg-primary text-primary-foreground'
                  : 'bg-muted text-muted-foreground',
              ]"
            >
              {{ stepInfo.number }}
            </div>
            <span
              :class="[
                'text-sm font-medium hidden sm:inline',
                step >= stepInfo.number ? 'text-foreground' : 'text-muted-foreground',
              ]"
            >
              {{ stepInfo.label }}
            </span>
          </div>
          <div
            v-if="index < 2"
            :class="[
              'flex-1 h-0.5 mx-2',
              step > stepInfo.number ? 'bg-primary' : 'bg-muted',
            ]"
          />
        </div>
      </div>

      <!-- Step 1: Select Branch -->
      <div v-show="step === 1" class="space-y-4">
        <h2 class="text-xl font-bold flex items-center gap-2">
          <MapPin class="w-5 h-5" />
          Select Location
        </h2>

        <div class="grid gap-4 md:grid-cols-2">
          <button
            v-for="branch in branches"
            :key="branch.id"
            @click="selectBranch(branch)"
            class="p-6 bg-card border rounded-lg text-left hover:border-primary hover:shadow-md transition-all"
          >
            <h3 class="font-bold text-lg mb-1">{{ branch.name }}</h3>
            <p class="text-sm text-muted-foreground">{{ branch.address }}</p>
            <p class="text-xs text-muted-foreground mt-2">Code: {{ branch.code }}</p>
          </button>
        </div>
      </div>

      <!-- Step 2: Select Package -->
      <div v-show="step === 2" class="space-y-4">
        <div class="flex items-center justify-between">
          <h2 class="text-xl font-bold flex items-center gap-2">
            <Package class="w-5 h-5" />
            Select Package
          </h2>
          <button
            @click="step = 1"
            class="text-sm text-primary hover:underline"
          >
            Change Location
          </button>
        </div>

        <div class="grid gap-4 md:grid-cols-2">
          <button
            v-for="pkg in packages"
            :key="pkg.id"
            @click="selectPackage(pkg)"
            class="p-6 bg-card border rounded-lg text-left hover:border-primary hover:shadow-md transition-all"
          >
            <h3 class="font-bold text-lg mb-2">{{ pkg.name }}</h3>
            <p class="text-sm text-muted-foreground mb-4">{{ pkg.description }}</p>
            <div class="flex items-center justify-between">
              <div class="text-2xl font-bold text-primary">${{ pkg.price.toFixed(2) }}</div>
              <div class="text-sm text-yellow-600 font-medium">
                +{{ pkg.loyalty_points }} pts bonus
              </div>
            </div>
            <div class="text-xs text-muted-foreground mt-2">
              Est. {{ pkg.estimated_duration }} minutes
            </div>
          </button>
        </div>
      </div>

      <!-- Step 3: Enter Details -->
      <div v-show="step === 3" class="space-y-6">
        <div class="flex items-center justify-between">
          <h2 class="text-xl font-bold flex items-center gap-2">
            <FileText class="w-5 h-5" />
            Appointment Details
          </h2>
          <button
            @click="step = 2"
            class="text-sm text-primary hover:underline"
          >
            Change Package
          </button>
        </div>

        <!-- Summary Card -->
        <div class="p-6 bg-gradient-to-br from-primary/5 to-primary/10 border border-primary/20 rounded-lg">
          <div class="grid gap-4 sm:grid-cols-2">
            <div>
              <div class="text-sm text-muted-foreground mb-1">Location</div>
              <div class="font-semibold">{{ selectedBranch?.name }}</div>
            </div>
            <div>
              <div class="text-sm text-muted-foreground mb-1">Package</div>
              <div class="font-semibold">{{ selectedPackage?.name }}</div>
            </div>
            <div>
              <div class="text-sm text-muted-foreground mb-1">Price</div>
              <div class="font-bold text-primary">${{ selectedPackage?.price.toFixed(2) }}</div>
            </div>
            <div>
              <div class="text-sm text-muted-foreground mb-1">Bonus Points</div>
              <div class="font-semibold text-yellow-600">+{{ selectedPackage?.loyalty_points }} pts</div>
            </div>
          </div>
        </div>

        <form @submit.prevent="submit" class="space-y-4">
          <!-- Date & Time -->
          <div>
            <label class="block text-sm font-medium mb-2">
              <Calendar class="w-4 h-4 inline mr-1" />
              Date & Time
            </label>
            <input
              v-model="form.scheduled_at"
              type="datetime-local"
              :min="getMinDateTime()"
              required
              class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary"
            />
            <p v-if="form.errors.scheduled_at" class="text-sm text-red-600 mt-1">
              {{ form.errors.scheduled_at }}
            </p>
          </div>

          <!-- Vehicle Type -->
          <div>
            <label class="block text-sm font-medium mb-2">
              <Car class="w-4 h-4 inline mr-1" />
              Vehicle Type
            </label>
            <select
              v-model="form.vehicle_type"
              required
              class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary"
            >
              <option value="">Select vehicle type</option>
              <option value="Sedan">Sedan</option>
              <option value="SUV">SUV</option>
              <option value="Truck">Truck</option>
              <option value="Van">Van</option>
              <option value="Motorcycle">Motorcycle</option>
            </select>
            <p v-if="form.errors.vehicle_type" class="text-sm text-red-600 mt-1">
              {{ form.errors.vehicle_type }}
            </p>
          </div>

          <!-- Plate Number -->
          <div>
            <label class="block text-sm font-medium mb-2">Plate Number</label>
            <input
              v-model="form.plate_number"
              type="text"
              required
              placeholder="ABC-1234"
              class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary uppercase"
            />
            <p v-if="form.errors.plate_number" class="text-sm text-red-600 mt-1">
              {{ form.errors.plate_number }}
            </p>
          </div>

          <!-- Special Requests -->
          <div>
            <label class="block text-sm font-medium mb-2">Special Requests (Optional)</label>
            <textarea
              v-model="form.special_requests"
              rows="3"
              placeholder="Any special instructions for your wash..."
              class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary resize-none"
            />
            <p v-if="form.errors.special_requests" class="text-sm text-red-600 mt-1">
              {{ form.errors.special_requests }}
            </p>
          </div>

          <!-- Submit -->
          <div class="flex items-center gap-4 pt-4">
            <button
              type="button"
              @click="step = 2"
              class="px-6 py-3 border rounded-lg font-medium hover:bg-muted transition-colors"
            >
              Back
            </button>
            <button
              type="submit"
              :disabled="form.processing"
              class="flex-1 flex items-center justify-center gap-2 px-6 py-3 bg-primary text-primary-foreground rounded-lg font-medium hover:bg-primary/90 transition-colors disabled:opacity-50"
            >
              <span>{{ form.processing ? 'Booking...' : 'Confirm Booking' }}</span>
              <ArrowRight v-if="!form.processing" class="w-5 h-5" />
            </button>
          </div>
        </form>
      </div>
    </div>
  </CustomerLayout>
</template>
