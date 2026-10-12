import { useInfiniteQuery } from '@tanstack/react-query'

import { fetchImages } from '../api/imageApi'
import { imageKeys } from '../api/queryKeys'

export function useInfiniteImages() {
  return useInfiniteQuery({
    queryKey: imageKeys.list(),
    queryFn: ({ pageParam }) => fetchImages(pageParam),
    initialPageParam: null as string | null,
    getNextPageParam: (lastPage) => lastPage.meta.next_cursor,
  })
}
