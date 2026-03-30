<script setup lang="ts">
import type { ComponentRecord, EntryRecord } from '@/types/models'

defineProps<{
  groups: Array<{
    component: ComponentRecord
    versions: EntryRecord[]
  }>
}>()

function statusColor(status: EntryRecord['status']) {
  if (status === 'Certified') return '#0F66CB'
  if (status === 'Supported') return '#1F9B42'
  return 'error'
}
</script>

<template>
  <div class="result-grid">
    <v-card
      v-for="group in groups"
      :key="group.component.id"
      class="result-card"
      variant="outlined"
    >
      <v-card-item class="component-card-header">
        <v-card-title :title="group.component.name">{{ group.component.name }}</v-card-title>
        <v-card-subtitle :title="group.component.description">{{ group.component.description }}</v-card-subtitle>
      </v-card-item>
      <v-divider class="component-card-divider" />
      <v-list>
        <v-list-item v-for="entry in group.versions" :key="entry.id">
          <div class="entry-header-row">
            <v-list-item-title class="entry-title" :title="entry.componentVersionNumber">
              {{ entry.componentVersionNumber || 'No Release Specified' }}
              <span class="entry-build">
                {{ entry.componentReleaseNumber ? `(Build ${entry.componentReleaseNumber})` : '' }}
              </span>
            </v-list-item-title>
            <v-chip :color="statusColor(entry.status)" size="small" variant="tonal">
              {{ entry.status }}
            </v-chip>
          </div>
          <v-list-item-subtitle v-if="entry.notes">
            <div class="entry-note-row">
              <span :title="entry.notes">{{ entry.notes }}</span>
            </div>
          </v-list-item-subtitle>
        </v-list-item>
      </v-list>
    </v-card>
  </div>
</template>

<style scoped>
.result-grid {
  display: grid;
  gap: 20px;
  grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
}

.result-card {
  background: rgba(255, 255, 255, 0.88);
  border-color: #E0E0E0;
}

.component-card-header {
  background: #F3F3F3;
}

.component-card-divider {
  opacity: 0.7;
}

:deep(.component-card-header .v-card-title) {
  font-size: 1.05rem;
  line-height: 1.3;
  color: rgb(var(--v-theme-secondary));
}

:deep(.component-card-header .v-card-subtitle) {
  font-size: 0.82rem;
}

.entry-header-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 16px;
  margin-bottom: 6px;
}

.entry-title {
  flex: 1 1 auto;
  font-size: 0.95rem;
  line-height: 1.3;
}

.entry-build {
  font-size: 0.78rem;
  opacity: 0.7;
  
}

.entry-meta-row {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  gap: 16px;
}

.entry-meta-left,
.entry-meta-right {
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  gap: 8px;
}

.entry-meta-right {
  justify-content: flex-end;
}

.entry-note-row {
  display: block;
  width: 100%;
  margin-top: 6px;
  font-size: 0.82rem;
  line-height: 1.45;
  white-space: normal;
  color: #4d6179;
}

</style>
