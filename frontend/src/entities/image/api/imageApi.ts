import { httpClient } from '@/shared/api'

import type { ImageDetails, ImagesPage } from '../model/types'

const IMAGES_PER_PAGE = 10

export async function fetchImages(cursor: string | null): Promise<ImagesPage> {
  const { data } = await httpClient.get<ImagesPage>('/images', {
    params: { per_page: IMAGES_PER_PAGE, cursor: cursor ?? undefined },
  })

  return data
}

export async function fetchImage(id: string): Promise<ImageDetails> {
  const { data } = await httpClient.get<{ data: ImageDetails }>(`/images/${id}`)

  return data.data
}
