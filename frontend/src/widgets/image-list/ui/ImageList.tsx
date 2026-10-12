import { ImageCard, useInfiniteImages } from '@/entities/image'
import { DeleteImageButton } from '@/features/delete-image'
import { DownloadImageButton } from '@/features/download-image'
import { ErrorMessage, Spinner } from '@/shared/ui'

import { useInfiniteScroll } from '../model/useInfiniteScroll'

export function ImageList() {
  const {
    data,
    error,
    isPending,
    isError,
    isFetchNextPageError,
    isFetchingNextPage,
    hasNextPage,
    fetchNextPage,
    refetch,
  } = useInfiniteImages()
  const sentinelRef = useInfiniteScroll({ hasNextPage, isFetchingNextPage, fetchNextPage })

  if (isPending) {
    return (
      <div className="flex justify-center py-12">
        <Spinner label="Ładowanie obrazów…" className="size-8" />
      </div>
    )
  }

  if (isError && !isFetchNextPageError) {
    return <ErrorMessage message={error.message} onRetry={() => void refetch()} />
  }

  const images = data.pages.flatMap((page) => page.data)

  if (images.length === 0) {
    return (
      <p className="py-12 text-center text-sm text-slate-600">
        Nie ma jeszcze żadnych obrazów. Dodaj pierwszy.
      </p>
    )
  }

  return (
    <div className="space-y-6">
      <ul className="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
        {images.map((image) => (
          <li key={image.id}>
            <ImageCard
              image={image}
              actions={
                <>
                  <DownloadImageButton image={image} />
                  <DeleteImageButton image={image} />
                </>
              }
            />
          </li>
        ))}
      </ul>

      {isFetchNextPageError && (
        <ErrorMessage message={error.message} onRetry={() => void fetchNextPage()} />
      )}
      {isFetchingNextPage && (
        <div className="flex justify-center">
          <Spinner label="Ładowanie kolejnych obrazów…" />
        </div>
      )}
      {!hasNextPage && (
        <p className="text-center text-sm text-slate-500">To już wszystkie obrazy.</p>
      )}
      <div ref={sentinelRef} aria-hidden="true" />
    </div>
  )
}
