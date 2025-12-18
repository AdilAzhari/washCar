<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import CustomerLayout from '@/Layouts/CustomerLayout.vue'
import { Star, TrendingUp, History, Gift, Award } from 'lucide-vue-next'

const props = defineProps<{
  loyaltyPoints: {
    points: number
    lifetime_points: number
    tier: string
  }
  transactions: Array<{
    id: number
    type: string
    points: number
    description: string
    created_at: string
  }>
  nextTier: {
    name: string
    threshold: number
    pointsNeeded: number
  } | null
  tierBenefits: {
    current: Array<string>
    next: Array<string>
  }
}>()

const tierInfo = {
  bronze: {
    name: 'Bronze',
    color: 'text-amber-600',
    bgColor: 'bg-amber-50',
    borderColor: 'border-amber-200',
    multiplier: '1x',
  },
  silver: {
    name: 'Silver',
    color: 'text-slate-400',
    bgColor: 'bg-slate-50',
    borderColor: 'border-slate-200',
    multiplier: '1.25x',
  },
  gold: {
    name: 'Gold',
    color: 'text-yellow-500',
    bgColor: 'bg-yellow-50',
    borderColor: 'border-yellow-200',
    multiplier: '1.5x',
  },
  platinum: {
    name: 'Platinum',
    color: 'text-purple-500',
    bgColor: 'bg-purple-50',
    borderColor: 'border-purple-200',
    multiplier: '2x',
  },
}

const currentTierInfo = tierInfo[props.loyaltyPoints.tier as 'bronze' | 'silver' | 'gold' | 'platinum']

const formatDate = (dateString: string) => {
  return new Date(dateString).toLocaleDateString('en-US', {
    month: 'short',
    day: 'numeric',
    year: 'numeric',
    hour: 'numeric',
    minute: '2-digit',
  })
}

const transactionTypeColors: Record<string, string> = {
  earned: 'text-green-600',
  redeemed: 'text-blue-600',
  expired: 'text-red-600',
  adjusted: 'text-gray-600',
}
</script>

<template>
  <Head title="Loyalty & Rewards" />

  <CustomerLayout>
    <div class="p-6 space-y-6">
      <!-- Page Header -->
      <div>
        <h1 class="text-3xl font-bold">Loyalty & Rewards</h1>
        <p class="text-muted-foreground mt-1">Track your points and unlock exclusive benefits</p>
      </div>

      <!-- Current Tier Card -->
      <div :class="['border-2 rounded-xl p-8', currentTierInfo.borderColor, currentTierInfo.bgColor]">
        <div class="flex items-start justify-between mb-6">
          <div>
            <div class="flex items-center gap-2 mb-2">
              <Star :class="['w-6 h-6', currentTierInfo.color]" />
              <h2 :class="['text-2xl font-bold', currentTierInfo.color]">
                {{ currentTierInfo.name }} Member
              </h2>
            </div>
            <p class="text-muted-foreground">Earning at {{ currentTierInfo.multiplier }} rate</p>
          </div>
          <div class="text-right">
            <div class="text-sm text-muted-foreground mb-1">Current Points</div>
            <div class="text-4xl font-bold">{{ loyaltyPoints.points.toLocaleString() }}</div>
          </div>
        </div>

        <!-- Progress to Next Tier -->
        <div v-if="nextTier" class="mt-6">
          <div class="flex items-center justify-between mb-2 text-sm">
            <span class="font-medium">Progress to {{ nextTier.name }}</span>
            <span class="text-muted-foreground">
              {{ loyaltyPoints.lifetime_points.toLocaleString() }} / {{ nextTier.threshold.toLocaleString() }}
            </span>
          </div>
          <div class="w-full bg-white rounded-full h-3 overflow-hidden">
            <div
              class="h-full bg-gradient-to-r from-primary to-primary/70 transition-all"
              :style="{
                width: `${Math.min((loyaltyPoints.lifetime_points / nextTier.threshold) * 100, 100)}%`
              }"
            ></div>
          </div>
          <p class="text-sm text-muted-foreground mt-2">
            {{ nextTier.pointsNeeded.toLocaleString() }} more points needed to reach {{ nextTier.name }}
          </p>
        </div>

        <div v-else class="mt-6">
          <div class="flex items-center gap-2 p-4 bg-white rounded-lg">
            <Award class="w-5 h-5 text-purple-600" />
            <span class="font-semibold text-purple-600">You've reached the highest tier!</span>
          </div>
        </div>
      </div>

      <!-- Stats Grid -->
      <div class="grid gap-4 md:grid-cols-3">
        <div class="bg-card border rounded-lg p-6">
          <div class="flex items-center gap-2 mb-2">
            <Star class="w-5 h-5 text-primary" />
            <span class="text-sm font-medium text-muted-foreground">Total Earned</span>
          </div>
          <div class="text-2xl font-bold">{{ loyaltyPoints.lifetime_points.toLocaleString() }}</div>
          <p class="text-xs text-muted-foreground mt-1">Lifetime points</p>
        </div>

        <div class="bg-card border rounded-lg p-6">
          <div class="flex items-center gap-2 mb-2">
            <Gift class="w-5 h-5 text-blue-600" />
            <span class="text-sm font-medium text-muted-foreground">Available</span>
          </div>
          <div class="text-2xl font-bold">{{ loyaltyPoints.points.toLocaleString() }}</div>
          <p class="text-xs text-muted-foreground mt-1">Ready to redeem</p>
        </div>

        <div class="bg-card border rounded-lg p-6">
          <div class="flex items-center gap-2 mb-2">
            <TrendingUp class="w-5 h-5 text-green-600" />
            <span class="text-sm font-medium text-muted-foreground">Multiplier</span>
          </div>
          <div class="text-2xl font-bold">{{ currentTierInfo.multiplier }}</div>
          <p class="text-xs text-muted-foreground mt-1">Points per dollar</p>
        </div>
      </div>

      <!-- Current Tier Benefits -->
      <div class="grid gap-6 lg:grid-cols-2">
        <div class="bg-card border rounded-lg p-6">
          <h3 class="text-lg font-bold mb-4 flex items-center gap-2">
            <Award class="w-5 h-5" />
            Your Current Benefits
          </h3>
          <ul class="space-y-2">
            <li
              v-for="(benefit, index) in tierBenefits.current"
              :key="index"
              class="flex items-start gap-2 text-sm"
            >
              <Star class="w-4 h-4 text-green-600 flex-shrink-0 mt-0.5" />
              <span>{{ benefit }}</span>
            </li>
          </ul>
        </div>

        <div v-if="nextTier" class="bg-card border rounded-lg p-6">
          <h3 class="text-lg font-bold mb-4 flex items-center gap-2">
            <TrendingUp class="w-5 h-5" />
            Unlock at {{ nextTier.name }}
          </h3>
          <ul class="space-y-2">
            <li
              v-for="(benefit, index) in tierBenefits.next"
              :key="index"
              class="flex items-start gap-2 text-sm text-muted-foreground"
            >
              <Star class="w-4 h-4 flex-shrink-0 mt-0.5" />
              <span>{{ benefit }}</span>
            </li>
          </ul>
        </div>
      </div>

      <!-- Transaction History -->
      <div class="bg-card border rounded-lg">
        <div class="px-6 py-4 border-b">
          <h2 class="text-xl font-bold flex items-center gap-2">
            <History class="w-5 h-5" />
            Points History
          </h2>
        </div>

        <div v-if="transactions.length === 0" class="px-6 py-12 text-center">
          <History class="w-12 h-12 mx-auto text-muted-foreground mb-3" />
          <p class="text-muted-foreground">No transaction history yet</p>
        </div>

        <div v-else class="divide-y">
          <div
            v-for="transaction in transactions"
            :key="transaction.id"
            class="px-6 py-4"
          >
            <div class="flex items-center justify-between">
              <div class="flex-1">
                <div class="font-semibold">{{ transaction.description }}</div>
                <div class="text-sm text-muted-foreground mt-1">
                  {{ formatDate(transaction.created_at) }}
                </div>
              </div>
              <div class="text-right">
                <div
                  :class="['font-bold text-lg', transactionTypeColors[transaction.type]]"
                >
                  {{ transaction.type === 'redeemed' || transaction.type === 'expired' ? '-' : '+' }}{{ Math.abs(transaction.points).toLocaleString() }}
                </div>
                <div class="text-xs text-muted-foreground capitalize">{{ transaction.type }}</div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Redeem Points CTA -->
      <div class="bg-gradient-to-r from-primary to-primary/80 text-primary-foreground rounded-lg p-8 text-center">
        <Gift class="w-12 h-12 mx-auto mb-4" />
        <h3 class="text-2xl font-bold mb-2">Ready to Redeem?</h3>
        <p class="mb-6 opacity-90">Turn your points into rewards and save on your next wash</p>
        <button
          class="px-6 py-3 bg-white text-primary rounded-lg font-semibold hover:bg-white/90 transition-colors"
          disabled
        >
          Coming Soon
        </button>
      </div>
    </div>
  </CustomerLayout>
</template>
