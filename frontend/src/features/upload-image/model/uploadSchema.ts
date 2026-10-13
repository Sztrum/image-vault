import { z } from 'zod'

export const MAX_FILE_BYTES = 5 * 1024 * 1024

export const ACCEPTED_MIME_TYPES = [
  'image/jpeg',
  'image/png',
  'image/webp',
  'image/tiff',
  'image/bmp',
] as const

export const ACCEPTED_EXTENSIONS = '.jpg,.jpeg,.png,.webp,.tif,.tiff,.bmp'

export const imageFileSchema = z
  .custom<File>((value) => value instanceof File, 'Wybierz plik obrazu.')
  .refine(
    (file) => (ACCEPTED_MIME_TYPES as readonly string[]).includes(file.type),
    'Dozwolone formaty: JPG, PNG, WebP, TIFF, BMP.',
  )
  .refine((file) => file.size <= MAX_FILE_BYTES, 'Obraz może mieć najwyżej 5 MB.')

export const uploaderSchema = z.object({
  uploader_name: z
    .string()
    .trim()
    .min(1, 'Podaj imię.')
    .max(100, 'Imię może mieć najwyżej 100 znaków.'),
  uploader_email: z
    .string()
    .trim()
    .min(1, 'Podaj adres e-mail.')
    .pipe(z.email('Podaj poprawny adres e-mail.')),
})

export const uploadSchema = uploaderSchema.extend({
  files: z
    .array(z.custom<File>((value) => value instanceof File))
    .min(1, 'Wybierz co najmniej jeden obraz.'),
})

export type UploaderValues = z.infer<typeof uploaderSchema>

export type UploadFormValues = z.infer<typeof uploadSchema>

export type UploadImageValues = UploaderValues & {
  file: File
}
