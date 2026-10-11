import type { RequestHandler } from 'msw'

// Default handlers shared by all tests; individual tests add their own with server.use().
export const handlers: RequestHandler[] = []
