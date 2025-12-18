<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3'
import ManagerLayout from '@/Layouts/ManagerLayout.vue'
import { BarChart3, DollarSign, Package, TrendingUp, Calendar } from 'lucide-vue-next'
import { ref } from 'vue'

const props = defineProps<{
  dateRange: {
    start: string
    end: string
  }
  summary: {
    totalRevenue: number
    totalWashes: number
    averageTicket: number
  }
  packageBreakdown: Array<{
    package: { id: number; name: string; price: number }
    count: number
    revenue: number
  }>
}>()

const startDate = ref(props.dateRange.start)
const endDate = ref(props.dateRange.end)

const applyFilter = () => {
  router.get(route('manager.reports'), {
    start_date: startDate.value,
    end_date: endDate.value,
  }, {
    preserveState: true,
  })
}

const formatCurrency = (amount: number) => {
  return new Intl.NumberFormat('ms-MY', {
    style: 'currency',
    currency: 'MYR',
  }).format(amount)
}
</script>

<template>
  <Head title="Reports" />

  <ManagerLayout>
    <div class="p-6 space-y-6">
      <!-- Header -->
      <div>
        <h1 class="text-3xl font-bold">Branch Reports</h1>
        <p class="text-muted-foreground mt-1">Detailed analytics for your branch performance</p>
      </div>

      <!-- Date Range Filter -->
      <div class="bg-card border rounded-lg p-6">
        <h3 class="text-lg font-bold mb-4 flex items-center gap-2">
          <Calendar class="w-5 h-5" />
          Date Range
        </h3>
        <div class="flex items-end gap-4">
          <div class="flex-1">
            <label class="block text-sm font-medium mb-2">Start Date</label>
            <input
              v-model="startDate"
              type="date"
              class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary"
            />
          </div>
          <div class="flex-1">
            <label class="block text-sm font-medium mb-2">End Date</label>
            <input
              v-model="endDate"
              type="date"
              class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary"
            />
          </div>
          <button
            @click="applyFilter"
            class="px-6 py-2 bg-primary text-primary-foreground rounded-lg font-medium hover:bg-primary/90 transition-colors"
          >
            Apply
          </button>
        </div>
      </div>

      <!-- Summary KPIs -->
      <div class="grid gap-6 md:grid-cols-3">
        <div class="bg-card border rounded-lg p-6">
          <div class="flex items-center gap-2 mb-2">
            <DollarSign class="w-5 h-5 text-green-600" />
            <span class="text-sm font-medium text-muted-foreground">Total Revenue</span>
          </div>
          <div class="text-3xl font-bold">{{ formatCurrency(summary.totalRevenue) }}</div>
          <p class="text-xs text-muted-foreground mt-1">
            {{ summary.totalWashes }} washes completed
          </p>
        </div>

        <div class="bg-card border rounded-lg p-6">
          <div class="flex items-center gap-2 mb-2">
            <BarChart3 class="w-5 h-5 text-blue-600" />
            <span class="text-sm font-medium text-muted-foreground">Total Washes</span>
          </div>
          <div class="text-3xl font-bold">{{ summary.totalWashes }}</div>
          <p class="text-xs text-muted-foreground mt-1">Services completed</p>
        </div>

        <div class="bg-card border rounded-lg p-6">
          <div class="flex items-center gap-2 mb-2">
            <TrendingUp class="w-5 h-5 text-purple-600" />
            <span class="text-sm font-medium text-muted-foreground">Average Ticket</span>
          </div>
          <div class="text-3xl font-bold">{{ formatCurrency(summary.averageTicket) }}</div>
          <p class="text-xs text-muted-foreground mt-1">Per wash</p>
        </div>
      </div>

      <!-- Package Breakdown -->
      <div class="bg-card border rounded-lg">
        <div class="px-6 py-4 border-b">
          <h2 class="text-xl font-bold flex items-center gap-2">
            <Package class="w-5 h-5" />
            Package Performance
          </h2>
        </div>

        <div v-if="packageBreakdown.length === 0" class="px-6 py-12 text-center">
          <Package class="w-12 h-12 mx-auto text-muted-foreground mb-3" />
          <p class="text-muted-foreground">No data available for the selected period</p>
        </div>

        <div v-else class="overflow-x-auto">
          <table class="w-full">
            <thead class="bg-muted/50">
              <tr>
                <th class="px-6 py-3 text-left text-sm font-semibold">Package Name</th>
                <th class="px-6 py-3 text-left text-sm font-semibold">Price</th>
                <th class="px-6 py-3 text-right text-sm font-semibold">Count</th>
                <th class="px-6 py-3 text-right text-sm font-semibold">Revenue</th>
                <th class="px-6 py-3 text-right text-sm font-semibold">% of Total</th>
              </tr>
            </thead>
            <tbody class="divide-y">
              <tr
                v-for="item in packageBreakdown"
                :key="item.package.id"
                class="hover:bg-muted/50 transition-colors"
              >
                <td class="px-6 py-4 font-semibold">{{ item.package.name }}</td>
                <td class="px-6 py-4 text-muted-foreground">
                  {{ formatCurrency(item.package.price) }}
                </td>
                <td class="px-6 py-4 text-right font-medium">{{ item.count }}</td>
                <td class="px-6 py-4 text-right font-bold text-green-600">
                  {{ formatCurrency(item.revenue) }}
                </td>
                <td class="px-6 py-4 text-right text-muted-foreground">
                  {{ ((item.revenue / summary.totalRevenue) * 100).toFixed(1) }}%
                </td>
              </tr>
            </tbody>
            <tfoot class="bg-muted/50 font-bold">
              <tr>
                <td class="px-6 py-4" colspan="2">Total</td>
                <td class="px-6 py-4 text-right">{{ summary.totalWashes }}</td>
                <td class="px-6 py-4 text-right text-green-600">
                  {{ formatCurrency(summary.totalRevenue) }}
                </td>
                <td class="px-6 py-4 text-right">100%</td>
              </tr>
            </tfoot>
          </table>
        </div>
      </div>

      <!-- Visual Breakdown -->
      <div class="bg-card border rounded-lg p-6">
        <h3 class="text-lg font-bold mb-4">Revenue Distribution</h3>
        <div class="space-y-3">
          <div
            v-for="item in packageBreakdown"
            :key="item.package.id"
          >
            <div class="flex items-center justify-between mb-1 text-sm">
              <span class="font-medium">{{ item.package.name }}</span>
              <span class="text-muted-foreground">{{ formatCurrency(item.revenue) }}</span>
            </div>
            <div class="w-full bg-muted rounded-full h-3 overflow-hidden">
              <div
                class="h-full bg-gradient-to-r from-primary to-primary/70 transition-all"
                :style="{
                  width: `${(item.revenue / summary.totalRevenue) * 100}%`
                }"
              ></div>
            </div>
          </div>
        </div>
      </div>

      <!-- Export Options -->
      <div class="bg-gradient-to-r from-primary/10 to-primary/5 border border-primary/20 rounded-lg p-6">
        <h3 class="text-lg font-bold mb-2">Export Report</h3>
        <p class="text-sm text-muted-foreground mb-4">
          Download this report for your records or further analysis
        </p>
        <div class="flex gap-3">
          <button
            class="px-4 py-2 bg-white border rounded-lg font-medium hover:bg-muted transition-colors"
            disabled
          >
            Export as PDF
          </button>
          <button
            class="px-4 py-2 bg-white border rounded-lg font-medium hover:bg-muted transition-colors"
            disabled
          >
            Export as CSV
          </button>
        </div>
        <p class="text-xs text-muted-foreground mt-2">Coming soon</p>
      </div>
    </div>
  </ManagerLayout>
</template>
