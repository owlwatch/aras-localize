import type {
  ComponentGroupRecord,
  ComponentRecord,
  EntryRecord,
  ImportStatus,
  MatrixPayload,
  NoteRecord,
  ReleaseRecord,
  WordPressConfig,
} from '@/types/models'

type ComponentPayload = Omit<ComponentRecord, 'id' | 'groups'> & {
  groups: Array<ComponentGroupRecord | number | string>
}

type CreateReleaseResponse = {
  release: ReleaseRecord
  entries: EntryRecord[]
}

declare global {
  interface Window {
    ArasSupportMatrixConfig?: Partial<WordPressConfig>
  }
}

const fallbackConfig: WordPressConfig = {
  restBase: '/wp-json/aras-support-matrix/v1',
  nonce: '',
  isAdmin: true,
  initialTab: 'public',
  embedScriptUrl: '/wp-content/plugins/aras-support-matrix/ui/dist/embed.js',
  mountSelector: '#aras-support-matrix-app',
}

export function getConfig(): WordPressConfig {
  return {
    ...fallbackConfig,
    ...window.ArasSupportMatrixConfig,
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
  createComponent(payload: ComponentPayload) {
    return request<ComponentRecord>('/components', {
      method: 'POST',
      body: JSON.stringify(payload),
    })
  },
  updateComponent(payload: Omit<ComponentRecord, 'groups'> & { id: number; groups: Array<ComponentGroupRecord | number | string> }) {
    return request<ComponentRecord>(`/components/${payload.id}`, {
      method: 'PUT',
      body: JSON.stringify(payload),
    })
  },
  deleteComponent(id: number) {
    return request<{ deleted: boolean }>(`/components/${id}`, { method: 'DELETE' })
  },
  createRelease(payload: Omit<ReleaseRecord, 'id'>) {
    return request<CreateReleaseResponse>('/releases', {
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
  createEntry(payload: Omit<EntryRecord, 'id' | 'componentName' | 'releaseName'>) {
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
  createNote(payload: Omit<NoteRecord, 'id'>) {
    return request<NoteRecord>('/notes', {
      method: 'POST',
      body: JSON.stringify(payload),
    })
  },
  updateNote(payload: NoteRecord) {
    return request<NoteRecord>(`/notes/${payload.id}`, {
      method: 'PUT',
      body: JSON.stringify(payload),
    })
  },
  deleteNote(id: number) {
    return request<{ deleted: boolean }>(`/notes/${id}`, { method: 'DELETE' })
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
