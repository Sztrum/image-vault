import { useState } from 'react'

import type { Image } from '@/entities/image'
import { Button, Dialog } from '@/shared/ui'

import { ImageDetailsContent } from './ImageDetailsContent'

type ImageDetailsButtonProps = {
  image: Image
}

export function ImageDetailsButton({ image }: ImageDetailsButtonProps) {
  const [isOpen, setIsOpen] = useState(false)
  const close = () => setIsOpen(false)

  return (
    <>
      <Button variant="secondary" onClick={() => setIsOpen(true)}>
        Szczegóły
      </Button>
      <Dialog open={isOpen} title={image.original_name} className="max-w-2xl" onClose={close}>
        <div className="mt-4 overflow-y-auto">
          <ImageDetailsContent imageId={image.id} />
        </div>
        <div className="mt-6 flex justify-end">
          <Button variant="secondary" onClick={close} autoFocus>
            Zamknij
          </Button>
        </div>
      </Dialog>
    </>
  )
}
