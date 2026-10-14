import { screen } from '@testing-library/react'
import userEvent from '@testing-library/user-event'
import { http, HttpResponse } from 'msw'
import { describe, expect, it } from 'vitest'

import { GalleryPage } from '@/pages/gallery'
import { apiUrl } from '@/test/apiUrl'
import { makeImage, makePage } from '@/test/fixtures/images'
import { server } from '@/test/msw/server'
import { renderWithQueryClient } from '@/test/renderWithQueryClient'

import { UploadImageForm } from './UploadImageForm'

const pngFile = (name = 'gory.png', size = 1024) =>
  new File([new Uint8Array(size)], name, { type: 'image/png' })

function failOnUpload() {
  server.use(
    http.post(apiUrl('/images'), () => {
      throw new Error('The form must not be sent')
    }),
  )
}

async function fillUploader(user: ReturnType<typeof userEvent.setup>) {
  await user.type(screen.getByLabelText('Imię'), 'Anna')
  await user.type(screen.getByLabelText('E-mail'), 'anna@example.com')
}

describe('UploadImageForm', () => {
  it('shows client-side errors for empty fields without sending the form', async () => {
    const user = userEvent.setup()
    failOnUpload()

    renderWithQueryClient(<UploadImageForm />)

    await user.click(screen.getByRole('button', { name: 'Wyślij' }))

    expect(await screen.findByText('Wybierz co najmniej jeden obraz.')).toBeInTheDocument()
    expect(screen.getByText('Podaj imię.')).toBeInTheDocument()
    expect(screen.getByText('Podaj adres e-mail.')).toBeInTheDocument()
  })

  it.each([
    [
      new File(['gif'], 'anim.gif', { type: 'image/gif' }),
      'Dozwolone formaty: JPG, PNG, WebP, TIFF, BMP.',
    ],
    [pngFile('big.png', 5 * 1024 * 1024 + 1), 'Obraz może mieć najwyżej 5 MB.'],
  ])('rejects an invalid file before sending (%#)', async (file, message) => {
    const user = userEvent.setup({ applyAccept: false })
    failOnUpload()

    renderWithQueryClient(<UploadImageForm />)

    await user.upload(screen.getByLabelText('Pliki'), file)
    await fillUploader(user)
    await user.click(screen.getByRole('button', { name: 'Wyślij' }))

    expect(await screen.findByText(message)).toBeInTheDocument()
  })

  it('shows server validation errors under the matching field', async () => {
    const user = userEvent.setup()
    server.use(
      http.post(apiUrl('/images'), () =>
        HttpResponse.json(
          {
            status: 422,
            message: 'Nieprawidłowe dane.',
            errors: { file: ['Obraz musi mieć co najmniej 500×500 px.'] },
          },
          { status: 422 },
        ),
      ),
    )

    renderWithQueryClient(<UploadImageForm />)

    await user.upload(screen.getByLabelText('Pliki'), pngFile())
    await fillUploader(user)
    await user.click(screen.getByRole('button', { name: 'Wyślij' }))

    expect(await screen.findByText('Obraz musi mieć co najmniej 500×500 px.')).toBeInTheDocument()
  })

  it('sends the form, resets it and refreshes the gallery', async () => {
    const user = userEvent.setup()
    let listRequests = 0
    let contentType: string | null = null
    server.use(
      http.get(apiUrl('/images'), () => {
        listRequests++

        return HttpResponse.json(makePage([makeImage()]))
      }),
      http.post(apiUrl('/images'), ({ request }) => {
        contentType = request.headers.get('content-type')

        return HttpResponse.json({ status: 201, message: 'Obraz został dodany.' }, { status: 201 })
      }),
    )

    renderWithQueryClient(<GalleryPage />)
    await screen.findByRole('article')

    await user.upload(screen.getByLabelText('Pliki'), pngFile('gory.png'))
    expect(screen.getByRole('img', { name: 'Podgląd: gory.png' })).toBeInTheDocument()
    await fillUploader(user)
    await user.click(screen.getByRole('button', { name: 'Wyślij' }))

    expect(await screen.findByText('Dodano obrazów: 1.')).toBeInTheDocument()
    expect(contentType).toMatch(/^multipart\/form-data; boundary=/)
    expect(screen.getByLabelText('Imię')).toHaveValue('')
    expect(screen.getByLabelText('E-mail')).toHaveValue('')
    expect(screen.queryByRole('img', { name: 'Podgląd: gory.png' })).not.toBeInTheDocument()
    expect(listRequests).toBe(2)
  })

  it('disables the submit button while sending', async () => {
    const user = userEvent.setup()
    let finishUpload = () => {}
    server.use(
      http.post(apiUrl('/images'), async () => {
        await new Promise<void>((resolve) => (finishUpload = resolve))

        return HttpResponse.json({ status: 201, message: 'Obraz został dodany.' }, { status: 201 })
      }),
    )

    renderWithQueryClient(<UploadImageForm />)

    await user.upload(screen.getByLabelText('Pliki'), pngFile())
    await fillUploader(user)
    await user.click(screen.getByRole('button', { name: 'Wyślij' }))

    expect(await screen.findByRole('button', { name: /Wyślij/ })).toBeDisabled()

    finishUpload()

    expect(await screen.findByRole('button', { name: 'Wyślij' })).toBeEnabled()
  })
})
