export const imageKeys = {
  all: ['images'] as const,
  list: () => [...imageKeys.all, 'list'] as const,
  detail: (id: string) => [...imageKeys.all, 'detail', id] as const,
}
