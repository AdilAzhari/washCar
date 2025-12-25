<script setup lang="ts">
import { ref, computed } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import CustomerFormModal from '@/Components/Customer/CustomerFormModal.vue'
import { EmptyState, Card, CardContent, Badge, Button, Input, Table, TableHeader, TableBody, TableRow, TableHead, TableCell, DeleteConfirmDialog } from '@/Components/ui'
import StatCard from '@/Components/Dashboard/StatCard.vue'
import { Plus, Edit, Trash2, Users, User, Phone, Car } from 'lucide-vue-next'

interface Customer {
  id: number
  name: string | null
  phone: string | null
  email?: string | null
  plate_number: string | null
  vehicle_type: string | null
  make?: string | null
  model?: string | null
  color?: string | null
  membership: string
  status: string
  washes_count?: number
}

const props = defineProps<{
  customers: {
    data: Customer[]
    current_page: number
    last_page: number
    per_page: number
    total: number
  }
}>()

const isFormOpen = ref(false)
const selectedCustomer = ref<Customer | null>(null)
const searchQuery = ref('')
const deleteDialogOpen = ref(false)
const customerToDelete = ref<Customer | null>(null)
const isDeleting = ref(false)

const stats = computed(() => ({
  total: props.customers.total,
  active: props.customers.data.filter(c => c.status === 'active').length,
  regular: props.customers.data.filter(c => c.membership === 'Regular').length,
  premium: props.customers.data.filter(c => ['Gold', 'Platinum'].includes(c.membership)).length,
}))

const filteredCustomers = computed(() => {
  const query = searchQuery.value.toLowerCase()
  if (!query) return props.customers.data

  return props.customers.data.filter(customer =>
    customer.name?.toLowerCase().includes(query) ||
    customer.phone?.toLowerCase().includes(query) ||
    customer.plate_number?.toLowerCase().includes(query)
  )
})

const handleEdit = (customer: Customer) => {
  selectedCustomer.value = customer
  isFormOpen.value = true
}

const handleDelete = (customerId: number) => {
  const customer = props.customers.data.find(c => c.id === customerId)
  if (customer) {
    customerToDelete.value = customer
    deleteDialogOpen.value = true
  }
}

const handleDeleteConfirm = () => {
  if (customerToDelete.value) {
    isDeleting.value = true
    router.delete(route('customers.destroy', customerToDelete.value.id), {
      onSuccess: () => {
        deleteDialogOpen.value = false
        customerToDelete.value = null
      },
      onFinish: () => {
        isDeleting.value = false
      }
    })
  }
}

const openCreateModal = () => {
  selectedCustomer.value = null
  isFormOpen.value = true
}

const getMembershipColor = (membership: string) => {
  switch (membership) {
    case 'Platinum':
      return 'bg-purple-100 text-purple-800'
    case 'Gold':
      return 'bg-yellow-100 text-yellow-800'
    case 'Silver':
      return 'bg-gray-100 text-gray-800'
    default:
      return 'bg-blue-100 text-blue-800'
  }
}
</script>

<template>
  <Head title="Customer Management" />

  <AuthenticatedLayout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Customer Management
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="space-y-6 animate-fade-in">
          <!-- Header -->
          <div class="flex items-center justify-between">
            <div>
              <h1 class="text-2xl font-bold mb-2">Customer Management</h1>
              <p class="text-muted-foreground">Manage customer information and history</p>
            </div>
            <Button class="btn-primary" @click="openCreateModal">
              <Plus class="w-4 h-4 mr-2" />
              Add Customer
            </Button>
          </div>

          <!-- Stats Grid -->
          <div class="grid gap-4 md:grid-cols-4">
            <StatCard
              title="Total Customers"
              :value="stats.total"
              subtitle="All registered"
              :icon="Users"
              accent-color="primary"
            />
            <StatCard
              title="Active"
              :value="stats.active"
              subtitle="Current customers"
              :icon="User"
              accent-color="success"
            />
            <StatCard
              title="Regular Members"
              :value="stats.regular"
              subtitle="Standard tier"
              :icon="User"
              accent-color="tier-regular"
            />
            <StatCard
              title="Premium"
              :value="stats.premium"
              subtitle="Gold & Platinum"
              :icon="User"
              accent-color="tier-gold"
            />
          </div>

          <!-- Search -->
          <div class="flex items-center gap-4">
            <Input
              v-model="searchQuery"
              placeholder="Search by name, phone, or plate number..."
              class="max-w-md"
            />
          </div>

          <!-- Customers Table -->
          <Card>
            <Table>
              <TableHeader>
                <TableRow>
                  <TableHead>Customer</TableHead>
                  <TableHead>Contact</TableHead>
                  <TableHead>Vehicle</TableHead>
                  <TableHead>Membership</TableHead>
                  <TableHead>Washes</TableHead>
                  <TableHead>Status</TableHead>
                  <TableHead class="text-right">Actions</TableHead>
                </TableRow>
              </TableHeader>
              <TableBody>
                <TableRow v-for="customer in filteredCustomers" :key="customer.id" class="hover:bg-muted/50">
                  <TableCell>
                    <div>
                      <p class="font-medium">{{ customer.name || 'N/A' }}</p>
                      <p class="text-sm text-muted-foreground">{{ customer.plate_number || 'N/A' }}</p>
                    </div>
                  </TableCell>
                  <TableCell>
                    <div class="text-sm">
                      <div class="flex items-center gap-1">
                        <Phone class="w-3 h-3" />
                        {{ customer.phone }}
                      </div>
                      <p v-if="customer.email" class="text-muted-foreground">{{ customer.email }}</p>
                    </div>
                  </TableCell>
                  <TableCell>
                    <div class="flex items-center gap-2">
                      <Car class="w-4 h-4 text-muted-foreground" />
                      <div class="text-sm">
                        <p class="capitalize">{{ customer.vehicle_type }}</p>
                        <p v-if="customer.make" class="text-muted-foreground">{{ customer.make }} {{ customer.model }}</p>
                      </div>
                    </div>
                  </TableCell>
                  <TableCell>
                    <Badge :class="getMembershipColor(customer.membership)">
                      {{ customer.membership }}
                    </Badge>
                  </TableCell>
                  <TableCell>
                    <span class="font-medium">{{ customer.washes_count || 0 }}</span>
                  </TableCell>
                  <TableCell>
                    <Badge :variant="customer.status === 'active' ? 'default' : 'secondary'">
                      {{ customer.status }}
                    </Badge>
                  </TableCell>
                  <TableCell class="text-right">
                    <div class="flex justify-end gap-2">
                      <Button size="sm" variant="outline" @click="handleEdit(customer)">
                        <Edit class="w-4 h-4" />
                      </Button>
                      <Button size="sm" variant="outline" @click="handleDelete(customer.id)">
                        <Trash2 class="w-4 h-4" />
                      </Button>
                    </div>
                  </TableCell>
                </TableRow>
              </TableBody>
            </Table>
          </Card>

          <!-- Empty State -->
          <EmptyState
            v-if="filteredCustomers.length === 0 && searchQuery"
            :icon="Users"
            title="No customers found"
            message="Try adjusting your search query to find what you're looking for."
          />

          <EmptyState
            v-if="filteredCustomers.length === 0 && !searchQuery"
            :icon="Users"
            title="No customers yet"
            message="Start building your customer base by adding your first customer. Track their wash history and membership tiers."
            action-label="Add Customer"
            help-text="Customers can also join the queue by scanning QR codes at your branches."
            @action="openCreateModal"
          />

          <!-- Customer Form Modal -->
          <CustomerFormModal
            :is-open="isFormOpen"
            :customer="selectedCustomer"
            @close="() => { isFormOpen = false; selectedCustomer = null }"
          />

          <!-- Delete Confirmation Dialog -->
          <DeleteConfirmDialog
            :is-open="deleteDialogOpen"
            :item-name="customerToDelete?.name ?? undefined"
            :is-deleting="isDeleting"
            title="Delete Customer"
            message="Are you sure you want to delete this customer? This will also delete their wash history. This action cannot be undone."
            @close="deleteDialogOpen = false"
            @confirm="handleDeleteConfirm"
          />
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
