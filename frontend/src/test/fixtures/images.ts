import type { Image, ImagesPage } from '@/entities/image'

import { apiUrl } from '../apiUrl'

export function makeImage(overrides: Partial<Image> = {}): Image {
  const id = overrides.id ?? crypto.randomUUID()

  return {
    id,
    original_name: `${id}.jpg`,
    extension: 'jpg',
    size_bytes: 1024,
    width: 800,
    height: 600,
    uploader_name: 'Anna',
    thumbnail_url: apiUrl(`/images/${id}/thumbnail`),
    download_url: apiUrl(`/images/${id}/download`),
    created_at: '2026-10-12T12:00:00+02:00',
    ...overrides,
  }
}

export function makeImages(count: number, prefix: string): Image[] {
  return Array.from({ length: count }, (_, index) => makeImage({ id: `${prefix}-${index + 1}` }))
}

export function makePage(data: Image[], nextCursor: string | null = null): ImagesPage {
  return { data, meta: { next_cursor: nextCursor } }
}
