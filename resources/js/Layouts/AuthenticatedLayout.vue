<script setup lang="ts">
import { ref } from 'vue'
import { Link } from '@inertiajs/vue3'
import ApplicationLogo from '@/Components/ApplicationLogo.vue'
import {
  LayoutDashboard,
  Building2,
  Waves,
  Users,
  Package,
  ListOrdered,
  UsersRound,
  PackageOpen,
  Receipt,
  Bell,
  Menu,
  X,
  LogOut,
  User,
  Activity,
} from 'lucide-vue-next'
import UserDropdown from '@/Components/UserDropdown.vue'

const sidebarOpen = ref(false)

const navigation = [
  { name: 'Dashboard', href: 'admin.dashboard', icon: LayoutDashboard },
  { name: 'Branches', href: 'admin.branches.index', icon: Building2 },
  { name: 'Bays', href: 'admin.bays.index', icon: Waves },
  { name: 'Queue', href: 'admin.queue.index', icon: ListOrdered },
  { name: 'View Queue', href: 'admin.queue.view', icon: Activity },
  { name: 'Customers', href: 'admin.customers.index', icon: Users },
  { name: 'Packages', href: 'admin.packages.index', icon: Package },
  { name: 'Staff', href: 'admin.staff.index', icon: UsersRound },
  { name: 'Inventory', href: 'admin.inventory.index', icon: PackageOpen },
  { name: 'Transactions', href: 'admin.transactions.index', icon: Receipt },
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
          <Link :href="route('admin.dashboard')" class="flex items-center gap-2">
            <ApplicationLogo class="h-8 w-auto" />
            <span class="text-xl font-bold">WashyWashy</span>
          </Link>
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
