import { useMutation, useQueryClient } from '@tanstack/react-query'

import { imageKeys } from '@/entities/image'

import { uploadImage } from '../api/uploadImage'

export function useUploadImage() {
  const queryClient = useQueryClient()

  return useMutation({
    mutationFn: uploadImage,
    onSuccess: () => queryClient.invalidateQueries({ queryKey: imageKeys.list() }),
  })
}
