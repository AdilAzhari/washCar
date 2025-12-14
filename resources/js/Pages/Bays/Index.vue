<script setup lang="ts">
import { ref, computed } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import BayFormModal from '@/Components/Bay/BayFormModal.vue'
import StatCard from '@/Components/Dashboard/StatCard.vue'
import { Card, CardContent, CardHeader, CardTitle, Badge, Button, DeleteConfirmDialog } from '@/Components/ui'
import { Plus, Edit, Trash2, Waves, Activity } from 'lucide-vue-next'

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

const stats = computed(() => ({
  totalBays: props.bays.length,
  idle: props.bays.filter(b => b.status === 'idle').length,
  active: props.bays.filter(b => b.status === 'active').length,
  maintenance: props.bays.filter(b => b.status === 'maintenance').length,
}))

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
    router.delete(route('bays.destroy', bayToDelete.value.id), {
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

const getStatusVariant = (status: string) => {
  switch (status) {
    case 'idle':
      return 'default'
    case 'active':
      return 'secondary'
    case 'maintenance':
      return 'destructive'
    default:
      return 'default'
  }
}

const updateStatus = (bayId: number, status: string) => {
  router.patch(route('bays.update-status', bayId), { status }, {
    preserveScroll: true,
  })
}
</script>

<template>
  <Head title="Bay Management" />

  <AuthenticatedLayout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Bay Management
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="space-y-6 animate-fade-in">
          <!-- Header -->
          <div class="flex items-center justify-between">
            <div>
              <h1 class="text-2xl font-bold mb-2">Bay Management</h1>
              <p class="text-muted-foreground">Manage washing stations and monitor performance</p>
            </div>
            <Button class="btn-primary" @click="openCreateModal">
              <Plus class="w-4 h-4 mr-2" />
              Add Bay
            </Button>
          </div>

          <!-- Stats Grid -->
          <div class="grid gap-4 md:grid-cols-4">
            <StatCard
              title="Total Bays"
              :value="stats.totalBays"
              :icon="Waves"
              icon-class-name="bg-primary/10 text-primary"
            />
            <StatCard
              title="Idle"
              :value="stats.idle"
              :icon="Activity"
              icon-class-name="bg-muted/10 text-muted-foreground"
            />
            <StatCard
              title="Active"
              :value="stats.active"
              :icon="Activity"
              icon-class-name="bg-success/10 text-success"
            />
            <StatCard
              title="Maintenance"
              :value="stats.maintenance"
              :icon="Activity"
              icon-class-name="bg-destructive/10 text-destructive"
            />
          </div>

          <!-- Bays Grid -->
          <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
            <Card v-for="bay in bays" :key="bay.id" class="hover-lift">
              <CardHeader>
                <div class="flex items-center justify-between">
                  <CardTitle class="text-lg">{{ bay.name }}</CardTitle>
                  <Badge :variant="getStatusVariant(bay.status)" class="capitalize">
                    {{ bay.status }}
                  </Badge>
                </div>
                <p class="text-sm text-muted-foreground">{{ bay.branch.name }}</p>
              </CardHeader>
              <CardContent class="space-y-3">
                <div v-if="bay.currentWash" class="bg-muted/50 rounded-lg p-3 space-y-2">
                  <p class="text-sm font-medium">Current Wash</p>
                  <div class="text-xs text-muted-foreground">
                    Active wash in progress
                  </div>
                </div>
                <div v-else class="bg-muted/50 rounded-lg p-3">
                  <p class="text-sm text-center text-muted-foreground">No active wash</p>
                </div>

                <!-- Status Change Buttons -->
                <div class="flex flex-wrap gap-1">
                  <Button
                    v-if="bay.status !== 'idle'"
                    size="sm"
                    variant="outline"
                    @click="updateStatus(bay.id, 'idle')"
                  >
                    Set Idle
                  </Button>
                  <Button
                    v-if="bay.status !== 'active'"
                    size="sm"
                    variant="outline"
                    @click="updateStatus(bay.id, 'active')"
                  >
                    Set Active
                  </Button>
                  <Button
                    v-if="bay.status !== 'maintenance'"
                    size="sm"
                    variant="outline"
                    @click="updateStatus(bay.id, 'maintenance')"
                  >
                    Maintenance
                  </Button>
                </div>

                <!-- Action Buttons -->
                <div class="flex gap-2 pt-2 border-t">
                  <Button size="sm" variant="outline" class="flex-1" @click="handleEdit(bay)">
                    <Edit class="w-4 h-4 mr-1" />
                    Edit
                  </Button>
                  <Button size="sm" variant="outline" @click="handleDelete(bay.id)">
                    <Trash2 class="w-4 h-4" />
                  </Button>
                </div>
              </CardContent>
            </Card>
          </div>

          <!-- Empty State -->
          <div v-if="bays.length === 0" class="text-center py-12">
            <Waves class="w-12 h-12 text-muted-foreground mx-auto mb-4" />
            <h3 class="text-lg font-semibold mb-2">No bays found</h3>
            <p class="text-muted-foreground mb-4">Get started by adding your first bay</p>
            <Button @click="openCreateModal">
              <Plus class="w-4 h-4 mr-2" />
              Add Bay
            </Button>
          </div>

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
