<script setup lang="ts">
import { computed, reactive, ref, watch } from 'vue'

import AdminEntryFilters from '@/components/admin/AdminEntryFilters.vue'
import EntryFormFields from '@/components/shared/EntryFormFields.vue'
import { api } from '@/composables/api'
import type { ComponentRecord, EntryRecord, PublicationStatus, ReleaseRecord, StatusRecord, SupportStatus } from '@/types/models'

type SortDirection = 'asc' | 'desc'
type EntrySortKey = 'componentName' | 'releaseName' | 'componentVersionNumber' | 'status' | 'publicationStatus'

interface EntryFormState {
  id: number
  componentId: number | null
  componentName: string
  innovatorReleaseId: number | null
  releaseName: string
  publicationStatus: PublicationStatus
  componentVersionNumber: string
  componentReleaseNumber: string
  status: SupportStatus
  endOfLifeDate: string
  notes: string
}

interface InlineEntryDraft {
  componentVersionNumber: string
  componentReleaseNumber: string
  status: SupportStatus | null
  publicationStatus: PublicationStatus
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
  publicationStatus: '',
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
const inlineDrafts = reactive<Record<number, InlineEntryDraft>>({})
const savingRowIds = reactive<Record<number, boolean>>({})
const cellMenus = reactive<Record<string, boolean>>({})

const form = reactive<EntryFormState>({
  id: 0,
  componentId: null,
  componentName: '',
  innovatorReleaseId: null,
  releaseName: '',
  publicationStatus: 'draft',
  componentVersionNumber: '',
  componentReleaseNumber: '',
  status: 'Supported',
  endOfLifeDate: '',
  notes: '',
})

watch(() => props.entries, (value) => { entriesState.value = [...value] }, { immediate: true })
watch(
  entriesState,
  (value) => {
    const activeIds = new Set<number>()

    value.forEach((entry) => {
      activeIds.add(entry.id)
      inlineDrafts[entry.id] = {
        componentVersionNumber: entry.componentVersionNumber,
        componentReleaseNumber: entry.componentReleaseNumber,
        status: entry.status || null,
        publicationStatus: entry.publicationStatus,
        notes: entry.notes,
      }
    })

    Object.keys(inlineDrafts).forEach((id) => {
      if (!activeIds.has(Number(id))) {
        delete inlineDrafts[Number(id)]
        delete savingRowIds[Number(id)]
      }
    })
  },
  { immediate: true },
)

const entryStatusOptions = computed(() => props.statuses.map((status) => status.name))
const publicationStatusOptions = [
  { title: 'Draft', value: 'draft' as const },
  { title: 'Published', value: 'publish' as const },
]
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

const columnCount = computed(() => (props.showIdColumns ? 8 : 7))

function sortIndicator(active: boolean, direction: SortDirection) {
  if (!active) return ''
  return direction === 'asc' ? ' mdi-chevron-up' : ' mdi-chevron-down'
}

function syncEntries(next: EntryRecord[]) {
  entriesState.value = next
  emit('update:entries', next)
}

function statusLabel(publicationStatus: PublicationStatus) {
  return publicationStatus === 'publish' ? 'Published' : 'Draft'
}

function statusColor(publicationStatus: PublicationStatus) {
  return publicationStatus === 'publish' ? 'success' : 'error'
}

function rowDraft(item: EntryRecord) {
  return inlineDrafts[item.id]
}

function cellMenuKey(itemId: number, field: 'version' | 'status' | 'notes') {
  return `${itemId}:${field}`
}

function closeCellMenu(itemId: number, field: 'version' | 'status' | 'notes') {
  cellMenus[cellMenuKey(itemId, field)] = false
}

async function saveInlineCell(item: EntryRecord, field: 'version' | 'status' | 'notes') {
  await saveInlineItem(item)
  closeCellMenu(item.id, field)
}

function buildEntryUpdatePayload(item: EntryRecord, draft: InlineEntryDraft): EntryRecord {
  return {
    ...item,
    componentVersionNumber: draft.componentVersionNumber.trim(),
    componentReleaseNumber: draft.componentReleaseNumber.trim(),
    status: draft.status ?? '',
    publicationStatus: draft.publicationStatus,
    notes: draft.notes,
  }
}

async function saveInlineItem(item: EntryRecord) {
  const draft = rowDraft(item)

  if (!draft || savingRowIds[item.id]) {
    return
  }

  const nextPayload = buildEntryUpdatePayload(item, draft)

  if (
    nextPayload.componentVersionNumber === item.componentVersionNumber
    && nextPayload.componentReleaseNumber === item.componentReleaseNumber
    && nextPayload.status === item.status
    && nextPayload.publicationStatus === item.publicationStatus
    && nextPayload.notes === item.notes
  ) {
    return
  }

  savingRowIds[item.id] = true

  try {
    const saved = await api.updateEntry(nextPayload)
    const nextEntries = [...entriesState.value]
    upsertById(nextEntries, saved)
    syncEntries(nextEntries)
  } finally {
    savingRowIds[item.id] = false
  }
}

function onPublicationToggle(item: EntryRecord, value: boolean | null) {
  const draft = rowDraft(item)

  if (!draft) {
    return
  }

  draft.publicationStatus = value ? 'publish' : 'draft'
  void saveInlineItem(item)
}

function resetForm() {
  Object.assign(form, {
    id: 0,
    componentId: null,
    componentName: '',
    innovatorReleaseId: latestReleaseId.value,
    releaseName: '',
    publicationStatus: 'draft',
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

function copyItem(item: EntryRecord) {
  cancelEdit()
  Object.assign(form, {
    ...item,
    id: 0,
  })
  clearFormErrors()
  showNewDialog.value = true
}

function clearFormErrors() {
  formErrors.componentId = ''
  formErrors.publicationStatus = ''
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

  if (!form.publicationStatus) {
    formErrors.publicationStatus = 'Please select a status.'
  }

  if (!form.componentVersionNumber.trim()) {
    formErrors.componentVersionNumber = 'Please enter a release.'
  }

  if (!form.componentReleaseNumber.trim()) {
    formErrors.componentReleaseNumber = 'Please enter a build.'
  }

  if (!form.status) {
    formErrors.status = 'Please select a support status.'
  }

  return !formErrors.componentId
    && !formErrors.publicationStatus
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
    publicationStatus: form.publicationStatus,
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

watch(() => form.publicationStatus, () => {
  if (form.publicationStatus) {
    formErrors.publicationStatus = ''
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
  <div class="button-row align-start">
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
          <th><button class="sort-button" type="button" @click="toggleSort('publicationStatus')"><span>Status</span><v-icon v-if="sortState.key === 'publicationStatus'" class="sort-icon" :icon="sortIndicator(true, sortState.direction)" size="16" /></button></th>
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
                  :publication-status-error="formErrors.publicationStatus"
                  :publication-status-options="publicationStatusOptions"
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
            <td class="entry-inline-cell">
              <v-menu
                v-model="cellMenus[cellMenuKey(item.id, 'version')]"
                :close-on-content-click="false"
                location="bottom start"
              >
                <template #activator="{ props: menuProps }">
                  <button class="entry-edit-trigger" type="button" v-bind="menuProps">
                    <span>{{ item.componentVersionNumber }}<template v-if="item.componentReleaseNumber"> / {{ item.componentReleaseNumber }}</template></span>
                    <v-icon class="entry-edit-icon" icon="mdi-pencil-outline" size="14" />
                  </button>
                </template>
                <v-card class="entry-edit-menu" min-width="280">
                  <v-card-text class="entry-edit-grid">
                    <v-text-field
                      v-model="rowDraft(item).componentVersionNumber"
                      density="compact"
                      hide-details
                      label="Release"
                      variant="outlined"
                    />
                    <v-text-field
                      v-model="rowDraft(item).componentReleaseNumber"
                      density="compact"
                      hide-details
                      label="Build"
                      variant="outlined"
                    />
                  </v-card-text>
                  <v-card-actions>
                    <v-spacer />
                    <v-btn variant="text" @click="closeCellMenu(item.id, 'version')">Cancel</v-btn>
                    <v-btn :loading="savingRowIds[item.id]" color="primary" @click="saveInlineCell(item, 'version')">Save</v-btn>
                  </v-card-actions>
                </v-card>
              </v-menu>
            </td>
            <td class="entry-inline-cell">
              <v-menu
                v-model="cellMenus[cellMenuKey(item.id, 'status')]"
                :close-on-content-click="false"
                location="bottom start"
              >
                <template #activator="{ props: menuProps }">
                  <button class="entry-edit-trigger" type="button" v-bind="menuProps">
                    <span>{{ item.status || '—' }}</span>
                    <v-icon class="entry-edit-icon" icon="mdi-pencil-outline" size="14" />
                  </button>
                </template>
                <v-card class="entry-edit-menu" min-width="240">
                  <v-card-text>
                    <v-select
                      v-model="rowDraft(item).status"
                      density="compact"
                      hide-details
                      :items="entryStatusOptions"
                      label="Support Status"
                      variant="outlined"
                    />
                  </v-card-text>
                  <v-card-actions>
                    <v-spacer />
                    <v-btn variant="text" @click="closeCellMenu(item.id, 'status')">Cancel</v-btn>
                    <v-btn :loading="savingRowIds[item.id]" color="primary" @click="saveInlineCell(item, 'status')">Save</v-btn>
                  </v-card-actions>
                </v-card>
              </v-menu>
            </td>
            <td class="entry-inline-cell">
              <v-menu
                v-model="cellMenus[cellMenuKey(item.id, 'notes')]"
                :close-on-content-click="false"
                location="bottom start"
              >
                <template #activator="{ props: menuProps }">
                  <button class="entry-edit-trigger entry-edit-trigger--notes" type="button" v-bind="menuProps">
                    <span>{{ item.notes || '—' }}</span>
                    <v-icon class="entry-edit-icon" icon="mdi-pencil-outline" size="14" />
                  </button>
                </template>
                <v-card class="entry-edit-menu" min-width="320">
                  <v-card-text>
                    <v-textarea
                      v-model="rowDraft(item).notes"
                      density="compact"
                      hide-details
                      label="Notes"
                      rows="3"
                      variant="outlined"
                    />
                  </v-card-text>
                  <v-card-actions>
                    <v-spacer />
                    <v-btn variant="text" @click="closeCellMenu(item.id, 'notes')">Cancel</v-btn>
                    <v-btn :loading="savingRowIds[item.id]" color="primary" @click="saveInlineCell(item, 'notes')">Save</v-btn>
                  </v-card-actions>
                </v-card>
              </v-menu>
            </td>
            <td class="entry-inline-cell">
              <div class="entry-publish-cell">
                <v-switch
                  :color="statusColor(rowDraft(item).publicationStatus)"
                  density="compact"
                  hide-details
                  inset
                  :loading="savingRowIds[item.id]"
                  :model-value="rowDraft(item).publicationStatus === 'publish'"
                  @update:model-value="onPublicationToggle(item, $event)"
                />
              </div>
            </td>
            <td class="actions-cell">
              <v-btn size="small" variant="text" @click="copyItem(item)">Copy</v-btn>
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
          :publication-status-error="formErrors.publicationStatus"
          :publication-status-options="publicationStatusOptions"
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

.entry-inline-cell {
  min-width: 170px;
  vertical-align: middle;
}

.entry-publish-cell {
  display: flex;
  align-items: center;
  min-width: 90px;
}

.entry-edit-trigger {
  display: flex;
  align-items: center;
  gap: 6px;
  justify-content: space-between;
  width: 100%;
  min-width: 0;
  padding: 6px 0;
  border: 0;
  background: transparent;
  color: inherit;
  font: inherit;
  cursor: pointer;
  text-align: left;
}

.entry-edit-trigger > span {
  flex: 1 1 auto;
  min-width: 0;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.entry-edit-trigger--notes {
  display: flex;
}

.entry-edit-icon {
  flex: 0 0 auto;
  opacity: 0;
  color: #6b7a90;
  transition: opacity 0.15s ease;
}

.entry-edit-trigger:hover .entry-edit-icon,
.entry-edit-trigger:focus-visible .entry-edit-icon {
  opacity: 1;
}

.entry-edit-menu {
  max-width: min(360px, calc(100vw - 32px));
}

.entry-edit-grid {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 12px;
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
