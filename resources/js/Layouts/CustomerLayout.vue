<script setup lang="ts">
import { ref, computed } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import ApplicationLogo from '@/Components/ApplicationLogo.vue'
import {
  LayoutDashboard,
  Calendar,
  History,
  Star,
  Bell,
  Menu,
  X,
  LogOut,
  User,
} from 'lucide-vue-next'
import UserDropdown from '@/Components/UserDropdown.vue'

const sidebarOpen = ref(false)
const page = usePage()

const user = computed(() => page.props.auth.user)
const loyaltyPoints = computed(() => user.value?.loyalty_points || 0)
const loyaltyTier = computed(() => user.value?.loyalty_tier || 'bronze')

const navigation = [
  { name: 'Dashboard', href: 'customer.dashboard', icon: LayoutDashboard },
  { name: 'My Appointments', href: 'customer.appointments.index', icon: Calendar },
  { name: 'Wash History', href: 'customer.history', icon: History },
  { name: 'Loyalty & Rewards', href: 'customer.loyalty', icon: Star },
  { name: 'Notifications', href: 'notifications.index', icon: Bell },
]

const isActive = (routeName: string) => {
  return route().current(routeName) || route().current(`${routeName}.*`)
}

const tierColors: Record<string, string> = {
  bronze: 'text-amber-600',
  silver: 'text-slate-400',
  gold: 'text-yellow-500',
  platinum: 'text-purple-500',
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
          <Link :href="route('customer.dashboard')" class="flex items-center gap-2">
            <ApplicationLogo class="h-8 w-auto" />
            <span class="text-xl font-bold">WashyWashy</span>
          </Link>
        </div>

        <!-- Loyalty Points Card -->
        <div class="px-4 py-4 border-b">
          <div class="bg-gradient-to-br from-primary/10 to-primary/5 rounded-lg p-4">
            <div class="flex items-center justify-between mb-2">
              <span class="text-xs font-medium text-muted-foreground uppercase">Loyalty Points</span>
              <Star :class="['w-4 h-4', tierColors[loyaltyTier]]" />
            </div>
            <div class="text-2xl font-bold">{{ loyaltyPoints.toLocaleString() }}</div>
            <div class="text-xs text-muted-foreground mt-1 capitalize">
              {{ loyaltyTier }} Member
            </div>
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
