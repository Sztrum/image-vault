export type Image = {
  id: string
  original_name: string
  extension: string
  size_bytes: number
  width: number
  height: number
  uploader_name: string
  thumbnail_url: string
  download_url: string
  created_at: string
}

export type ImagesPage = {
  data: Image[]
  meta: {
    next_cursor: string | null
  }
}

export type ImageMetadata = Record<string, unknown>

export type ImageDetails = Image & {
  uploader_email: string
  temperature_celsius: number | null
  temperature_measured_at: string | null
  exif: ImageMetadata | null
  iptc: ImageMetadata | null
}
