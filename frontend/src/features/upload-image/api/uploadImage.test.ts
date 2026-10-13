import { afterEach, describe, expect, it, vi } from 'vitest'

import { httpClient } from '@/shared/api'

import { uploadImage } from './uploadImage'

describe('uploadImage', () => {
  afterEach(() => {
    vi.restoreAllMocks()
  })

  it('sends file and uploader as multipart form data and returns the message', async () => {
    const post = vi
      .spyOn(httpClient, 'post')
      .mockResolvedValue({ data: { message: 'Obraz został dodany.' } })
    const file = new File(['png'], 'gory.png', { type: 'image/png' })

    const message = await uploadImage({
      file,
      uploader_name: 'Anna',
      uploader_email: 'anna@example.com',
    })

    expect(message).toBe('Obraz został dodany.')
    expect(post).toHaveBeenCalledWith('/images', expect.any(FormData))
    const formData = post.mock.calls[0]?.[1] as FormData
    expect(formData.get('file')).toBe(file)
    expect(formData.get('uploader_name')).toBe('Anna')
    expect(formData.get('uploader_email')).toBe('anna@example.com')
  })
})
