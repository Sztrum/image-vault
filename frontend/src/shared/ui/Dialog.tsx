import { useEffect, useId, type ReactNode } from 'react'

type DialogProps = {
  open: boolean
  title: string
  role?: 'dialog' | 'alertdialog'
  describedBy?: string
  closeOnEscape?: boolean
  className?: string
  onClose: () => void
  children: ReactNode
}

export function Dialog({
  open,
  title,
  role = 'dialog',
  describedBy,
  closeOnEscape = true,
  className = 'max-w-sm',
  onClose,
  children,
}: DialogProps) {
  const titleId = useId()

  useEffect(() => {
    if (!open || !closeOnEscape) {
      return
    }

    const handleKeyDown = (event: KeyboardEvent) => {
      if (event.key === 'Escape') {
        onClose()
      }
    }

    document.addEventListener('keydown', handleKeyDown)

    return () => document.removeEventListener('keydown', handleKeyDown)
  }, [open, closeOnEscape, onClose])

  if (!open) {
    return null
  }

  return (
    <div className="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 p-4">
      <div
        role={role}
        aria-modal="true"
        aria-labelledby={titleId}
        aria-describedby={describedBy}
        className={`flex max-h-full w-full flex-col rounded-lg bg-white p-6 shadow-xl ${className}`}
      >
        <h2 id={titleId} className="text-base font-semibold break-all text-slate-900">
          {title}
        </h2>
        {children}
      </div>
    </div>
  )
}
