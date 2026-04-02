<script setup lang="ts">
import { ref } from 'vue'

import type { ComponentGroupRecord, ComponentRecord, PublicationStatus } from '@/types/models'

const props = defineProps<{
  model: Pick<ComponentRecord, 'name' | 'description'> & {
    groups: Array<ComponentGroupRecord | string>
    publicationStatus: PublicationStatus
  }
  groupOptions?: ComponentGroupRecord[]
  publicationStatusOptions?: { title: string; value: PublicationStatus }[]
}>()

const groupSearch = ref('')

function normalizeGroups(groups: Array<ComponentGroupRecord | string>) {
  const seen = new Set<string>()

  return groups.reduce<Array<ComponentGroupRecord | string>>((result, group) => {
    if (typeof group === 'string') {
      const name = group.trim()

      if (! name) {
        return result
      }

      const key = `name:${name.toLowerCase()}`
      if (seen.has(key)) {
        return result
      }

      seen.add(key)
      result.push(name)
      return result
    }

    const key = group.id ? `id:${group.id}` : `name:${group.name.toLowerCase()}`
    if (seen.has(key)) {
      return result
    }

    seen.add(key)
    result.push(group)
    return result
  }, [])
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
  <v-select
    v-model="model.publicationStatus"
    hide-details
    :items="publicationStatusOptions ?? []"
    item-title="title"
    item-value="value"
    label="Status"
    variant="outlined"
  />
  <v-combobox
    v-model="model.groups"
    v-model:search="groupSearch"
    chips
    closable-chips
    hide-details
    :items="groupOptions ?? []"
    item-title="name"
    label="Groups"
    multiple
    variant="outlined"
    @blur="commitGroupSearch"
    @keydown.enter.prevent="commitGroupSearch"
    @update:model-value="model.groups = normalizeGroups($event as Array<ComponentGroupRecord | string>)"
  />
</template>
