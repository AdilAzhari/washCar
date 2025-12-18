<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import ManagerLayout from '@/Layouts/ManagerLayout.vue'
import {
  DollarSign,
  Waves,
  ListOrdered,
  Package,
  TrendingUp,
  Calendar,
  AlertTriangle,
} from 'lucide-vue-next'

defineProps<{
  kpis: {
    todayRevenue: number
    todayWashes: number
    weekRevenue: number
    monthRevenue: number
    activeBays: number
    idleBays: number
    maintenanceBays: number
    waitingQueue: number
  }
  revenueChart: Array<{
    date: string
    revenue: number
  }>
  topPackages: Array<{
    package: { id: number; name: string }
    count: number
    revenue: number
  }>
  upcomingAppointments: Array<{
    id: number
    scheduled_at: string
    customer: { name: string }
    package: { name: string }
    status: string
  }>
  lowStockItems: Array<{
    id: number
    name: string
    quantity: number
    unit: string
    low_stock_threshold: number
  }>
}>()

const formatCurrency = (amount: number) => {
  return new Intl.NumberFormat('en-US', {
    style: 'currency',
    currency: 'USD',
  }).format(amount)
}

const formatDate = (dateString: string) => {
  return new Date(dateString).toLocaleDateString('en-US', {
    month: 'short',
    day: 'numeric',
    hour: 'numeric',
    minute: '2-digit',
  })
}
</script>

<template>
  <Head title="Manager Dashboard" />

  <ManagerLayout>
    <div class="p-6 space-y-6">
      <!-- Page Header -->
      <div>
        <h1 class="text-3xl font-bold">Branch Dashboard</h1>
        <p class="text-muted-foreground mt-1">Monitor your branch performance and operations</p>
      </div>

      <!-- KPI Cards -->
      <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-4">
        <!-- Today's Revenue -->
        <div class="bg-card border rounded-lg p-6">
          <div class="flex items-center justify-between mb-2">
            <span class="text-sm font-medium text-muted-foreground">Today's Revenue</span>
            <DollarSign class="w-5 h-5 text-green-600" />
          </div>
          <div class="text-2xl font-bold">{{ formatCurrency(kpis.todayRevenue) }}</div>
          <p class="text-xs text-muted-foreground mt-1">{{ kpis.todayWashes }} washes</p>
        </div>

        <!-- Week Revenue -->
        <div class="bg-card border rounded-lg p-6">
          <div class="flex items-center justify-between mb-2">
            <span class="text-sm font-medium text-muted-foreground">This Week</span>
            <TrendingUp class="w-5 h-5 text-blue-600" />
          </div>
          <div class="text-2xl font-bold">{{ formatCurrency(kpis.weekRevenue) }}</div>
          <p class="text-xs text-muted-foreground mt-1">Weekly revenue</p>
        </div>

        <!-- Month Revenue -->
        <div class="bg-card border rounded-lg p-6">
          <div class="flex items-center justify-between mb-2">
            <span class="text-sm font-medium text-muted-foreground">This Month</span>
            <TrendingUp class="w-5 h-5 text-purple-600" />
          </div>
          <div class="text-2xl font-bold">{{ formatCurrency(kpis.monthRevenue) }}</div>
          <p class="text-xs text-muted-foreground mt-1">Monthly revenue</p>
        </div>

        <!-- Queue Status -->
        <div class="bg-card border rounded-lg p-6">
          <div class="flex items-center justify-between mb-2">
            <span class="text-sm font-medium text-muted-foreground">Current Queue</span>
            <ListOrdered class="w-5 h-5 text-orange-600" />
          </div>
          <div class="text-2xl font-bold">{{ kpis.waitingQueue }}</div>
          <p class="text-xs text-muted-foreground mt-1">Customers waiting</p>
        </div>
      </div>

      <!-- Bay Status & Queue -->
      <div class="grid gap-6 lg:grid-cols-3">
        <!-- Bay Status -->
        <div class="bg-card border rounded-lg p-6">
          <h3 class="text-lg font-bold mb-4 flex items-center gap-2">
            <Waves class="w-5 h-5" />
            Bay Status
          </h3>
          <div class="space-y-3">
            <div class="flex items-center justify-between">
              <div class="flex items-center gap-2">
                <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                <span class="text-sm">Active</span>
              </div>
              <span class="font-semibold">{{ kpis.activeBays }}</span>
            </div>
            <div class="flex items-center justify-between">
              <div class="flex items-center gap-2">
                <div class="w-3 h-3 bg-gray-400 rounded-full"></div>
                <span class="text-sm">Idle</span>
              </div>
              <span class="font-semibold">{{ kpis.idleBays }}</span>
            </div>
            <div class="flex items-center justify-between">
              <div class="flex items-center gap-2">
                <div class="w-3 h-3 bg-orange-500 rounded-full"></div>
                <span class="text-sm">Maintenance</span>
              </div>
              <span class="font-semibold">{{ kpis.maintenanceBays }}</span>
            </div>
          </div>
          <Link
            :href="route('manager.bays.index')"
            class="block w-full mt-4 py-2 text-center text-sm font-medium text-primary hover:underline"
          >
            Manage Bays
          </Link>
        </div>

        <!-- Revenue Chart (Last 7 Days) -->
        <div class="lg:col-span-2 bg-card border rounded-lg p-6">
          <h3 class="text-lg font-bold mb-4">Revenue Trend (Last 7 Days)</h3>
          <div class="space-y-2">
            <div
              v-for="day in revenueChart"
              :key="day.date"
              class="flex items-center gap-3"
            >
              <div class="w-16 text-sm text-muted-foreground">{{ day.date }}</div>
              <div class="flex-1 bg-muted rounded-full h-8 relative overflow-hidden">
                <div
                  class="bg-primary h-full rounded-full transition-all"
                  :style="{
                    width: `${Math.max((day.revenue / Math.max(...revenueChart.map(d => d.revenue))) * 100, 2)}%`
                  }"
                ></div>
                <div class="absolute inset-0 flex items-center px-3">
                  <span class="text-xs font-medium">{{ formatCurrency(day.revenue) }}</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Top Packages & Appointments -->
      <div class="grid gap-6 lg:grid-cols-2">
        <!-- Top Packages -->
        <div class="bg-card border rounded-lg">
          <div class="px-6 py-4 border-b">
            <h3 class="text-lg font-bold flex items-center gap-2">
              <Package class="w-5 h-5" />
              Top Packages This Month
            </h3>
          </div>
          <div class="p-6">
            <div v-if="topPackages.length === 0" class="text-center py-8 text-muted-foreground">
              No data available
            </div>
            <div v-else class="space-y-4">
              <div
                v-for="(pkg, index) in topPackages"
                :key="pkg.package.id"
                class="flex items-center justify-between"
              >
                <div class="flex items-center gap-3">
                  <div class="flex items-center justify-center w-8 h-8 bg-primary/10 rounded-lg text-sm font-bold">
                    {{ index + 1 }}
                  </div>
                  <div>
                    <div class="font-semibold">{{ pkg.package.name }}</div>
                    <div class="text-sm text-muted-foreground">{{ pkg.count }} washes</div>
                  </div>
                </div>
                <div class="text-right">
                  <div class="font-bold">{{ formatCurrency(pkg.revenue) }}</div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Upcoming Appointments -->
        <div class="bg-card border rounded-lg">
          <div class="px-6 py-4 border-b flex items-center justify-between">
            <h3 class="text-lg font-bold flex items-center gap-2">
              <Calendar class="w-5 h-5" />
              Upcoming Appointments
            </h3>
            <Link
              :href="route('manager.appointments.index')"
              class="text-sm text-primary hover:underline"
            >
              View All
            </Link>
          </div>
          <div class="p-6">
            <div v-if="upcomingAppointments.length === 0" class="text-center py-8 text-muted-foreground">
              No upcoming appointments
            </div>
            <div v-else class="space-y-4">
              <div
                v-for="appointment in upcomingAppointments"
                :key="appointment.id"
              >
                <div class="font-semibold text-sm">{{ appointment.customer.name }}</div>
                <div class="text-sm text-muted-foreground">
                  {{ appointment.package.name }} • {{ formatDate(appointment.scheduled_at) }}
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Low Stock Alerts -->
      <div v-if="lowStockItems.length > 0" class="bg-card border border-orange-200 rounded-lg">
        <div class="px-6 py-4 border-b border-orange-200 bg-orange-50">
          <h3 class="text-lg font-bold flex items-center gap-2 text-orange-900">
            <AlertTriangle class="w-5 h-5" />
            Low Stock Alerts
          </h3>
        </div>
        <div class="p-6">
          <div class="space-y-3">
            <div
              v-for="item in lowStockItems"
              :key="item.id"
              class="flex items-center justify-between p-3 bg-orange-50 rounded-lg"
            >
              <div>
                <div class="font-semibold">{{ item.name }}</div>
                <div class="text-sm text-muted-foreground">
                  Threshold: {{ item.low_stock_threshold }} {{ item.unit }}
                </div>
              </div>
              <div class="text-right">
                <div class="font-bold text-orange-600">{{ item.quantity }} {{ item.unit }}</div>
                <Link
                  :href="route('manager.inventory.index')"
                  class="text-xs text-primary hover:underline"
                >
                  Restock
                </Link>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Quick Actions -->
      <div class="grid gap-4 md:grid-cols-3">
        <Link
          :href="route('manager.reports')"
          class="flex items-center justify-center gap-2 p-4 bg-primary text-primary-foreground rounded-lg hover:bg-primary/90 transition-colors"
        >
          <TrendingUp class="w-5 h-5" />
          <span class="font-medium">View Detailed Reports</span>
        </Link>

        <Link
          :href="route('manager.analytics.all-branches')"
          class="flex items-center justify-center gap-2 p-4 bg-card border rounded-lg hover:shadow-md transition-shadow"
        >
          <TrendingUp class="w-5 h-5" />
          <span class="font-medium">All Branches Analytics</span>
        </Link>

        <Link
          :href="route('manager.staff.index')"
          class="flex items-center justify-center gap-2 p-4 bg-card border rounded-lg hover:shadow-md transition-shadow"
        >
          <Package class="w-5 h-5" />
          <span class="font-medium">Manage Staff</span>
        </Link>
      </div>
    </div>
  </ManagerLayout>
</template>
