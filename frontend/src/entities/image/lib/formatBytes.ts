const UNITS = ['B', 'KB', 'MB', 'GB'] as const

const numberFormat = new Intl.NumberFormat('pl-PL', { maximumFractionDigits: 1 })

export function formatBytes(bytes: number): string {
  const exponent =
    bytes > 0 ? Math.min(Math.floor(Math.log(bytes) / Math.log(1024)), UNITS.length - 1) : 0

  return `${numberFormat.format(bytes / 1024 ** exponent)} ${UNITS[exponent]}`
}
