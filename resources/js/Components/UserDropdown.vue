<script setup lang="ts">
import { computed } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import { User, LogOut, ChevronDown } from 'lucide-vue-next'
import Dropdown from '@/Components/Dropdown.vue'
import DropdownLink from '@/Components/DropdownLink.vue'

const page = usePage()
const user = computed(() => page.props.auth.user)

const userRole = computed(() => {
  const role = user.value?.role
  if (!role) return ''
  return role.charAt(0).toUpperCase() + role.slice(1)
})

const branchName = computed(() => user.value?.branch?.name)
</script>

<template>
  <Dropdown align="right" width="48">
    <template #trigger>
      <button
        type="button"
        class="flex items-center gap-2 p-2 rounded-lg hover:bg-muted transition-colors"
      >
        <div class="flex items-center justify-center w-8 h-8 rounded-full bg-primary/10">
          <User class="w-4 h-4 text-primary" />
        </div>
        <div class="hidden md:flex flex-col items-start min-w-0 text-left">
          <span class="text-sm font-medium truncate max-w-[120px]">
            {{ user.name }}
          </span>
          <span class="text-xs text-muted-foreground truncate max-w-[120px]">
            {{ userRole }}{{ branchName ? ` • ${branchName}` : '' }}
          </span>
        </div>
        <ChevronDown class="w-4 h-4 text-muted-foreground" />
      </button>
    </template>

    <template #content>
      <div class="px-4 py-2 border-b md:hidden">
        <p class="text-sm font-medium truncate">{{ user.name }}</p>
        <p class="text-xs text-muted-foreground truncate">{{ user.email }}</p>
      </div>
      
      <DropdownLink :href="route('profile.edit')" class="flex items-center gap-2">
        <User class="w-4 h-4" />
        Profile
      </DropdownLink>

      <DropdownLink
        :href="route('logout')"
        method="post"
        as="button"
        class="flex items-center gap-2 text-destructive hover:text-destructive hover:bg-destructive/10"
      >
        <LogOut class="w-4 h-4" />
        Log Out
      </DropdownLink>
    </template>
  </Dropdown>
</template>
