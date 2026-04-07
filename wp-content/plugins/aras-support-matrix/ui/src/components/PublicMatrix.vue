<script setup lang="ts">
import { computed, reactive, ref, watch } from 'vue'

import PublicEntryCards from '@/components/public/PublicEntryCards.vue'
import PublicEntryTable from '@/components/public/PublicEntryTable.vue'
import { api } from '@/composables/api'
import type { ComponentRecord, EntryRecord, NoteRecord, ReleaseRecord, StatusRecord, SupportStatus } from '@/types/models'

const props = defineProps<{
  components: ComponentRecord[]
  entries: EntryRecord[]
  isAdmin: boolean
  notes: NoteRecord[]
  releases: ReleaseRecord[]
  statuses: StatusRecord[]
}>()

interface EntryFormState extends Omit<EntryRecord, 'status'> {
  status: SupportStatus
  newNoteTitle: string
  newNoteContent: string
  newNoteType: 'info' | 'warning'
}

const entriesState = ref<EntryRecord[]>([])
const selectedReleaseId = ref<number | null>(
  props.releases.length ? props.releases[props.releases.length - 1].id : null,
)
const selectedComponentIds = ref<number[]>([])
const viewMode = ref<'cards' | 'table'>('cards')
const componentSearch = ref('')
const componentMenuOpen = ref(false)
const sortBy = ref<'component' | 'version'>('component')
const sortDirection = ref<'asc' | 'desc'>('asc')
const entryEditingId = ref<number | null>(null)
const releaseNoteDialogOpen = ref(false)
const loading = ref(false)
const entryStatusOptions = computed(() => props.statuses.map((status) => status.name))
const publishedEntries = computed(() => entriesState.value.filter((entry) => entry.publicationStatus === 'publish'))
const publishedComponents = computed(() => {
  const publishedComponentIds = new Set(publishedEntries.value.map((entry) => entry.componentId))

  return props.components.filter((component) => {
    return component.publicationStatus === 'publish' && publishedComponentIds.has(component.id)
  })
})

const entryForm = reactive<EntryFormState>({
  id: 0,
  componentId: 0,
  componentName: '',
  innovatorReleaseId: 0,
  releaseName: '',
  componentVersionNumber: '',
  componentReleaseNumber: '',
  status: 'Supported',
  publicationStatus: 'draft',
  endOfLifeDate: '',
  noteId: null,
  noteTitle: '',
  notes: '',
  newNoteTitle: '',
  newNoteContent: '',
  newNoteType: 'info',
})

watch(
  () => props.entries,
  (value) => {
    entriesState.value = [...value]
  },
  { immediate: true },
)

watch(selectedComponentIds, () => {
  componentSearch.value = ''
})

const visibleComponents = computed(() => {
  const componentIdsWithData = new Set(
    publishedEntries.value
      .filter((entry) => entry.innovatorReleaseId === selectedReleaseId.value)
      .map((entry) => entry.componentId),
  )

  return publishedComponents.value.filter((component) => componentIdsWithData.has(component.id))
})

const orderedReleases = computed(() => {
  return [...props.releases].sort((left, right) => {
    const leftDate = left.releaseDate || left.name
    const rightDate = right.releaseDate || right.name
    return rightDate.localeCompare(leftDate)
  })
})

const selectedRelease = computed(() => {
  return orderedReleases.value.find((release) => release.id === selectedReleaseId.value) ?? null
})

const selectedReleaseIsPastEol = computed(() => {
  if (!selectedRelease.value?.endOfLifeDate) {
    return false
  }

  const today = new Date()
  const currentDate = new Date(today.getFullYear(), today.getMonth(), today.getDate())
  const endOfLifeDate = new Date(`${selectedRelease.value.endOfLifeDate}T00:00:00`)

  return !Number.isNaN(endOfLifeDate.getTime()) && endOfLifeDate < currentDate
})

const filteredEntries = computed(() => {
  return publishedEntries.value
    .filter((entry) => entry.innovatorReleaseId === selectedReleaseId.value)
    .filter((entry) => {
      return selectedComponentIds.value.length === 0 || selectedComponentIds.value.includes(entry.componentId)
    })
})

const sortedEntries = computed(() => {
  return [...filteredEntries.value].sort((left, right) => {
    let result = 0

    if (sortBy.value === 'component') {
      result = left.componentName.localeCompare(right.componentName, undefined, { sensitivity: 'base' })
    } else if (sortBy.value === 'version') {
      result = left.componentVersionNumber.localeCompare(right.componentVersionNumber, undefined, {
        numeric: true,
        sensitivity: 'base',
      })
    }

    return sortDirection.value === 'asc' ? result : -result
  })
})

const groupedEntries = computed(() => {
  return visibleComponents.value
    .filter((component) => {
      return selectedComponentIds.value.length === 0 || selectedComponentIds.value.includes(component.id)
    })
    .map((component) => ({
      component,
      versions: sortedEntries.value.filter((entry) => entry.componentId === component.id),
    }))
    .filter((group) => group.versions.length > 0)
    .sort((left, right) => {
      if (sortBy.value !== 'component') {
        return 0
      }

      const result = left.component.name.localeCompare(right.component.name, undefined, { sensitivity: 'base' })
      return sortDirection.value === 'asc' ? result : -result
    })
})

function toggleTableSort(column: 'component' | 'version') {
  if (sortBy.value === column) {
    sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc'
    return
  }

  sortBy.value = column
  sortDirection.value = 'asc'
}

function tableSortIcon(column: 'component' | 'version') {
  if (sortBy.value !== column) {
    return 'mdi-unfold-more-horizontal'
  }

  return sortDirection.value === 'asc' ? 'mdi-chevron-up' : 'mdi-chevron-down'
}

function formatDate(value: string) {
  if (!value) {
    return 'Not specified'
  }

  return new Date(`${value}T00:00:00`).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
  })
}

function noteIcon(type?: 'info' | 'warning') {
  return type === 'warning' ? 'mdi-alert-outline' : 'mdi-information-outline'
}

function releaseNoteToneClass(type?: 'info' | 'warning') {
  return type === 'warning' ? 'release-note-icon--warning' : 'release-note-icon--info'
}

function editEntry(entry: EntryRecord) {
  Object.assign(entryForm, {
    ...entry,
    status: entry.status || 'Supported',
    newNoteTitle: '',
    newNoteContent: '',
    newNoteType: 'info',
  })
  entryEditingId.value = entry.id
}

function cancelEntryEdit() {
  entryEditingId.value = null
  Object.assign(entryForm, {
    id: 0,
    componentId: 0,
    componentName: '',
    innovatorReleaseId: 0,
    releaseName: '',
    componentVersionNumber: '',
    componentReleaseNumber: '',
    status: 'Supported',
    publicationStatus: 'draft',
    noteId: null,
    noteTitle: '',
    notes: '',
    newNoteTitle: '',
    newNoteContent: '',
    newNoteType: 'info',
  })
}

async function submitEntry() {
  loading.value = true

  try {
    const savedEntry = await api.updateEntry({ ...entryForm } as EntryRecord)
    const index = entriesState.value.findIndex((entry) => entry.id === savedEntry.id)

    if (index !== -1) {
      entriesState.value.splice(index, 1, savedEntry)
    }

    cancelEntryEdit()
  } finally {
    loading.value = false
  }
}

</script>

<template>
  <div class="matrix-stack public-matrix">
    <v-alert
      type="info"
      variant="tonal"
      title="How to use this tool"
      text="Select an Aras Innovator release, then filter components to review certified, supported, and end-of-life versions."
      class="matrix-intro"
    />

    <div class="matrix-stack">
      <div class="controls-row">
        <v-select
          v-model="selectedReleaseId"
          class="release-select"
          density="comfortable"
          hide-details
          item-title="name"
          item-value="id"
          :items="orderedReleases"
          label="Aras Innovator Release"
          variant="outlined"
        >
          <template #selection="{ item }">
            <span>
              {{ item.raw.name }}
              <span v-if="item.raw.buildNumber" class="text-medium-emphasis">
                (Build {{ item.raw.buildNumber }})
              </span>
            </span>
          </template>
          <template #item="{ props: itemProps, item }">
            <v-list-item
              v-bind="itemProps"
              :title="item.raw.name"
              :subtitle="item.raw.buildNumber ? `Build ${item.raw.buildNumber}` : undefined"
            />
          </template>
        </v-select>

        <div v-if="selectedRelease" class="release-meta">
          <span class="release-meta-label release-meta-label--eol">EOL</span>
          <span
            class="release-meta-value"
            :class="{ 'release-meta-value--expired': selectedReleaseIsPastEol }"
          >
            {{ formatDate(selectedRelease.endOfLifeDate) }}
          </span>
          <v-btn
            v-if="selectedRelease.notes"
            :icon="noteIcon(selectedRelease.note?.type)"
            size="small"
            variant="text"
            @click="releaseNoteDialogOpen = true"
          />
        </div>

        <v-btn-toggle v-model="viewMode" color="primary" density="comfortable" mandatory variant="outlined">
          <v-btn icon="mdi-table" value="table" />
          <v-btn icon="mdi-view-grid-outline" value="cards" />
        </v-btn-toggle>
      </div>

      <v-autocomplete
        v-model="selectedComponentIds"
        v-model:menu="componentMenuOpen"
        v-model:search="componentSearch"
        class="component-filter"
        chips
        clearable
        closable-chips
        hide-details
        item-title="name"
        item-value="id"
        :items="visibleComponents"
        label="Filter Components"
        multiple
        variant="outlined"
      >
        <template #append-item>
          <div class="autocomplete-menu-footer">
            <v-btn color="primary" variant="flat" @click="componentMenuOpen = false">
              Done
            </v-btn>
            <v-btn variant="tonal" @click="selectedComponentIds = []">
              Clear All Selections
            </v-btn>
          </div>
        </template>
      </v-autocomplete>

      <v-dialog v-model="releaseNoteDialogOpen" max-width="420">
        <v-card>
          <v-card-title>Release Information</v-card-title>
          <v-card-text>
            <div class="release-note-title">
              <v-icon class="release-note-icon" :class="releaseNoteToneClass(selectedRelease?.note?.type)" :icon="noteIcon(selectedRelease?.note?.type)" size="18" />
              <span>{{ selectedRelease?.name }}</span>
            </div>
            <div class="release-note-eol" :class="{ 'release-note-eol--expired': selectedReleaseIsPastEol }">
              <span class="release-note-eol-label">EOL:</span>
              {{ formatDate(selectedRelease?.endOfLifeDate || '') }}
            </div>
            <p class="release-note-copy">{{ selectedRelease?.notes }}</p>
          </v-card-text>
          <v-card-actions>
            <v-spacer />
            <v-btn variant="text" @click="releaseNoteDialogOpen = false">Close</v-btn>
          </v-card-actions>
        </v-card>
      </v-dialog>

      <template v-if="groupedEntries.length">
        <PublicEntryCards
          v-if="viewMode === 'cards'"
          :groups="groupedEntries"
        />
        

        <PublicEntryTable
          v-else
          :components="components"
          :editing-id="entryEditingId"
          :entries="sortedEntries"
          :entry-form="entryForm"
          :is-admin="isAdmin"
          :loading="loading"
          :notes="notes"
          :releases="releases"
          :sort-icon="tableSortIcon"
          :status-options="entryStatusOptions"
          @cancel="cancelEntryEdit"
          @edit="editEntry"
          @save="submitEntry"
          @sort="toggleTableSort"
        />
      </template>

      <v-alert
        v-else
        border="start"
        type="warning"
        variant="tonal"
        text="Create releases, components, and compatibility entries in the admin tabs to populate this matrix."
        title="No compatibility data"
      />
    </div>
  </div>
</template>

<style scoped>
.matrix-stack {
  display: grid;
  gap: 20px;
}

.public-matrix :deep(a:focus),
.public-matrix :deep(a:focus-visible),
.public-matrix :deep(button:focus),
.public-matrix :deep(button:focus-visible),
.public-matrix :deep(input:focus),
.public-matrix :deep(input:focus-visible),
.public-matrix :deep(select:focus),
.public-matrix :deep(select:focus-visible),
.public-matrix :deep(textarea:focus),
.public-matrix :deep(textarea:focus-visible),
.public-matrix :deep([tabindex]:focus),
.public-matrix :deep([tabindex]:focus-visible) {
  outline: none;
}

.controls-row {
  display: flex;
  gap: 12px;
  align-items: center;
  flex-wrap: wrap;
}

.release-select {
  min-width: 320px;
  max-width: 460px;
}

.component-filter {
  min-width: 0;
}

.release-meta {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 0 4px;
  color: #4d6179;
}

.release-meta-label {
  font-size: 0.78rem;
  font-weight: 700;
  letter-spacing: 0.08em;
  text-transform: uppercase;
  color: #48627f;
}

.release-meta-label--eol {
  font-size: 0.9rem;
}

.release-meta-value {
  font-size: 0.95rem;
}

.release-meta-value--expired {
  color: #c62828;
}

.release-note-title {
  display: flex;
  align-items: center;
  gap: 8px;
  font-weight: 700;
  margin-bottom: 4px;
}

.release-note-icon {
  flex: 0 0 auto;
}

.release-note-icon--info {
  color: #0F66CB;
}

.release-note-icon--warning {
  color: #D49623;
}

.release-note-eol {
  margin-bottom: 12px;
  color: #4d6179;
  font-size: 0.95rem;
}

.release-note-eol-label {
  font-size: 1.02rem;
  font-weight: 700;
  margin-right: 4px;
}

.release-note-eol--expired {
  color: #c62828;
}

.release-note-copy {
  margin: 0;
  white-space: pre-wrap;
}

.autocomplete-menu-footer {
  display: flex;
  gap: 8px;
  justify-content: flex-start;
  position: sticky;
  bottom: 0;
  z-index: 1;
  padding: 8px 12px;
  border-top: 1px solid rgba(16, 35, 61, 0.08);
  background: rgb(var(--v-theme-surface));
}

.autocomplete-menu-footer :deep(.v-btn) {
  display: inline-flex;
}

.controls-row .release-select :deep(.v-field),
.controls-row .component-filter :deep(.v-field) {
  min-height: 56px;
}

.controls-row .release-select :deep(.v-field__input),
.controls-row .component-filter :deep(.v-field__input) {
  min-height: 56px;
  align-items: center;
}

.controls-row :deep(.v-btn-toggle) {
  margin-left: auto;
}

@media (max-width: 960px) {
  .release-select {
    min-width: 0;
    width: 100%;
    max-width: none;
  }

  .release-meta {
    width: 100%;
    padding-inline: 0;
  }
}
</style>
