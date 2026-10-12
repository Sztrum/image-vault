import { useEffect, useRef, useState } from 'react'

const PREVIEWABLE_MIME_TYPES = ['image/jpeg', 'image/png', 'image/webp', 'image/bmp']

export function useFilePreview() {
  const [previewUrl, setPreviewUrl] = useState<string | null>(null)
  const previewUrlRef = useRef<string | null>(null)

  const showPreview = (file: File | null) => {
    if (previewUrlRef.current) {
      URL.revokeObjectURL(previewUrlRef.current)
    }

    previewUrlRef.current =
      file && PREVIEWABLE_MIME_TYPES.includes(file.type) ? URL.createObjectURL(file) : null
    setPreviewUrl(previewUrlRef.current)
  }

  useEffect(() => {
    const previewUrls = previewUrlRef

    return () => {
      if (previewUrls.current) {
        URL.revokeObjectURL(previewUrls.current)
      }
    }
  }, [])

  return { previewUrl, showPreview }
}
