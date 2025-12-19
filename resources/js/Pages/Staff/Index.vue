<script setup lang="ts">
import { ref, computed } from 'vue'
import { Head, router, usePage } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import StaffFormModal from '@/Components/Staff/StaffFormModal.vue'
import { Card, CardContent, Badge, Button, Table, TableHeader, TableBody, TableRow, TableHead, TableCell, DeleteConfirmDialog } from '@/Components/ui'
import { Plus, Edit, Trash2, Users } from 'lucide-vue-next'

interface Staff {
  id: number
  name: string
  email: string
  role: string
  branch?: { id: number; name: string }
}

const props = defineProps<{
  staff: Staff[]
  branches: any[]
}>()

const page = usePage()
const userRole = computed(() => (page.props.auth as any)?.user?.role || 'admin')

const getRouteName = (routeName: string) => {
  return `${userRole.value}.${routeName}`
}

const isFormOpen = ref(false)
const selectedStaff = ref<Staff | null>(null)
const deleteDialogOpen = ref(false)
const staffToDelete = ref<Staff | null>(null)
const isDeleting = ref(false)

const handleEdit = (staff: Staff) => {
  selectedStaff.value = staff
  isFormOpen.value = true
}

const handleDelete = (id: number) => {
  const staff = props.staff.find(s => s.id === id)
  if (staff) {
    staffToDelete.value = staff
    deleteDialogOpen.value = true
  }
}

const handleDeleteConfirm = () => {
  if (staffToDelete.value) {
    isDeleting.value = true
    router.delete(route(getRouteName('staff.destroy'), staffToDelete.value.id), {
      onSuccess: () => {
        deleteDialogOpen.value = false
        staffToDelete.value = null
      },
      onFinish: () => {
        isDeleting.value = false
      }
    })
  }
}

const getRoleBadge = (role: string) => {
  switch (role) {
    case 'admin': return 'destructive'
    case 'manager': return 'secondary'
    default: return 'default'
  }
}
</script>

<template>
  <Head title="Staff Management" />
  <AuthenticatedLayout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">Staff Management</h2>
    </template>
    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="space-y-6 animate-fade-in">
          <!-- Header -->
          <div class="flex items-center justify-between">
            <div>
              <h1 class="text-2xl font-bold mb-2">Staff Management</h1>
              <p class="text-muted-foreground">Manage staff members and roles</p>
            </div>
            <Button class="btn-primary" @click="() => { selectedStaff = null; isFormOpen = true }">
              <Plus class="w-4 h-4 mr-2" />
              Add Staff
            </Button>
          </div>
          <Card>
            <Table>
              <TableHeader>
                <TableRow>
                  <TableHead>Name</TableHead>
                  <TableHead>Email</TableHead>
                  <TableHead>Role</TableHead>
                  <TableHead>Branch</TableHead>
                  <TableHead class="text-right">Actions</TableHead>
                </TableRow>
              </TableHeader>
              <TableBody>
                <TableRow v-for="member in staff" :key="member.id">
                  <TableCell class="font-medium">{{ member.name }}</TableCell>
                  <TableCell>{{ member.email }}</TableCell>
                  <TableCell><Badge :variant="getRoleBadge(member.role)" class="capitalize">{{ member.role }}</Badge></TableCell>
                  <TableCell>{{ member.branch?.name || 'N/A' }}</TableCell>
                  <TableCell class="text-right">
                    <div class="flex justify-end gap-2">
                      <Button size="sm" variant="outline" @click="handleEdit(member)"><Edit class="w-4 h-4" /></Button>
                      <Button size="sm" variant="outline" @click="handleDelete(member.id)"><Trash2 class="w-4 h-4" /></Button>
                    </div>
                  </TableCell>
                </TableRow>
              </TableBody>
            </Table>
          </Card>
          <StaffFormModal :is-open="isFormOpen" :staff="selectedStaff" :branches="branches" @close="() => { isFormOpen = false; selectedStaff = null }" />

          <!-- Delete Confirmation Dialog -->
          <DeleteConfirmDialog
            :is-open="deleteDialogOpen"
            :item-name="staffToDelete?.name"
            :is-deleting="isDeleting"
            title="Delete Staff Member"
            message="Are you sure you want to delete this staff member? This action cannot be undone."
            @close="deleteDialogOpen = false"
            @confirm="handleDeleteConfirm"
          />
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
