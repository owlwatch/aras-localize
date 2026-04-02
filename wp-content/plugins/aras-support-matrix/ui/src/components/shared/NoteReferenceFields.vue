<script setup lang="ts">
import { computed, ref, watch } from 'vue'

import type { NoteRecord, NoteType } from '@/types/models'

const noteTypeOptions: Array<{ title: string; value: NoteType }> = [
  { title: 'Info', value: 'info' },
  { title: 'Warning', value: 'warning' },
]

const props = defineProps<{
  model: {
    noteId: number | null
    newNoteTitle: string
    newNoteContent: string
    newNoteType: NoteType
  }
  notes: NoteRecord[]
}>()

const showNewNoteFields = ref(false)
const noteMode = ref<'none' | 'existing' | 'new'>('none')

const hasNewNoteDraft = computed(() => {
  return props.model.newNoteTitle.trim() !== '' || props.model.newNoteContent.trim() !== ''
})

watch(
  () => props.model.noteId,
  (value) => {
    if (noteMode.value === 'new' && hasNewNoteDraft.value) {
      return
    }

    noteMode.value = value ? 'existing' : 'none'
    showNewNoteFields.value = false
  },
  { immediate: true },
)

watch(hasNewNoteDraft, (value) => {
  if (value) {
    noteMode.value = 'new'
    showNewNoteFields.value = true
  }
}, { immediate: true })

function setNoteMode(mode: 'none' | 'existing' | 'new') {
  noteMode.value = mode
  showNewNoteFields.value = mode === 'new'

  if (mode !== 'existing') {
    props.model.noteId = null
  }

  if (mode !== 'new') {
    props.model.newNoteTitle = ''
    props.model.newNoteContent = ''
    props.model.newNoteType = 'info'
  }
}
</script>

<template>
  <div class="note-reference-fields">
    <v-btn-toggle
      :model-value="noteMode"
      color="primary"
      density="compact"
      class="note-mode-toggle"
      mandatory
      variant="outlined"
      @update:model-value="setNoteMode($event)"
    >
      <v-btn value="none">None</v-btn>
      <v-btn value="existing">Choose Existing</v-btn>
      <v-btn value="new">New Note</v-btn>
    </v-btn-toggle>

    <v-select
      v-if="noteMode === 'existing'"
      v-model="model.noteId"
      clearable
      hide-details
      :items="notes"
      item-title="title"
      item-value="id"
      label="Note"
      variant="outlined"
    >
      <template #selection="{ item }">
        <span>{{ item.raw.title }} ({{ item.raw.type }})</span>
      </template>
      <template #item="{ props: itemProps, item }">
        <v-list-item
          v-bind="itemProps"
          :subtitle="item.raw.type"
          :title="item.raw.title"
        />
      </template>
    </v-select>

    <div v-if="showNewNoteFields" class="note-reference-create">
      <div class="note-reference-divider">Create a new note</div>

      <v-text-field
        v-model="model.newNoteTitle"
        hide-details
        label="New Note Title"
        variant="outlined"
      />

      <v-select
        v-model="model.newNoteType"
        hide-details
        :items="noteTypeOptions"
        item-title="title"
        item-value="value"
        label="New Note Type"
        variant="outlined"
      />

      <v-textarea
        v-model="model.newNoteContent"
        hide-details
        label="New Note Content"
        rows="3"
        variant="outlined"
      />
    </div>
  </div>
</template>

<style scoped>
.note-reference-fields {
  display: grid;
  gap: 12px;
}

.note-mode-toggle {
  width: fit-content;
}

.note-mode-toggle :deep(.v-btn) {
  min-width: 0;
  padding-inline: 12px;
  text-transform: none;
}

.note-reference-create {
  display: grid;
  gap: 12px;
}

.note-reference-divider {
  font-size: 0.82rem;
  font-weight: 600;
  color: #4d6179;
}
</style>
