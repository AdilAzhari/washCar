<script setup lang="ts">
import { ref, watch, computed } from 'vue'
import { useForm, usePage } from '@inertiajs/vue3'
import { Dialog, DialogContent, DialogHeader, DialogTitle, Input, Label, Button } from '@/Components/ui'
import { toast } from 'vue-sonner'

interface Customer {
  id: number
  name: string | null
  phone: string | null
  email?: string | null
  plate_number: string | null
  vehicle_type: string | null
  make?: string | null
  model?: string | null
  color?: string | null
  membership: string
  status: string
}

const props = defineProps<{
  isOpen: boolean
  customer?: Customer | null
}>()

const emit = defineEmits<{
  close: []
}>()

const page = usePage()
const userRole = computed(() => (page.props.auth as any)?.user?.role || 'admin')

const getRouteName = (routeName: string) => {
  return `${userRole.value}.${routeName}`
}

const form = useForm({
  name: '',
  phone: '',
  email: '',
  plate_number: '',
  vehicle_type: 'sedan',
  make: '',
  model: '',
  color: '',
  membership: 'Regular',
  status: 'active',
})

watch([() => props.isOpen, () => props.customer], ([isOpen, customer]) => {
  if (isOpen) {
    if (customer) {
      form.name = customer.name || ''
      form.phone = customer.phone || ''
      form.email = customer.email || ''
      form.plate_number = customer.plate_number || ''
      form.vehicle_type = customer.vehicle_type || 'sedan'
      form.make = customer.make || ''
      form.model = customer.model || ''
      form.color = customer.color || ''
      form.membership = customer.membership
      form.status = customer.status
    } else {
      form.reset()
      form.vehicle_type = 'sedan'
      form.membership = 'Regular'
      form.status = 'active'
    }
  }
}, { immediate: true })

const handleSubmit = () => {
  if (props.customer) {
    form.put(route(getRouteName('customers.update'), props.customer.id), {
      onSuccess: () => {
        toast.success('Customer updated successfully')
        emit('close')
      },
    })
  } else {
    form.post(route(getRouteName('customers.store')), {
      onSuccess: () => {
        toast.success('Customer created successfully')
        emit('close')
      },
    })
  }
}
</script>

<template>
  <Dialog :open="isOpen" @update:open="(open) => !open && $emit('close')">
    <DialogContent class="sm:max-w-[600px] max-h-[90vh] overflow-y-auto">
      <DialogHeader>
        <DialogTitle>{{ customer ? 'Edit Customer' : 'Add New Customer' }}</DialogTitle>
      </DialogHeader>

      <form @submit.prevent="handleSubmit" class="space-y-4 mt-4">
        <div class="grid grid-cols-2 gap-4">
          <div class="space-y-2">
            <Label for="name">Name *</Label>
            <Input id="name" v-model="form.name" placeholder="Customer name" required />
          </div>

          <div class="space-y-2">
            <Label for="phone">Phone *</Label>
            <Input id="phone" v-model="form.phone" placeholder="+1 (555) 123-4567" required />
          </div>
        </div>

        <div class="space-y-2">
          <Label for="email">Email</Label>
          <Input id="email" v-model="form.email" type="email" placeholder="customer@email.com" />
        </div>

        <div class="grid grid-cols-2 gap-4">
          <div class="space-y-2">
            <Label for="plate_number">Plate Number *</Label>
            <Input id="plate_number" v-model="form.plate_number" placeholder="ABC-1234" required />
          </div>

          <div class="space-y-2">
            <Label for="vehicle_type">Vehicle Type *</Label>
            <select
              id="vehicle_type"
              v-model="form.vehicle_type"
              class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm"
              required
            >
              <option value="sedan">Sedan</option>
              <option value="suv">SUV</option>
              <option value="truck">Truck</option>
              <option value="van">Van</option>
              <option value="motorcycle">Motorcycle</option>
            </select>
          </div>
        </div>

        <div class="grid grid-cols-3 gap-4">
          <div class="space-y-2">
            <Label for="make">Make</Label>
            <Input id="make" v-model="form.make" placeholder="Toyota" />
          </div>

          <div class="space-y-2">
            <Label for="model">Model</Label>
            <Input id="model" v-model="form.model" placeholder="Camry" />
          </div>

          <div class="space-y-2">
            <Label for="color">Color</Label>
            <Input id="color" v-model="form.color" placeholder="White" />
          </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
          <div class="space-y-2">
            <Label for="membership">Membership</Label>
            <select
              id="membership"
              v-model="form.membership"
              class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm"
            >
              <option value="Regular">Regular</option>
              <option value="Silver">Silver</option>
              <option value="Gold">Gold</option>
              <option value="Platinum">Platinum</option>
            </select>
          </div>

          <div class="space-y-2">
            <Label for="status">Status</Label>
            <select
              id="status"
              v-model="form.status"
              class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm"
            >
              <option value="active">Active</option>
              <option value="inactive">Inactive</option>
            </select>
          </div>
        </div>

        <div class="flex justify-end gap-2 pt-4">
          <Button type="button" variant="outline" @click="$emit('close')">
            Cancel
          </Button>
          <Button type="submit" :disabled="form.processing">
            {{ form.processing ? 'Saving...' : (customer ? 'Update' : 'Create') }}
          </Button>
        </div>
      </form>
    </DialogContent>
  </Dialog>
</template>
