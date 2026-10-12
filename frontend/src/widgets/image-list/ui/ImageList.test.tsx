import { act, screen, within } from '@testing-library/react'
import userEvent from '@testing-library/user-event'
import { http, HttpResponse } from 'msw'
import { describe, expect, it } from 'vitest'

import type { Image } from '@/entities/image'
import { apiUrl } from '@/test/apiUrl'
import { makeImage, makeImages, makePage } from '@/test/fixtures/images'
import { scrollToSentinel } from '@/test/intersectionObserver'
import { server } from '@/test/msw/server'
import { renderWithQueryClient } from '@/test/renderWithQueryClient'

import { ImageList } from './ImageList'

function serveImages(images: Image[]) {
  server.use(http.get(apiUrl('/images'), () => HttpResponse.json(makePage(images))))
}

describe('ImageList', () => {
  it('shows image details required by the task', async () => {
    const image = makeImage({
      original_name: 'góry.jpg',
      extension: 'jpg',
      size_bytes: 2483011,
      width: 4000,
      height: 3000,
      uploader_name: 'Anna',
    })
    serveImages([image])

    renderWithQueryClient(<ImageList />)

    const card = within(await screen.findByRole('article'))
    expect(card.getByRole('heading', { name: 'góry.jpg' })).toBeInTheDocument()
    expect(card.getByRole('img', { name: 'góry.jpg' })).toHaveAttribute('src', image.thumbnail_url)
    expect(card.getByText('jpg')).toBeInTheDocument()
    expect(card.getByText('2,4 MB')).toBeInTheDocument()
    expect(card.getByText('4000×3000 px')).toBeInTheDocument()
    expect(card.getByText('Anna')).toBeInTheDocument()
    expect(card.getByRole('link', { name: 'Pobierz' })).toHaveAttribute('href', image.download_url)
  })

  it('loads the next page when the sentinel scrolls into view and stops at the end', async () => {
    const requestedCursors: (string | null)[] = []
    server.use(
      http.get(apiUrl('/images'), ({ request }) => {
        const cursor = new URL(request.url).searchParams.get('cursor')
        requestedCursors.push(cursor)

        return HttpResponse.json(
          cursor === 'page-2'
            ? makePage(makeImages(5, 'second'))
            : makePage(makeImages(10, 'first'), 'page-2'),
        )
      }),
    )

    renderWithQueryClient(<ImageList />)

    expect(await screen.findAllByRole('article')).toHaveLength(10)

    act(() => scrollToSentinel())

    expect(await screen.findByText('To już wszystkie obrazy.')).toBeInTheDocument()
    expect(screen.getAllByRole('article')).toHaveLength(15)

    act(() => scrollToSentinel())

    expect(requestedCursors).toEqual([null, 'page-2'])
  })

  it('shows an empty state when there are no images', async () => {
    serveImages([])

    renderWithQueryClient(<ImageList />)

    expect(
      await screen.findByText('Nie ma jeszcze żadnych obrazów. Dodaj pierwszy.'),
    ).toBeInTheDocument()
  })

  it('shows an error with retry when the list cannot be loaded', async () => {
    const user = userEvent.setup()
    let isApiDown = true
    server.use(
      http.get(apiUrl('/images'), () =>
        isApiDown ? HttpResponse.error() : HttpResponse.json(makePage([makeImage()])),
      ),
    )

    renderWithQueryClient(<ImageList />)

    expect(await screen.findByRole('alert')).toHaveTextContent('Brak połączenia z serwerem.')

    isApiDown = false
    await user.click(screen.getByRole('button', { name: 'Spróbuj ponownie' }))

    expect(await screen.findByRole('article')).toBeInTheDocument()
  })

  it('removes the image right after confirming deletion', async () => {
    const user = userEvent.setup()
    const [kept, deleted] = [makeImage({ id: 'kept' }), makeImage({ id: 'deleted' })]
    let images = [kept, deleted]
    let finishDelete = () => {}
    server.use(
      http.get(apiUrl('/images'), () => HttpResponse.json(makePage(images))),
      http.delete(apiUrl('/images/deleted'), async () => {
        await new Promise<void>((resolve) => (finishDelete = resolve))
        images = [kept]

        return new HttpResponse(null, { status: 204 })
      }),
    )

    renderWithQueryClient(<ImageList />)

    const card = within((await screen.findAllByRole('article'))[1]!)
    await user.click(card.getByRole('button', { name: 'Usuń' }))
    await user.click(within(screen.getByRole('alertdialog')).getByRole('button', { name: 'Usuń' }))

    expect(screen.getAllByRole('article')).toHaveLength(1)

    act(() => finishDelete())

    expect(await screen.findAllByRole('article')).toHaveLength(1)
    expect(screen.getByRole('heading', { name: kept.original_name })).toBeInTheDocument()
  })

  it('brings the image back with an error when deletion fails', async () => {
    const user = userEvent.setup()
    const image = makeImage({ id: 'broken' })
    serveImages([image])
    server.use(
      http.delete(apiUrl('/images/broken'), () =>
        HttpResponse.json(
          { status: 500, message: 'Wystąpił błąd serwera. Spróbuj ponownie później.' },
          { status: 500 },
        ),
      ),
    )

    renderWithQueryClient(<ImageList />)

    await user.click(await screen.findByRole('button', { name: 'Usuń' }))
    await user.click(within(screen.getByRole('alertdialog')).getByRole('button', { name: 'Usuń' }))

    expect(await screen.findByRole('alert')).toHaveTextContent(
      'Nie udało się usunąć obrazu: Wystąpił błąd serwera. Spróbuj ponownie później.',
    )
    expect(screen.getByRole('heading', { name: image.original_name })).toBeInTheDocument()
  })
})
