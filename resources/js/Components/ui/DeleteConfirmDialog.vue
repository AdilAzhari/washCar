<script setup lang="ts">
import { Dialog, DialogContent, DialogDescription, DialogHeader, DialogTitle, DialogFooter } from '@/Components/ui'
import { Button } from '@/Components/ui'
import { AlertTriangle } from 'lucide-vue-next'

const props = defineProps<{
  isOpen: boolean
  title?: string
  message?: string
  itemName?: string
  isDeleting?: boolean
}>()

const emit = defineEmits<{
  close: []
  confirm: []
}>()
</script>

<template>
  <Dialog :open="isOpen" @update:open="(open) => !open && $emit('close')">
    <DialogContent class="sm:max-w-[425px]">
      <DialogHeader>
        <DialogTitle class="flex items-center gap-2">
          <AlertTriangle class="w-5 h-5 text-destructive" />
          {{ title || 'Confirm Delete' }}
        </DialogTitle>
        <DialogDescription class="sr-only">
          {{ message || 'Confirm if you want to permanently delete this item.' }}
        </DialogDescription>
      </DialogHeader>
      <div class="py-4">
        <p class="text-muted-foreground">
          {{ message || 'Are you sure you want to delete this item? This action cannot be undone.' }}
        </p>
        <p v-if="itemName" class="mt-3 font-semibold">
          {{ itemName }}
        </p>
      </div>
      <DialogFooter>
        <Button variant="outline" @click="$emit('close')" :disabled="isDeleting">
          Cancel
        </Button>
        <Button variant="destructive" @click="$emit('confirm')" :disabled="isDeleting">
          {{ isDeleting ? 'Deleting...' : 'Delete' }}
        </Button>
      </DialogFooter>
    </DialogContent>
  </Dialog>
</template>
