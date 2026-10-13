import { zodResolver } from '@hookform/resolvers/zod'
import { useState } from 'react'
import { Controller, useForm, useWatch } from 'react-hook-form'

import { isApiValidationError } from '@/shared/api'
import { Button, Field } from '@/shared/ui'

import { useFilePreview } from '../model/useFilePreview'
import { ACCEPTED_EXTENSIONS, uploadSchema, type UploadFormValues } from '../model/uploadSchema'
import { useUploadImage } from '../model/useUploadImage'
import { FilePreview } from './FilePreview'

const FORM_FIELDS = ['file', 'uploader_name', 'uploader_email'] as const

const inputClassName =
  'rounded-md border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none aria-invalid:border-red-500'

const isFormField = (field: string): field is (typeof FORM_FIELDS)[number] =>
  (FORM_FIELDS as readonly string[]).includes(field)

export function UploadImageForm() {
  const [fileInputKey, setFileInputKey] = useState(0)
  const { previewUrl, showPreview } = useFilePreview()
  const { mutate, isPending, isSuccess, data: successMessage } = useUploadImage()
  const {
    control,
    register,
    handleSubmit,
    reset,
    setError,
    formState: { errors },
  } = useForm<UploadFormValues>({
    resolver: zodResolver(uploadSchema),
    defaultValues: { uploader_name: '', uploader_email: '' },
  })
  const selectedFile = useWatch({ control, name: 'file' })

  const submit = handleSubmit((values) =>
    mutate(values, {
      onSuccess: () => {
        reset()
        showPreview(null)
        setFileInputKey((key) => key + 1)
      },
      onError: (error) => {
        if (!isApiValidationError(error)) {
          setError('root', { message: error.message })

          return
        }

        Object.entries(error.errors).forEach(([field, messages]) => {
          setError(isFormField(field) ? field : 'root', { message: messages[0] })
        })
      },
    }),
  )

  return (
    <form
      onSubmit={submit}
      noValidate
      className="space-y-4 rounded-lg border border-slate-200 bg-white p-6 shadow-sm"
    >
      <h2 className="text-lg font-semibold text-slate-900">Dodaj obraz</h2>

      <div className="grid gap-4 md:grid-cols-3">
        <Field
          id="file"
          label="Plik"
          hint="JPG, PNG, WebP, TIFF lub BMP, do 5 MB, min. 500×500 px"
          error={errors.file?.message}
        >
          <Controller
            control={control}
            name="file"
            render={({ field }) => (
              <input
                key={fileInputKey}
                ref={field.ref}
                id="file"
                name={field.name}
                type="file"
                accept={ACCEPTED_EXTENSIONS}
                aria-invalid={errors.file ? true : undefined}
                onBlur={field.onBlur}
                onChange={(event) => {
                  const file = event.target.files?.[0] ?? null
                  showPreview(file)
                  field.onChange(file)
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

      {selectedFile instanceof File && <FilePreview file={selectedFile} previewUrl={previewUrl} />}

      {errors.root?.message && (
        <p role="alert" className="text-sm text-red-600">
          {errors.root.message}
        </p>
      )}
      {isSuccess && (
        <p role="status" className="text-sm text-green-700">
          {successMessage}
        </p>
      )}

      <Button type="submit" isLoading={isPending}>
        Wyślij
      </Button>
    </form>
  )
}
