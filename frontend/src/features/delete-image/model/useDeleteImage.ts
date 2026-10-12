import {
  useMutation,
  useMutationState,
  useQueryClient,
  type InfiniteData,
} from '@tanstack/react-query'

import { imageKeys, type ImagesPage } from '@/entities/image'

import { deleteImage } from '../api/deleteImage'

const deleteImageKey = (imageId: string) => ['delete-image', imageId] as const

export function useDeleteImage(imageId: string) {
  const queryClient = useQueryClient()

  return useMutation({
    mutationKey: deleteImageKey(imageId),
    mutationFn: () => deleteImage(imageId),
    onMutate: async () => {
      await queryClient.cancelQueries({ queryKey: imageKeys.list() })

      const previous = queryClient.getQueryData<InfiniteData<ImagesPage>>(imageKeys.list())

      queryClient.setQueryData<InfiniteData<ImagesPage>>(imageKeys.list(), (data) =>
        data
          ? {
              ...data,
              pages: data.pages.map((page) => ({
                ...page,
                data: page.data.filter((image) => image.id !== imageId),
              })),
            }
          : data,
      )

      return { previous }
    },
    onError: (_error, _variables, result) => {
      queryClient.setQueryData(imageKeys.list(), result?.previous)
    },
    onSettled: () => queryClient.invalidateQueries({ queryKey: imageKeys.list() }),
  })
}

export function useLastDeleteError(imageId: string): Error | null {
  const errors = useMutationState({
    filters: { mutationKey: deleteImageKey(imageId) },
    select: (mutation) => mutation.state.error,
  })

  return errors.at(-1) ?? null
}
