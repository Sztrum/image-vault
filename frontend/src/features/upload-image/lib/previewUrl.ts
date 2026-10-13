const PREVIEWABLE_MIME_TYPES = ['image/jpeg', 'image/png', 'image/webp', 'image/bmp']

export function createPreviewUrl(file: File): string | null {
  return PREVIEWABLE_MIME_TYPES.includes(file.type) ? URL.createObjectURL(file) : null
}

export function revokePreviewUrl(url: string | null): void {
  if (url) {
    URL.revokeObjectURL(url)
  }
}
