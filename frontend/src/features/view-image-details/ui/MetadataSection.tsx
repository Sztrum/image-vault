import type { ImageMetadata } from '@/entities/image'

import { MetadataTree } from './MetadataTree'

type MetadataSectionProps = {
  title: string
  data: ImageMetadata | null
}

export function MetadataSection({ title, data }: MetadataSectionProps) {
  if (!data) {
    return (
      <p>
        <span className="font-medium text-slate-900">{title}:</span> brak danych
      </p>
    )
  }

  return (
    <details className="rounded-md border border-slate-200 p-3">
      <summary className="cursor-pointer font-medium text-slate-900">{title}</summary>
      <div className="mt-2 font-mono text-xs">
        <MetadataTree data={data} />
      </div>
    </details>
  )
}
