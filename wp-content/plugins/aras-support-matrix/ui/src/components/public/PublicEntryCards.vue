<script setup lang="ts">
import type { ComponentRecord, EntryRecord } from '@/types/models'

defineProps<{
  groups: Array<{
    component: ComponentRecord
    versions: EntryRecord[]
  }>
}>()

function statusColor(status: EntryRecord['status']) {
  if (status === 'Certified') return 'success'
  if (status === 'Supported') return 'info'
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
      <v-card-item>
        <v-card-title>{{ group.component.name }}</v-card-title>
        <v-card-subtitle>{{ group.component.description }}</v-card-subtitle>
      </v-card-item>
      <v-divider />
      <v-list>
        <v-list-item v-for="entry in group.versions" :key="entry.id">
          <v-list-item-title>{{ entry.componentVersionNumber }}</v-list-item-title>
          <v-list-item-subtitle>
            <div class="entry-meta-row">
              <div class="entry-meta-left">
                <span>Build {{ entry.componentVersionNumber || 'Not specified' }}</span>
              </div>
              <div class="entry-meta-right">
                <v-chip :color="statusColor(entry.status)" size="small" variant="tonal">
                  {{ entry.status }}
                </v-chip>
              </div>
            </div>
            <div v-if="entry.notes" class="entry-detail-row">
              <span v-if="entry.notes">{{ entry.notes }}</span>
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
}

.entry-meta-row {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  gap: 16px;
}

.entry-meta-left,
.entry-meta-right,
.entry-detail-row {
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  gap: 8px;
}

.entry-meta-right {
  justify-content: flex-end;
}

.entry-detail-row {
  margin-top: 6px;
}

</style>
