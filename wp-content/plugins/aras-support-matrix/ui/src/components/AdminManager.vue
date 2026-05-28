<script setup lang="ts">
import { computed, onMounted, ref } from 'vue'

import AdminComponentsTab from '@/components/admin/AdminComponentsTab.vue'
import AdminEntriesTab from '@/components/admin/AdminEntriesTab.vue'
import AdminNotesTab from '@/components/admin/AdminNotesTab.vue'
import AdminReleasesTab from '@/components/admin/AdminReleasesTab.vue'
import { api, getConfig } from '@/composables/api'
import type { ComponentRecord, EntryRecord, ImportStatus, NoteRecord, ReleaseRecord, StatusRecord } from '@/types/models'

const props = defineProps<{
  components: ComponentRecord[]
  releases: ReleaseRecord[]
  entries: EntryRecord[]
  notes: NoteRecord[]
  statuses: StatusRecord[]
}>()

const emit = defineEmits<{
  refresh: []
  'update:components': [value: ComponentRecord[]]
  'update:entries': [value: EntryRecord[]]
  'update:notes': [value: NoteRecord[]]
  'update:releases': [value: ReleaseRecord[]]
}>()

const adminTab = ref('components')
const importLoading = ref(false)
const importLoopActive = ref(false)
const showIdColumns = ref(false)
const dismissedImportInstanceKey = ref('')
const initiatedImportInstanceKey = ref('')
const settingsMenuOpen = ref(false)
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
const importInitiatedStorageKey = 'aras-support-matrix.import-initiated'
const config = getConfig()

const importInstanceKey = computed(() => {
  if (importStatus.value.status === 'idle') {
    return ''
  }

  return JSON.stringify({
    startedAt: importStatus.value.startedAt,
    reset: importStatus.value.reset,
  })
})

const showImportAlert = computed(() => {
  return importStatus.value.status !== 'idle'
    && initiatedImportInstanceKey.value === importInstanceKey.value
    && dismissedImportInstanceKey.value !== importInstanceKey.value
})

const embedSnippet = computed(() => {
  const closingScriptTag = '</' + 'script>'
  const embedScriptUrl = new URL(config.embedScriptUrl, window.location.origin).toString()

  return [
    '<script id="aras-support-matrix-loader">',
    "  (function () {",
    "    var script = document.getElementById('aras-support-matrix-loader');",
    "    var mount = document.getElementById('aras-support-matrix-embed');",
    '',
    '    if (!mount) {',
    "      mount = document.createElement('div');",
    "      mount.id = 'aras-support-matrix-embed';",
    '',
    '      if (script && script.parentNode) {',
    "        script.parentNode.insertBefore(mount, script);",
    '      } else {',
    "        document.body.appendChild(mount);",
    '      }',
    '    }',
    '',
    '    window.ArasSupportMatrixConfig = {',
    `      restBase: '${config.restBase}',`,
    "      mountSelector: '#aras-support-matrix-embed'",
    '    };',
    '',
    "    var embedScript = document.createElement('script');",
    "    embedScript.type = 'module';",
    `    embedScript.src = '${embedScriptUrl}?t=' + Date.now();`,
    "    document.head.appendChild(embedScript);",
    '  })();',
    closingScriptTag,
  ].join('\n')
})

async function copyEmbedSnippet() {
  await navigator.clipboard.writeText(embedSnippet.value)
}

function dismissImportAlert() {
  if (!importInstanceKey.value) {
    return
  }

  dismissedImportInstanceKey.value = importInstanceKey.value
  window.localStorage.setItem(importDismissStorageKey, importInstanceKey.value)
}

function markImportInitiated() {
  if (!importInstanceKey.value) {
    return
  }

  initiatedImportInstanceKey.value = importInstanceKey.value
  dismissedImportInstanceKey.value = ''
  window.localStorage.setItem(importInitiatedStorageKey, importInstanceKey.value)
  window.localStorage.removeItem(importDismissStorageKey)
}

async function refreshImportStatus() {
  importStatus.value = await api.getImportStatus()
}

async function runImport(reset: boolean) {
  importLoading.value = true

  try {
    importStatus.value = await api.startImport(reset)
    markImportInitiated()
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

async function resumeImport() {
  markImportInitiated()
  await continueImport()
}

onMounted(async () => {
  dismissedImportInstanceKey.value = window.localStorage.getItem(importDismissStorageKey) ?? ''
  initiatedImportInstanceKey.value = window.localStorage.getItem(importInitiatedStorageKey) ?? ''
  await refreshImportStatus()

  if (importStatus.value.status === 'running') {
    await continueImport()
  }
})
</script>

<template>
  <div class="matrix-stack admin-manager">
    <div class="admin-header-row">
      <div>
        <p class="hero-copy">
          Manage components, releases, and compatibility entries with WordPress-backed data.
        </p>
      </div>

      <v-menu v-model="settingsMenuOpen" :close-on-content-click="false" location="bottom end">
        <template #activator="{ props: menuProps }">
          <v-btn v-bind="menuProps" icon="mdi-cog-outline" variant="text" />
        </template>

        <v-card class="admin-settings-menu" min-width="520">
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
                  @click="resumeImport"
                >
                  Resume
                </v-btn>
              </div>
            </div>

            <v-alert density="comfortable" type="info" variant="tonal">
              <div class="embed-note">
                <div class="embed-note-header">
                  <div class="text-subtitle-2">Embed Snippet</div>
                  <v-btn size="small" variant="text" @click="copyEmbedSnippet">
                    Copy
                  </v-btn>
                </div>
                <div class="text-body-2 text-medium-emphasis">
                  Copy this into the third-party site where the matrix should render.
                </div>
                <pre class="embed-snippet" @click.stop><code>{{ embedSnippet }}</code></pre>
              </div>
            </v-alert>
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

    <v-tabs v-model="adminTab" color="primary" bg-color="grey-lighten-3">
      <v-tab value="components">Components</v-tab>
      <v-tab value="releases">Releases</v-tab>
      <v-tab value="entries">Entries</v-tab>
      <v-tab value="notes">Notes</v-tab>
    </v-tabs>

    <v-window v-model="adminTab" :crossfade="true" :transition-duration="0.2">
      <v-window-item value="components">
        <AdminComponentsTab
          :components="components"
          :entries="entries"
          :show-id-columns="showIdColumns"
          @update:components="emit('update:components', $event)"
          @update:entries="emit('update:entries', $event)"
        />
      </v-window-item>

      <v-window-item value="releases">
        <AdminReleasesTab
          :entries="entries"
          :notes="notes"
          :releases="releases"
          :show-id-columns="showIdColumns"
          @update:entries="emit('update:entries', $event)"
          @update:notes="emit('update:notes', $event)"
          @update:releases="emit('update:releases', $event)"
        />
      </v-window-item>

      <v-window-item value="entries">
        <AdminEntriesTab
          :components="components"
          :entries="entries"
          :notes="notes"
          :releases="releases"
          :show-id-columns="showIdColumns"
          :statuses="statuses"
          @update:entries="emit('update:entries', $event)"
          @update:notes="emit('update:notes', $event)"
        />
      </v-window-item>

      <v-window-item value="notes">
        <AdminNotesTab
          :notes="notes"
          :show-id-columns="showIdColumns"
          @update:notes="emit('update:notes', $event)"
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
  max-width: min(680px, calc(100vw - 32px));
}

.hero-copy {
  max-width: 720px;
  font-size: 1rem;
  color: #4d6179;
}

.embed-note {
  display: grid;
  gap: 10px;
}

.embed-note-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 12px;
}

.embed-snippet {
  margin: 0;
  padding: 12px;
  overflow-x: auto;
  white-space: pre-wrap;
  word-break: break-word;
  border-radius: 10px;
  background: rgba(17, 24, 39, 0.06);
  font-size: 0.7rem;
  line-height: 1.45;
}
</style>
