import { useEffect, useRef, useState } from 'react'

import { createPreviewUrl, revokePreviewUrl } from '../lib/previewUrl'
import { fileErrorMessage, uploaderErrorsOf, type UploaderField } from '../lib/uploadErrors'
import { imageFileSchema, type UploaderValues } from './uploadSchema'
import { useRefreshGallery } from './useRefreshGallery'
import { useUploadImage } from './useUploadImage'

export type UploadStatus = 'queued' | 'uploading' | 'done' | 'error'

export type UploadItem = {
  key: string
  file: File
  previewUrl: string | null
  status: UploadStatus
  message?: string
}

export type UploadResult = {
  sent: number
  failed: number
  uploaderErrors: Partial<Record<UploaderField, string>> | null
}

export function useUploadQueue() {
  const [items, setItems] = useState<UploadItem[]>([])
  const selectedRef = useRef<UploadItem[]>([])
  const { mutateAsync } = useUploadImage()
  const refreshGallery = useRefreshGallery()

  const update = (key: string, patch: Partial<UploadItem>) =>
    setItems((current) => current.map((item) => (item.key === key ? { ...item, ...patch } : item)))

  const select = (files: File[]) => {
    selectedRef.current.forEach((item) => revokePreviewUrl(item.previewUrl))
    selectedRef.current = files.map((file) => ({
      key: crypto.randomUUID(),
      file,
      previewUrl: createPreviewUrl(file),
      status: 'queued',
    }))
    setItems(selectedRef.current)
  }

  const upload = async (uploader: UploaderValues): Promise<UploadResult> => {
    const result: UploadResult = { sent: 0, failed: 0, uploaderErrors: null }

    for (const item of items.filter(({ status }) => status !== 'done')) {
      const check = imageFileSchema.safeParse(item.file)
      if (!check.success) {
        update(item.key, { status: 'error', message: check.error.issues[0]?.message })
        result.failed++
        continue
      }

      update(item.key, { status: 'uploading', message: undefined })
      try {
        update(item.key, {
          status: 'done',
          message: await mutateAsync({ ...uploader, file: item.file }),
        })
        result.sent++
      } catch (error) {
        result.uploaderErrors = uploaderErrorsOf(error)
        if (result.uploaderErrors) {
          update(item.key, { status: 'queued' })
          break
        }

        update(item.key, { status: 'error', message: fileErrorMessage(error) })
        result.failed++
      }
    }

    if (result.sent > 0) {
      await refreshGallery()
    }

    return result
  }

  useEffect(() => {
    const selected = selectedRef

    return () => selected.current.forEach((item) => revokePreviewUrl(item.previewUrl))
  }, [])

  return { items, select, upload }
}
