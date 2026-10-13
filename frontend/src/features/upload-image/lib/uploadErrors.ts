import { isApiValidationError, type ValidationErrors } from '@/shared/api'

const UPLOADER_FIELDS = ['uploader_name', 'uploader_email'] as const

export type UploaderField = (typeof UPLOADER_FIELDS)[number]

export function uploaderErrorsOf(error: unknown): Partial<Record<UploaderField, string>> | null {
  if (!isApiValidationError(error)) {
    return null
  }

  const entries = UPLOADER_FIELDS.flatMap((field) => {
    const message = error.errors[field]?.[0]

    return message ? [[field, message] as const] : []
  })

  return entries.length > 0 ? Object.fromEntries(entries) : null
}

export function fileErrorMessage(error: unknown): string {
  if (isApiValidationError(error)) {
    return firstMessage(error.errors) ?? error.message
  }

  return error instanceof Error ? error.message : 'Nie udało się wysłać obrazu.'
}

function firstMessage(errors: ValidationErrors): string | undefined {
  return errors.file?.[0] ?? Object.values(errors)[0]?.[0]
}
