import { env } from '@/shared/config'

export const apiUrl = (path: string) => `${env.apiUrl}${path}`
