<script setup lang="ts">
import { Table, TableHeader, TableBody, TableRow, TableHead, TableCell } from '@/Components/ui'
import Skeleton from './Skeleton.vue'

defineProps<{
  columns?: number
  rows?: number
}>()
</script>

<template>
  <div class="rounded-md border">
    <Table>
      <TableHeader>
        <TableRow>
          <TableHead v-for="i in (columns || 4)" :key="i">
            <Skeleton class="h-4 w-24" />
          </TableHead>
        </TableRow>
      </TableHeader>
      <TableBody>
        <TableRow v-for="row in (rows || 5)" :key="row">
          <TableCell v-for="col in (columns || 4)" :key="col">
            <Skeleton class="h-4 w-full" />
          </TableCell>
        </TableRow>
      </TableBody>
    </Table>
  </div>
</template>
