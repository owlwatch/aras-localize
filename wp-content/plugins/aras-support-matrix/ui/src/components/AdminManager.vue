<script setup lang="ts">
import { computed, onMounted, ref, watch } from 'vue'

import AdminComponentsTab from '@/components/admin/AdminComponentsTab.vue'
import AdminEntriesTab from '@/components/admin/AdminEntriesTab.vue'
import AdminReleasesTab from '@/components/admin/AdminReleasesTab.vue'
import { api } from '@/composables/api'
import type { ComponentRecord, EntryRecord, ImportStatus, ReleaseRecord, StatusRecord } from '@/types/models'

const props = defineProps<{
  components: ComponentRecord[]
  releases: ReleaseRecord[]
  entries: EntryRecord[]
  statuses: StatusRecord[]
}>()

const emit = defineEmits<{
  refresh: []
}>()

const adminTab = ref('components')
const importLoading = ref(false)
const importLoopActive = ref(false)
const showIdColumns = ref(false)
const dismissedImportFingerprint = ref('')
const componentsState = ref<ComponentRecord[]>([])
const releasesState = ref<ReleaseRecord[]>([])
const entriesState = ref<EntryRecord[]>([])
const importStatus = ref<ImportStatus>({
  status: 'idle',
  phase: 'idle',
  progress: 0,
  processedRows: 0,
  totalRows: 0,
  counts: {
    releases: 0,
    components: 0,
    entries: 0,
    updated_entries: 0,
  },
  message: '',
  lastError: '',
  startedAt: 0,
  finishedAt: 0,
  reset: false,
})
const importDismissStorageKey = 'aras-support-matrix.import-dismissed'

componentsState.value = [...props.components]
releasesState.value = [...props.releases]
entriesState.value = [...props.entries]

const importFingerprint = computed(() => {
  if (importStatus.value.status === 'idle') {
    return ''
  }

  return JSON.stringify({
    status: importStatus.value.status,
    phase: importStatus.value.phase,
    startedAt: importStatus.value.startedAt,
    finishedAt: importStatus.value.finishedAt,
    message: importStatus.value.message,
    lastError: importStatus.value.lastError,
    progress: importStatus.value.progress,
    counts: importStatus.value.counts,
    reset: importStatus.value.reset,
  })
})

const showImportAlert = computed(() => {
  return importStatus.value.status !== 'idle' && dismissedImportFingerprint.value !== importFingerprint.value
})

function dismissImportAlert() {
  if (!importFingerprint.value) {
    return
  }

  dismissedImportFingerprint.value = importFingerprint.value
  window.localStorage.setItem(importDismissStorageKey, importFingerprint.value)
}

async function refreshImportStatus() {
  importStatus.value = await api.getImportStatus()
}

async function runImport(reset: boolean) {
  importLoading.value = true

  try {
    importStatus.value = await api.startImport(reset)
    await continueImport()
  } finally {
    importLoading.value = false
  }
}

async function continueImport() {
  if (importLoopActive.value) {
    return
  }

  importLoopActive.value = true

  try {
    while (importStatus.value.status === 'running') {
      importStatus.value = await api.runImportStep()

      if (importStatus.value.status === 'completed') {
        emit('refresh')
        break
      }

      if (importStatus.value.status === 'error') {
        break
      }
    }
  } finally {
    importLoopActive.value = false
  }
}

watch(importFingerprint, (value) => {
  if (!value) {
    dismissedImportFingerprint.value = ''
    window.localStorage.removeItem(importDismissStorageKey)
    return
  }

  if (dismissedImportFingerprint.value === value) {
    return
  }

  const storedValue = window.localStorage.getItem(importDismissStorageKey) ?? ''
  dismissedImportFingerprint.value = storedValue
})

onMounted(async () => {
  dismissedImportFingerprint.value = window.localStorage.getItem(importDismissStorageKey) ?? ''
  await refreshImportStatus()

  if (importStatus.value.status === 'running') {
    await continueImport()
  }
})
</script>

<template>
  <div class="matrix-stack">
    <div class="admin-header-row">
      <div>
        <p class="hero-copy">
          Manage components, releases, and compatibility entries with WordPress-backed data.
        </p>
      </div>

      <v-menu location="bottom end">
        <template #activator="{ props: menuProps }">
          <v-btn v-bind="menuProps" icon="mdi-cog-outline" variant="text" />
        </template>

        <v-card class="admin-settings-menu" min-width="320">
          <v-card-title>Admin Options</v-card-title>
          <v-card-text class="matrix-stack">
            <v-switch
              v-model="showIdColumns"
              color="primary"
              hide-details
              inset
              label="Show ID Columns"
            />

            <div class="matrix-stack">
              <div class="text-subtitle-2">Import data.csv</div>
              <div class="button-row">
                <v-btn :disabled="importLoading || importLoopActive" color="primary" @click="runImport(false)">
                  Import
                </v-btn>
                <v-btn :disabled="importLoading || importLoopActive" variant="outlined" @click="runImport(true)">
                  Reset + Import
                </v-btn>
                <v-btn
                  v-if="importStatus.status === 'running' && !importLoopActive"
                  variant="text"
                  @click="continueImport"
                >
                  Resume
                </v-btn>
              </div>
            </div>
          </v-card-text>
        </v-card>
      </v-menu>
    </div>

    <v-alert
      v-if="showImportAlert"
      :type="importStatus.status === 'error' ? 'error' : 'info'"
      variant="tonal"
    >
      <div class="matrix-stack">
        <div>
          <strong>{{ importStatus.message || 'Import in progress.' }}</strong>
          <div v-if="importStatus.totalRows" class="text-medium-emphasis">
            {{ importStatus.processedRows }} / {{ importStatus.totalRows }} rows
          </div>
          <div v-if="importStatus.lastError" class="text-error">
            {{ importStatus.lastError }}
          </div>
        </div>

        <v-progress-linear
          :indeterminate="importStatus.status === 'running' && importStatus.phase === 'reset'"
          :model-value="importStatus.progress"
          color="primary"
          height="10"
          rounded
        />

        <div class="button-row">
          <span>Releases: {{ importStatus.counts.releases }}</span>
          <span>Components: {{ importStatus.counts.components }}</span>
          <span>New entries: {{ importStatus.counts.entries }}</span>
          <span>Updated entries: {{ importStatus.counts.updated_entries }}</span>
          <v-spacer />
          <v-btn size="small" variant="text" @click="dismissImportAlert">Dismiss</v-btn>
        </div>
      </div>
    </v-alert>

    <v-tabs v-model="adminTab" color="primary">
      <v-tab value="components">Components</v-tab>
      <v-tab value="releases">Releases</v-tab>
      <v-tab value="entries">Entries</v-tab>
    </v-tabs>

    <v-window v-model="adminTab">
      <v-window-item value="components">
        <AdminComponentsTab
          :components="componentsState"
          :entries="entriesState"
          :show-id-columns="showIdColumns"
          @update:components="componentsState = $event"
          @update:entries="entriesState = $event"
        />
      </v-window-item>

      <v-window-item value="releases">
        <AdminReleasesTab
          :entries="entriesState"
          :releases="releasesState"
          :show-id-columns="showIdColumns"
          @request:refresh="emit('refresh')"
          @update:entries="entriesState = $event"
          @update:releases="releasesState = $event"
        />
      </v-window-item>

      <v-window-item value="entries">
        <AdminEntriesTab
          :components="componentsState"
          :entries="entriesState"
          :releases="releasesState"
          :show-id-columns="showIdColumns"
          :statuses="statuses"
          @update:entries="entriesState = $event"
        />
      </v-window-item>
    </v-window>
  </div>
</template>

<style scoped>
.matrix-stack {
  display: grid;
  gap: 20px;
}

.button-row {
  display: flex;
  gap: 12px;
  align-items: center;
  flex-wrap: wrap;
}

.admin-header-row {
  display: flex;
  align-items: start;
  justify-content: space-between;
  gap: 16px;
}

.admin-settings-menu {
  max-width: 360px;
}

.hero-copy {
  max-width: 720px;
  font-size: 1rem;
  color: #4d6179;
}
</style>
