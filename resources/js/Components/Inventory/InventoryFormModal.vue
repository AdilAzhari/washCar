<script setup lang="ts">
import { watch } from 'vue'
import { useForm } from '@inertiajs/vue3'
import { Dialog, DialogContent, DialogHeader, DialogTitle, Input, Label, Button, Textarea } from '@/Components/ui'
import { toast } from 'vue-sonner'

const props = defineProps<{
  isOpen: boolean
  item?: any
  branches: any[]
}>()

const emit = defineEmits<{ close: [] }>()

const form = useForm({
  branch_id: null as number | null,
  name: '',
  category: '',
  quantity: 0,
  min_quantity: 10,
  unit: '',
  unit_price: 0,
  notes: '',
})

watch(() => props.isOpen, (isOpen) => {
  if (isOpen && props.item) {
    form.branch_id = props.item.branch.id
    form.name = props.item.name
    form.category = props.item.category
    form.quantity = props.item.quantity
    form.min_quantity = props.item.min_quantity
    form.unit = props.item.unit
    form.unit_price = props.item.unit_price
    form.notes = props.item.notes || ''
  } else if (isOpen) {
    form.reset()
  }
})

const handleSubmit = () => {
  if (props.item) {
    form.put(route('inventory.update', props.item.id), {
      onSuccess: () => {
        toast.success('Item updated successfully')
        emit('close')
      },
    })
  } else {
    form.post(route('inventory.store'), {
      onSuccess: () => {
        toast.success('Item created successfully')
        emit('close')
      },
    })
  }
}
</script>

<template>
  <Dialog :open="isOpen" @update:open="(open) => !open && $emit('close')">
    <DialogContent class="sm:max-w-[500px] max-h-[90vh] overflow-y-auto">
      <DialogHeader>
        <DialogTitle>{{ item ? 'Edit Item' : 'Add Item' }}</DialogTitle>
      </DialogHeader>
      <form @submit.prevent="handleSubmit" class="space-y-4 mt-4">
        <div class="space-y-2">
          <Label for="branch_id">Branch *</Label>
          <select id="branch_id" v-model="form.branch_id" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm" required>
            <option :value="null">Select Branch</option>
            <option v-for="branch in branches" :key="branch.id" :value="branch.id">{{ branch.name }}</option>
          </select>
        </div>
        <div class="grid grid-cols-2 gap-4">
          <div class="space-y-2">
            <Label for="name">Name *</Label>
            <Input id="name" v-model="form.name" required />
          </div>
          <div class="space-y-2">
            <Label for="category">Category *</Label>
            <Input id="category" v-model="form.category" required />
          </div>
        </div>
        <div class="grid grid-cols-3 gap-4">
          <div class="space-y-2">
            <Label for="quantity">Quantity *</Label>
            <Input id="quantity" v-model.number="form.quantity" type="number" min="0" required />
          </div>
          <div class="space-y-2">
            <Label for="min_quantity">Min Qty *</Label>
            <Input id="min_quantity" v-model.number="form.min_quantity" type="number" min="0" required />
          </div>
          <div class="space-y-2">
            <Label for="unit">Unit *</Label>
            <Input id="unit" v-model="form.unit" placeholder="pcs, ltr" required />
          </div>
        </div>
        <div class="space-y-2">
          <Label for="unit_price">Unit Price ($) *</Label>
          <Input id="unit_price" v-model.number="form.unit_price" type="number" step="0.01" min="0" required />
        </div>
        <div class="space-y-2">
          <Label for="notes">Notes</Label>
          <Textarea id="notes" v-model="form.notes" rows="3" />
        </div>
        <div class="flex justify-end gap-2 pt-4">
          <Button type="button" variant="outline" @click="$emit('close')">Cancel</Button>
          <Button type="submit" :disabled="form.processing">{{ form.processing ? 'Saving...' : (item ? 'Update' : 'Create') }}</Button>
        </div>
      </form>
    </DialogContent>
  </Dialog>
</template>
