<script setup lang="ts">
import { Head } from '@inertiajs/vue3'
import ManagerLayout from '@/Layouts/ManagerLayout.vue'
import { Building2, DollarSign, Waves, TrendingUp, Info } from 'lucide-vue-next'

defineProps<{
  branches: Array<{
    id: number
    name: string
    code: string
    todayRevenue: number
    monthRevenue: number
    activeBays: number
    totalBays: number
  }>
}>()

const formatCurrency = (amount: number) => {
  return new Intl.NumberFormat('en-US', {
    style: 'currency',
    currency: 'USD',
  }).format(amount)
}

const getBayUtilization = (active: number, total: number) => {
  if (total === 0) return 0
  return ((active / total) * 100).toFixed(0)
}
</script>

<template>
  <Head title="All Branches Analytics" />

  <ManagerLayout>
    <div class="p-6 space-y-6">
      <!-- Header -->
      <div>
        <h1 class="text-3xl font-bold">All Branches Analytics</h1>
        <p class="text-muted-foreground mt-1">View performance across all locations (read-only)</p>
      </div>

      <!-- Info Notice -->
      <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 flex items-start gap-3">
        <Info class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" />
        <div class="flex-1">
          <h3 class="font-semibold text-blue-900 mb-1">Read-Only Access</h3>
          <p class="text-sm text-blue-700">
            You can view analytics for all branches to understand system-wide performance, but you can only manage your assigned branch.
          </p>
        </div>
      </div>

      <!-- Summary Stats -->
      <div class="grid gap-6 md:grid-cols-4">
        <div class="bg-card border rounded-lg p-6">
          <div class="flex items-center gap-2 mb-2">
            <Building2 class="w-5 h-5 text-primary" />
            <span class="text-sm font-medium text-muted-foreground">Total Branches</span>
          </div>
          <div class="text-3xl font-bold">{{ branches.length }}</div>
          <p class="text-xs text-muted-foreground mt-1">Active locations</p>
        </div>

        <div class="bg-card border rounded-lg p-6">
          <div class="flex items-center gap-2 mb-2">
            <DollarSign class="w-5 h-5 text-green-600" />
            <span class="text-sm font-medium text-muted-foreground">Today's Total</span>
          </div>
          <div class="text-3xl font-bold">
            {{ formatCurrency(branches.reduce((sum, b) => sum + b.todayRevenue, 0)) }}
          </div>
          <p class="text-xs text-muted-foreground mt-1">All branches</p>
        </div>

        <div class="bg-card border rounded-lg p-6">
          <div class="flex items-center gap-2 mb-2">
            <TrendingUp class="w-5 h-5 text-blue-600" />
            <span class="text-sm font-medium text-muted-foreground">Month's Total</span>
          </div>
          <div class="text-3xl font-bold">
            {{ formatCurrency(branches.reduce((sum, b) => sum + b.monthRevenue, 0)) }}
          </div>
          <p class="text-xs text-muted-foreground mt-1">All branches</p>
        </div>

        <div class="bg-card border rounded-lg p-6">
          <div class="flex items-center gap-2 mb-2">
            <Waves class="w-5 h-5 text-purple-600" />
            <span class="text-sm font-medium text-muted-foreground">Active Bays</span>
          </div>
          <div class="text-3xl font-bold">
            {{ branches.reduce((sum, b) => sum + b.activeBays, 0) }}
          </div>
          <p class="text-xs text-muted-foreground mt-1">
            of {{ branches.reduce((sum, b) => sum + b.totalBays, 0) }} total
          </p>
        </div>
      </div>

      <!-- Branch Comparison Table -->
      <div class="bg-card border rounded-lg overflow-hidden">
        <div class="px-6 py-4 border-b">
          <h2 class="text-xl font-bold">Branch Performance Comparison</h2>
        </div>

        <div class="overflow-x-auto">
          <table class="w-full">
            <thead class="bg-muted/50">
              <tr>
                <th class="px-6 py-3 text-left text-sm font-semibold">Branch</th>
                <th class="px-6 py-3 text-left text-sm font-semibold">Code</th>
                <th class="px-6 py-3 text-right text-sm font-semibold">Today's Revenue</th>
                <th class="px-6 py-3 text-right text-sm font-semibold">Month Revenue</th>
                <th class="px-6 py-3 text-right text-sm font-semibold">Bay Utilization</th>
                <th class="px-6 py-3 text-right text-sm font-semibold">Bays</th>
              </tr>
            </thead>
            <tbody class="divide-y">
              <tr
                v-for="branch in branches"
                :key="branch.id"
                class="hover:bg-muted/50 transition-colors"
              >
                <td class="px-6 py-4">
                  <div class="flex items-center gap-2">
                    <Building2 class="w-4 h-4 text-muted-foreground" />
                    <span class="font-semibold">{{ branch.name }}</span>
                  </div>
                </td>
                <td class="px-6 py-4 text-muted-foreground font-mono text-sm">
                  {{ branch.code }}
                </td>
                <td class="px-6 py-4 text-right font-bold text-green-600">
                  {{ formatCurrency(branch.todayRevenue) }}
                </td>
                <td class="px-6 py-4 text-right font-semibold">
                  {{ formatCurrency(branch.monthRevenue) }}
                </td>
                <td class="px-6 py-4 text-right">
                  <div class="flex items-center justify-end gap-2">
                    <div class="w-16 bg-muted rounded-full h-2 overflow-hidden">
                      <div
                        class="h-full bg-gradient-to-r from-primary to-primary/70"
                        :style="{
                          width: `${getBayUtilization(branch.activeBays, branch.totalBays)}%`
                        }"
                      ></div>
                    </div>
                    <span class="text-sm font-medium w-10 text-right">
                      {{ getBayUtilization(branch.activeBays, branch.totalBays) }}%
                    </span>
                  </div>
                </td>
                <td class="px-6 py-4 text-right text-muted-foreground">
                  <span class="font-semibold text-foreground">{{ branch.activeBays }}</span> / {{ branch.totalBays }}
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Top Performers -->
      <div class="grid gap-6 lg:grid-cols-2">
        <!-- Top by Today's Revenue -->
        <div class="bg-card border rounded-lg p-6">
          <h3 class="text-lg font-bold mb-4 flex items-center gap-2">
            <TrendingUp class="w-5 h-5 text-green-600" />
            Top Performers Today
          </h3>
          <div class="space-y-3">
            <div
              v-for="(branch, index) in branches.slice().sort((a, b) => b.todayRevenue - a.todayRevenue).slice(0, 5)"
              :key="branch.id"
              class="flex items-center justify-between p-3 bg-muted/50 rounded-lg"
            >
              <div class="flex items-center gap-3">
                <div class="flex items-center justify-center w-8 h-8 bg-primary/10 rounded-lg text-sm font-bold">
                  {{ index + 1 }}
                </div>
                <div>
                  <div class="font-semibold">{{ branch.name }}</div>
                  <div class="text-xs text-muted-foreground">{{ branch.code }}</div>
                </div>
              </div>
              <div class="text-right">
                <div class="font-bold text-green-600">{{ formatCurrency(branch.todayRevenue) }}</div>
              </div>
            </div>
          </div>
        </div>

        <!-- Top by Month Revenue -->
        <div class="bg-card border rounded-lg p-6">
          <h3 class="text-lg font-bold mb-4 flex items-center gap-2">
            <TrendingUp class="w-5 h-5 text-blue-600" />
            Top Performers This Month
          </h3>
          <div class="space-y-3">
            <div
              v-for="(branch, index) in branches.slice().sort((a, b) => b.monthRevenue - a.monthRevenue).slice(0, 5)"
              :key="branch.id"
              class="flex items-center justify-between p-3 bg-muted/50 rounded-lg"
            >
              <div class="flex items-center gap-3">
                <div class="flex items-center justify-center w-8 h-8 bg-blue-500/10 rounded-lg text-sm font-bold text-blue-600">
                  {{ index + 1 }}
                </div>
                <div>
                  <div class="font-semibold">{{ branch.name }}</div>
                  <div class="text-xs text-muted-foreground">{{ branch.code }}</div>
                </div>
              </div>
              <div class="text-right">
                <div class="font-bold text-blue-600">{{ formatCurrency(branch.monthRevenue) }}</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </ManagerLayout>
</template>
