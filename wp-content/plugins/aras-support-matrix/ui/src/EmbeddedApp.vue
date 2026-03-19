<script setup lang="ts">
import { onMounted, ref } from 'vue'

import PublicMatrix from '@/components/PublicMatrix.vue'
import { api } from '@/composables/api'
import type { MatrixPayload } from '@/types/models'

const data = ref<MatrixPayload>({
  components: [],
  releases: [],
  entries: [],
  statuses: [],
})
const loading = ref(true)
const error = ref('')

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

onMounted(loadData)
</script>

<template>
  <div class="aras-support-matrix-root">
    <v-app>
      <v-main class="app-shell">
        <v-container class="app-container embed-container" max-width="1400">
          <v-alert v-if="error" class="mb-6" type="error" variant="tonal">
            {{ error }}
          </v-alert>

          <v-skeleton-loader v-if="loading" type="article, table, article" />

          <PublicMatrix
            v-else
            :components="data.components"
            :entries="data.entries"
            :is-admin="false"
            :releases="data.releases"
            :statuses="data.statuses"
          />
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

.app-container {
  position: relative;
  padding-top: 48px;
  padding-bottom: 72px;
}

.embed-container {
  padding-top: 0;
  padding-bottom: 0;
}
</style>
