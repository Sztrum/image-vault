type FilePreviewProps = {
  file: File
  previewUrl: string | null
}

export function FilePreview({ file, previewUrl }: FilePreviewProps) {
  return (
    <div className="flex items-center gap-3 rounded-md border border-slate-200 bg-slate-50 p-2">
      {previewUrl && (
        <img
          src={previewUrl}
          alt="Podgląd wybranego pliku"
          className="size-16 shrink-0 rounded object-cover"
        />
      )}
      <span className="truncate text-sm text-slate-700">{file.name}</span>
    </div>
  )
}
