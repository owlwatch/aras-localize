<script setup lang="ts">
import NoteReferenceFields from '@/components/shared/NoteReferenceFields.vue'
import type { ComponentRecord, NoteRecord, NoteType, PublicationStatus, ReleaseRecord, SupportStatus } from '@/types/models'

defineProps<{
  model: {
    componentId: number | null
    innovatorReleaseId: number | null
    publicationStatus: PublicationStatus
    componentVersionNumber: string
    componentReleaseNumber: string
    status: SupportStatus
    noteId: number | null
    notes: string
    newNoteTitle: string
    newNoteContent: string
    newNoteType: NoteType
  }
  components: ComponentRecord[]
  notes: NoteRecord[]
  releases: ReleaseRecord[]
  statusOptions: SupportStatus[]
  publicationStatusOptions: Array<{ title: string; value: PublicationStatus }>
  componentError?: string
  publicationStatusError?: string
  releaseError?: string
  versionError?: string
  releaseNumberError?: string
  statusError?: string
}>()
</script>

<template>
  <v-select
    v-model="model.componentId"
    class="entry-field"
    density="comfortable"
    :error-messages="componentError ? [componentError] : []"
    item-title="name"
    item-value="id"
    :items="components"
    label="Component"
    persistent-placeholder
    placeholder="Please Select Component"
    variant="outlined"
  />
  <v-select
    v-model="model.publicationStatus"
    class="entry-field"
    density="comfortable"
    :error-messages="publicationStatusError ? [publicationStatusError] : []"
    :items="publicationStatusOptions"
    label="Status"
    variant="outlined"
  />
  <v-select
    v-model="model.innovatorReleaseId"
    class="entry-field"
    density="comfortable"
    :error-messages="releaseError ? [releaseError] : []"
    item-title="name"
    item-value="id"
    :items="releases"
    label="Aras Innovator Version"
    variant="outlined"
  />
  <v-text-field
    v-model="model.componentVersionNumber"
    class="entry-field"
    density="comfortable"
    :error-messages="versionError ? [versionError] : []"
    label="Release"
    variant="outlined"
  />
  <v-text-field
    v-model="model.componentReleaseNumber"
    class="entry-field"
    density="comfortable"
    :error-messages="releaseNumberError ? [releaseNumberError] : []"
    label="Build"
    variant="outlined"
  />
  <v-select
    v-model="model.status"
    class="entry-field"
    density="comfortable"
    :error-messages="statusError ? [statusError] : []"
    :items="statusOptions"
    label="Support Status"
    variant="outlined"
  />
  <div class="inline-form-span">
    <NoteReferenceFields :model="model" :notes="notes" />
  </div>
</template>

<style scoped>
.entry-field :deep(.v-field) {
  min-height: 56px;
}

.entry-field :deep(.v-field__input) {
  min-height: 56px;
  align-items: center;
}

.entry-field :deep(.v-input--horizontal) {
  align-items: flex-start;
}

.inline-form-span {
  grid-column: 1 / -1;
}
</style>
