<script setup lang="ts">
import { ref, computed } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import BranchCard from '@/Components/Branch/BranchCard.vue'
import BranchFormModal from '@/Components/Branch/BranchFormModal.vue'
import { Button, Input, DeleteConfirmDialog } from '@/Components/ui'
import { Building2, Plus, TrendingUp, MapPin, GitCompare } from 'lucide-vue-next'

interface Branch {
  id: number
  name: string
  code: string
  is_active: boolean
  address?: string
  phone?: string
  operating_hours?: string
  bays_count?: number
  washes_count?: number
}

const props = defineProps<{
  branches: Branch[]
}>()

const isModalOpen = ref(false)
const selectedBranch = ref<Branch | null>(null)
const searchQuery = ref('')
const deleteDialogOpen = ref(false)
const branchToDelete = ref<Branch | null>(null)
const isDeleting = ref(false)

const stats = computed(() => ({
  total: props.branches.length,
  active: props.branches.filter(b => b.is_active).length,
  totalBays: props.branches.reduce((sum, b) => sum + (b.bays_count || 0), 0),
  totalWashes: props.branches.reduce((sum, b) => sum + (b.washes_count || 0), 0),
}))

const filteredBranches = computed(() => {
  const query = searchQuery.value.toLowerCase()
  return props.branches.filter(branch =>
    branch.name.toLowerCase().includes(query) ||
    branch.code.toLowerCase().includes(query) ||
    (branch.address && branch.address.toLowerCase().includes(query))
  )
})

const handleEdit = (branch: Branch) => {
  selectedBranch.value = branch
  isModalOpen.value = true
}

const handleDelete = (id: number) => {
  const branch = props.branches.find(b => b.id === id)
  if (branch) {
    branchToDelete.value = branch
    deleteDialogOpen.value = true
  }
}

const handleDeleteConfirm = () => {
  if (branchToDelete.value) {
    isDeleting.value = true
    router.delete(route('branches.destroy', branchToDelete.value.id), {
      onSuccess: () => {
        deleteDialogOpen.value = false
        branchToDelete.value = null
      },
      onFinish: () => {
        isDeleting.value = false
      }
    })
  }
}

const openCreateModal = () => {
  selectedBranch.value = null
  isModalOpen.value = true
}
</script>

<template>
  <Head title="Branch Management" />

  <AuthenticatedLayout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Branch Management
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="space-y-6">
          <!-- Header -->
          <div class="flex items-center justify-between">
            <div>
              <h1 class="text-3xl font-bold tracking-tight">Branch Management</h1>
              <p class="text-muted-foreground mt-1">Manage your car wash locations</p>
            </div>
            <div class="flex items-center gap-2">
              <Button variant="outline" @click="router.visit('/branches/comparison')">
                <GitCompare class="w-4 h-4 mr-2" />
                Compare Branches
              </Button>
              <Button @click="openCreateModal" class="btn-primary">
                <Plus class="w-4 h-4 mr-2" />
                Add Branch
              </Button>
            </div>
          </div>

          <!-- Stats Cards -->
          <div class="grid gap-4 md:grid-cols-4">
            <div class="stat-card">
              <div class="flex items-center justify-between">
                <div>
                  <p class="text-sm text-muted-foreground">Total Branches</p>
                  <p class="text-3xl font-bold mt-1">{{ stats.total }}</p>
                </div>
                <Building2 class="w-8 h-8 text-primary" />
              </div>
            </div>

            <div class="stat-card">
              <div class="flex items-center justify-between">
                <div>
                  <p class="text-sm text-muted-foreground">Active Branches</p>
                  <p class="text-3xl font-bold mt-1 text-success">{{ stats.active }}</p>
                </div>
                <MapPin class="w-8 h-8 text-success" />
              </div>
            </div>

            <div class="stat-card">
              <div class="flex items-center justify-between">
                <div>
                  <p class="text-sm text-muted-foreground">Total Bays</p>
                  <p class="text-3xl font-bold mt-1">{{ stats.totalBays }}</p>
                </div>
                <Building2 class="w-8 h-8 text-accent" />
              </div>
            </div>

            <div class="stat-card">
              <div class="flex items-center justify-between">
                <div>
                  <p class="text-sm text-muted-foreground">Total Washes</p>
                  <p class="text-3xl font-bold mt-1">{{ stats.totalWashes }}</p>
                </div>
                <TrendingUp class="w-8 h-8 text-primary" />
              </div>
            </div>
          </div>

          <!-- Search -->
          <div class="flex items-center gap-4">
            <Input
              v-model="searchQuery"
              placeholder="Search branches by name, code, or address..."
              class="max-w-md"
            />
          </div>

          <!-- Branch Grid -->
          <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
            <BranchCard
              v-for="branch in filteredBranches"
              :key="branch.id"
              :branch="branch"
              @edit="handleEdit"
              @delete="handleDelete"
            />
          </div>

          <!-- Empty State -->
          <div v-if="filteredBranches.length === 0" class="text-center py-12">
            <Building2 class="w-12 h-12 text-muted-foreground mx-auto mb-4" />
            <h3 class="text-lg font-semibold mb-2">No branches found</h3>
            <p class="text-muted-foreground">Try adjusting your search query</p>
          </div>

          <!-- Branch Form Modal -->
          <BranchFormModal
            :is-open="isModalOpen"
            :branch="selectedBranch"
            @close="() => { isModalOpen = false; selectedBranch = null }"
          />

          <!-- Delete Confirmation Dialog -->
          <DeleteConfirmDialog
            :is-open="deleteDialogOpen"
            :item-name="branchToDelete?.name"
            :is-deleting="isDeleting"
            title="Delete Branch"
            message="Are you sure you want to delete this branch? This will also delete all associated bays and data. This action cannot be undone."
            @close="deleteDialogOpen = false"
            @confirm="handleDeleteConfirm"
          />
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
