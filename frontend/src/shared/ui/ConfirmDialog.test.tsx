import { render, screen } from '@testing-library/react'
import userEvent from '@testing-library/user-event'
import { describe, expect, it, vi } from 'vitest'

import { ConfirmDialog } from './ConfirmDialog'

describe('ConfirmDialog', () => {
  it('renders nothing when closed', () => {
    render(<ConfirmDialog open={false} title="Usunąć?" onConfirm={vi.fn()} onCancel={vi.fn()} />)

    expect(screen.queryByRole('alertdialog')).not.toBeInTheDocument()
  })

  it('calls handlers for confirm, cancel and escape', async () => {
    const user = userEvent.setup()
    const onConfirm = vi.fn()
    const onCancel = vi.fn()

    render(
      <ConfirmDialog
        open
        title="Usunąć obraz?"
        confirmLabel="Usuń"
        onConfirm={onConfirm}
        onCancel={onCancel}
      />,
    )

    expect(screen.getByRole('alertdialog', { name: 'Usunąć obraz?' })).toBeInTheDocument()

    await user.click(screen.getByRole('button', { name: 'Usuń' }))
    await user.click(screen.getByRole('button', { name: 'Anuluj' }))
    await user.keyboard('{Escape}')

    expect(onConfirm).toHaveBeenCalledTimes(1)
    expect(onCancel).toHaveBeenCalledTimes(2)
  })

  it('disables actions while pending', () => {
    render(<ConfirmDialog open isPending title="Usunąć?" onConfirm={vi.fn()} onCancel={vi.fn()} />)

    expect(screen.getByRole('button', { name: 'Anuluj' })).toBeDisabled()
    expect(screen.getByRole('button', { name: /Potwierdź/ })).toBeDisabled()
  })
})
