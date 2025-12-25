<script setup lang="ts">
import { ref, computed } from 'vue'
import { Head, router, usePage } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import ManagerLayout from '@/Layouts/ManagerLayout.vue'
import StaffLayout from '@/Layouts/StaffLayout.vue'
import QueueFormModal from '@/Components/Queue/QueueFormModal.vue'
import QueueItemCard from '@/Components/Queue/QueueItemCard.vue'
import StatCard from '@/Components/Dashboard/StatCard.vue'
import { EmptyState, Button } from '@/Components/ui'
import { Plus, Clock, Activity, Users } from 'lucide-vue-next'

interface QueueEntry {
  id: number
  branch: { id: number; name: string }
  customer?: { id: number; name: string }
  package?: { id: number; name: string; price: number; duration_minutes: number }
  plate_number: string
  position: number
  status: string
  payment_status: string
  joined_at: string
  wash: { id: number; status: string } | null
}

const props = defineProps<{
  queueEntries: QueueEntry[]
  branches: any[]
  customers: any[]
  packages: any[]
}>()

const isFormOpen = ref(false)

const page = usePage()
const userRole = computed(() => (page.props.auth as any)?.user?.role || 'admin')

const Layout = computed(() => {
  if (userRole.value === 'manager') return ManagerLayout
  if (userRole.value === 'staff') return StaffLayout
  return AuthenticatedLayout
})

const getRouteName = (routeName: string) => {
  return `${userRole.value}.${routeName}`
}

const waitingQueue = computed(() =>
  props.queueEntries.filter(q => q.status === 'waiting')
)

const inProgressQueue = computed(() =>
  props.queueEntries.filter(q => q.status === 'in_progress')
)

const stats = computed(() => ({
  total: props.queueEntries.length,
  waiting: waitingQueue.value.length,
  inProgress: inProgressQueue.value.length,
}))

const startWash = (id: number) => {
  router.post(route(getRouteName('queue.start'), id), {}, { preserveScroll: true })
}

const completeWash = (washId: number) => {
  router.post(route(getRouteName('wash.complete'), washId), {}, { preserveScroll: true })
}

const removeFromQueue = (id: number) => {
  if (confirm('Remove this customer from the queue?')) {
    router.post(route(getRouteName('queue.cancel'), id), {}, { preserveScroll: true })
  }
}

const getWaitTime = (joinedAt: string) => {
  const joined = new Date(joinedAt)
  const now = new Date()
  const diffMs = now.getTime() - joined.getTime()
  const diffMins = Math.floor(diffMs / 60000)
  return diffMins
}
</script>

<template>
  <Head title="Queue Management" />

  <component :is="Layout">
    <template #header>
      <div class="flex items-center justify-between">
        <h2 class="font-semibold text-xl leading-tight">Queue Management</h2>
        <Button variant="primary-action" @click="isFormOpen = true">
          <Plus class="w-4 h-4" />
          Add to Queue
        </Button>
      </div>
    </template>

    <div class="py-8">
      <div class="max-w-[1800px] mx-auto sm:px-6 lg:px-8">
        <div class="space-y-6 animate-fade-in-fast">
          <!-- Stats Grid -->
          <div class="grid gap-4 md:grid-cols-3">
            <StatCard
              title="Total in Queue"
              :value="stats.total"
              subtitle="Active customers"
              :icon="Users"
              accent-color="primary"
            />
            <StatCard
              title="Waiting"
              :value="stats.waiting"
              subtitle="Ready to start"
              :icon="Clock"
              accent-color="warning"
            />
            <StatCard
              title="In Progress"
              :value="stats.inProgress"
              subtitle="Being served"
              :icon="Activity"
              accent-color="bay-active"
            />
          </div>

          <!-- Two-Column Queue Layout -->
          <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Waiting Queue -->
            <div class="space-y-4">
              <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold flex items-center gap-2">
                  <div class="w-1 h-6 bg-queue-waiting rounded-full"></div>
                  Waiting Queue
                  <span class="text-sm font-normal text-muted-foreground ml-1">({{ stats.waiting }})</span>
                </h2>
              </div>

              <div class="space-y-3">
                <QueueItemCard
                  v-for="entry in waitingQueue"
                  :key="entry.id"
                  :position="entry.position"
                  :customer-name="entry.customer?.name || 'Walk-in'"
                  :vehicle-info="{
                    make: '',
                    model: '',
                    plate: entry.plate_number
                  }"
                  :package-name="entry.package?.name || 'No package'"
                  :duration-estimate="entry.package?.duration_minutes || 0"
                  :wait-time="getWaitTime(entry.joined_at)"
                  status="waiting"
                  :payment-status="entry.payment_status"
                  :has-package="!!entry.package"
                  @start="startWash(entry.id)"
                  @remove="removeFromQueue(entry.id)"
                />

                <EmptyState
                  v-if="waitingQueue.length === 0"
                  :icon="Clock"
                  title="No customers waiting"
                  message="The waiting queue is empty. Customers can join via QR code or be added manually."
                  action-label="Add Customer"
                  @action="isFormOpen = true"
                />
              </div>
            </div>

            <!-- In Progress Queue -->
            <div class="space-y-4">
              <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold flex items-center gap-2">
                  <div class="w-1 h-6 bg-queue-in-progress rounded-full"></div>
                  In Progress
                  <span class="text-sm font-normal text-muted-foreground ml-1">({{ stats.inProgress }})</span>
                </h2>
              </div>

              <div class="space-y-3">
                <QueueItemCard
                  v-for="entry in inProgressQueue"
                  :key="entry.id"
                  :position="entry.position"
                  :customer-name="entry.customer?.name || 'Walk-in'"
                  :vehicle-info="{
                    make: '',
                    model: '',
                    plate: entry.plate_number
                  }"
                  :package-name="entry.package?.name || 'No package'"
                  :duration-estimate="entry.package?.duration_minutes || 0"
                  :wait-time="getWaitTime(entry.joined_at)"
                  status="in-progress"
                  @complete="entry.wash && completeWash(entry.wash.id)"
                  @remove="removeFromQueue(entry.id)"
                />

                <EmptyState
                  v-if="inProgressQueue.length === 0"
                  :icon="Activity"
                  title="No active washes"
                  message="All bays are currently idle. Start a wash from the waiting queue."
                />
              </div>
            </div>
          </div>

          <QueueFormModal
            :is-open="isFormOpen"
            :branches="branches"
            :customers="customers"
            :packages="packages"
            @close="isFormOpen = false"
          />
        </div>
      </div>
    </div>
  </component>
</template>
