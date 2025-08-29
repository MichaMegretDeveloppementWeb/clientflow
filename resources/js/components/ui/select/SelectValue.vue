<template>
  <SelectValue
    v-bind="$attrs"
    :class="cn('text-sm', props.class)"
  />
</template>

<script setup lang="ts">
import { SelectValue } from 'reka-ui'
import { cn } from '@/lib/utils'

interface Props {
  class?: string
}

const props = defineProps<Props>()
</script>