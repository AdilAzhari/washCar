<script setup lang="ts">
import { ref, computed } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import ApplicationLogo from '@/Components/ApplicationLogo.vue'
import {
  LayoutDashboard,
  ListOrdered,
  Waves,
  Users,
  PackageOpen,
  Calendar,
  Bell,
  Building2,
  Menu,
  X,
  LogOut,
  User,
} from 'lucide-vue-next'
import UserDropdown from '@/Components/UserDropdown.vue'

const sidebarOpen = ref(false)
const page = usePage()

const user = computed(() => page.props.auth.user)
const branch = computed(() => user.value?.branch)

const navigation = [
  { name: 'Dashboard', href: 'staff.dashboard', icon: LayoutDashboard },
  { name: 'Queue', href: 'staff.queue.index', icon: ListOrdered },
  { name: 'Bays', href: 'staff.bays.index', icon: Waves },
  { name: 'Appointments', href: 'staff.appointments.index', icon: Calendar },
  { name: 'Customers', href: 'staff.customers.index', icon: Users },
  { name: 'Inventory', href: 'staff.inventory.index', icon: PackageOpen },
  { name: 'Notifications', href: 'notifications.index', icon: Bell },
]

const isActive = (routeName: string) => {
  return route().current(routeName) || route().current(`${routeName}.*`)
}
</script>

<template>
  <div class="min-h-screen bg-background">
    <!-- Mobile sidebar backdrop -->
    <div
      v-if="sidebarOpen"
      class="fixed inset-0 z-40 bg-black/50 lg:hidden"
      @click="sidebarOpen = false"
    ></div>

    <!-- Sidebar -->
    <aside
      :class="[
        'fixed inset-y-0 left-0 z-50 w-64 bg-card border-r transform transition-transform duration-200 ease-in-out lg:translate-x-0',
        sidebarOpen ? 'translate-x-0' : '-translate-x-full',
      ]"
    >
      <div class="flex flex-col h-full">
        <!-- Logo -->
        <div class="flex items-center gap-2 px-6 py-6 border-b">
          <Link :href="route('staff.dashboard')" class="flex items-center gap-2">
            <ApplicationLogo class="h-8 w-auto" />
            <span class="text-xl font-bold">WashyWashy</span>
          </Link>
        </div>

        <!-- Branch Info Card -->
        <div v-if="branch" class="px-4 py-4 border-b">
          <div class="bg-gradient-to-br from-green-500/10 to-green-500/5 rounded-lg p-4">
            <div class="flex items-center gap-2 mb-1">
              <Building2 class="w-4 h-4 text-green-600" />
              <span class="text-xs font-medium text-muted-foreground uppercase">Your Branch</span>
            </div>
            <div class="text-sm font-bold">{{ branch.name }}</div>
            <div class="text-xs text-muted-foreground mt-0.5">Code: {{ branch.code }}</div>
          </div>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 px-4 py-6 space-y-1 overflow-y-auto">
          <Link
            v-for="item in navigation"
            :key="item.name"
            :href="route(item.href)"
            :class="[
              'flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors',
              isActive(item.href)
                ? 'bg-primary text-primary-foreground'
                : 'text-muted-foreground hover:bg-muted hover:text-foreground',
            ]"
          >
            <component :is="item.icon" class="w-5 h-5" />
            {{ item.name }}
          </Link>
        </nav>

      </div>
    </aside>

    <!-- Main Content -->
    <div class="lg:pl-64">
      <!-- Top Bar -->
      <header class="sticky top-0 z-30 bg-card border-b">
        <div class="flex items-center justify-between px-4 py-4">
          <button
            @click="sidebarOpen = !sidebarOpen"
            class="p-2 rounded-lg text-muted-foreground hover:bg-muted lg:hidden"
          >
            <Menu v-if="!sidebarOpen" class="w-6 h-6" />
            <X v-else class="w-6 h-6" />
          </button>

          <div v-if="$slots.header" class="flex-1">
            <slot name="header" />
          </div>

          <div class="flex items-center gap-4">
            <UserDropdown />
          </div>
        </div>
      </header>

      <!-- Page Content -->
      <main class="min-h-[calc(100vh-73px)]">
        <slot />
      </main>
    </div>
  </div>
</template>
