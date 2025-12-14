<script setup lang="ts">
import { ref, watch } from 'vue'
import { useForm } from '@inertiajs/vue3'
import { Dialog, DialogContent, DialogHeader, DialogTitle, Input, Label, Button, Textarea } from '@/Components/ui'
import { toast } from 'vue-sonner'

interface Package {
  id: number
  name: string
  description?: string
  price: number
  duration_minutes: number
  color: string
  is_active: boolean
}

const props = defineProps<{
  isOpen: boolean
  package?: Package | null
}>()

const emit = defineEmits<{
  close: []
}>()

const form = useForm({
  name: '',
  description: '',
  price: 0,
  duration_minutes: 15,
  color: '#3b82f6',
  is_active: true,
})

const predefinedColors = [
  { name: 'Blue', value: '#3b82f6' },
  { name: 'Green', value: '#10b981' },
  { name: 'Orange', value: '#f59e0b' },
  { name: 'Purple', value: '#a855f7' },
  { name: 'Red', value: '#ef4444' },
  { name: 'Pink', value: '#ec4899' },
  { name: 'Teal', value: '#14b8a6' },
  { name: 'Yellow', value: '#eab308' },
]

watch(() => props.isOpen, (isOpen) => {
  if (isOpen) {
    if (props.package) {
      form.name = props.package.name
      form.description = props.package.description || ''
      form.price = props.package.price
      form.duration_minutes = props.package.duration_minutes
      form.color = props.package.color
      form.is_active = props.package.is_active
    } else {
      form.reset()
      form.duration_minutes = 15
      form.color = '#3b82f6'
      form.is_active = true
    }
  }
})

const handleSubmit = () => {
  if (props.package) {
    form.put(route('packages.update', props.package.id), {
      onSuccess: () => {
        toast.success('Package updated successfully')
        emit('close')
      },
    })
  } else {
    form.post(route('packages.store'), {
      onSuccess: () => {
        toast.success('Package created successfully')
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
        <DialogTitle>{{ package ? 'Edit Package' : 'Add New Package' }}</DialogTitle>
      </DialogHeader>

      <form @submit.prevent="handleSubmit" class="space-y-4 mt-4">
        <div class="space-y-2">
          <Label for="name">Package Name *</Label>
          <Input id="name" v-model="form.name" placeholder="e.g., Premium Wash" required />
        </div>

        <div class="space-y-2">
          <Label for="description">Description</Label>
          <Textarea
            id="description"
            v-model="form.description"
            placeholder="Describe what's included in this package"
            rows="3"
          />
        </div>

        <div class="grid grid-cols-2 gap-4">
          <div class="space-y-2">
            <Label for="price">Price ($) *</Label>
            <Input
              id="price"
              v-model.number="form.price"
              type="number"
              step="0.01"
              min="0"
              placeholder="25.00"
              required
            />
          </div>

          <div class="space-y-2">
            <Label for="duration_minutes">Duration (minutes) *</Label>
            <Input
              id="duration_minutes"
              v-model.number="form.duration_minutes"
              type="number"
              min="1"
              placeholder="30"
              required
            />
          </div>
        </div>

        <div class="space-y-2">
          <Label>Color *</Label>
          <div class="grid grid-cols-4 gap-2">
            <button
              v-for="color in predefinedColors"
              :key="color.value"
              type="button"
              @click="form.color = color.value"
              class="flex items-center gap-2 p-2 rounded-md border-2 transition-all hover:scale-105"
              :class="form.color === color.value ? 'border-primary' : 'border-transparent'"
            >
              <div
                class="w-6 h-6 rounded-full"
                :style="{ backgroundColor: color.value }"
              ></div>
              <span class="text-xs">{{ color.name }}</span>
            </button>
          </div>
          <div class="flex items-center gap-2 mt-2">
            <Label for="custom-color" class="text-xs">Custom:</Label>
            <input
              id="custom-color"
              v-model="form.color"
              type="color"
              class="h-10 w-20 rounded cursor-pointer"
            />
            <Input
              v-model="form.color"
              placeholder="#3b82f6"
              class="flex-1"
            />
          </div>
        </div>

        <div class="space-y-2">
          <Label for="is_active">Status</Label>
          <select
            id="is_active"
            v-model="form.is_active"
            class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm"
          >
            <option :value="true">Active</option>
            <option :value="false">Inactive</option>
          </select>
        </div>

        <div class="flex justify-end gap-2 pt-4">
          <Button type="button" variant="outline" @click="$emit('close')">
            Cancel
          </Button>
          <Button type="submit" :disabled="form.processing">
            {{ form.processing ? 'Saving...' : (package ? 'Update' : 'Create') }}
          </Button>
        </div>
      </form>
    </DialogContent>
  </Dialog>
</template>
