<script setup lang="ts">
import { ref, computed } from 'vue'
import { Head, router, usePage } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import BayFormModal from '@/Components/Bay/BayFormModal.vue'
import BayStatusCard from '@/Components/Dashboard/BayStatusCard.vue'
import StatCard from '@/Components/Dashboard/StatCard.vue'
import { EmptyState, Button, DeleteConfirmDialog } from '@/Components/ui'
import { Plus, Waves, Activity, Wrench, ListX } from 'lucide-vue-next'

interface Bay {
  id: number
  name: string
  status: string
  branch: {
    id: number
    name: string
  }
  currentWash?: any
}

interface Branch {
  id: number
  name: string
}

const props = defineProps<{
  bays: Bay[]
  branches: Branch[]
}>()

const isFormOpen = ref(false)
const selectedBay = ref<Bay | null>(null)
const deleteDialogOpen = ref(false)
const bayToDelete = ref<Bay | null>(null)
const isDeleting = ref(false)
const filterStatus = ref<string>('all')

const page = usePage()
const userRole = computed(() => (page.props.auth as any)?.user?.role || 'admin')

const getRouteName = (routeName: string) => {
  return `${userRole.value}.${routeName}`
}

const stats = computed(() => ({
  totalBays: props.bays.length,
  idle: props.bays.filter(b => b.status === 'idle').length,
  active: props.bays.filter(b => b.status === 'active').length,
  maintenance: props.bays.filter(b => b.status === 'maintenance').length,
}))

const filteredBays = computed(() => {
  if (filterStatus.value === 'all') return props.bays
  return props.bays.filter(b => b.status === filterStatus.value)
})

const handleEdit = (bay: Bay) => {
  selectedBay.value = bay
  isFormOpen.value = true
}

const handleDelete = (bayId: number) => {
  const bay = props.bays.find(b => b.id === bayId)
  if (bay) {
    bayToDelete.value = bay
    deleteDialogOpen.value = true
  }
}

const handleDeleteConfirm = () => {
  if (bayToDelete.value) {
    isDeleting.value = true
    router.delete(route(getRouteName('bays.destroy'), bayToDelete.value.id), {
      onSuccess: () => {
        deleteDialogOpen.value = false
        bayToDelete.value = null
      },
      onFinish: () => {
        isDeleting.value = false
      }
    })
  }
}

const openCreateModal = () => {
  selectedBay.value = null
  isFormOpen.value = true
}

const updateStatus = (bayId: number, status: string) => {
  router.patch(route(getRouteName('bays.update-status'), bayId), { status }, {
    preserveScroll: true,
  })
}
</script>

<template>
  <Head title="Bay Management" />

  <AuthenticatedLayout>
    <template #header>
      <div class="flex items-center justify-between">
        <h2 class="font-semibold text-xl leading-tight">Bay Management</h2>
        <Button variant="primary-action" @click="openCreateModal">
          <Plus class="w-4 h-4" />
          Add Bay
        </Button>
      </div>
    </template>

    <div class="py-8">
      <div class="max-w-[1800px] mx-auto sm:px-6 lg:px-8">
        <div class="space-y-6 animate-fade-in-fast">
          <!-- Stats Grid -->
          <div class="grid gap-4 md:grid-cols-4">
            <StatCard
              title="Total Bays"
              :value="stats.totalBays"
              subtitle="All washing stations"
              :icon="Waves"
              accent-color="primary"
            />
            <StatCard
              title="Idle"
              :value="stats.idle"
              subtitle="Ready for service"
              :icon="ListX"
              accent-color="bay-idle"
            />
            <StatCard
              title="Active"
              :value="stats.active"
              subtitle="Currently washing"
              :icon="Activity"
              accent-color="bay-active"
            />
            <StatCard
              title="Maintenance"
              :value="stats.maintenance"
              subtitle="Under maintenance"
              :icon="Wrench"
              accent-color="bay-maintenance"
            />
          </div>

          <!-- Filter Buttons -->
          <div class="flex items-center gap-2">
            <Button
              size="sm"
              :variant="filterStatus === 'all' ? 'default' : 'outline'"
              @click="filterStatus = 'all'"
            >
              All ({{ stats.totalBays }})
            </Button>
            <Button
              size="sm"
              :variant="filterStatus === 'idle' ? 'default' : 'outline'"
              @click="filterStatus = 'idle'"
            >
              Idle ({{ stats.idle }})
            </Button>
            <Button
              size="sm"
              :variant="filterStatus === 'active' ? 'default' : 'outline'"
              @click="filterStatus = 'active'"
            >
              Active ({{ stats.active }})
            </Button>
            <Button
              size="sm"
              :variant="filterStatus === 'maintenance' ? 'default' : 'outline'"
              @click="filterStatus = 'maintenance'"
            >
              Maintenance ({{ stats.maintenance }})
            </Button>
          </div>

          <!-- Bays Grid -->
          <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
            <BayStatusCard
              v-for="bay in filteredBays"
              :key="bay.id"
              :bay-number="bay.name"
              :status="bay.status.toLowerCase() as 'idle' | 'active' | 'maintenance' | 'completed'"
              :current-wash="bay.currentWash"
              @edit="handleEdit(bay)"
              @delete="handleDelete(bay.id)"
              @status-change="(newStatus: string) => updateStatus(bay.id, newStatus)"
            />
          </div>

          <!-- Empty State -->
          <EmptyState
            v-if="filteredBays.length === 0 && bays.length > 0"
            :icon="Waves"
            :title="`No ${filterStatus} bays`"
            :message="`There are no bays with ${filterStatus} status.`"
          />

          <EmptyState
            v-if="bays.length === 0"
            :icon="Waves"
            title="No bays found"
            message="Get started by adding your first washing bay to begin managing your car wash operations."
            action-label="Add Bay"
            help-text="Bays are the washing stations where vehicles are serviced."
            @action="openCreateModal"
          />

          <!-- Bay Form Modal -->
          <BayFormModal
            :is-open="isFormOpen"
            :bay="selectedBay"
            :branches="branches"
            @close="() => { isFormOpen = false; selectedBay = null }"
          />

          <!-- Delete Confirmation Dialog -->
          <DeleteConfirmDialog
            :is-open="deleteDialogOpen"
            :item-name="bayToDelete?.name"
            :is-deleting="isDeleting"
            title="Delete Bay"
            message="Are you sure you want to delete this bay? This action cannot be undone."
            @close="deleteDialogOpen = false"
            @confirm="handleDeleteConfirm"
          />
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
