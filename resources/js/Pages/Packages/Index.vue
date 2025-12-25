<script setup lang="ts">
import { ref, computed } from 'vue'
import { Head, router, usePage } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import PackageFormModal from '@/Components/Package/PackageFormModal.vue'
import { EmptyState, Card, CardContent, CardHeader, CardTitle, Badge, Button, Input, DeleteConfirmDialog } from '@/Components/ui'
import StatCard from '@/Components/Dashboard/StatCard.vue'
import { Plus, Edit, Trash2, Package as PackageIcon, DollarSign, Clock } from 'lucide-vue-next'

interface Package {
  id: number
  name: string
  description?: string
  price: number
  duration_minutes: number
  color: string
  is_active: boolean
  washes_count?: number
}

const props = defineProps<{
  packages: Package[]
}>()

const page = usePage()
const userRole = computed(() => (page.props.auth as any)?.user?.role || 'admin')

const getRouteName = (routeName: string) => {
  return `${userRole.value}.${routeName}`
}

const isFormOpen = ref(false)
const selectedPackage = ref<Package | null>(null)
const searchQuery = ref('')
const deleteDialogOpen = ref(false)
const packageToDelete = ref<Package | null>(null)
const isDeleting = ref(false)

const stats = computed(() => ({
  total: props.packages.length,
  active: props.packages.filter(p => p.is_active).length,
  totalRevenue: props.packages.reduce((sum, p) => sum + (p.price * (p.washes_count || 0)), 0),
  totalWashes: props.packages.reduce((sum, p) => sum + (p.washes_count || 0), 0),
}))

const filteredPackages = computed(() => {
  const query = searchQuery.value.toLowerCase()
  if (!query) return props.packages

  return props.packages.filter(pkg =>
    pkg.name.toLowerCase().includes(query) ||
    (pkg.description && pkg.description.toLowerCase().includes(query))
  )
})

const handleEdit = (pkg: Package) => {
  selectedPackage.value = pkg
  isFormOpen.value = true
}

const handleDelete = (packageId: number) => {
  const pkg = props.packages.find(p => p.id === packageId)
  if (pkg) {
    packageToDelete.value = pkg
    deleteDialogOpen.value = true
  }
}

const handleDeleteConfirm = () => {
  if (packageToDelete.value) {
    isDeleting.value = true
    router.delete(route(getRouteName('packages.destroy'), packageToDelete.value.id), {
      onSuccess: () => {
        deleteDialogOpen.value = false
        packageToDelete.value = null
      },
      onFinish: () => {
        isDeleting.value = false
      }
    })
  }
}

const openCreateModal = () => {
  selectedPackage.value = null
  isFormOpen.value = true
}

const formatPrice = (price: number) => {
  return new Intl.NumberFormat('ms-MY', {
    style: 'currency',
    currency: 'MYR',
  }).format(price)
}

const formatDuration = (minutes: number) => {
  if (minutes < 60) return `${minutes} min`
  const hours = Math.floor(minutes / 60)
  const mins = minutes % 60
  return mins > 0 ? `${hours}h ${mins}m` : `${hours}h`
}
</script>

<template>
  <Head title="Package Management" />

  <AuthenticatedLayout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Package Management
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="space-y-6 animate-fade-in">
          <!-- Header -->
          <div class="flex items-center justify-between">
            <div>
              <h1 class="text-2xl font-bold mb-2">Package Management</h1>
              <p class="text-muted-foreground">Manage wash packages and pricing</p>
            </div>
            <Button class="btn-primary" @click="openCreateModal">
              <Plus class="w-4 h-4 mr-2" />
              Add Package
            </Button>
          </div>

          <!-- Stats Grid -->
          <div class="grid gap-4 md:grid-cols-4">
            <StatCard
              title="Total Packages"
              :value="stats.total"
              subtitle="All service packages"
              :icon="PackageIcon"
              accent-color="primary"
            />
            <StatCard
              title="Active"
              :value="stats.active"
              subtitle="Available for sale"
              :icon="PackageIcon"
              accent-color="success"
            />
            <StatCard
              title="Total Washes"
              :value="stats.totalWashes"
              subtitle="Across all packages"
              :icon="Clock"
              accent-color="bay-active"
            />
            <StatCard
              title="Total Revenue"
              :value="formatPrice(stats.totalRevenue)"
              subtitle="Lifetime earnings"
              :icon="DollarSign"
              accent-color="primary"
            />
          </div>

          <!-- Search -->
          <div class="flex items-center gap-4">
            <Input
              v-model="searchQuery"
              placeholder="Search packages by name or description..."
              class="max-w-md"
            />
          </div>

          <!-- Packages Grid -->
          <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
            <Card v-for="pkg in filteredPackages" :key="pkg.id" class="hover-lift">
              <CardHeader>
                <div class="flex items-center justify-between">
                  <div class="flex items-center gap-2">
                    <div
                      class="w-3 h-3 rounded-full"
                      :style="{ backgroundColor: pkg.color }"
                    ></div>
                    <CardTitle class="text-lg">{{ pkg.name }}</CardTitle>
                  </div>
                  <Badge :variant="pkg.is_active ? 'default' : 'secondary'">
                    {{ pkg.is_active ? 'Active' : 'Inactive' }}
                  </Badge>
                </div>
                <p v-if="pkg.description" class="text-sm text-muted-foreground mt-2">
                  {{ pkg.description }}
                </p>
              </CardHeader>
              <CardContent class="space-y-3">
                <div class="grid grid-cols-2 gap-4">
                  <div class="bg-muted/50 rounded-lg p-3">
                    <p class="text-xs text-muted-foreground mb-1">Price</p>
                    <p class="text-lg font-bold">{{ formatPrice(pkg.price) }}</p>
                  </div>
                  <div class="bg-muted/50 rounded-lg p-3">
                    <p class="text-xs text-muted-foreground mb-1">Duration</p>
                    <p class="text-lg font-bold">{{ formatDuration(pkg.duration_minutes) }}</p>
                  </div>
                </div>

                <div class="bg-muted/50 rounded-lg p-3">
                  <p class="text-xs text-muted-foreground mb-1">Total Washes</p>
                  <p class="text-lg font-bold">{{ pkg.washes_count || 0 }}</p>
                </div>

                <!-- Action Buttons -->
                <div class="flex gap-2 pt-2 border-t">
                  <Button size="sm" variant="outline" class="flex-1" @click="handleEdit(pkg)">
                    <Edit class="w-4 h-4 mr-1" />
                    Edit
                  </Button>
                  <Button size="sm" variant="outline" @click="handleDelete(pkg.id)">
                    <Trash2 class="w-4 h-4" />
                  </Button>
                </div>
              </CardContent>
            </Card>
          </div>

          <!-- Empty State -->
          <EmptyState
            v-if="filteredPackages.length === 0 && searchQuery"
            :icon="PackageIcon"
            title="No packages found"
            message="Try adjusting your search query to find what you're looking for."
          />

          <EmptyState
            v-if="filteredPackages.length === 0 && !searchQuery"
            :icon="PackageIcon"
            title="No packages yet"
            message="Create your first wash package to start offering services. Define pricing, duration, and service details for your customers."
            action-label="Add Package"
            help-text="Packages define the services you offer and help organize your pricing structure."
            @action="openCreateModal"
          />

          <!-- Package Form Modal -->
          <PackageFormModal
            :is-open="isFormOpen"
            :package="selectedPackage"
            @close="() => { isFormOpen = false; selectedPackage = null }"
          />

          <!-- Delete Confirmation Dialog -->
          <DeleteConfirmDialog
            :is-open="deleteDialogOpen"
            :item-name="packageToDelete?.name"
            :is-deleting="isDeleting"
            title="Delete Package"
            message="Are you sure you want to delete this package? This action cannot be undone."
            @close="deleteDialogOpen = false"
            @confirm="handleDeleteConfirm"
          />
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
