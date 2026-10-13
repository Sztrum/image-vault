import { useImageDetails } from '@/entities/image'
import { ErrorMessage, Spinner } from '@/shared/ui'

import { formatDateTime, formatTemperature } from '../lib/formatDetails'
import { MetadataSection } from './MetadataSection'

type ImageDetailsContentProps = {
  imageId: string
}

export function ImageDetailsContent({ imageId }: ImageDetailsContentProps) {
  const { data: image, error, isPending, isError, refetch } = useImageDetails(imageId)

  if (isPending) {
    return (
      <div className="flex justify-center py-8">
        <Spinner label="Ładowanie szczegółów…" className="size-6" />
      </div>
    )
  }

  if (isError) {
    return <ErrorMessage message={error.message} onRetry={() => void refetch()} />
  }

  return (
    <div className="space-y-4 text-sm text-slate-600">
      <dl className="grid grid-cols-[auto_1fr] gap-x-4 gap-y-1">
        <dt>Temperatura w Katowicach</dt>
        <dd className="text-slate-900">
          {image.temperature_celsius === null
            ? 'brak danych'
            : formatTemperature(image.temperature_celsius)}
          {image.temperature_measured_at && (
            <span className="text-slate-500">
              {' '}
              (pomiar: {formatDateTime(image.temperature_measured_at)})
            </span>
          )}
        </dd>
        <dt>Dodano</dt>
        <dd className="text-slate-900">{formatDateTime(image.created_at)}</dd>
        <dt>Imię</dt>
        <dd className="break-all text-slate-900">{image.uploader_name}</dd>
        <dt>E-mail</dt>
        <dd className="break-all text-slate-900">{image.uploader_email}</dd>
      </dl>
      <MetadataSection title="EXIF" data={image.exif} />
      <MetadataSection title="IPTC" data={image.iptc} />
    </div>
  )
}
