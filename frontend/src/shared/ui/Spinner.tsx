type SpinnerProps = {
  label?: string
  className?: string
}

export function Spinner({ label = 'Ładowanie…', className = 'size-5' }: SpinnerProps) {
  return (
    <span role="status" className="inline-flex items-center">
      <span
        aria-hidden="true"
        className={`${className} animate-spin rounded-full border-2 border-current border-t-transparent`}
      />
      <span className="sr-only">{label}</span>
    </span>
  )
}
