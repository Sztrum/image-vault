import { GalleryPage } from '@/pages/gallery'

import { Providers } from './providers'

export function App() {
  return (
    <Providers>
      <div className="min-h-screen bg-slate-50 text-slate-900">
        <header className="border-b border-slate-200 bg-white">
          <div className="mx-auto max-w-6xl px-4 py-4">
            <h1 className="text-xl font-semibold">Image Vault</h1>
          </div>
        </header>
        <main className="mx-auto max-w-6xl px-4 py-8">
          <GalleryPage />
        </main>
      </div>
    </Providers>
  )
}
