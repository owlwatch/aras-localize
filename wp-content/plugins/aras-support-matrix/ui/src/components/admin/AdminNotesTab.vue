<script setup lang="ts">
import { computed, reactive, ref, watch } from 'vue'

import { api } from '@/composables/api'
import type { NoteRecord, NoteType } from '@/types/models'

type SortDirection = 'asc' | 'desc'
type NoteSortKey = 'title' | 'type' | 'content'

const props = defineProps<{
  notes: NoteRecord[]
  showIdColumns: boolean
}>()

const emit = defineEmits<{
  'update:notes': [value: NoteRecord[]]
}>()

const loading = ref(false)
const editingId = ref<number | null>(null)
const showNewDialog = ref(false)
const sortState = reactive<{ key: NoteSortKey; direction: SortDirection }>({
  key: 'title',
  direction: 'asc',
})

const form = reactive<NoteRecord>({
  id: 0,
  title: '',
  content: '',
  type: 'info',
})

const noteTypeOptions: Array<{ title: string; value: NoteType }> = [
  { title: 'Info', value: 'info' },
  { title: 'Warning', value: 'warning' },
]

watch(() => props.notes, () => {
  if (editingId.value && !props.notes.find((note) => note.id === editingId.value)) {
    cancelEdit()
  }
})

const sortedNotes = computed(() => {
  return [...props.notes].sort((left, right) => {
    const result = String(left[sortState.key] ?? '').localeCompare(String(right[sortState.key] ?? ''), undefined, {
      numeric: true,
      sensitivity: 'base',
    })

    return sortState.direction === 'asc' ? result : -result
  })
})

const columnCount = computed(() => (props.showIdColumns ? 5 : 4))

function toggleSort(key: NoteSortKey) {
  if (sortState.key === key) {
    sortState.direction = sortState.direction === 'asc' ? 'desc' : 'asc'
    return
  }

  sortState.key = key
  sortState.direction = 'asc'
}

function sortIndicator(active: boolean, direction: SortDirection) {
  if (!active) return ''
  return direction === 'asc' ? ' mdi-chevron-up' : ' mdi-chevron-down'
}

function resetForm() {
  Object.assign(form, {
    id: 0,
    title: '',
    content: '',
    type: 'info',
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

function editItem(item: NoteRecord) {
  Object.assign(form, item)
  editingId.value = item.id
}

async function submit() {
  loading.value = true

  try {
    let saved: NoteRecord

    if (form.id) {
      saved = await api.updateNote({ ...form })
    } else {
      saved = await api.createNote({
        title: form.title,
        content: form.content,
        type: form.type,
      })
    }

    const nextNotes = [...props.notes]
    const index = nextNotes.findIndex((note) => note.id === saved.id)
    if (index === -1) {
      nextNotes.unshift(saved)
    } else {
      nextNotes.splice(index, 1, saved)
    }

    cancelEdit()
    emit('update:notes', nextNotes)
  } finally {
    loading.value = false
  }
}

async function removeItem(item: NoteRecord) {
  loading.value = true

  try {
    await api.deleteNote(item.id)
    emit('update:notes', props.notes.filter((note) => note.id !== item.id))
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <div class="button-row">
    <v-btn color="primary" @click="openNew">Add Note</v-btn>
  </div>

  <v-table>
    <thead>
      <tr>
        <th v-if="showIdColumns">ID</th>
        <th><button class="sort-button" type="button" @click="toggleSort('title')"><span>Title</span><v-icon v-if="sortState.key === 'title'" class="sort-icon" :icon="sortIndicator(true, sortState.direction)" size="16" /></button></th>
        <th><button class="sort-button" type="button" @click="toggleSort('type')"><span>Type</span><v-icon v-if="sortState.key === 'type'" class="sort-icon" :icon="sortIndicator(true, sortState.direction)" size="16" /></button></th>
        <th><button class="sort-button" type="button" @click="toggleSort('content')"><span>Content</span><v-icon v-if="sortState.key === 'content'" class="sort-icon" :icon="sortIndicator(true, sortState.direction)" size="16" /></button></th>
        <th></th>
      </tr>
    </thead>
    <tbody>
      <template v-for="item in sortedNotes" :key="item.id">
        <tr v-if="editingId === item.id">
          <td :colspan="columnCount">
            <div class="inline-form-grid">
              <v-text-field v-model="form.title" hide-details label="Title" variant="outlined" />
              <v-select v-model="form.type" hide-details :items="noteTypeOptions" item-title="title" item-value="value" label="Type" variant="outlined" />
              <v-textarea v-model="form.content" class="inline-form-span" hide-details label="Content" rows="4" variant="outlined" />
              <div class="button-row inline-form-actions">
                <v-btn :loading="loading" color="primary" @click="submit">Save</v-btn>
                <v-btn variant="text" @click="cancelEdit">Cancel</v-btn>
              </div>
            </div>
          </td>
        </tr>
        <tr v-else>
          <td v-if="showIdColumns">{{ item.id }}</td>
          <td><button class="admin-link-button" type="button" @click="editItem(item)">{{ item.title }}</button></td>
          <td>{{ item.type }}</td>
          <td class="note-content-cell">{{ item.content || '—' }}</td>
          <td class="actions-cell">
            <v-btn size="small" variant="text" @click="editItem(item)">Edit</v-btn>
            <v-btn size="small" variant="text" color="error" @click="removeItem(item)">Delete</v-btn>
          </td>
        </tr>
      </template>
    </tbody>
  </v-table>

  <v-dialog v-model="showNewDialog" max-width="760">
    <v-card>
      <v-card-title>Add Note</v-card-title>
      <v-card-text class="inline-form-grid">
        <v-text-field v-model="form.title" hide-details label="Title" variant="outlined" />
        <v-select v-model="form.type" hide-details :items="noteTypeOptions" item-title="title" item-value="value" label="Type" variant="outlined" />
        <v-textarea v-model="form.content" class="inline-form-span" hide-details label="Content" rows="4" variant="outlined" />
      </v-card-text>
      <v-card-actions>
        <v-spacer />
        <v-btn variant="text" @click="cancelEdit">Cancel</v-btn>
        <v-btn :loading="loading" color="primary" @click="submit">Save</v-btn>
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

.note-content-cell {
  white-space: pre-wrap;
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

.inline-form-span,
.inline-form-actions {
  grid-column: 1 / -1;
}
</style>
