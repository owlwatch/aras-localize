<script setup lang="ts">
import { computed, reactive, ref, watch } from 'vue'

import ComponentFormFields from '@/components/shared/ComponentFormFields.vue'
import { api } from '@/composables/api'
import type { ComponentRecord, EntryRecord } from '@/types/models'

type SortDirection = 'asc' | 'desc'
type ComponentSortKey = 'name' | 'description' | 'groups'

const props = defineProps<{
  components: ComponentRecord[]
  entries: EntryRecord[]
  showIdColumns: boolean
}>()

const emit = defineEmits<{
  'update:components': [value: ComponentRecord[]]
  'update:entries': [value: EntryRecord[]]
}>()

const loading = ref(false)
const editingId = ref<number | null>(null)
const showNewDialog = ref(false)
const confirmDeleteOpen = ref(false)
const confirmDeleteTitle = ref('')
const confirmDeleteMessage = ref('')
const confirmDeleteAction = ref<null | (() => Promise<void>)>(null)
const selectedIds = ref<number[]>([])
const batchGroupName = ref('')

const sortState = reactive<{ key: ComponentSortKey; direction: SortDirection }>({
  key: 'name',
  direction: 'asc',
})

const componentsState = ref<ComponentRecord[]>([])
const entriesState = ref<EntryRecord[]>([])

const form = reactive<ComponentRecord>({
  id: 0,
  name: '',
  description: '',
  groups: [],
})

watch(() => props.components, (value) => { componentsState.value = [...value] }, { immediate: true })
watch(() => props.entries, (value) => { entriesState.value = [...value] }, { immediate: true })

const sortedComponents = computed(() => {
  return [...componentsState.value].sort((left, right) => {
    const leftValue = sortState.key === 'groups' ? left.groups.join(', ') : left[sortState.key]
    const rightValue = sortState.key === 'groups' ? right.groups.join(', ') : right[sortState.key]
    const result = String(leftValue).localeCompare(String(rightValue), undefined, { numeric: true, sensitivity: 'base' })
    return sortState.direction === 'asc' ? result : -result
  })
})

const groupOptions = computed(() => {
  return Array.from(new Set(componentsState.value.flatMap((component) => component.groups).filter(Boolean))).sort(
    (left, right) => left.localeCompare(right, undefined, { sensitivity: 'base' }),
  )
})

const allVisibleSelected = computed(() => {
  return sortedComponents.value.length > 0 && sortedComponents.value.every((item) => selectedIds.value.includes(item.id))
})

const columnCount = computed(() => (props.showIdColumns ? 5 : 4))

function sortIndicator(active: boolean, direction: SortDirection) {
  if (!active) return ''
  return direction === 'asc' ? ' mdi-chevron-up' : ' mdi-chevron-down'
}

function syncComponents(nextComponents: ComponentRecord[]) {
  componentsState.value = nextComponents
  emit('update:components', nextComponents)
}

function syncEntries(nextEntries: EntryRecord[]) {
  entriesState.value = nextEntries
  emit('update:entries', nextEntries)
}

function resetForm() {
  Object.assign(form, { id: 0, name: '', description: '', groups: [] })
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

function editItem(item: ComponentRecord) {
  Object.assign(form, item)
  editingId.value = item.id
}

function toggleSort(key: ComponentSortKey) {
  if (sortState.key === key) {
    sortState.direction = sortState.direction === 'asc' ? 'desc' : 'asc'
    return
  }

  sortState.key = key
  sortState.direction = 'asc'
}

function toggleAll(selected: boolean | null) {
  selectedIds.value = selected ? sortedComponents.value.map((item) => item.id) : []
}

function toggleOne(id: number, selected: boolean | null) {
  selectedIds.value = selected
    ? Array.from(new Set([...selectedIds.value, id]))
    : selectedIds.value.filter((current) => current !== id)
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
    description: form.description,
    groups: form.groups,
  }

  const saved = await runWithLoading(() =>
    form.id ? api.updateComponent({ ...form }) : api.createComponent(payload),
  )

  const nextComponents = [...componentsState.value]
  upsertById(nextComponents, saved)
  syncComponents(nextComponents)
  syncEntries(
    entriesState.value.map((entry) =>
      entry.componentId === saved.id ? { ...entry, componentName: saved.name } : entry,
    ),
  )

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
  if (!confirmDeleteAction.value) {
    return
  }

  try {
    await confirmDeleteAction.value()
  } finally {
    closeDeleteDialog()
  }
}

function removeItem(item: ComponentRecord) {
  openDeleteDialog(
    'Delete Component',
    `Delete "${item.name}" and all related compatibility entries?`,
    async () => {
      await runWithLoading(() => api.deleteComponent(item.id))
      syncComponents(componentsState.value.filter((component) => component.id !== item.id))
      syncEntries(entriesState.value.filter((entry) => entry.componentId !== item.id))
      selectedIds.value = selectedIds.value.filter((id) => id !== item.id)
    },
  )
}

function removeSelected() {
  const selectedItems = componentsState.value.filter((component) => selectedIds.value.includes(component.id))
  if (!selectedItems.length) {
    return
  }

  openDeleteDialog(
    'Delete Components',
    `Delete ${selectedItems.length} selected components and all related compatibility entries?`,
    async () => {
      for (const item of selectedItems) {
        await runWithLoading(() => api.deleteComponent(item.id))
      }

      const removedIds = new Set(selectedItems.map((item) => item.id))
      syncComponents(componentsState.value.filter((component) => !removedIds.has(component.id)))
      syncEntries(entriesState.value.filter((entry) => !removedIds.has(entry.componentId)))
      selectedIds.value = []
    },
  )
}

async function addSelectedToGroup() {
  const groupName = batchGroupName.value.trim()
  const selectedItems = componentsState.value.filter((component) => selectedIds.value.includes(component.id))

  if (!groupName || !selectedItems.length) {
    return
  }

  const nextComponents = [...componentsState.value]

  await runWithLoading(async () => {
    for (const item of selectedItems) {
      const groups = Array.from(new Set([...item.groups, groupName]))
      const saved = await api.updateComponent({ ...item, groups })
      upsertById(nextComponents, saved)
    }
  })

  syncComponents(nextComponents)
  batchGroupName.value = ''
}
</script>

<template>
  <div class="button-row">
    <v-btn color="primary" @click="openNew">Add Component</v-btn>
  </div>

  <div v-if="selectedIds.length" class="button-row">
    <v-chip color="primary" variant="tonal">{{ selectedIds.length }} selected</v-chip>
    <v-combobox
      v-model="batchGroupName"
      class="batch-group-input"
      hide-details
      :items="groupOptions"
      label="Add To Group"
      variant="outlined"
    />
    <v-btn :disabled="!batchGroupName.trim()" color="primary" variant="outlined" @click="addSelectedToGroup">Add To Group</v-btn>
    <v-btn color="error" variant="outlined" @click="removeSelected">Delete Selected</v-btn>
  </div>

  <v-table>
    <thead>
      <tr>
        <th class="checkbox-cell">
          <v-checkbox-btn :model-value="allVisibleSelected" @update:model-value="toggleAll" />
        </th>
        <th v-if="showIdColumns">ID</th>
        <th><button class="sort-button" type="button" @click="toggleSort('name')"><span>Name</span><v-icon v-if="sortState.key === 'name'" class="sort-icon" :icon="sortIndicator(true, sortState.direction)" size="16" /></button></th>
        <th><button class="sort-button" type="button" @click="toggleSort('description')"><span>Description</span><v-icon v-if="sortState.key === 'description'" class="sort-icon" :icon="sortIndicator(true, sortState.direction)" size="16" /></button></th>
        <th><button class="sort-button" type="button" @click="toggleSort('groups')"><span>Groups</span><v-icon v-if="sortState.key === 'groups'" class="sort-icon" :icon="sortIndicator(true, sortState.direction)" size="16" /></button></th>
        <th></th>
      </tr>
    </thead>
    <tbody>
      <template v-for="item in sortedComponents" :key="item.id">
        <tr v-if="editingId === item.id">
          <td :colspan="columnCount + 1">
            <div class="inline-form-grid">
              <ComponentFormFields :model="form" :group-options="groupOptions" />
              <div class="button-row inline-form-actions">
                <v-btn :loading="loading" color="primary" @click="submit">Save</v-btn>
                <v-btn variant="text" @click="cancelEdit">Cancel</v-btn>
              </div>
            </div>
          </td>
        </tr>
        <tr v-else>
          <td class="checkbox-cell">
            <v-checkbox-btn :model-value="selectedIds.includes(item.id)" @update:model-value="toggleOne(item.id, $event)" />
          </td>
          <td v-if="showIdColumns">{{ item.id }}</td>
          <td><button class="admin-link-button" type="button" @click="editItem(item)">{{ item.name }}</button></td>
          <td>{{ item.description }}</td>
          <td>{{ item.groups.join(', ') }}</td>
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
      <v-card-title>Add Component</v-card-title>
      <v-card-text class="inline-form-grid">
        <ComponentFormFields :model="form" :group-options="groupOptions" />
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

.checkbox-cell {
  width: 48px;
}

.batch-group-input {
  min-width: 240px;
  max-width: 320px;
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

.inline-form-actions {
  grid-column: 1 / -1;
}
</style>
