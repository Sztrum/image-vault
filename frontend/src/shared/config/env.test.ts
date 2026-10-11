import { describe, expect, it } from 'vitest'

import { parseEnv } from './env'

describe('parseEnv', () => {
  it('returns api url without trailing slash', () => {
    expect(parseEnv({ VITE_API_URL: 'http://localhost:8000/api/v1/' })).toEqual({
      apiUrl: 'http://localhost:8000/api/v1',
    })
  })

  it('throws when api url is missing or invalid', () => {
    expect(() => parseEnv({})).toThrow(/VITE_API_URL/)
    expect(() => parseEnv({ VITE_API_URL: 'not-a-url' })).toThrow(/VITE_API_URL/)
  })
})
