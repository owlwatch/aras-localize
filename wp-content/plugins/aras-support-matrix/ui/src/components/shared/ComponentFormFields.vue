<script setup lang="ts">
import { ref } from 'vue'

import type { ComponentRecord } from '@/types/models'

const props = defineProps<{
  model: Pick<ComponentRecord, 'name' | 'description' | 'groups'>
  groupOptions?: string[]
}>()

const groupSearch = ref('')

function normalizeGroups(groups: string[]) {
  return Array.from(
    new Set(
      groups
        .map((group) => group.trim())
        .filter(Boolean),
    ),
  )
}

function commitGroupSearch() {
  const value = groupSearch.value.trim()

  if (! value) {
    return
  }

  props.model.groups = normalizeGroups([...props.model.groups, value])
  groupSearch.value = ''
}
</script>

<template>
  <v-text-field v-model="model.name" hide-details label="Name" variant="outlined" />
  <v-textarea v-model="model.description" hide-details label="Description" rows="2" variant="outlined" />
  <v-combobox
    v-model="model.groups"
    v-model:search="groupSearch"
    chips
    closable-chips
    hide-details
    :items="groupOptions ?? []"
    label="Groups"
    multiple
    variant="outlined"
    @blur="commitGroupSearch"
    @keydown.enter.prevent="commitGroupSearch"
    @update:model-value="model.groups = normalizeGroups($event as string[])"
  />
</template>
