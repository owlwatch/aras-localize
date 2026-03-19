<script setup lang="ts">
import { computed, reactive, ref, watch } from 'vue'

import AdminEntryFilters from '@/components/admin/AdminEntryFilters.vue'
import EntryFormFields from '@/components/shared/EntryFormFields.vue'
import { api } from '@/composables/api'
import type { ComponentRecord, EntryRecord, ReleaseRecord, StatusRecord, SupportStatus } from '@/types/models'

type SortDirection = 'asc' | 'desc'
type EntrySortKey = 'componentName' | 'releaseName' | 'componentVersionNumber' | 'status'

interface EntryFormState {
  id: number
  componentId: number | null
  componentName: string
  innovatorReleaseId: number | null
  releaseName: string
  componentVersionNumber: string
  componentReleaseNumber: string
  status: SupportStatus
  endOfLifeDate: string
  notes: string
}

const props = defineProps<{
  components: ComponentRecord[]
  entries: EntryRecord[]
  releases: ReleaseRecord[]
  showIdColumns: boolean
  statuses: StatusRecord[]
}>()

const emit = defineEmits<{
  'update:entries': [value: EntryRecord[]]
}>()

const loading = ref(false)
const editingId = ref<number | null>(null)
const showNewDialog = ref(false)
const confirmDeleteOpen = ref(false)
const confirmDeleteTitle = ref('')
const confirmDeleteMessage = ref('')
const confirmDeleteAction = ref<null | (() => Promise<void>)>(null)
const componentFilter = ref<number | null>(null)
const releaseFilter = ref<number | null>(null)
const currentPage = ref(1)
const rowsPerPage = ref(50)
const rowsPerPageOptions = [25, 50, 100, 250]
const formErrors = reactive({
  componentId: '',
  innovatorReleaseId: '',
  componentVersionNumber: '',
  componentReleaseNumber: '',
  status: '',
})

const sortState = reactive<{ key: EntrySortKey; direction: SortDirection }>({
  key: 'componentName',
  direction: 'asc',
})

const entriesState = ref<EntryRecord[]>([])

const form = reactive<EntryFormState>({
  id: 0,
  componentId: null,
  componentName: '',
  innovatorReleaseId: null,
  releaseName: '',
  componentVersionNumber: '',
  componentReleaseNumber: '',
  status: 'Supported',
  endOfLifeDate: '',
  notes: '',
})

watch(() => props.entries, (value) => { entriesState.value = [...value] }, { immediate: true })

const entryStatusOptions = computed(() => props.statuses.map((status) => status.name))
const filteredAndSortedEntries = computed(() => {
  return entriesState.value
    .filter((entry) => !componentFilter.value || entry.componentId === componentFilter.value)
    .filter((entry) => !releaseFilter.value || entry.innovatorReleaseId === releaseFilter.value)
    .slice()
    .sort((left, right) => {
      const result = String(left[sortState.key] ?? '').localeCompare(String(right[sortState.key] ?? ''), undefined, {
        numeric: true,
        sensitivity: 'base',
      })
      return sortState.direction === 'asc' ? result : -result
    })
})

const pageCount = computed(() => {
  return Math.max(1, Math.ceil(filteredAndSortedEntries.value.length / rowsPerPage.value))
})

const paginatedEntries = computed(() => {
  const start = (currentPage.value - 1) * rowsPerPage.value
  return filteredAndSortedEntries.value.slice(start, start + rowsPerPage.value)
})

const orderedReleases = computed(() => {
  return [...props.releases].sort((left, right) => {
    const leftDate = left.releaseDate || left.name
    const rightDate = right.releaseDate || right.name
    return rightDate.localeCompare(leftDate)
  })
})
const latestReleaseId = computed<number | null>(() => orderedReleases.value[0]?.id ?? null)

const columnCount = computed(() => (props.showIdColumns ? 7 : 6))

function sortIndicator(active: boolean, direction: SortDirection) {
  if (!active) return ''
  return direction === 'asc' ? ' mdi-chevron-up' : ' mdi-chevron-down'
}

function syncEntries(next: EntryRecord[]) {
  entriesState.value = next
  emit('update:entries', next)
}

function resetForm() {
  Object.assign(form, {
    id: 0,
    componentId: null,
    componentName: '',
    innovatorReleaseId: latestReleaseId.value,
    releaseName: '',
    componentVersionNumber: '',
    componentReleaseNumber: '',
    status: 'Supported',
    endOfLifeDate: '',
    notes: '',
  })
  clearFormErrors()
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

function editItem(item: EntryRecord) {
  Object.assign(form, item)
  clearFormErrors()
  editingId.value = item.id
}

function clearFormErrors() {
  formErrors.componentId = ''
  formErrors.innovatorReleaseId = ''
  formErrors.componentVersionNumber = ''
  formErrors.componentReleaseNumber = ''
  formErrors.status = ''
}

function validateForm() {
  clearFormErrors()

  if (!form.componentId) {
    formErrors.componentId = 'Please select a component.'
  }

  if (!form.innovatorReleaseId) {
    formErrors.innovatorReleaseId = 'Please select a release.'
  }

  if (!form.componentVersionNumber.trim()) {
    formErrors.componentVersionNumber = 'Please enter a version.'
  }

  if (!form.componentReleaseNumber.trim()) {
    formErrors.componentReleaseNumber = 'Please enter a release number.'
  }

  if (!form.status) {
    formErrors.status = 'Please select a support status.'
  }

  return !formErrors.componentId
    && !formErrors.innovatorReleaseId
    && !formErrors.componentVersionNumber
    && !formErrors.componentReleaseNumber
    && !formErrors.status
}

function toggleSort(key: EntrySortKey) {
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
  if (!validateForm()) {
    return
  }

  const payload = {
    componentId: Number(form.componentId),
    innovatorReleaseId: Number(form.innovatorReleaseId),
    componentVersionNumber: form.componentVersionNumber,
    componentReleaseNumber: form.componentReleaseNumber,
    status: form.status,
    endOfLifeDate: form.endOfLifeDate,
    notes: form.notes,
  }

  const saved = await runWithLoading(() =>
    form.id ? api.updateEntry({ ...form } as EntryRecord) : api.createEntry(payload),
  )

  const nextEntries = [...entriesState.value]
  upsertById(nextEntries, saved)
  syncEntries(nextEntries)
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

function removeItem(item: EntryRecord) {
  openDeleteDialog(
    'Delete Entry',
    `Delete the entry for "${item.componentName}" on "${item.releaseName}"?`,
    async () => {
      await runWithLoading(() => api.deleteEntry(item.id))
      syncEntries(entriesState.value.filter((entry) => entry.id !== item.id))
    },
  )
}

watch([componentFilter, releaseFilter, () => sortState.key, () => sortState.direction], () => {
  currentPage.value = 1
})

watch(rowsPerPage, () => {
  currentPage.value = 1
})

watch(pageCount, (value) => {
  if (currentPage.value > value) {
    currentPage.value = value
  }
})

watch(() => form.componentId, () => {
  if (form.componentId) {
    formErrors.componentId = ''
  }
})

watch(() => form.innovatorReleaseId, () => {
  if (form.innovatorReleaseId) {
    formErrors.innovatorReleaseId = ''
  }
})

watch(() => form.componentVersionNumber, () => {
  if (form.componentVersionNumber.trim()) {
    formErrors.componentVersionNumber = ''
  }
})

watch(() => form.componentReleaseNumber, () => {
  if (form.componentReleaseNumber.trim()) {
    formErrors.componentReleaseNumber = ''
  }
})

watch(() => form.status, () => {
  if (form.status) {
    formErrors.status = ''
  }
})
</script>

<template>
  <div class="button-row">
    <v-btn color="primary" @click="openNew">Add Entry</v-btn>
    <AdminEntryFilters
      v-model:component-filter="componentFilter"
      v-model:release-filter="releaseFilter"
      :components="components"
      :releases="orderedReleases"
    />
  </div>

  <v-table>
      <thead>
        <tr>
          <th v-if="showIdColumns">ID</th>
          <th><button class="sort-button" type="button" @click="toggleSort('componentName')"><span>Component</span><v-icon v-if="sortState.key === 'componentName'" class="sort-icon" :icon="sortIndicator(true, sortState.direction)" size="16" /></button></th>
          <th><button class="sort-button" type="button" @click="toggleSort('releaseName')"><span>Aras Innovator Release</span><v-icon v-if="sortState.key === 'releaseName'" class="sort-icon" :icon="sortIndicator(true, sortState.direction)" size="16" /></button></th>
          <th><button class="sort-button" type="button" @click="toggleSort('componentVersionNumber')"><span>Version</span><v-icon v-if="sortState.key === 'componentVersionNumber'" class="sort-icon" :icon="sortIndicator(true, sortState.direction)" size="16" /></button></th>
          <th><button class="sort-button" type="button" @click="toggleSort('status')"><span>Support Status</span><v-icon v-if="sortState.key === 'status'" class="sort-icon" :icon="sortIndicator(true, sortState.direction)" size="16" /></button></th>
          <th>Notes</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        <template v-for="item in paginatedEntries" :key="item.id">
          <tr v-if="editingId === item.id">
            <td :colspan="columnCount">
              <div class="inline-form-grid inline-form-grid--three">
                <EntryFormFields
                  :model="form"
                  :components="components"
                  :component-error="formErrors.componentId"
                  :releases="releases"
                  :release-error="formErrors.innovatorReleaseId"
                  :release-number-error="formErrors.componentReleaseNumber"
                  :status-error="formErrors.status"
                  :status-options="entryStatusOptions"
                  :version-error="formErrors.componentVersionNumber"
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
            <td><button class="admin-link-button" type="button" @click="editItem(item)">{{ item.componentName }}</button></td>
            <td>{{ item.releaseName }}</td>
            <td>{{ item.componentVersionNumber }}<template v-if="item.componentReleaseNumber"> / {{ item.componentReleaseNumber }}</template></td>
            <td>{{ item.status }}</td>
            <td>{{ item.notes || '—' }}</td>
            <td class="actions-cell">
              <v-btn size="small" variant="text" @click="editItem(item)">Edit</v-btn>
              <v-btn size="small" variant="text" color="error" @click="removeItem(item)">Delete</v-btn>
            </td>
          </tr>
        </template>
      </tbody>
  </v-table>

  <div class="table-pagination-row">
    <div class="table-pagination-summary">
      Showing
      {{ filteredAndSortedEntries.length === 0 ? 0 : (currentPage - 1) * rowsPerPage + 1 }}
      -
      {{ Math.min(currentPage * rowsPerPage, filteredAndSortedEntries.length) }}
      of {{ filteredAndSortedEntries.length }}
    </div>
    <v-select
      v-model="rowsPerPage"
      style="flex-grow: 0"
      class="rows-per-page-select"
      density="compact"
      hide-details
      :items="rowsPerPageOptions"
      label="Rows"
      variant="outlined"
    />
    <v-pagination
      v-model="currentPage"
      :length="pageCount"
      :total-visible="7"
    />
  </div>

  <v-dialog v-model="showNewDialog" max-width="960">
    <v-card>
      <v-card-title>Add Entry</v-card-title>
      <v-card-text class="inline-form-grid inline-form-grid--three">
        <EntryFormFields
          :model="form"
          :components="components"
          :component-error="formErrors.componentId"
          :releases="releases"
          :release-error="formErrors.innovatorReleaseId"
          :release-number-error="formErrors.componentReleaseNumber"
          :status-error="formErrors.status"
          :status-options="entryStatusOptions"
          :version-error="formErrors.componentVersionNumber"
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

.table-pagination-row {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 16px;
  margin-top: 16px;
  flex-wrap: wrap;
  justify-self: center;
  width: 100%;
}

.table-pagination-summary {
  color: #4d6179;
  font-size: 0.95rem;
}

.rows-per-page-select {
  width: 104px;
  flex-grow: 0;
  margin-right: auto;
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
