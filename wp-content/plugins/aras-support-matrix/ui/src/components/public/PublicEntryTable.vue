<script setup lang="ts">
import EntryFormFields from '@/components/shared/EntryFormFields.vue'
import type { ComponentRecord, EntryRecord, ReleaseRecord, SupportStatus } from '@/types/models'

defineProps<{
  components: ComponentRecord[]
  editingId: number | null
  entries: EntryRecord[]
  entryForm: {
    componentId: number
    innovatorReleaseId: number
    componentVersionNumber: string
    componentReleaseNumber: string
    status: SupportStatus
    notes: string
  }
  isAdmin: boolean
  loading: boolean
  releases: ReleaseRecord[]
  sortIcon: (column: 'component' | 'version') => string
  statusOptions: SupportStatus[]
}>()

const emit = defineEmits<{
  cancel: []
  edit: [entry: EntryRecord]
  save: []
  sort: [column: 'component' | 'version']
}>()

function statusColor(status: EntryRecord['status']) {
  if (status === 'Certified') return 'success'
  if (status === 'Supported') return 'info'
  return 'error'
}
</script>

<template>
  <v-table class="table-shell">
    <thead>
      <tr>
        <th>
          <button class="sort-button" type="button" @click="emit('sort', 'component')">
            <span>Component</span>
            <v-icon class="sort-icon" :icon="sortIcon('component')" size="16" />
          </button>
        </th>
        <th>
          <button class="sort-button" type="button" @click="emit('sort', 'version')">
            <span>Version</span>
            <v-icon class="sort-icon" :icon="sortIcon('version')" size="16" />
          </button>
        </th>
        <th>Release Number</th>
        <th>Status</th>
        <th>Notes</th>
      </tr>
    </thead>
    <tbody>
      <template v-for="entry in entries" :key="entry.id">
        <tr v-if="isAdmin && editingId === entry.id">
          <td colspan="6">
            <div class="inline-form-grid inline-form-grid--three">
              <EntryFormFields
                :model="entryForm"
                :components="components"
                :releases="releases"
                :status-options="statusOptions"
              />
              <div class="button-row inline-form-actions">
                <v-btn :loading="loading" color="primary" @click="emit('save')">Save</v-btn>
                <v-btn variant="text" @click="emit('cancel')">Cancel</v-btn>
              </div>
            </div>
          </td>
        </tr>
        <tr v-else>
          <td>
            <button
              v-if="isAdmin"
              class="admin-link-button"
              type="button"
              @click="emit('edit', entry)"
            >
              {{ entry.componentName }}
            </button>
            <template v-else>{{ entry.componentName }}</template>
          </td>
          <td>{{ entry.componentVersionNumber }}</td>
          <td>{{ entry.componentReleaseNumber }}</td>
          <td>
            <v-chip :color="statusColor(entry.status)" size="small" variant="tonal">
              {{ entry.status }}
            </v-chip>
          </td>
          <td>{{ entry.notes || '—' }}</td>
        </tr>
      </template>
    </tbody>
  </v-table>
</template>

<style scoped>
.table-shell {
  background: rgba(255, 255, 255, 0.88);
}

.sort-button {
  display: inline-flex;
  align-items: center;
  gap: 4px;
  padding: 0;
  border: 0;
  background: transparent;
  color: inherit;
  font: inherit;
  font-weight: 600;
  cursor: pointer;
}

.sort-icon {
  opacity: 0.7;
}

.admin-link-button {
  padding: 0;
  border: 0;
  background: transparent;
  color: #0c5ff4;
  font: inherit;
  text-align: left;
  cursor: pointer;
}

.admin-link-button:hover {
  text-decoration: underline;
}

.inline-form-grid {
  display: grid;
  gap: 16px;
  padding: 12px 0;
}

.inline-form-grid--three {
  grid-template-columns: repeat(3, minmax(0, 1fr));
}

.inline-form-actions {
  grid-column: 1 / -1;
}

@media (max-width: 960px) {
  .inline-form-grid--three {
    grid-template-columns: 1fr;
  }
}
</style>
