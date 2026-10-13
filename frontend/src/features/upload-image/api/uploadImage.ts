import { httpClient } from '@/shared/api'

import type { UploadImageValues } from '../model/uploadSchema'

type UploadImageResponse = {
  message: string
}

export async function uploadImage(values: UploadImageValues): Promise<string> {
  const formData = new FormData()
  formData.append('file', values.file)
  formData.append('uploader_name', values.uploader_name)
  formData.append('uploader_email', values.uploader_email)

  const { data } = await httpClient.post<UploadImageResponse>('/images', formData)

  return data.message
}
