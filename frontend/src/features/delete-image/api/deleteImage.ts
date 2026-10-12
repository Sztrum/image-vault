import { httpClient } from '@/shared/api'

export async function deleteImage(imageId: string): Promise<void> {
  await httpClient.delete(`/images/${imageId}`)
}
