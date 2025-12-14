<script setup lang="ts">
import { ref, computed } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import InventoryFormModal from '@/Components/Inventory/InventoryFormModal.vue'
import { Card, CardContent, CardHeader, CardTitle, Badge, Button, DeleteConfirmDialog } from '@/Components/ui'
import { Plus, Edit, Trash2, Package, AlertTriangle } from 'lucide-vue-next'

interface InventoryItem {
  id: number
  name: string
  category: string
  quantity: number
  min_quantity: number
  unit: string
  unit_price: number
  branch: { id: number; name: string }
  notes?: string
}

const props = defineProps<{
  items: InventoryItem[]
  branches: any[]
}>()

const isFormOpen = ref(false)
const selectedItem = ref<InventoryItem | null>(null)
const deleteDialogOpen = ref(false)
const itemToDelete = ref<InventoryItem | null>(null)
const isDeleting = ref(false)

const stats = computed(() => ({
  total: props.items.length,
  lowStock: props.items.filter(i => i.quantity <= i.min_quantity).length,
  totalValue: props.items.reduce((sum, i) => sum + (i.quantity * i.unit_price), 0),
}))

const handleEdit = (item: InventoryItem) => {
  selectedItem.value = item
  isFormOpen.value = true
}

const handleDelete = (id: number) => {
  const item = props.items.find(i => i.id === id)
  if (item) {
    itemToDelete.value = item
    deleteDialogOpen.value = true
  }
}

const handleDeleteConfirm = () => {
  if (itemToDelete.value) {
    isDeleting.value = true
    router.delete(route('inventory.destroy', itemToDelete.value.id), {
      onSuccess: () => {
        deleteDialogOpen.value = false
        itemToDelete.value = null
      },
      onFinish: () => {
        isDeleting.value = false
      }
    })
  }
}

const isLowStock = (item: InventoryItem) => item.quantity <= item.min_quantity
</script>

<template>
  <Head title="Inventory" />
  <AuthenticatedLayout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">Inventory</h2>
    </template>
    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="space-y-6 animate-fade-in">
          <!-- Header -->
          <div class="flex items-center justify-between">
            <div>
              <h1 class="text-2xl font-bold mb-2">Inventory Management</h1>
              <p class="text-muted-foreground">Track supplies and stock levels</p>
            </div>
            <Button class="btn-primary" @click="() => { selectedItem = null; isFormOpen = true }">
              <Plus class="w-4 h-4 mr-2" />
              Add Item
            </Button>
          </div>
          <!-- Stats Grid -->
          <div class="grid gap-4 md:grid-cols-3">
            <Card class="stat-card">
              <CardContent class="p-6">
                <div class="flex items-center justify-between">
                  <div>
                    <p class="text-sm text-muted-foreground">Total Items</p>
                    <p class="text-3xl font-bold mt-1">{{ stats.total }}</p>
                  </div>
                  <Package class="w-8 h-8 text-primary" />
                </div>
              </CardContent>
            </Card>
            <Card class="stat-card">
              <CardContent class="p-6">
                <div class="flex items-center justify-between">
                  <div>
                    <p class="text-sm text-muted-foreground">Low Stock</p>
                    <p class="text-3xl font-bold mt-1 text-destructive">{{ stats.lowStock }}</p>
                  </div>
                  <AlertTriangle class="w-8 h-8 text-destructive" />
                </div>
              </CardContent>
            </Card>
            <Card class="stat-card">
              <CardContent class="p-6">
                <div class="flex items-center justify-between">
                  <div>
                    <p class="text-sm text-muted-foreground">Total Value</p>
                    <p class="text-3xl font-bold mt-1">${{ stats.totalValue.toFixed(2) }}</p>
                  </div>
                  <Package class="w-8 h-8 text-success" />
                </div>
              </CardContent>
            </Card>
          </div>
          <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
            <Card v-for="item in items" :key="item.id" class="hover-lift">
              <CardHeader>
                <div class="flex items-center justify-between">
                  <CardTitle class="text-lg">{{ item.name }}</CardTitle>
                  <Badge v-if="isLowStock(item)" variant="destructive"><AlertTriangle class="w-3 h-3 mr-1" />Low Stock</Badge>
                </div>
                <p class="text-sm text-muted-foreground">{{ item.category }} • {{ item.branch.name }}</p>
              </CardHeader>
              <CardContent class="space-y-3">
                <div class="grid grid-cols-2 gap-2">
                  <div class="bg-muted/50 rounded-lg p-2">
                    <p class="text-xs text-muted-foreground">Quantity</p>
                    <p class="font-bold">{{ item.quantity }} {{ item.unit }}</p>
                  </div>
                  <div class="bg-muted/50 rounded-lg p-2">
                    <p class="text-xs text-muted-foreground">Unit Price</p>
                    <p class="font-bold">${{ item.unit_price }}</p>
                  </div>
                </div>
                <div class="flex gap-2 pt-2 border-t">
                  <Button size="sm" variant="outline" class="flex-1" @click="handleEdit(item)"><Edit class="w-4 h-4 mr-1" />Edit</Button>
                  <Button size="sm" variant="outline" @click="handleDelete(item.id)"><Trash2 class="w-4 h-4" /></Button>
                </div>
              </CardContent>
            </Card>
          </div>
          <InventoryFormModal :is-open="isFormOpen" :item="selectedItem" :branches="branches" @close="() => { isFormOpen = false; selectedItem = null }" />

          <!-- Delete Confirmation Dialog -->
          <DeleteConfirmDialog
            :is-open="deleteDialogOpen"
            :item-name="itemToDelete?.name"
            :is-deleting="isDeleting"
            title="Delete Inventory Item"
            message="Are you sure you want to delete this inventory item? This action cannot be undone."
            @close="deleteDialogOpen = false"
            @confirm="handleDeleteConfirm"
          />
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
