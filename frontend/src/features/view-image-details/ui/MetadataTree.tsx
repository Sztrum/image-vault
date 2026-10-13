import type { ImageMetadata } from '@/entities/image'

import { formatMetadataValue } from '../lib/formatDetails'

type MetadataTreeProps = {
  data: ImageMetadata
}

const isBranch = (value: unknown): value is ImageMetadata =>
  typeof value === 'object' && value !== null

export function MetadataTree({ data }: MetadataTreeProps) {
  return (
    <ul className="space-y-1 border-l border-slate-200 pl-3">
      {Object.entries(data).map(([key, value]) => (
        <li key={key}>
          {isBranch(value) ? (
            <details>
              <summary className="cursor-pointer font-medium text-slate-700">{key}</summary>
              <MetadataTree data={value} />
            </details>
          ) : (
            <span className="break-all">
              <span className="font-medium text-slate-700">{key}:</span>{' '}
              {formatMetadataValue(value)}
            </span>
          )}
        </li>
      ))}
    </ul>
  )
}
