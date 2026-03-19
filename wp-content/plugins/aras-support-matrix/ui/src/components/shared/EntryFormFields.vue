<script setup lang="ts">
import type { ComponentRecord, ReleaseRecord, SupportStatus } from '@/types/models'

defineProps<{
  model: {
    componentId: number | null
    innovatorReleaseId: number | null
    componentVersionNumber: string
    componentReleaseNumber: string
    status: SupportStatus
    notes: string
  }
  components: ComponentRecord[]
  releases: ReleaseRecord[]
  statusOptions: SupportStatus[]
  componentError?: string
  releaseError?: string
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
    v-model="model.innovatorReleaseId"
    class="entry-field"
    density="comfortable"
    :error-messages="releaseError ? [releaseError] : []"
    item-title="name"
    item-value="id"
    :items="releases"
    label="Release"
    variant="outlined"
  />
  <v-text-field v-model="model.componentVersionNumber" class="entry-field" density="comfortable" hide-details label="Version" variant="outlined" />
  <v-text-field v-model="model.componentReleaseNumber" class="entry-field" density="comfortable" hide-details label="Release Number" variant="outlined" />
  <v-select v-model="model.status" class="entry-field" density="comfortable" hide-details :items="statusOptions" label="Support Status" variant="outlined" />
  <v-textarea v-model="model.notes" hide-details class="inline-form-span" label="Notes" rows="2" variant="outlined" />
</template>

<style scoped>
.entry-field :deep(.v-field) {
  min-height: 56px;
}

.entry-field :deep(.v-field__input) {
  min-height: 56px;
  align-items: center;
}

.inline-form-span {
  grid-column: 1 / -1;
}
</style>
