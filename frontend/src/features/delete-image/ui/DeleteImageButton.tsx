import { useState } from 'react'

import type { Image } from '@/entities/image'
import { Button, ConfirmDialog } from '@/shared/ui'

import { useDeleteImage, useLastDeleteError } from '../model/useDeleteImage'

type DeleteImageButtonProps = {
  image: Image
}

export function DeleteImageButton({ image }: DeleteImageButtonProps) {
  const [isConfirmOpen, setIsConfirmOpen] = useState(false)
  const { mutate } = useDeleteImage(image.id)
  const lastError = useLastDeleteError(image.id)

  const confirm = () => {
    setIsConfirmOpen(false)
    mutate()
  }

  return (
    <>
      <Button variant="danger" onClick={() => setIsConfirmOpen(true)}>
        Usuń
      </Button>
      {lastError && (
        <p role="alert" className="w-full text-xs text-red-700">
          Nie udało się usunąć obrazu: {lastError.message}
        </p>
      )}
      <ConfirmDialog
        open={isConfirmOpen}
        title="Usunąć obraz?"
        description={`Obraz „${image.original_name}” zostanie trwale usunięty.`}
        confirmLabel="Usuń"
        onConfirm={confirm}
        onCancel={() => setIsConfirmOpen(false)}
      />
    </>
  )
}
