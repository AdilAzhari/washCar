<script setup lang="ts">
import { Head } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import StatCard from '@/Components/Dashboard/StatCard.vue'
import BayStatusCard from '@/Components/Dashboard/BayStatusCard.vue'
import { Card, CardContent, CardHeader, CardTitle, Badge } from '@/Components/ui'
import { Activity, Clock, CheckCircle, DollarSign, TrendingUp, Package as PackageIcon } from 'lucide-vue-next'

defineProps<{
  stats: {
    ongoingWashes: number
    inQueue: number
    completedToday: number
    todayRevenue: string
  }
  bays: Array<{
    id: number
    bayName: string
    isOperational: boolean
    status: string
    currentWash?: {
      id: number
      customerName: string
      vehiclePlate: string
      packageName: string
      packageColor: string
      progress: number
      startedAt: string
      estimatedCompletion?: string
    }
    queueCount: number
  }>
  recentTransactions: Array<{
    id: string
    customer: string
    plate: string
    amount: string
    method: string
    status: string
    completedAt: string
  }>
  packageSales: Array<{
    id: number
    name: string
    color: string
    count: number
  }>
}>()
</script>

<template>
  <Head title="Dashboard" />

  <AuthenticatedLayout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Dashboard
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="space-y-6 animate-fade-in">
          <!-- Stats Grid -->
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <StatCard
              title="Ongoing Washes"
              :value="stats.ongoingWashes"
              subtitle="Currently in progress"
              :icon="Activity"
              icon-class-name="bg-primary/10 text-primary"
            />
            <StatCard
              title="In Queue"
              :value="stats.inQueue"
              subtitle="Waiting customers"
              :icon="Clock"
              icon-class-name="bg-warning/10 text-warning"
            />
            <StatCard
              title="Completed Today"
              :value="stats.completedToday"
              subtitle="Total washes"
              :icon="CheckCircle"
              icon-class-name="bg-success/10 text-success"
            />
            <StatCard
              title="Today's Revenue"
              :value="stats.todayRevenue"
              subtitle="Total earnings"
              :icon="DollarSign"
              icon-class-name="bg-accent/10 text-accent"
            />
          </div>

          <!-- Bay Status Grid -->
          <div>
            <h2 class="text-xl font-semibold mb-4">Bay Status & Queue Overview</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
              <BayStatusCard
                v-for="bay in bays"
                :key="bay.id"
                :bay-name="bay.bayName"
                :is-operational="bay.isOperational"
                :current-wash="bay.currentWash"
                :queue-count="bay.queueCount"
              />
            </div>
          </div>

          <!-- Recent Activity Grid -->
          <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Recent Transactions -->
            <Card class="border-border/50">
              <CardHeader>
                <CardTitle class="flex items-center justify-between">
                  <span>Recent Transactions</span>
                  <TrendingUp class="h-5 w-5 text-muted-foreground" />
                </CardTitle>
              </CardHeader>
              <CardContent>
                <div class="space-y-3">
                  <div
                    v-for="txn in recentTransactions"
                    :key="txn.id"
                    class="flex items-center justify-between p-3 rounded-lg hover:bg-muted/50 transition-colors"
                  >
                    <div class="flex-1">
                      <p class="font-medium text-sm">{{ txn.customer }}</p>
                      <p class="text-xs text-muted-foreground">{{ txn.plate }}</p>
                    </div>
                    <div class="flex items-center gap-3">
                      <Badge variant="outline" class="badge-status">
                        {{ txn.method }}
                      </Badge>
                      <p class="font-semibold text-sm w-16 text-right">{{ txn.amount }}</p>
                      <Badge
                        :variant="txn.status === 'completed' ? 'default' : 'secondary'"
                        class="badge-status w-20 justify-center"
                      >
                        {{ txn.status }}
                      </Badge>
                    </div>
                  </div>
                </div>
              </CardContent>
            </Card>

            <!-- Package Sales -->
            <Card class="border-border/50">
              <CardHeader>
                <CardTitle class="flex items-center justify-between">
                  <span>Today's Package Sales</span>
                  <PackageIcon class="h-5 w-5 text-muted-foreground" />
                </CardTitle>
              </CardHeader>
              <CardContent>
                <div class="space-y-4">
                  <div v-for="pkg in packageSales" :key="pkg.id" class="space-y-2">
                    <div class="flex items-center justify-between">
                      <div class="flex items-center gap-2">
                        <div class="w-3 h-3 rounded-full" :style="{ backgroundColor: pkg.color }" />
                        <span class="text-sm font-medium">{{ pkg.name }}</span>
                      </div>
                      <span class="text-sm font-semibold">{{ pkg.count }} sales</span>
                    </div>
                    <div class="h-2 bg-muted rounded-full overflow-hidden">
                      <div
                        class="h-full rounded-full transition-all duration-500"
                        :style="{
                          width: `${stats.completedToday > 0 ? (pkg.count / stats.completedToday) * 100 : 0}%`,
                          backgroundColor: pkg.color
                        }"
                      />
                    </div>
                  </div>
                </div>
              </CardContent>
            </Card>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
