<script setup lang="ts">
import { computed, onMounted, ref } from 'vue'

import AdminManager from '@/components/AdminManager.vue'
import PublicMatrix from '@/components/PublicMatrix.vue'
import { api, getConfig } from '@/composables/api'
import type { EntryRecord, MatrixPayload, NoteRecord, ReleaseRecord } from '@/types/models'

const data = ref<MatrixPayload>({
  components: [],
  releases: [],
  entries: [],
  notes: [],
  statuses: [],
})
const loading = ref(true)
const error = ref('')
const config = getConfig()
const activeTab = ref(config.initialTab)

const isAdmin = computed(() => config.isAdmin)
const publicData = computed<MatrixPayload>(() => {
  const publishedReleaseIds = new Set(
    data.value.releases
      .filter((release) => release.publicationStatus === 'publish')
      .map((release) => release.id),
  )

  return {
    components: data.value.components,
    releases: data.value.releases.filter((release) => publishedReleaseIds.has(release.id)),
    entries: data.value.entries.filter((entry) => publishedReleaseIds.has(entry.innovatorReleaseId)),
    notes: data.value.notes,
    statuses: data.value.statuses,
  }
})

async function loadData() {
  loading.value = true
  error.value = ''

  try {
    data.value = await api.getMatrix()
  } catch (err) {
    error.value = err instanceof Error ? err.message : 'Failed to load support matrix data.'
  } finally {
    loading.value = false
  }
}

function handleComponentsUpdate(components: MatrixPayload['components']) {
  data.value.components = components
}

function handleEntriesUpdate(entries: EntryRecord[]) {
  data.value.entries = entries
}

function handleReleasesUpdate(releases: ReleaseRecord[]) {
  data.value.releases = releases
}

function handleNotesUpdate(notes: NoteRecord[]) {
  const noteMap = new Map(notes.map((note) => [note.id, note]))

  data.value.notes = notes
  data.value.releases = data.value.releases.map((release) => {
    if (!release.noteId) {
      return {
        ...release,
        note: undefined,
      }
    }

    const nextNote = noteMap.get(release.noteId)

    if (!nextNote) {
      return {
        ...release,
        noteId: null,
        noteTitle: '',
        notes: '',
        note: undefined,
      }
    }

    return {
      ...release,
      noteTitle: nextNote.title,
      notes: nextNote.content,
      note: nextNote,
    }
  })
  data.value.entries = data.value.entries.map((entry) => {
    if (!entry.noteId) {
      return {
        ...entry,
        note: undefined,
      }
    }

    const nextNote = noteMap.get(entry.noteId)

    if (!nextNote) {
      return {
        ...entry,
        noteId: null,
        noteTitle: '',
        notes: '',
        note: undefined,
      }
    }

    return {
      ...entry,
      noteTitle: nextNote.title,
      notes: nextNote.content,
      note: nextNote,
    }
  })
}

onMounted(loadData)
</script>

<template>
  <div class="aras-support-matrix-root">
    <v-app>
      <v-main class="app-shell">
        <div class="app-background"></div>
        <v-container class="app-container" max-width="1400">
          <div class="hero-bar">
            <div>
              <div class="eyebrow">Aras Innovator</div>
              <h1 class="text-h3 font-weight-bold mt-0 mb-0">Support Matrix</h1>
            </div>
          </div>

          <v-alert v-if="error" class="mb-6" type="error" variant="tonal">
            {{ error }}
          </v-alert>

        <v-skeleton-loader v-if="loading" type="article, table" />

        <template v-else>
          <v-btn-toggle
            v-if="isAdmin"
            v-model="activeTab"
            class="app-view-toggle mb-6"
            color="primary"
            density="comfortable"
            mandatory
            variant="outlined"
          >
            <v-btn value="admin">Admin</v-btn>
            <v-btn value="public">Public View</v-btn>
          </v-btn-toggle>

          <template v-if="isAdmin">
            <v-window v-model="activeTab" :crossfade="true" :transition-duration="200">
              <v-window-item value="admin">
                <AdminManager
                  :components="data.components"
                  :entries="data.entries"
                  :notes="data.notes"
                  :releases="data.releases"
                  :statuses="data.statuses"
                  @update:components="handleComponentsUpdate"
                  @update:entries="handleEntriesUpdate"
                  @update:notes="handleNotesUpdate"
                  @update:releases="handleReleasesUpdate"
                  @refresh="loadData"
                />
              </v-window-item>

              <v-window-item value="public">
                <PublicMatrix
                  :components="publicData.components"
                  :entries="publicData.entries"
                  :is-admin="isAdmin"
                  :notes="publicData.notes"
                  :releases="publicData.releases"
                  :statuses="publicData.statuses"
                />
              </v-window-item>
            </v-window>
          </template>

          <PublicMatrix
            v-else
            :components="publicData.components"
            :entries="publicData.entries"
            :is-admin="false"
            :notes="publicData.notes"
            :releases="publicData.releases"
            :statuses="publicData.statuses"
          />
        </template>
      </v-container>
    </v-main>
    </v-app>
  </div>
</template>

<style scoped>
.app-shell {
  position: relative;
  min-height: 100vh;
}

.app-background {
  display: none;
}

.app-container {
  position: relative;
  padding-top: 48px;
  padding-bottom: 72px;
}

.hero-bar {
  display: flex;
  justify-content: space-between;
  align-items: end;
  gap: 24px;
  margin-bottom: 32px;
}

.eyebrow {
  margin-bottom: 12px;
  letter-spacing: 0.14em;
  text-transform: uppercase;
  font-size: 0.78rem;
  color: #48627f;
}

.hero-copy {
  max-width: 720px;
  font-size: 1rem;
  color: #4d6179;
}

.app-view-toggle {
  display: inline-flex;
  width: fit-content;
  border: 1px solid rgba(16, 35, 61, 0.12);
  border-radius: 999px;
  overflow: hidden;
  background: #fff;
}

.app-view-toggle :deep(.v-btn) {
  min-width: 120px;
  text-transform: none;
  letter-spacing: 0;
  font-weight: 600;
}

@media (max-width: 960px) {
  .hero-bar {
    align-items: start;
  }
}
</style>
