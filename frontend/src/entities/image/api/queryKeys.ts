export const imageKeys = {
  all: ['images'] as const,
  list: () => [...imageKeys.all, 'list'] as const,
}
