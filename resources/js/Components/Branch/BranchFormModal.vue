<script setup lang="ts">
import { ref, watch } from 'vue'
import { useForm } from '@inertiajs/vue3'
import { Dialog, DialogContent, DialogHeader, DialogTitle, Input, Label, Textarea, Button } from '@/Components/ui'
import { toast } from 'vue-sonner'

interface Branch {
  id: number
  name: string
  code: string
  is_active: boolean
  address?: string
  phone?: string
  operating_hours?: string
}

const props = defineProps<{
  isOpen: boolean
  branch?: Branch | null
}>()

const emit = defineEmits<{
  close: []
}>()

const form = useForm({
  name: '',
  code: '',
  address: '',
  phone: '',
  operating_hours: '',
  is_active: true,
})

watch([() => props.isOpen, () => props.branch], ([isOpen, branch]) => {
  if (isOpen) {
    if (branch) {
      form.name = branch.name
      form.code = branch.code
      form.address = branch.address || ''
      form.phone = branch.phone || ''
      form.operating_hours = branch.operating_hours || ''
      form.is_active = branch.is_active
    } else {
      form.reset()
      form.code = `BR-${Math.floor(Math.random() * 1000).toString().padStart(3, '0')}`
      form.is_active = true
    }
  }
}, { immediate: true })

const handleSubmit = () => {
  if (props.branch) {
    form.put(route('admin.branches.update', props.branch.id), {
      onSuccess: () => {
        toast.success('Branch updated successfully')
        emit('close')
      },
    })
  } else {
    form.post(route('admin.branches.store'), {
      onSuccess: () => {
        toast.success('Branch created successfully')
        emit('close')
      },
    })
  }
}
</script>

<template>
  <Dialog :open="isOpen" @update:open="(open) => !open && $emit('close')">
    <DialogContent class="sm:max-w-[600px]">
      <DialogHeader>
        <DialogTitle>{{ branch ? 'Edit Branch' : 'Add New Branch' }}</DialogTitle>
      </DialogHeader>

      <form @submit.prevent="handleSubmit" class="space-y-4 mt-4">
        <div class="grid grid-cols-2 gap-4">
          <div class="space-y-2">
            <Label for="name">Branch Name *</Label>
            <Input id="name" v-model="form.name" placeholder="Enter branch name" required />
          </div>

          <div class="space-y-2">
            <Label for="code">Branch Code *</Label>
            <Input id="code" v-model="form.code" placeholder="BR-001" required />
          </div>
        </div>

        <div class="space-y-2">
          <Label for="address">Address</Label>
          <Textarea id="address" v-model="form.address" placeholder="Enter branch address" rows="2" />
        </div>

        <div class="grid grid-cols-2 gap-4">
          <div class="space-y-2">
            <Label for="phone">Phone</Label>
            <Input id="phone" v-model="form.phone" placeholder="+1 (555) 123-4567" />
          </div>

          <div class="space-y-2">
            <Label for="operating_hours">Operating Hours</Label>
            <Input id="operating_hours" v-model="form.operating_hours" placeholder="8:00 AM - 8:00 PM" />
          </div>
        </div>

        <div class="flex items-center gap-2">
          <input
            id="is_active"
            v-model="form.is_active"
            type="checkbox"
            class="h-4 w-4 rounded border-gray-300"
          />
          <Label for="is_active" class="!mb-0">Active</Label>
        </div>

        <div class="flex justify-end gap-2 pt-4">
          <Button type="button" variant="outline" @click="$emit('close')">
            Cancel
          </Button>
          <Button type="submit" :disabled="form.processing">
            {{ form.processing ? 'Saving...' : (branch ? 'Update' : 'Create') }}
          </Button>
        </div>
      </form>
    </DialogContent>
  </Dialog>
</template>
