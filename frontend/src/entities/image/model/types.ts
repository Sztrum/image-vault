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
