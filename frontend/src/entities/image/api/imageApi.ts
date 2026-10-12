import { httpClient } from '@/shared/api'

import type { ImagesPage } from '../model/types'

export const IMAGES_PER_PAGE = 10

export async function fetchImages(cursor: string | null): Promise<ImagesPage> {
  const { data } = await httpClient.get<ImagesPage>('/images', {
    params: { per_page: IMAGES_PER_PAGE, cursor: cursor ?? undefined },
  })

  return data
}
