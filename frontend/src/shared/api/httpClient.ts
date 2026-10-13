import axios from 'axios'

import { env } from '@/shared/config'

import { toApiError } from './apiError'

export const httpClient = axios.create({
  adapter: 'fetch',
  baseURL: env.apiUrl,
  headers: {
    Accept: 'application/json',
  },
})

httpClient.interceptors.response.use(
  (response) => response,
  (error: unknown) => Promise.reject(toApiError(error)),
)
