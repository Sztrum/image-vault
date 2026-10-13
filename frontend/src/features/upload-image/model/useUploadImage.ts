import { useMutation, useQueryClient, type InfiniteData } from '@tanstack/react-query'

import { imageKeys, type ImagesPage } from '@/entities/image'

import { uploadImage } from '../api/uploadImage'

export function useUploadImage() {
  const queryClient = useQueryClient()

  return useMutation({
    mutationFn: uploadImage,
    onSuccess: () => {
      queryClient.setQueryData<InfiniteData<ImagesPage>>(imageKeys.list(), (data) =>
        data ? { pages: data.pages.slice(0, 1), pageParams: data.pageParams.slice(0, 1) } : data,
      )

      return queryClient.invalidateQueries({ queryKey: imageKeys.list() })
    },
  })
}
