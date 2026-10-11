import { isAxiosError } from 'axios'
import { z } from 'zod'

export type ValidationErrors = Record<string, string[]>

export class ApiError extends Error {
  readonly status: number | null

  constructor(message: string, status: number | null) {
    super(message)
    this.name = 'ApiError'
    this.status = status
  }
}

export class ApiValidationError extends ApiError {
  readonly errors: ValidationErrors

  constructor(message: string, errors: ValidationErrors) {
    super(message, 422)
    this.name = 'ApiValidationError'
    this.errors = errors
  }
}

const errorBodySchema = z.object({
  message: z.string().optional(),
  errors: z.record(z.string(), z.array(z.string())).optional(),
})

const FALLBACK_MESSAGE = 'Wystąpił nieoczekiwany błąd. Spróbuj ponownie.'
const NETWORK_MESSAGE = 'Brak połączenia z serwerem.'

export function toApiError(error: unknown): ApiError {
  if (error instanceof ApiError) {
    return error
  }

  if (!isAxiosError(error)) {
    return new ApiError(FALLBACK_MESSAGE, null)
  }

  if (!error.response) {
    return new ApiError(NETWORK_MESSAGE, null)
  }

  const { status, data } = error.response
  const body = errorBodySchema.safeParse(data)
  const message = body.success && body.data.message ? body.data.message : FALLBACK_MESSAGE

  if (status === 422 && body.success && body.data.errors) {
    return new ApiValidationError(message, body.data.errors)
  }

  return new ApiError(message, status)
}

export function isApiValidationError(error: unknown): error is ApiValidationError {
  return error instanceof ApiValidationError
}
