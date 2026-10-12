import type { Image } from '@/entities/image'
import { ButtonLink } from '@/shared/ui'

type DownloadImageButtonProps = {
  image: Image
}

export function DownloadImageButton({ image }: DownloadImageButtonProps) {
  return (
    <ButtonLink variant="secondary" href={image.download_url} download={image.original_name}>
      Pobierz
    </ButtonLink>
  )
}
