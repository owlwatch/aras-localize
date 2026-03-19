import type {
  ComponentRecord,
  EmbedConfig,
  EntryRecord,
  ImportStatus,
  MatrixPayload,
  ReleaseRecord,
  WordPressConfig,
} from '@/types/models'

declare global {
  interface Window {
    ArasSupportMatrixConfig?: Partial<WordPressConfig>
    ArasSupportMatrixEmbedConfig?: Partial<EmbedConfig>
  }
}

const fallbackConfig: WordPressConfig = {
  restBase: '/wp-json/aras-support-matrix/v1',
  nonce: '',
  isAdmin: true,
  initialTab: 'public',
}

export function getConfig(): WordPressConfig {
  return {
    ...fallbackConfig,
    ...window.ArasSupportMatrixConfig,
  }
}

const fallbackEmbedConfig: EmbedConfig = {
  restBase: '/wp-json/aras-support-matrix/v1',
  mountSelector: '#aras-support-matrix-embed',
}

export function getEmbedConfig(): EmbedConfig {
  return {
    ...fallbackEmbedConfig,
    ...window.ArasSupportMatrixEmbedConfig,
  }
}

async function request<T>(path: string, init: RequestInit = {}): Promise<T> {
  const config = getConfig()
  const response = await fetch(`${config.restBase}${path}`, {
    ...init,
    headers: {
      'Content-Type': 'application/json',
      ...(config.nonce ? { 'X-WP-Nonce': config.nonce } : {}),
      ...(init.headers ?? {}),
    },
  })

  if (!response.ok) {
    const text = await response.text()
    throw new Error(text || `Request failed: ${response.status}`)
  }

  return response.json() as Promise<T>
}

export const api = {
  getMatrix() {
    return request<MatrixPayload>('/matrix')
  },
  createComponent(payload: Omit<ComponentRecord, 'id'>) {
    return request<ComponentRecord>('/components', {
      method: 'POST',
      body: JSON.stringify(payload),
    })
  },
  updateComponent(payload: ComponentRecord) {
    return request<ComponentRecord>(`/components/${payload.id}`, {
      method: 'PUT',
      body: JSON.stringify(payload),
    })
  },
  deleteComponent(id: number) {
    return request<{ deleted: boolean }>(`/components/${id}`, { method: 'DELETE' })
  },
  createRelease(payload: Omit<ReleaseRecord, 'id'>) {
    return request<ReleaseRecord>('/releases', {
      method: 'POST',
      body: JSON.stringify(payload),
    })
  },
  updateRelease(payload: ReleaseRecord) {
    return request<ReleaseRecord>(`/releases/${payload.id}`, {
      method: 'PUT',
      body: JSON.stringify(payload),
    })
  },
  deleteRelease(id: number) {
    return request<{ deleted: boolean }>(`/releases/${id}`, { method: 'DELETE' })
  },
  createEntry(payload: Omit<EntryRecord, 'id' | 'componentName' | 'releaseName' | 'publicationStatus'>) {
    return request<EntryRecord>('/entries', {
      method: 'POST',
      body: JSON.stringify(payload),
    })
  },
  updateEntry(payload: EntryRecord) {
    return request<EntryRecord>(`/entries/${payload.id}`, {
      method: 'PUT',
      body: JSON.stringify(payload),
    })
  },
  deleteEntry(id: number) {
    return request<{ deleted: boolean }>(`/entries/${id}`, { method: 'DELETE' })
  },
  getImportStatus() {
    return request<ImportStatus>('/import/status')
  },
  startImport(reset = false) {
    return request<ImportStatus>('/import/start', {
      method: 'POST',
      body: JSON.stringify({ reset }),
    })
  },
  runImportStep() {
    return request<ImportStatus>('/import/step', {
      method: 'POST',
    })
  },
}
