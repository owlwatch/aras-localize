<script setup lang="ts">
import { ref, watch } from 'vue'

const props = defineProps<{
  label: string
  modelValue: string
}>()

const emit = defineEmits<{
  'update:modelValue': [value: string]
}>()

const menuOpen = ref(false)
const inputValue = ref('')

function formatIsoDate(value: Date) {
  const year = value.getFullYear()
  const month = String(value.getMonth() + 1).padStart(2, '0')
  const day = String(value.getDate()).padStart(2, '0')

  return `${year}-${month}-${day}`
}

function normalizeValue(value: unknown): string {
  if (typeof value === 'string') {
    return value
  }

  if (value instanceof Date && !Number.isNaN(value.getTime())) {
    return formatIsoDate(value)
  }

  return ''
}

function formatDisplayValue(value: string) {
  if (!value) {
    return ''
  }

  return new Date(`${value}T00:00:00`).toLocaleDateString('en-US', {
    year: 'numeric',
    month: '2-digit',
    day: '2-digit',
  })
}

function isValidDatePart(year: number, month: number, day: number) {
  const parsedDate = new Date(year, month - 1, day)

  return (
    !Number.isNaN(parsedDate.getTime()) &&
    parsedDate.getFullYear() === year &&
    parsedDate.getMonth() === month - 1 &&
    parsedDate.getDate() === day
  )
}

function parseInputValue(value: string) {
  const trimmedValue = value.trim()

  if (!trimmedValue) {
    return ''
  }

  const isoMatch = trimmedValue.match(/^(\d{4})-(\d{1,2})-(\d{1,2})$/)
  if (isoMatch) {
    const [, yearText, monthText, dayText] = isoMatch
    const year = Number(yearText)
    const month = Number(monthText)
    const day = Number(dayText)

    if (isValidDatePart(year, month, day)) {
      return `${yearText}-${String(month).padStart(2, '0')}-${String(day).padStart(2, '0')}`
    }
  }

  const localMatch = trimmedValue.match(/^(\d{1,2})\/(\d{1,2})\/(\d{4})$/)
  if (localMatch) {
    const [, monthText, dayText, yearText] = localMatch
    const year = Number(yearText)
    const month = Number(monthText)
    const day = Number(dayText)

    if (isValidDatePart(year, month, day)) {
      return `${yearText}-${String(month).padStart(2, '0')}-${String(day).padStart(2, '0')}`
    }
  }

  return null
}

watch(
  () => props.modelValue,
  (value) => {
    inputValue.value = formatDisplayValue(value)
  },
  { immediate: true }
)

function updateValue(value: unknown) {
  const normalizedValue = Array.isArray(value) ? normalizeValue(value[0]) : normalizeValue(value)

  if (!normalizedValue) {
    return
  }

  emit('update:modelValue', normalizedValue)
  inputValue.value = formatDisplayValue(normalizedValue)
  menuOpen.value = false
}

function clearValue() {
  inputValue.value = ''
  emit('update:modelValue', '')
}

function commitInputValue() {
  const normalizedValue = parseInputValue(inputValue.value)

  if (normalizedValue === null) {
    inputValue.value = formatDisplayValue(props.modelValue)
    return
  }

  if (normalizedValue === '') {
    clearValue()
    return
  }

  emit('update:modelValue', normalizedValue)
  inputValue.value = formatDisplayValue(normalizedValue)
}
</script>

<template>
  <v-menu v-model="menuOpen" :close-on-content-click="false" location="bottom" :min-width="0">
    <template #activator="{ props: menuProps }">
      <v-text-field
        v-model="inputValue"
        clearable
        hide-details
        :label="label"
        prepend-inner-icon="mdi-calendar"
        variant="outlined"
        v-bind="menuProps"
        @click:clear.stop="clearValue"
        @blur="commitInputValue"
        @keydown.enter.prevent="commitInputValue"
      />
    </template>

    <v-card class="date-picker-menu-card">
      <v-date-picker
        :model-value="modelValue || null"
        hide-header
        @update:model-value="updateValue"
      />
    </v-card>
  </v-menu>
</template>

<style scoped>
.date-picker-menu-card {
  max-width: none;
  width: fit-content;
}
</style>
