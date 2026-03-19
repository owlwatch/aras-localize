<script setup lang="ts">
import { computed, reactive, ref, watch } from 'vue'

import ReleaseFormFields from '@/components/shared/ReleaseFormFields.vue'
import { api } from '@/composables/api'
import type { EntryRecord, PublicationStatus, ReleaseRecord } from '@/types/models'

type SortDirection = 'asc' | 'desc'
type ReleaseSortKey = 'name' | 'buildNumber' | 'releaseDate' | 'endOfLifeDate' | 'notes' | 'publicationStatus'

interface ReleaseFormState extends ReleaseRecord {
  copyFromReleaseId: number | null
}

const props = defineProps<{
  entries: EntryRecord[]
  releases: ReleaseRecord[]
  showIdColumns: boolean
}>()

const emit = defineEmits<{
  'request:refresh': []
  'update:entries': [value: EntryRecord[]]
  'update:releases': [value: ReleaseRecord[]]
}>()

const loading = ref(false)
const editingId = ref<number | null>(null)
const showNewDialog = ref(false)
const confirmDeleteOpen = ref(false)
const confirmDeleteTitle = ref('')
const confirmDeleteMessage = ref('')
const confirmDeleteAction = ref<null | (() => Promise<void>)>(null)

const sortState = reactive<{ key: ReleaseSortKey; direction: SortDirection }>({
  key: 'buildNumber',
  direction: 'desc',
})

const releasesState = ref<ReleaseRecord[]>([])
const entriesState = ref<EntryRecord[]>([])

const form = reactive<ReleaseFormState>({
  id: 0,
  name: '',
  buildNumber: '',
  releaseDate: '',
  endOfLifeDate: '',
  notes: '',
  publicationStatus: 'draft',
  copyFromReleaseId: null,
})

const publicationStatusOptions: { title: string; value: PublicationStatus }[] = [
  { title: 'Draft', value: 'draft' },
  { title: 'Published', value: 'publish' },
]

watch(() => props.releases, (value) => { releasesState.value = [...value] }, { immediate: true })
watch(() => props.entries, (value) => { entriesState.value = [...value] }, { immediate: true })

const orderedReleases = computed(() => {
  return [...releasesState.value].sort((left, right) => {
    const leftDate = left.releaseDate || left.name
    const rightDate = right.releaseDate || right.name
    return rightDate.localeCompare(leftDate)
  })
})

const sortedReleases = computed(() => {
  return [...releasesState.value].sort((left, right) => {
    const result = String(left[sortState.key] ?? '').localeCompare(String(right[sortState.key] ?? ''), undefined, {
      numeric: true,
      sensitivity: 'base',
    })
    return sortState.direction === 'asc' ? result : -result
  })
})

const latestReleaseId = computed<number | null>(() => orderedReleases.value[0]?.id ?? null)
const columnCount = computed(() => (props.showIdColumns ? 8 : 7))

function sortIndicator(active: boolean, direction: SortDirection) {
  if (!active) return ''
  return direction === 'asc' ? ' mdi-chevron-up' : ' mdi-chevron-down'
}

function syncReleases(next: ReleaseRecord[]) {
  releasesState.value = next
  emit('update:releases', next)
}

function syncEntries(next: EntryRecord[]) {
  entriesState.value = next
  emit('update:entries', next)
}

function resetForm() {
  Object.assign(form, {
    id: 0,
    name: '',
    buildNumber: '',
    releaseDate: '',
    endOfLifeDate: '',
    notes: '',
    publicationStatus: 'draft',
    copyFromReleaseId: latestReleaseId.value,
  })
}

function cancelEdit() {
  editingId.value = null
  showNewDialog.value = false
  resetForm()
}

function openNew() {
  cancelEdit()
  showNewDialog.value = true
}

function editItem(item: ReleaseRecord) {
  Object.assign(form, { ...item, copyFromReleaseId: null })
  editingId.value = item.id
}

function toggleSort(key: ReleaseSortKey) {
  if (sortState.key === key) {
    sortState.direction = sortState.direction === 'asc' ? 'desc' : 'asc'
    return
  }

  sortState.key = key
  sortState.direction = 'asc'
}

async function runWithLoading<T>(action: () => Promise<T>) {
  loading.value = true
  try {
    return await action()
  } finally {
    loading.value = false
  }
}

function upsertById<T extends { id: number }>(items: T[], item: T) {
  const index = items.findIndex((current) => current.id === item.id)
  if (index === -1) {
    items.unshift(item)
    return items
  }

  items.splice(index, 1, item)
  return items
}

async function submit() {
  const payload = {
    name: form.name,
    buildNumber: form.buildNumber,
    releaseDate: form.releaseDate,
    endOfLifeDate: form.endOfLifeDate,
    notes: form.notes,
    publicationStatus: form.publicationStatus,
    copyFromReleaseId: form.id ? null : form.copyFromReleaseId,
  }

  const saved = await runWithLoading(() =>
    form.id ? api.updateRelease({ ...form }) : api.createRelease(payload),
  )

  const nextReleases = [...releasesState.value]
  upsertById(nextReleases, saved)
  syncReleases(nextReleases)
  syncEntries(
    entriesState.value.map((entry) => {
      if (entry.innovatorReleaseId !== saved.id) {
        return entry
      }

      return {
        ...entry,
        releaseName: saved.name,
        publicationStatus: saved.publicationStatus,
      }
    }),
  )

  if (!form.id && form.copyFromReleaseId) {
    emit('request:refresh')
  }

  cancelEdit()
}

function openDeleteDialog(title: string, message: string, action: () => Promise<void>) {
  confirmDeleteTitle.value = title
  confirmDeleteMessage.value = message
  confirmDeleteAction.value = action
  confirmDeleteOpen.value = true
}

function closeDeleteDialog() {
  confirmDeleteOpen.value = false
  confirmDeleteTitle.value = ''
  confirmDeleteMessage.value = ''
  confirmDeleteAction.value = null
}

async function confirmDelete() {
  if (!confirmDeleteAction.value) return
  try {
    await confirmDeleteAction.value()
  } finally {
    closeDeleteDialog()
  }
}

function removeItem(item: ReleaseRecord) {
  openDeleteDialog(
    'Delete Release',
    `Delete "${item.name}" and all corresponding entries?`,
    async () => {
      await runWithLoading(() => api.deleteRelease(item.id))
      syncReleases(releasesState.value.filter((release) => release.id !== item.id))
      syncEntries(entriesState.value.filter((entry) => entry.innovatorReleaseId !== item.id))
    },
  )
}
</script>

<template>
  <div class="button-row">
    <v-btn color="primary" @click="openNew">Add Release</v-btn>
  </div>

  <v-table>
    <thead>
      <tr>
        <th v-if="showIdColumns">ID</th>
        <th><button class="sort-button" type="button" @click="toggleSort('name')"><span>Name</span><v-icon v-if="sortState.key === 'name'" class="sort-icon" :icon="sortIndicator(true, sortState.direction)" size="16" /></button></th>
        <th><button class="sort-button" type="button" @click="toggleSort('buildNumber')"><span>Build</span><v-icon v-if="sortState.key === 'buildNumber'" class="sort-icon" :icon="sortIndicator(true, sortState.direction)" size="16" /></button></th>
        <th><button class="sort-button" type="button" @click="toggleSort('releaseDate')"><span>Release Date</span><v-icon v-if="sortState.key === 'releaseDate'" class="sort-icon" :icon="sortIndicator(true, sortState.direction)" size="16" /></button></th>
        <th><button class="sort-button" type="button" @click="toggleSort('endOfLifeDate')"><span>EOL</span><v-icon v-if="sortState.key === 'endOfLifeDate'" class="sort-icon" :icon="sortIndicator(true, sortState.direction)" size="16" /></button></th>
        <th><button class="sort-button" type="button" @click="toggleSort('notes')"><span>Notes</span><v-icon v-if="sortState.key === 'notes'" class="sort-icon" :icon="sortIndicator(true, sortState.direction)" size="16" /></button></th>
        <th><button class="sort-button" type="button" @click="toggleSort('publicationStatus')"><span>Status</span><v-icon v-if="sortState.key === 'publicationStatus'" class="sort-icon" :icon="sortIndicator(true, sortState.direction)" size="16" /></button></th>
        <th></th>
      </tr>
    </thead>
    <tbody>
      <template v-for="item in sortedReleases" :key="item.id">
        <tr v-if="editingId === item.id">
          <td :colspan="columnCount">
            <div class="inline-form-grid inline-form-grid--two">
              <ReleaseFormFields
                :model="form"
                :ordered-releases="orderedReleases"
                :publication-status-options="publicationStatusOptions"
              />
              <div class="button-row inline-form-actions">
                <v-btn :loading="loading" color="primary" @click="submit">Save</v-btn>
                <v-btn variant="text" @click="cancelEdit">Cancel</v-btn>
              </div>
            </div>
          </td>
        </tr>
        <tr v-else>
          <td v-if="showIdColumns">{{ item.id }}</td>
          <td><button class="admin-link-button" type="button" @click="editItem(item)">{{ item.name }}</button></td>
          <td>{{ item.buildNumber }}</td>
          <td>{{ item.releaseDate }}</td>
          <td>{{ item.endOfLifeDate }}</td>
          <td>{{ item.notes || '—' }}</td>
          <td>{{ item.publicationStatus === 'publish' ? 'Published' : 'Draft' }}</td>
          <td class="actions-cell">
            <v-btn size="small" variant="text" @click="editItem(item)">Edit</v-btn>
            <v-btn size="small" variant="text" color="error" @click="removeItem(item)">Delete</v-btn>
          </td>
        </tr>
      </template>
    </tbody>
  </v-table>

  <v-dialog v-model="showNewDialog" max-width="860">
    <v-card>
      <v-card-title>Add Release</v-card-title>
      <v-card-text class="inline-form-grid inline-form-grid--two">
        <ReleaseFormFields
          :model="form"
          :ordered-releases="orderedReleases"
          :publication-status-options="publicationStatusOptions"
          show-copy-from
        />
      </v-card-text>
      <v-card-actions>
        <v-spacer />
        <v-btn variant="text" @click="cancelEdit">Cancel</v-btn>
        <v-btn :loading="loading" color="primary" @click="submit">Save</v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>

  <v-dialog v-model="confirmDeleteOpen" max-width="520">
    <v-card>
      <v-card-title>{{ confirmDeleteTitle }}</v-card-title>
      <v-card-text>{{ confirmDeleteMessage }}</v-card-text>
      <v-card-actions>
        <v-spacer />
        <v-btn variant="text" @click="closeDeleteDialog">Cancel</v-btn>
        <v-btn :loading="loading" color="error" @click="confirmDelete">Delete</v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<style scoped>
.button-row {
  display: flex;
  gap: 12px;
  align-items: center;
  flex-wrap: wrap;
}

.actions-cell {
  white-space: nowrap;
  text-align: right;
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

.inline-form-grid--two {
  grid-template-columns: repeat(2, minmax(0, 1fr));
}

.inline-form-actions {
  grid-column: 1 / -1;
}

@media (max-width: 960px) {
  .inline-form-grid--two {
    grid-template-columns: 1fr;
  }
}
</style>
