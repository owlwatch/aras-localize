export type SupportStatus = 'Certified' | 'Supported' | 'End of Life'
export type PublicationStatus = 'draft' | 'publish'

export interface ComponentGroupRecord {
  id: number
  name: string
  slug: string
}

export interface ComponentRecord {
  id: number
  name: string
  description: string
  groups: ComponentGroupRecord[]
}

export interface ReleaseRecord {
  id: number
  name: string
  buildNumber: string
  releaseDate: string
  endOfLifeDate: string
  notes: string
  publicationStatus: PublicationStatus
}

export interface EntryRecord {
  id: number
  componentId: number
  componentName: string
  innovatorReleaseId: number
  releaseName: string
  componentVersionNumber: string
  componentReleaseNumber: string
  status: SupportStatus | ''
  publicationStatus: PublicationStatus
  endOfLifeDate: string
  notes: string
}

export interface StatusRecord {
  id: number
  name: SupportStatus
  slug: string
}

export interface MatrixPayload {
  components: ComponentRecord[]
  releases: ReleaseRecord[]
  entries: EntryRecord[]
  statuses: StatusRecord[]
}

export interface ImportCounts {
  releases: number
  components: number
  entries: number
  updated_entries: number
}

export interface ImportStatus {
  status: 'idle' | 'running' | 'completed' | 'error'
  phase: 'idle' | 'reset' | 'import' | 'done'
  progress: number
  processedRows: number
  totalRows: number
  counts: ImportCounts
  message: string
  lastError: string
  startedAt: number
  finishedAt: number
  reset: boolean
}

export interface WordPressConfig {
  restBase: string
  nonce: string
  isAdmin: boolean
  initialTab: 'public' | 'admin'
  embedScriptUrl: string
  mountSelector: string
}
