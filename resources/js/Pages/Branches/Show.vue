<script setup lang="ts">
import { ref } from 'vue'
import { Head, router, Link } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import BranchFormModal from '@/Components/Branch/BranchFormModal.vue'
import StaffFormModal from '@/Components/Staff/StaffFormModal.vue'
import { Card, CardContent, CardHeader, CardTitle, Badge, Button } from '@/Components/ui'
import { ArrowLeft, Edit, QrCode, MapPin, Phone, Clock, Users, DollarSign, Activity } from 'lucide-vue-next'

interface Branch {
  id: number
  name: string
  code: string
  status: string
  address: string
  phone: string
  manager_name: string
  manager_contact: string
  opening_time: string
  closing_time: string
  is_active: boolean
  users: any[]
  bays: any[]
}

const props = defineProps<{
  branch: Branch
  todayStats: {
    revenue: number
    completed: number
    in_progress: number
    waiting: number
  }
  revenueData: Array<{
    month: string
    revenue: number
    washes: number
  }>
  staff: any[]
}>()

const isEditFormOpen = ref(false)
const isStaffFormOpen = ref(false)
const selectedStaff = ref(null)

const formatPrice = (price: number) => {
  return new Intl.NumberFormat('ms-MY', { style: 'currency', currency: 'MYR' }).format(price)
}

const handleEditStaff = (staff: any) => {
  selectedStaff.value = staff
  isStaffFormOpen.value = true
}

const handleDeleteStaff = (id: number) => {
  if (confirm('Delete this staff member?')) {
    router.delete(route('staff.destroy', id))
  }
}

const maxRevenue = Math.max(...props.revenueData.map(d => d.revenue), 1)
</script>

<template>
  <Head :title="branch.name" />
  <AuthenticatedLayout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">Branch Details</h2>
    </template>
    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="space-y-6 animate-fade-in">
          <!-- Header -->
          <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
              <Link :href="route('admin.branches.index')">
                <Button variant="outline" size="sm">
                  <ArrowLeft class="w-4 h-4 mr-2" />
                  Back
                </Button>
              </Link>
              <div>
                <div class="flex items-center gap-3 mb-1">
                  <h1 class="text-2xl font-bold">{{ branch.name }}</h1>
                  <Badge :variant="branch.is_active ? 'default' : 'secondary'">
                    {{ branch.is_active ? 'Active' : 'Inactive' }}
                  </Badge>
                </div>
                <p class="text-muted-foreground">Code: {{ branch.code }}</p>
              </div>
            </div>
            <div class="flex gap-2">
              <Link :href="route('admin.branches.qrcode', branch.id)">
                <Button variant="outline">
                  <QrCode class="w-4 h-4 mr-2" />
                  QR Code
                </Button>
              </Link>
              <Button class="btn-primary" @click="isEditFormOpen = true">
                <Edit class="w-4 h-4 mr-2" />
                Edit Branch
              </Button>
            </div>
          </div>

          <!-- Branch Information Card -->
          <Card>
            <CardHeader>
              <CardTitle>Branch Information</CardTitle>
            </CardHeader>
            <CardContent class="grid gap-4 md:grid-cols-2">
              <div class="space-y-3">
                <div class="flex items-start gap-2">
                  <MapPin class="w-5 h-5 text-muted-foreground mt-0.5" />
                  <div>
                    <p class="text-sm text-muted-foreground">Address</p>
                    <p>{{ branch.address }}</p>
                  </div>
                </div>
                <div class="flex items-start gap-2">
                  <Phone class="w-5 h-5 text-muted-foreground mt-0.5" />
                  <div>
                    <p class="text-sm text-muted-foreground">Phone</p>
                    <p>{{ branch.phone }}</p>
                  </div>
                </div>
              </div>
              <div class="space-y-3">
                <div class="flex items-start gap-2">
                  <Clock class="w-5 h-5 text-muted-foreground mt-0.5" />
                  <div>
                    <p class="text-sm text-muted-foreground">Operating Hours</p>
                    <p>{{ branch.opening_time }} - {{ branch.closing_time }}</p>
                  </div>
                </div>
                <div class="flex items-start gap-2">
                  <Users class="w-5 h-5 text-muted-foreground mt-0.5" />
                  <div>
                    <p class="text-sm text-muted-foreground">Manager</p>
                    <p>{{ branch.manager_name }}</p>
                    <p class="text-sm text-muted-foreground">{{ branch.manager_contact }}</p>
                  </div>
                </div>
              </div>
            </CardContent>
          </Card>

          <!-- Today's Statistics -->
          <div>
            <h2 class="text-xl font-bold mb-4">Today's Statistics</h2>
            <div class="grid gap-4 md:grid-cols-4">
              <Card class="stat-card">
                <CardContent class="p-6">
                  <div class="flex items-center justify-between">
                    <div>
                      <p class="text-sm text-muted-foreground">Today's Revenue</p>
                      <p class="text-3xl font-bold mt-1">{{ formatPrice(todayStats.revenue) }}</p>
                    </div>
                    <DollarSign class="w-8 h-8 text-primary" />
                  </div>
                </CardContent>
              </Card>
              <Card class="stat-card">
                <CardContent class="p-6">
                  <div class="flex items-center justify-between">
                    <div>
                      <p class="text-sm text-muted-foreground">Completed</p>
                      <p class="text-3xl font-bold mt-1">{{ todayStats.completed }}</p>
                    </div>
                    <Activity class="w-8 h-8 text-success" />
                  </div>
                </CardContent>
              </Card>
              <Card class="stat-card">
                <CardContent class="p-6">
                  <div class="flex items-center justify-between">
                    <div>
                      <p class="text-sm text-muted-foreground">In Progress</p>
                      <p class="text-3xl font-bold mt-1">{{ todayStats.in_progress }}</p>
                    </div>
                    <Activity class="w-8 h-8 text-accent" />
                  </div>
                </CardContent>
              </Card>
              <Card class="stat-card">
                <CardContent class="p-6">
                  <div class="flex items-center justify-between">
                    <div>
                      <p class="text-sm text-muted-foreground">Waiting</p>
                      <p class="text-3xl font-bold mt-1">{{ todayStats.waiting }}</p>
                    </div>
                    <Users class="w-8 h-8 text-warning" />
                  </div>
                </CardContent>
              </Card>
            </div>
          </div>

          <!-- 6-Month Revenue Trend -->
          <Card>
            <CardHeader>
              <CardTitle>6-Month Revenue Trend</CardTitle>
            </CardHeader>
            <CardContent>
              <div class="space-y-4">
                <div v-for="data in revenueData" :key="data.month" class="space-y-2">
                  <div class="flex items-center justify-between text-sm">
                    <span class="font-medium">{{ data.month }}</span>
                    <div class="flex items-center gap-4">
                      <span>{{ data.washes }} washes</span>
                      <span class="font-bold">{{ formatPrice(data.revenue) }}</span>
                    </div>
                  </div>
                  <div class="w-full bg-secondary rounded-full h-2">
                    <div
                      class="bg-primary h-2 rounded-full transition-all"
                      :style="{ width: `${(data.revenue / maxRevenue) * 100}%` }"
                    ></div>
                  </div>
                </div>
              </div>
            </CardContent>
          </Card>

          <!-- Staff Members -->
          <div>
            <div class="flex items-center justify-between mb-4">
              <h2 class="text-xl font-bold">Staff Members</h2>
              <Button class="btn-primary" @click="() => { selectedStaff = null; isStaffFormOpen = true }">
                <Users class="w-4 h-4 mr-2" />
                Add Staff
              </Button>
            </div>
            <div class="grid gap-4 md:grid-cols-3">
              <Card v-for="member in staff" :key="member.id" class="hover-lift">
                <CardContent class="p-6">
                  <div class="space-y-3">
                    <div>
                      <p class="font-semibold text-lg">{{ member.name }}</p>
                      <Badge :variant="member.role === 'admin' ? 'destructive' : member.role === 'manager' ? 'secondary' : 'default'" class="mt-2 capitalize">
                        {{ member.role }}
                      </Badge>
                    </div>
                    <div class="text-sm text-muted-foreground space-y-1">
                      <p>{{ member.email }}</p>
                    </div>
                    <div class="flex gap-2 pt-2 border-t">
                      <Button size="sm" variant="outline" class="flex-1" @click="handleEditStaff(member)">
                        <Edit class="w-4 h-4" />
                      </Button>
                      <Button size="sm" variant="outline" @click="handleDeleteStaff(member.id)">
                        <Activity class="w-4 h-4" />
                      </Button>
                    </div>
                  </div>
                </CardContent>
              </Card>

              <Card v-if="staff.length === 0" class="bg-muted/30">
                <CardContent class="p-12 text-center">
                  <Users class="w-12 h-12 text-muted-foreground mx-auto mb-4" />
                  <h3 class="text-lg font-semibold mb-2">No staff members</h3>
                  <p class="text-muted-foreground">Add staff to this branch</p>
                </CardContent>
              </Card>
            </div>
          </div>

          <!-- Modals -->
          <BranchFormModal :is-open="isEditFormOpen" :branch="branch" @close="isEditFormOpen = false" />
          <StaffFormModal :is-open="isStaffFormOpen" :staff="selectedStaff" :branches="[branch]" @close="() => { isStaffFormOpen = false; selectedStaff = null }" />
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
