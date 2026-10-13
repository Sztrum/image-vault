const temperatureFormat = new Intl.NumberFormat('pl-PL', {
  minimumFractionDigits: 1,
  maximumFractionDigits: 1,
})

const dateTimeFormat = new Intl.DateTimeFormat('pl-PL', { dateStyle: 'medium', timeStyle: 'short' })

const BINARY_PREFIX = 'base64:'

export function formatTemperature(celsius: number): string {
  return `${temperatureFormat.format(celsius)} °C`
}

export function formatDateTime(isoDate: string): string {
  return dateTimeFormat.format(new Date(isoDate))
}

export function formatMetadataValue(value: unknown): string {
  if (typeof value === 'string' && value.startsWith(BINARY_PREFIX)) {
    return `dane binarne (${value.length - BINARY_PREFIX.length} znaków base64)`
  }

  return value === null ? '—' : String(value)
}
