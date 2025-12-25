<script setup lang="ts">
import { ref, watch, computed } from 'vue'
import { useForm, usePage } from '@inertiajs/vue3'
import { Dialog, DialogContent, DialogDescription, DialogHeader, DialogTitle, Input, Label, Button } from '@/Components/ui'
import { toast } from 'vue-sonner'

const props = defineProps<{
  isOpen: boolean
  branches: any[]
  customers: any[]
  packages: any[]
}>()

const emit = defineEmits<{ close: [] }>()

const page = usePage()
const userRole = computed(() => (page.props.auth as any)?.user?.role || 'admin')

const getRouteName = (routeName: string) => {
  return `${userRole.value}.${routeName}`
}

const form = useForm({
  branch_id: null as number | null,
  customer_id: null as number | null,
  package_id: null as number | null,
  plate_number: '',
})

// Watch for modal state - reset form when opening or closing
watch(() => props.isOpen, (isOpen, wasOpen) => {
  if (isOpen && !wasOpen) {
    // Reset when opening
    form.reset()
  } else if (wasOpen && !isOpen) {
    // Reset when closing
    form.reset()
  }
})

const handleSubmit = () => {
  form.post(route(getRouteName('queue.store')), {
    onSuccess: () => {
      toast.success('Added to queue successfully')
      form.reset()
      emit('close')
    },
  })
}
</script>

<template>
  <Dialog :open="isOpen" @update:open="(open) => !open && $emit('close')">
    <DialogContent class="sm:max-w-[500px]">
      <DialogHeader>
        <DialogTitle>Add to Queue</DialogTitle>
        <DialogDescription class="sr-only">
          Enter customer and vehicle details to add them to the service queue.
        </DialogDescription>
      </DialogHeader>
      <form @submit.prevent="handleSubmit" class="space-y-4 mt-4">
        <div class="space-y-2">
          <Label for="branch_id">Branch *</Label>
          <select id="branch_id" v-model="form.branch_id" class="flex h-11 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background transition-all duration-200 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary focus-visible:ring-offset-2 focus-visible:border-primary" required>
            <option :value="null">Select Branch</option>
            <option v-for="branch in branches" :key="branch.id" :value="branch.id">{{ branch.name }}</option>
          </select>
        </div>
        <div class="space-y-2">
          <Label for="customer_id">Customer (Optional)</Label>
          <select id="customer_id" v-model="form.customer_id" class="flex h-11 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background transition-all duration-200 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary focus-visible:ring-offset-2 focus-visible:border-primary">
            <option :value="null">Walk-in Customer</option>
            <option v-for="customer in customers" :key="customer.id" :value="customer.id">{{ customer.name }}</option>
          </select>
        </div>
        <div class="space-y-2">
          <Label for="package_id">Package (Optional)</Label>
          <select id="package_id" v-model="form.package_id" class="flex h-11 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background transition-all duration-200 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary focus-visible:ring-offset-2 focus-visible:border-primary">
            <option :value="null">Select later</option>
            <option v-for="pkg in packages" :key="pkg.id" :value="pkg.id">{{ pkg.name }}</option>
          </select>
        </div>
        <div class="space-y-2">
          <Label for="plate_number">Plate Number *</Label>
          <Input id="plate_number" v-model="form.plate_number" required />
        </div>
        <div class="flex justify-end gap-2 pt-4">
          <Button type="button" variant="outline" @click="$emit('close')">Cancel</Button>
          <Button type="submit" :disabled="form.processing">{{ form.processing ? 'Adding...' : 'Add to Queue' }}</Button>
        </div>
      </form>
    </DialogContent>
  </Dialog>
</template>
