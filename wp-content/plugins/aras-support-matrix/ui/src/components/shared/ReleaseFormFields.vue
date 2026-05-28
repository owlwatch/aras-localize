<script setup lang="ts">
import DatePickerField from '@/components/shared/DatePickerField.vue'
import NoteReferenceFields from '@/components/shared/NoteReferenceFields.vue'
import type { NoteRecord, NoteType, PublicationStatus, ReleaseRecord } from '@/types/models'

defineProps<{
  model: {
    name: string
    buildNumber: string
    releaseDate: string
    endOfLifeDate: string
    noteId: number | null
    notes: string
    newNoteTitle: string
    newNoteContent: string
    newNoteType: NoteType
    publicationStatus: PublicationStatus
    copyFromReleaseId?: number | null
  }
  notes: NoteRecord[]
  orderedReleases: ReleaseRecord[]
  publicationStatusOptions: { title: string; value: PublicationStatus }[]
  showCopyFrom?: boolean
}>()
</script>

<template>
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
    v-if="showCopyFrom"
    v-model="model.publicationStatus"
    hide-details
    :items="publicationStatusOptions"
    item-title="title"
    item-value="value"
    label="Status"
    variant="outlined"
  />
  <v-text-field v-model="model.name" hide-details label="Name" variant="outlined" />
  <v-text-field v-model="model.buildNumber" hide-details label="Build Number" variant="outlined" />
  <DatePickerField v-model="model.releaseDate" label="Release Date" />
  <DatePickerField v-model="model.endOfLifeDate" label="End of Life Date" />
  <div class="inline-form-span">
    <NoteReferenceFields :model="model" :notes="notes" />
  </div>
  <v-select
    v-if="!showCopyFrom"
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
