<script setup lang="ts">
import { computed, reactive, ref, watch } from 'vue'

import ComponentFormFields from '@/components/shared/ComponentFormFields.vue'
import { api } from '@/composables/api'
import type { ComponentGroupRecord, ComponentRecord, EntryRecord, PublicationStatus } from '@/types/models'

type SortDirection = 'asc' | 'desc'
type ComponentSortKey = 'name' | 'description' | 'groups' | 'publicationStatus'

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

const sortState = reactive<{ key: ComponentSortKey; direction: SortDirection }>({
  key: 'name',
  direction: 'asc',
})

const componentsState = ref<ComponentRecord[]>([])
const entriesState = ref<EntryRecord[]>([])
const inlineDrafts = reactive<Record<number, Pick<ComponentRecord, 'name' | 'description' | 'publicationStatus'> & {
  groups: Array<ComponentGroupRecord | string>
}>>({})
const savingRowIds = reactive<Record<number, boolean>>({})
const cellMenus = reactive<Record<string, boolean>>({})
const publicationStatusOptions = [
  { title: 'Draft', value: 'draft' as const },
  { title: 'Published', value: 'publish' as const },
]

const form = reactive<ComponentRecord>({
  id: 0,
  name: '',
  description: '',
  groups: [],
  publicationStatus: 'draft',
})

watch(() => props.components, (value) => { componentsState.value = [...value] }, { immediate: true })
watch(() => props.entries, (value) => { entriesState.value = [...value] }, { immediate: true })
watch(
  componentsState,
  (value) => {
    const activeIds = new Set<number>()

    value.forEach((component) => {
      activeIds.add(component.id)
      if (savingRowIds[component.id] && inlineDrafts[component.id]) {
        return
      }

      inlineDrafts[component.id] = {
        name: component.name,
        description: component.description,
        groups: [...component.groups],
        publicationStatus: component.publicationStatus,
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

const sortedComponents = computed(() => {
  return [...componentsState.value].sort((left, right) => {
    const leftValue = sortState.key === 'groups' ? groupNames(left.groups) : left[sortState.key]
    const rightValue = sortState.key === 'groups' ? groupNames(right.groups) : right[sortState.key]
    const result = String(leftValue).localeCompare(String(rightValue), undefined, { numeric: true, sensitivity: 'base' })
    return sortState.direction === 'asc' ? result : -result
  })
})

const groupOptions = computed(() => {
  const uniqueGroups = new Map<number, ComponentGroupRecord>()

  componentsState.value.forEach((component) => {
    component.groups.forEach((group) => {
      uniqueGroups.set(group.id, group)
    })
  })

  return Array.from(uniqueGroups.values()).sort((left, right) =>
    left.name.localeCompare(right.name, undefined, { sensitivity: 'base' }),
  )
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

function groupNames(groups: ComponentGroupRecord[]) {
  return groups.map((group) => group.name).join(', ')
}

function publicationStatusLabel(status: PublicationStatus) {
  return status === 'publish' ? 'Published' : 'Draft'
}

function publicationStatusColor(status: PublicationStatus) {
  return status === 'publish' ? 'success' : 'error'
}

function rowDraft(item: ComponentRecord) {
  return inlineDrafts[item.id]
}

function cellMenuKey(itemId: number, field: 'name' | 'description' | 'groups' | 'publicationStatus') {
  return `${itemId}:${field}`
}

function closeCellMenu(itemId: number, field: 'name' | 'description' | 'groups' | 'publicationStatus') {
  cellMenus[cellMenuKey(itemId, field)] = false
}

function buildComponentUpdatePayload(item: ComponentRecord) {
  const draft = rowDraft(item)

  return {
    ...item,
    name: draft.name.trim(),
    description: draft.description.trim(),
    groups: draft.groups,
    publicationStatus: draft.publicationStatus,
  }
}

async function saveInlineItem(item: ComponentRecord) {
  const draft = rowDraft(item)

  if (!draft || savingRowIds[item.id]) {
    return
  }

  const nextPayload = buildComponentUpdatePayload(item)

  if (
    nextPayload.name === item.name
    && nextPayload.description === item.description
    && JSON.stringify(nextPayload.groups) === JSON.stringify(item.groups)
    && nextPayload.publicationStatus === item.publicationStatus
  ) {
    return
  }

  savingRowIds[item.id] = true

  try {
    const saved = await api.updateComponent(nextPayload)
    inlineDrafts[item.id] = {
      name: saved.name,
      description: saved.description,
      groups: [...saved.groups],
      publicationStatus: saved.publicationStatus,
    }
    const nextComponents = [...componentsState.value]
    upsertById(nextComponents, saved)
    syncComponents(nextComponents)
    syncEntries(
      entriesState.value.map((entry) =>
        entry.componentId === saved.id ? { ...entry, componentName: saved.name } : entry,
      ),
    )
  } finally {
    savingRowIds[item.id] = false
  }
}

async function saveInlineCell(item: ComponentRecord, field: 'name' | 'description' | 'groups' | 'publicationStatus') {
  await saveInlineItem(item)
  closeCellMenu(item.id, field)
}

async function onPublicationToggle(item: ComponentRecord, value: boolean | null) {
  const draft = rowDraft(item)

  if (!draft) {
    return
  }

  draft.publicationStatus = value ? 'publish' : 'draft'
  await saveInlineItem(item)
}

function resetForm() {
  Object.assign(form, { id: 0, name: '', description: '', groups: [], publicationStatus: 'draft' })
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
    publicationStatus: form.publicationStatus,
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
    },
  )
}
</script>

<template>
  <div class="button-row">
    <v-btn color="primary" @click="openNew">Add Component</v-btn>
  </div>

  <v-table>
    <thead>
      <tr>
        <th v-if="showIdColumns">ID</th>
        <th><button class="sort-button" type="button" @click="toggleSort('name')"><span>Name</span><v-icon v-if="sortState.key === 'name'" class="sort-icon" :icon="sortIndicator(true, sortState.direction)" size="16" /></button></th>
        <th><button class="sort-button" type="button" @click="toggleSort('description')"><span>Description</span><v-icon v-if="sortState.key === 'description'" class="sort-icon" :icon="sortIndicator(true, sortState.direction)" size="16" /></button></th>
        <th><button class="sort-button" type="button" @click="toggleSort('groups')"><span>Groups</span><v-icon v-if="sortState.key === 'groups'" class="sort-icon" :icon="sortIndicator(true, sortState.direction)" size="16" /></button></th>
        <th><button class="sort-button" type="button" @click="toggleSort('publicationStatus')"><span>Status</span><v-icon v-if="sortState.key === 'publicationStatus'" class="sort-icon" :icon="sortIndicator(true, sortState.direction)" size="16" /></button></th>
        <th></th>
      </tr>
    </thead>
    <tbody>
      <template v-for="item in sortedComponents" :key="item.id">
        <tr v-if="editingId === item.id">
          <td :colspan="columnCount + 1">
            <div class="inline-form-grid">
              <ComponentFormFields :model="form" :group-options="groupOptions" :publication-status-options="publicationStatusOptions" />
              <div class="button-row inline-form-actions">
                <v-btn :loading="loading" color="primary" @click="submit">Save</v-btn>
                <v-btn variant="text" @click="cancelEdit">Cancel</v-btn>
              </div>
            </div>
          </td>
        </tr>
        <tr v-else>
          <td v-if="showIdColumns">{{ item.id }}</td>
          <td class="entry-inline-cell">
            <v-menu
              v-model="cellMenus[cellMenuKey(item.id, 'name')]"
              :close-on-content-click="false"
              location="bottom start"
            >
              <template #activator="{ props: menuProps }">
                <button class="entry-edit-trigger" type="button" v-bind="menuProps">
                  <span>{{ item.name }}</span>
                  <v-icon class="entry-edit-icon" icon="mdi-pencil-outline" size="14" />
                </button>
              </template>
              <v-card class="entry-edit-menu" min-width="280">
                <v-card-text>
                  <v-text-field
                    v-model="rowDraft(item).name"
                    density="compact"
                    hide-details
                    label="Name"
                    variant="outlined"
                  />
                </v-card-text>
                <v-card-actions>
                  <v-spacer />
                  <v-btn variant="text" @click="closeCellMenu(item.id, 'name')">Cancel</v-btn>
                  <v-btn :loading="savingRowIds[item.id]" color="primary" @click="saveInlineCell(item, 'name')">Save</v-btn>
                </v-card-actions>
              </v-card>
            </v-menu>
          </td>
          <td class="entry-inline-cell">
            <v-menu
              v-model="cellMenus[cellMenuKey(item.id, 'description')]"
              :close-on-content-click="false"
              location="bottom start"
            >
              <template #activator="{ props: menuProps }">
                <button class="entry-edit-trigger entry-edit-trigger--notes" type="button" v-bind="menuProps">
                  <span>{{ item.description || '—' }}</span>
                  <v-icon class="entry-edit-icon" icon="mdi-pencil-outline" size="14" />
                </button>
              </template>
              <v-card class="entry-edit-menu" min-width="320">
                <v-card-text>
                  <v-textarea
                    v-model="rowDraft(item).description"
                    density="compact"
                    hide-details
                    label="Description"
                    rows="3"
                    variant="outlined"
                  />
                </v-card-text>
                <v-card-actions>
                  <v-spacer />
                  <v-btn variant="text" @click="closeCellMenu(item.id, 'description')">Cancel</v-btn>
                  <v-btn :loading="savingRowIds[item.id]" color="primary" @click="saveInlineCell(item, 'description')">Save</v-btn>
                </v-card-actions>
              </v-card>
            </v-menu>
          </td>
          <td class="entry-inline-cell">
            <v-menu
              v-model="cellMenus[cellMenuKey(item.id, 'groups')]"
              :close-on-content-click="false"
              location="bottom start"
            >
              <template #activator="{ props: menuProps }">
                <button class="entry-edit-trigger entry-edit-trigger--notes" type="button" v-bind="menuProps">
                  <span>{{ groupNames(item.groups) || '—' }}</span>
                  <v-icon class="entry-edit-icon" icon="mdi-pencil-outline" size="14" />
                </button>
              </template>
              <v-card class="entry-edit-menu" min-width="360">
                <v-card-text>
                  <v-combobox
                    v-model="rowDraft(item).groups"
                    chips
                    closable-chips
                    density="compact"
                    hide-details
                    :items="groupOptions"
                    item-title="name"
                    label="Groups"
                    multiple
                    variant="outlined"
                  />
                </v-card-text>
                <v-card-actions>
                  <v-spacer />
                  <v-btn variant="text" @click="closeCellMenu(item.id, 'groups')">Cancel</v-btn>
                  <v-btn :loading="savingRowIds[item.id]" color="primary" @click="saveInlineCell(item, 'groups')">Save</v-btn>
                </v-card-actions>
              </v-card>
            </v-menu>
          </td>
          <td>
            <v-switch
              class="entry-publish-switch"
              :color="publicationStatusColor(rowDraft(item).publicationStatus)"
              density="compact"
              :disabled="savingRowIds[item.id]"
              hide-details
              :loading="savingRowIds[item.id]"
              :model-value="rowDraft(item).publicationStatus === 'publish'"
              @update:model-value="onPublicationToggle(item, $event)"
            />
          </td>
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
        <ComponentFormFields :model="form" :group-options="groupOptions" :publication-status-options="publicationStatusOptions" />
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
