import type { UploadItem, UploadStatus } from '../model/useUploadQueue'

type UploadQueueListProps = {
  items: UploadItem[]
}

const statusLabels: Record<UploadStatus, string> = {
  queued: 'w kolejce',
  uploading: 'wysyłanie…',
  done: 'wysłano',
  error: 'błąd',
}

const statusClassNames: Record<UploadStatus, string> = {
  queued: 'text-slate-500',
  uploading: 'text-indigo-600',
  done: 'text-green-700',
  error: 'text-red-600',
}

export function UploadQueueList({ items }: UploadQueueListProps) {
  return (
    <ul className="space-y-2">
      {items.map((item) => (
        <li
          key={item.key}
          className="flex items-center gap-3 rounded-md border border-slate-200 bg-slate-50 p-2"
        >
          {item.previewUrl ? (
            <img
              src={item.previewUrl}
              alt={`Podgląd: ${item.file.name}`}
              className="size-12 shrink-0 rounded object-cover"
            />
          ) : (
            <div className="size-12 shrink-0 rounded bg-slate-200" aria-hidden="true" />
          )}
          <span className="min-w-0 flex-1 truncate text-sm text-slate-700">{item.file.name}</span>
          <span className={`text-right text-xs ${statusClassNames[item.status]}`}>
            {item.status === 'error' && item.message ? item.message : statusLabels[item.status]}
          </span>
        </li>
      ))}
    </ul>
  )
}
