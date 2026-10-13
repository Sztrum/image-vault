import { Controller, type Control } from 'react-hook-form'

import { Field } from '@/shared/ui'

import { ACCEPTED_EXTENSIONS, type UploadFormValues } from '../model/uploadSchema'

type UploadFilesFieldProps = {
  control: Control<UploadFormValues>
  resetKey: number
  disabled: boolean
  onSelect: (files: File[]) => void
}

export function UploadFilesField({ control, resetKey, disabled, onSelect }: UploadFilesFieldProps) {
  return (
    <Controller
      control={control}
      name="files"
      render={({ field, fieldState }) => (
        <Field
          id="files"
          label="Pliki"
          hint="Możesz wybrać kilka. JPG, PNG, WebP, TIFF lub BMP, do 5 MB, min. 500×500 px"
          error={fieldState.error?.message}
        >
          <input
            key={resetKey}
            ref={field.ref}
            id="files"
            name={field.name}
            type="file"
            multiple
            accept={ACCEPTED_EXTENSIONS}
            disabled={disabled}
            aria-invalid={fieldState.invalid || undefined}
            onBlur={field.onBlur}
            onChange={(event) => {
              const files = Array.from(event.target.files ?? [])
              onSelect(files)
              field.onChange(files)
            }}
            className="text-sm file:mr-3 file:rounded-md file:border-0 file:bg-slate-100 file:px-3 file:py-2 file:text-sm file:font-semibold"
          />
        </Field>
      )}
    />
  )
}
