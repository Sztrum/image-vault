import { useEffect, useState } from 'react'

import { createPreviewUrl, revokePreviewUrl } from '../lib/previewUrl'
import { fileErrorMessage, uploaderErrorsOf, type UploaderField } from '../lib/uploadErrors'
import { imageFileSchema, type UploaderValues } from './uploadSchema'
import { useRefreshGallery } from './useRefreshGallery'
import { useUploadImage } from './useUploadImage'

export type UploadStatus = 'queued' | 'uploading' | 'done' | 'error'

type UploadEntry = {
  key: string
  file: File
  previewUrl: string | null
}

type UploadProgress = {
  status: UploadStatus
  message?: string
}

export type UploadItem = UploadEntry & UploadProgress

export type UploadResult = {
  sent: number
  failed: number
  uploaderErrors: Partial<Record<UploaderField, string>> | null
}

const QUEUED: UploadProgress = { status: 'queued' }

export function useUploadQueue() {
  const [entries, setEntries] = useState<UploadEntry[]>([])
  const [progress, setProgress] = useState<Record<string, UploadProgress>>({})
  const { mutateAsync } = useUploadImage()
  const refreshGallery = useRefreshGallery()

  const items: UploadItem[] = entries.map((entry) => ({
    ...entry,
    ...(progress[entry.key] ?? QUEUED),
  }))

  const setItemProgress = (key: string, value: UploadProgress) =>
    setProgress((current) => ({ ...current, [key]: value }))

  const select = (files: File[]) => {
    setEntries(
      files.map((file) => ({ key: crypto.randomUUID(), file, previewUrl: createPreviewUrl(file) })),
    )
    setProgress({})
  }

  const clear = () => select([])

  const upload = async (uploader: UploaderValues): Promise<UploadResult> => {
    const result: UploadResult = { sent: 0, failed: 0, uploaderErrors: null }

    for (const item of items.filter(({ status }) => status !== 'done')) {
      const check = imageFileSchema.safeParse(item.file)
      if (!check.success) {
        setItemProgress(item.key, { status: 'error', message: check.error.issues[0]?.message })
        result.failed++
        continue
      }

      setItemProgress(item.key, { status: 'uploading' })
      try {
        setItemProgress(item.key, {
          status: 'done',
          message: await mutateAsync({ ...uploader, file: item.file }),
        })
        result.sent++
      } catch (error) {
        result.uploaderErrors = uploaderErrorsOf(error)
        if (result.uploaderErrors) {
          setItemProgress(item.key, QUEUED)
          break
        }

        setItemProgress(item.key, { status: 'error', message: fileErrorMessage(error) })
        result.failed++
      }
    }

    if (result.sent > 0) {
      await refreshGallery()
    }

    return result
  }

  useEffect(() => () => entries.forEach((entry) => revokePreviewUrl(entry.previewUrl)), [entries])

  return { items, select, clear, upload }
}
