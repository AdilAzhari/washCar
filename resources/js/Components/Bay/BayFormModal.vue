<script setup lang="ts">
import { ref, watch, computed } from 'vue'
import { useForm, usePage } from '@inertiajs/vue3'
import { Dialog, DialogContent, DialogHeader, DialogTitle, Input, Label, Button } from '@/Components/ui'
import { toast } from 'vue-sonner'

interface Bay {
  id: number
  name: string
  branch_id?: number
  branch?: {
    id: number
    name: string
  }
  status: string
}

interface Branch {
  id: number
  name: string
}

const props = defineProps<{
  isOpen: boolean
  bay?: Bay | null
  branches: Branch[]
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
  branch_id: null as number | null,
  status: 'idle' as 'idle' | 'active' | 'maintenance',
})

watch([() => props.isOpen, () => props.bay], ([isOpen, bay]) => {
  if (isOpen) {
    if (bay) {
      form.name = bay.name
      form.branch_id = bay.branch_id || bay.branch?.id || null
      form.status = bay.status as any
    } else {
      form.reset()
      form.status = 'idle'
    }
  }
})

const handleSubmit = () => {
  if (props.bay) {
    form.put(route(getRouteName('bays.update'), props.bay.id), {
      onSuccess: () => {
        toast.success('Bay updated successfully')
        emit('close')
      },
    })
  } else {
    form.post(route(getRouteName('bays.store')), {
      onSuccess: () => {
        toast.success('Bay created successfully')
        emit('close')
      },
    })
  }
}
</script>

<template>
  <Dialog :open="isOpen" @update:open="(open) => !open && $emit('close')">
    <DialogContent class="sm:max-w-[500px]">
      <DialogHeader>
        <DialogTitle>{{ bay ? 'Edit Bay' : 'Add New Bay' }}</DialogTitle>
      </DialogHeader>

      <form @submit.prevent="handleSubmit" class="space-y-4 mt-4">
        <div class="space-y-2">
          <Label for="name">Bay Name *</Label>
          <Input id="name" v-model="form.name" placeholder="Enter bay name" required />
        </div>

        <div class="space-y-2">
          <Label for="branch_id">Branch *</Label>
          <select
            id="branch_id"
            v-model="form.branch_id"
            class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background"
            required
          >
            <option :value="null" disabled>Select a branch</option>
            <option v-for="branch in branches" :key="branch.id" :value="branch.id">
              {{ branch.name }}
            </option>
          </select>
        </div>

        <div class="space-y-2">
          <Label for="status">Status *</Label>
          <select
            id="status"
            v-model="form.status"
            class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background"
            required
          >
            <option value="idle">Idle</option>
            <option value="active">Active</option>
            <option value="maintenance">Maintenance</option>
          </select>
        </div>

        <div class="flex justify-end gap-2 pt-4">
          <Button type="button" variant="outline" @click="$emit('close')">
            Cancel
          </Button>
          <Button type="submit" :disabled="form.processing">
            {{ form.processing ? 'Saving...' : (bay ? 'Update' : 'Create') }}
          </Button>
        </div>
      </form>
    </DialogContent>
  </Dialog>
</template>
