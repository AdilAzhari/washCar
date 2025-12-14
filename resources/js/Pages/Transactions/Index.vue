<script setup lang="ts">
import { Head } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Card, CardContent, CardHeader, CardTitle, Badge, Table, TableHeader, TableBody, TableRow, TableHead, TableCell } from '@/Components/ui'
import { DollarSign, TrendingUp, CheckCircle, Activity } from 'lucide-vue-next'

interface Transaction {
  id: number
  branch: string
  customer: string
  package: string
  bay: string
  amount: number
  status: string
  started_at: string
  completed_at: string
  created_at: string
}

const props = defineProps<{
  transactions: Transaction[]
  stats: {
    total: number
    completed: number
    active: number
    revenue: number
  }
}>()

const formatPrice = (price: number) => {
  return new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(price)
}

const formatDate = (date: string) => {
  return new Date(date).toLocaleString()
}
</script>

<template>
  <Head title="Transactions" />
  <AuthenticatedLayout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">Transactions</h2>
    </template>
    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="space-y-6 animate-fade-in">
          <!-- Header -->
          <div>
            <h1 class="text-2xl font-bold mb-2">Transaction History</h1>
            <p class="text-muted-foreground">View all wash transactions and revenue</p>
          </div>
          <!-- Stats Grid -->
          <div class="grid gap-4 md:grid-cols-4">
            <Card class="stat-card">
              <CardContent class="p-6">
                <div class="flex items-center justify-between">
                  <div>
                    <p class="text-sm text-muted-foreground">Total Transactions</p>
                    <p class="text-3xl font-bold mt-1">{{ stats.total }}</p>
                  </div>
                  <Activity class="w-8 h-8 text-primary" />
                </div>
              </CardContent>
            </Card>
            <Card class="stat-card">
              <CardContent class="p-6">
                <div class="flex items-center justify-between">
                  <div>
                    <p class="text-sm text-muted-foreground">Completed</p>
                    <p class="text-3xl font-bold mt-1 text-success">{{ stats.completed }}</p>
                  </div>
                  <CheckCircle class="w-8 h-8 text-success" />
                </div>
              </CardContent>
            </Card>
            <Card class="stat-card">
              <CardContent class="p-6">
                <div class="flex items-center justify-between">
                  <div>
                    <p class="text-sm text-muted-foreground">Active</p>
                    <p class="text-3xl font-bold mt-1">{{ stats.active }}</p>
                  </div>
                  <TrendingUp class="w-8 h-8 text-accent" />
                </div>
              </CardContent>
            </Card>
            <Card class="stat-card">
              <CardContent class="p-6">
                <div class="flex items-center justify-between">
                  <div>
                    <p class="text-sm text-muted-foreground">Revenue</p>
                    <p class="text-3xl font-bold mt-1">{{ formatPrice(stats.revenue) }}</p>
                  </div>
                  <DollarSign class="w-8 h-8 text-primary" />
                </div>
              </CardContent>
            </Card>
          </div>
          <Card>
            <CardHeader>
              <CardTitle>Recent Transactions</CardTitle>
            </CardHeader>
            <CardContent>
              <Table>
                <TableHeader>
                  <TableRow>
                    <TableHead>Branch</TableHead>
                    <TableHead>Customer</TableHead>
                    <TableHead>Package</TableHead>
                    <TableHead>Bay</TableHead>
                    <TableHead>Amount</TableHead>
                    <TableHead>Status</TableHead>
                    <TableHead>Date</TableHead>
                  </TableRow>
                </TableHeader>
                <TableBody>
                  <TableRow v-for="transaction in transactions" :key="transaction.id">
                    <TableCell>{{ transaction.branch || 'N/A' }}</TableCell>
                    <TableCell>{{ transaction.customer || 'Walk-in' }}</TableCell>
                    <TableCell>{{ transaction.package || 'N/A' }}</TableCell>
                    <TableCell>{{ transaction.bay || 'N/A' }}</TableCell>
                    <TableCell class="font-medium">{{ formatPrice(transaction.amount) }}</TableCell>
                    <TableCell>
                      <Badge :variant="transaction.status === 'completed' ? 'default' : 'secondary'" class="capitalize">
                        {{ transaction.status }}
                      </Badge>
                    </TableCell>
                    <TableCell class="text-sm text-muted-foreground">{{ formatDate(transaction.created_at) }}</TableCell>
                  </TableRow>
                </TableBody>
              </Table>
            </CardContent>
          </Card>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
