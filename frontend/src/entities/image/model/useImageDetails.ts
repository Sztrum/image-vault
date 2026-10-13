import { useQuery } from '@tanstack/react-query'

import { fetchImage } from '../api/imageApi'
import { imageKeys } from '../api/queryKeys'

export function useImageDetails(id: string) {
  return useQuery({
    queryKey: imageKeys.detail(id),
    queryFn: () => fetchImage(id),
  })
}
