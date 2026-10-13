import { zodResolver } from '@hookform/resolvers/zod'
import { useState } from 'react'
import { Controller, useForm } from 'react-hook-form'

import { Button, Field } from '@/shared/ui'

import type { UploaderField } from '../lib/uploadErrors'
import { ACCEPTED_EXTENSIONS, uploadSchema, type UploadFormValues } from '../model/uploadSchema'
import { useUploadQueue, type UploadResult } from '../model/useUploadQueue'
import { UploadQueueList } from './UploadQueueList'

const inputClassName =
  'rounded-md border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none aria-invalid:border-red-500'

const summaryOf = ({ sent, failed }: UploadResult) =>
  failed > 0 ? `Dodano obrazów: ${sent}, nie udało się: ${failed}.` : `Dodano obrazów: ${sent}.`

export function UploadImageForm() {
  const [fileInputKey, setFileInputKey] = useState(0)
  const [summary, setSummary] = useState<string | null>(null)
  const { items, select, upload } = useUploadQueue()
  const {
    control,
    register,
    handleSubmit,
    reset,
    setError,
    formState: { errors, isSubmitting },
  } = useForm<UploadFormValues>({
    resolver: zodResolver(uploadSchema),
    defaultValues: { files: [], uploader_name: '', uploader_email: '' },
  })

  const submit = handleSubmit(async ({ uploader_name, uploader_email }) => {
    setSummary(null)
    const result = await upload({ uploader_name, uploader_email })

    if (result.uploaderErrors) {
      Object.entries(result.uploaderErrors).forEach(([field, message]) =>
        setError(field as UploaderField, { message }),
      )

      return
    }

    setSummary(summaryOf(result))

    if (result.failed === 0) {
      reset()
      select([])
      setFileInputKey((key) => key + 1)
    }
  })

  return (
    <form
      onSubmit={submit}
      noValidate
      className="space-y-4 rounded-lg border border-slate-200 bg-white p-6 shadow-sm"
    >
      <h2 className="text-lg font-semibold text-slate-900">Dodaj obrazy</h2>

      <div className="grid gap-4 md:grid-cols-3">
        <Field
          id="files"
          label="Pliki"
          hint="Możesz wybrać kilka. JPG, PNG, WebP, TIFF lub BMP, do 5 MB, min. 500×500 px"
          error={errors.files?.message}
        >
          <Controller
            control={control}
            name="files"
            render={({ field }) => (
              <input
                key={fileInputKey}
                ref={field.ref}
                id="files"
                name={field.name}
                type="file"
                multiple
                accept={ACCEPTED_EXTENSIONS}
                disabled={isSubmitting}
                aria-invalid={errors.files ? true : undefined}
                onBlur={field.onBlur}
                onChange={(event) => {
                  const files = Array.from(event.target.files ?? [])
                  select(files)
                  setSummary(null)
                  field.onChange(files)
                }}
                className="text-sm file:mr-3 file:rounded-md file:border-0 file:bg-slate-100 file:px-3 file:py-2 file:text-sm file:font-semibold"
              />
            )}
          />
        </Field>

        <Field id="uploader_name" label="Imię" error={errors.uploader_name?.message}>
          <input
            id="uploader_name"
            type="text"
            autoComplete="given-name"
            aria-invalid={errors.uploader_name ? true : undefined}
            className={inputClassName}
            {...register('uploader_name')}
          />
        </Field>

        <Field id="uploader_email" label="E-mail" error={errors.uploader_email?.message}>
          <input
            id="uploader_email"
            type="email"
            autoComplete="email"
            aria-invalid={errors.uploader_email ? true : undefined}
            className={inputClassName}
            {...register('uploader_email')}
          />
        </Field>
      </div>

      {items.length > 0 && <UploadQueueList items={items} />}

      {summary && (
        <p role="status" className="text-sm text-slate-700">
          {summary}
        </p>
      )}

      <Button type="submit" isLoading={isSubmitting}>
        Wyślij
      </Button>
    </form>
  )
}
