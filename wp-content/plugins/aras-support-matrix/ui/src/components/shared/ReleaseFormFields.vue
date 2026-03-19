<script setup lang="ts">
import DatePickerField from '@/components/shared/DatePickerField.vue'
import type { PublicationStatus, ReleaseRecord } from '@/types/models'

defineProps<{
  model: {
    name: string
    buildNumber: string
    releaseDate: string
    endOfLifeDate: string
    notes: string
    publicationStatus: PublicationStatus
    copyFromReleaseId?: number | null
  }
  orderedReleases: ReleaseRecord[]
  publicationStatusOptions: { title: string; value: PublicationStatus }[]
  showCopyFrom?: boolean
}>()
</script>

<template>
  <v-text-field v-model="model.name" hide-details label="Name" variant="outlined" />
  <v-text-field v-model="model.buildNumber" hide-details label="Build Number" variant="outlined" />
  <DatePickerField v-model="model.releaseDate" label="Release Date" />
  <DatePickerField v-model="model.endOfLifeDate" label="End of Life Date" />
  <v-textarea v-model="model.notes" hide-details class="inline-form-span" label="Notes" rows="2" variant="outlined" />
  <v-select
    v-if="showCopyFrom"
    v-model="model.copyFromReleaseId"
    clearable
    hide-details
    :items="orderedReleases"
    item-title="name"
    item-value="id"
    label="Copy Entries From"
    variant="outlined"
  />
  <v-select
    v-model="model.publicationStatus"
    hide-details
    :items="publicationStatusOptions"
    item-title="title"
    item-value="value"
    label="Status"
    variant="outlined"
  />
</template>

<style scoped>
.inline-form-span {
  grid-column: 1 / -1;
}
</style>
