<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Card, CardContent, CardHeader, CardTitle, Badge, Button } from '@/Components/ui'
import { Bell, BellOff, CheckCircle, Trash2, CheckCheck } from 'lucide-vue-next'

interface Notification {
  id: number
  type: string
  title: string
  message: string
  is_read: boolean
  created_at: string
  user?: { name: string }
}

const props = defineProps<{
  notifications: Notification[]
  stats: {
    total: number
    unread: number
    read: number
  }
}>()

const markAsRead = (id: number) => {
  router.patch(route('notifications.mark-as-read', id), {}, { preserveScroll: true })
}

const markAllAsRead = () => {
  router.post(route('notifications.mark-all-as-read'), {}, { preserveScroll: true })
}

const deleteNotification = (id: number) => {
  if (confirm('Delete this notification?')) {
    router.delete(route('notifications.destroy', id))
  }
}

const getTypeColor = (type: string) => {
  switch (type) {
    case 'success': return 'bg-success/10 text-success'
    case 'warning': return 'bg-warning/10 text-warning'
    case 'error': return 'bg-destructive/10 text-destructive'
    default: return 'bg-primary/10 text-primary'
  }
}

const formatDate = (date: string) => {
  return new Date(date).toLocaleString()
}
</script>

<template>
  <Head title="Notifications" />
  <AuthenticatedLayout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">Notifications</h2>
    </template>
    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="space-y-6 animate-fade-in">
          <!-- Header -->
          <div class="flex items-center justify-between">
            <div>
              <h1 class="text-2xl font-bold mb-2">Notifications</h1>
              <p class="text-muted-foreground">Stay updated with system alerts</p>
            </div>
            <Button v-if="stats.unread > 0" class="btn-primary" @click="markAllAsRead">
              <CheckCheck class="w-4 h-4 mr-2" />
              Mark All as Read
            </Button>
          </div>

          <!-- Stats Grid -->
          <div class="grid gap-4 md:grid-cols-3">
            <Card class="stat-card">
              <CardContent class="p-6">
                <div class="flex items-center justify-between">
                  <div>
                    <p class="text-sm text-muted-foreground">Total</p>
                    <p class="text-3xl font-bold mt-1">{{ stats.total }}</p>
                  </div>
                  <Bell class="w-8 h-8 text-primary" />
                </div>
              </CardContent>
            </Card>
            <Card class="stat-card">
              <CardContent class="p-6">
                <div class="flex items-center justify-between">
                  <div>
                    <p class="text-sm text-muted-foreground">Unread</p>
                    <p class="text-3xl font-bold mt-1 text-warning">{{ stats.unread }}</p>
                  </div>
                  <BellOff class="w-8 h-8 text-warning" />
                </div>
              </CardContent>
            </Card>
            <Card class="stat-card">
              <CardContent class="p-6">
                <div class="flex items-center justify-between">
                  <div>
                    <p class="text-sm text-muted-foreground">Read</p>
                    <p class="text-3xl font-bold mt-1 text-success">{{ stats.read }}</p>
                  </div>
                  <CheckCircle class="w-8 h-8 text-success" />
                </div>
              </CardContent>
            </Card>
          </div>

          <div class="space-y-3">
            <div
              v-for="notification in notifications"
              :key="notification.id"
              :class="[
                'p-4 rounded-lg border transition-colors',
                notification.is_read ? 'bg-muted/30' : 'bg-card border-primary/20'
              ]"
            >
              <div class="flex items-start justify-between gap-4">
                <div class="flex-1">
                  <div class="flex items-center gap-2 mb-1">
                    <div :class="['w-2 h-2 rounded-full', getTypeColor(notification.type)]"></div>
                    <h3 class="font-semibold">{{ notification.title }}</h3>
                    {!notification.is_read && <Badge variant="default" class="text-xs">New</Badge>}
                  </div>
                  <p class="text-sm text-muted-foreground mb-2">{{ notification.message }}</p>
                  <p class="text-xs text-muted-foreground">{{ formatDate(notification.created_at) }}</p>
                </div>
                <div class="flex gap-2">
                  <Button
                    v-if="!notification.is_read"
                    size="sm"
                    variant="outline"
                    @click="markAsRead(notification.id)"
                  >
                    <CheckCircle class="w-4 h-4" />
                  </Button>
                  <Button
                    size="sm"
                    variant="outline"
                    @click="deleteNotification(notification.id)"
                  >
                    <Trash2 class="w-4 h-4" />
                  </Button>
                </div>
              </div>
            </div>

            <div v-if="notifications.length === 0" class="text-center py-12">
              <Bell class="w-12 h-12 text-muted-foreground mx-auto mb-4" />
              <h3 class="text-lg font-semibold mb-2">No notifications</h3>
              <p class="text-muted-foreground">You're all caught up!</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
