import type { ReactNode } from 'react'

import { formatBytes } from '../lib/formatBytes'
import type { Image } from '../model/types'

type ImageCardProps = {
  image: Image
  actions?: ReactNode
}

export function ImageCard({ image, actions }: ImageCardProps) {
  return (
    <article className="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
      <div className="flex aspect-square items-center justify-center bg-slate-100">
        <img
          src={image.thumbnail_url}
          alt={image.original_name}
          loading="lazy"
          className="max-h-full max-w-full object-contain"
        />
      </div>
      <div className="space-y-2 p-4">
        <h3 className="truncate text-sm font-semibold text-slate-900" title={image.original_name}>
          {image.original_name}
        </h3>
        <dl className="grid grid-cols-2 gap-x-2 gap-y-1 text-xs text-slate-600">
          <dt>Format</dt>
          <dd className="text-right uppercase">{image.extension}</dd>
          <dt>Rozmiar</dt>
          <dd className="text-right">{formatBytes(image.size_bytes)}</dd>
          <dt>Rozdzielczość</dt>
          <dd className="text-right">
            {image.width}×{image.height} px
          </dd>
          <dt>Dodał(a)</dt>
          <dd className="truncate text-right">{image.uploader_name}</dd>
        </dl>
        {actions && <div className="flex flex-wrap gap-2 pt-2">{actions}</div>}
      </div>
    </article>
  )
}
