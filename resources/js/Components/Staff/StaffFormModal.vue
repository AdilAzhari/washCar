<script setup lang="ts">
import { watch, computed } from 'vue'
import { useForm, usePage } from '@inertiajs/vue3'
import { Dialog, DialogContent, DialogDescription, DialogHeader, DialogTitle, Input, Label, Button } from '@/Components/ui'
import { toast } from 'vue-sonner'

const props = defineProps<{
  isOpen: boolean
  staff?: any
  branches: any[]
}>()

const emit = defineEmits<{ close: [] }>()

const page = usePage()
const userRole = computed(() => (page.props.auth as any)?.user?.role || 'admin')

const getRouteName = (routeName: string) => {
  return `${userRole.value}.${routeName}`
}

const form = useForm({
  name: '',
  email: '',
  password: '',
  role: 'staff',
  branch_id: null as number | null,
})

const resetToDefaults = () => {
  form.role = 'staff'
  form.branch_id = null
}

// Watch for both modal state and data changes to sync the form
watch(
  [() => props.isOpen, () => props.staff],
  ([isOpen, staff]) => {
    if (isOpen) {
      if (staff) {
        form.name = staff.name
        form.email = staff.email
        form.role = staff.role
        form.branch_id = staff.branch?.id || null
        form.password = ''
      } else {
        form.reset()
        resetToDefaults()
      }
    } else {
      form.reset()
      resetToDefaults()
    }
  },
  { immediate: true, deep: true }
)

const handleSubmit = () => {
  if (props.staff) {
    form.put(route(getRouteName('staff.update'), props.staff.id), {
      onSuccess: () => {
        toast.success('Staff updated successfully')
        form.reset()
        resetToDefaults()
        emit('close')
      },
    })
  } else {
    form.post(route(getRouteName('staff.store')), {
      onSuccess: () => {
        toast.success('Staff created successfully')
        form.reset()
        resetToDefaults()
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
        <DialogTitle>{{ staff ? 'Edit Staff' : 'Add Staff' }}</DialogTitle>
        <DialogDescription class="sr-only">
          {{ staff ? 'Update staff member details and permissions.' : 'Add a new member to your team.' }}
        </DialogDescription>
      </DialogHeader>
      <form @submit.prevent="handleSubmit" class="space-y-4 mt-4">
        <div class="space-y-2">
          <Label for="name">Name *</Label>
          <Input id="name" v-model="form.name" required />
        </div>
        <div class="space-y-2">
          <Label for="email">Email *</Label>
          <Input id="email" v-model="form.email" type="email" required />
        </div>
        <div class="space-y-2">
          <Label for="password">Password {{ staff ? '(leave blank to keep current)' : '*' }}</Label>
          <Input id="password" v-model="form.password" type="password" :required="!staff" />
        </div>
        <div class="space-y-2">
          <Label for="role">Role *</Label>
          <select id="role" v-model="form.role" class="flex h-11 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background transition-all duration-200 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary focus-visible:ring-offset-2 focus-visible:border-primary" required>
            <option value="staff">Staff</option>
            <option value="manager">Manager</option>
            <option value="admin">Admin</option>
          </select>
        </div>
        <div class="space-y-2">
          <Label for="branch_id">Branch</Label>
          <select id="branch_id" v-model="form.branch_id" class="flex h-11 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background transition-all duration-200 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary focus-visible:ring-offset-2 focus-visible:border-primary">
            <option :value="null">No branch assigned</option>
            <option v-for="branch in branches" :key="branch.id" :value="branch.id">{{ branch.name }}</option>
          </select>
        </div>
        <div class="flex justify-end gap-2 pt-4">
          <Button type="button" variant="outline" @click="$emit('close')">Cancel</Button>
          <Button type="submit" :disabled="form.processing">{{ form.processing ? 'Saving...' : (staff ? 'Update' : 'Create') }}</Button>
        </div>
      </form>
    </DialogContent>
  </Dialog>
</template>
