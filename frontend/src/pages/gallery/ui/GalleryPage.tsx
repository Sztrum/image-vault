import { UploadImageForm } from '@/features/upload-image'
import { ImageList } from '@/widgets/image-list'

export function GalleryPage() {
  return (
    <div className="space-y-8">
      <UploadImageForm />
      <section className="space-y-6">
        <h2 className="text-lg font-semibold text-slate-900">Galeria</h2>
        <ImageList />
      </section>
    </div>
  )
}
