<script setup lang="ts">
import type { ComponentRecord, ReleaseRecord } from '@/types/models'

defineProps<{
  componentFilter: number | null
  releaseFilter: number | null
  components: ComponentRecord[]
  releases: ReleaseRecord[]
}>()

const emit = defineEmits<{
  'update:componentFilter': [value: number | null]
  'update:releaseFilter': [value: number | null]
}>()

function formatReleaseTitle(release: ReleaseRecord) {
  return release.publicationStatus === 'draft' ? `${release.name} (Draft)` : release.name
}
</script>

<template>
  <div class="entry-filters">
    <v-select
      :model-value="componentFilter"
      clearable
      density="compact"
      hide-details
      class="entry-filter"
      item-title="name"
      item-value="id"
      :items="components"
      label="Filter By Component"
      variant="outlined"
      @update:model-value="emit('update:componentFilter', $event)"
    />
    <v-select
      :model-value="releaseFilter"
      clearable
      density="compact"
      hide-details
      class="entry-filter"
      item-value="id"
      :items="releases"
      label="Filter By Release"
      variant="outlined"
      @update:model-value="emit('update:releaseFilter', $event)"
    >
      <template #selection="{ item }">
        <span>{{ formatReleaseTitle(item.raw) }}</span>
      </template>
      <template #item="{ props: itemProps, item }">
        <v-list-item
          v-bind="itemProps"
          :title="formatReleaseTitle(item.raw)"
        />
      </template>
    </v-select>
  </div>
</template>

<style scoped>
.entry-filters {
  display: flex;
  gap: 12px;
  flex-wrap: wrap;
  justify-content: flex-end;
  flex: 1 1 0;
  margin-left: auto;
}

.entry-filter {
  width: 100%;
  min-width: 260px;
  max-width: 360px;
}

.entry-filter :deep(.v-field) {
  background: #ffffff;
}

.entry-filter :deep(.v-field__input) {
  min-height: 40px;
}
</style>
