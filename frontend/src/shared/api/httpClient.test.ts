import { http, HttpResponse } from 'msw'
import { describe, expect, it } from 'vitest'

import { apiUrl } from '@/test/apiUrl'
import { server } from '@/test/msw/server'

import { ApiError, ApiValidationError } from './apiError'
import { httpClient } from './httpClient'

describe('httpClient', () => {
  it('maps 422 response to ApiValidationError with field errors', async () => {
    server.use(
      http.post(apiUrl('/images'), () =>
        HttpResponse.json(
          {
            status: 422,
            message: 'Nieprawidłowe dane.',
            errors: { uploader_email: ['Pole nie jest poprawnym adresem e-mail.'] },
          },
          { status: 422 },
        ),
      ),
    )

    const error: unknown = await httpClient.post('/images').catch((caught: unknown) => caught)

    expect(error).toBeInstanceOf(ApiValidationError)
    expect((error as ApiValidationError).errors).toEqual({
      uploader_email: ['Pole nie jest poprawnym adresem e-mail.'],
    })
  })

  it('maps other error responses to ApiError with status and message', async () => {
    server.use(
      http.delete(apiUrl('/images/missing'), () =>
        HttpResponse.json({ status: 404, message: 'Nie znaleziono' }, { status: 404 }),
      ),
    )

    const error: unknown = await httpClient
      .delete('/images/missing')
      .catch((caught: unknown) => caught)

    expect(error).toBeInstanceOf(ApiError)
    expect(error).not.toBeInstanceOf(ApiValidationError)
    expect((error as ApiError).status).toBe(404)
    expect((error as ApiError).message).toBe('Nie znaleziono')
  })

  it('maps network failure to ApiError without status', async () => {
    server.use(http.get(apiUrl('/health'), () => HttpResponse.error()))

    const error: unknown = await httpClient.get('/health').catch((caught: unknown) => caught)

    expect(error).toBeInstanceOf(ApiError)
    expect((error as ApiError).status).toBeNull()
  })
})
