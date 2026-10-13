import { useId } from 'react'

import { Button } from './Button'
import { Dialog } from './Dialog'

type ConfirmDialogProps = {
  open: boolean
  title: string
  description?: string
  confirmLabel?: string
  cancelLabel?: string
  isPending?: boolean
  onConfirm: () => void
  onCancel: () => void
}

export function ConfirmDialog({
  open,
  title,
  description,
  confirmLabel = 'Potwierdź',
  cancelLabel = 'Anuluj',
  isPending = false,
  onConfirm,
  onCancel,
}: ConfirmDialogProps) {
  const descriptionId = useId()

  return (
    <Dialog
      open={open}
      title={title}
      role="alertdialog"
      describedBy={description ? descriptionId : undefined}
      closeOnEscape={!isPending}
      onClose={onCancel}
    >
      {description && (
        <p id={descriptionId} className="mt-2 text-sm text-slate-600">
          {description}
        </p>
      )}
      <div className="mt-6 flex justify-end gap-3">
        <Button variant="secondary" onClick={onCancel} disabled={isPending}>
          {cancelLabel}
        </Button>
        <Button variant="danger" onClick={onConfirm} isLoading={isPending} autoFocus>
          {confirmLabel}
        </Button>
      </div>
    </Dialog>
  )
}
