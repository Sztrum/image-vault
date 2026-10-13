import { useEffect, useRef } from 'react'

type UseInfiniteScrollOptions = {
  hasNextPage: boolean
  isFetchingNextPage: boolean
  isFetchNextPageError: boolean
  fetchNextPage: () => unknown
}

export function useInfiniteScroll({
  hasNextPage,
  isFetchingNextPage,
  isFetchNextPageError,
  fetchNextPage,
}: UseInfiniteScrollOptions) {
  const sentinelRef = useRef<HTMLDivElement>(null)

  useEffect(() => {
    const sentinel = sentinelRef.current

    if (!sentinel || !hasNextPage || isFetchingNextPage || isFetchNextPageError) {
      return
    }

    const observer = new IntersectionObserver(
      (entries) => {
        if (entries.some((entry) => entry.isIntersecting)) {
          fetchNextPage()
        }
      },
      { rootMargin: '300px' },
    )

    observer.observe(sentinel)

    return () => observer.disconnect()
  }, [hasNextPage, isFetchingNextPage, isFetchNextPageError, fetchNextPage])

  return sentinelRef
}
