<?php

declare(strict_types=1);

namespace App\Modules\MediaLibrary\Image\UI\Http\Controllers;

use App\Core\UI\Http\Controllers\ApiController;
use App\Modules\MediaLibrary\Image\Application\Commands\DeleteImageCommand;
use App\Modules\MediaLibrary\Image\Application\Commands\UploadImageCommand;
use App\Modules\MediaLibrary\Image\Application\Queries\GetImageQuery;
use App\Modules\MediaLibrary\Image\Application\Queries\ListImagesQuery;
use App\Modules\MediaLibrary\Image\Domain\DTO\UploadImageDTO;
use App\Modules\MediaLibrary\Image\Infrastructure\Storage\LaravelImageStorage;
use App\Modules\MediaLibrary\Image\UI\Http\Requests\ImageIndexRequest;
use App\Modules\MediaLibrary\Image\UI\Http\Requests\ImageStoreRequest;
use App\Modules\MediaLibrary\Image\UI\Http\Resources\CollectionImageResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Http\UploadedFile;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ImageController extends ApiController
{
    public function index(ImageIndexRequest $request, ListImagesQuery $query): CollectionImageResource
    {
        return new CollectionImageResource(
            resource: $query->execute(
                perPage: $request->integer('per_page', ImageIndexRequest::DEFAULT_PER_PAGE),
                cursor: $request->string('cursor')->toString() ?: null,
            ),
            asResponse: true,
        );
    }

    public function store(ImageStoreRequest $request): JsonResponse
    {
        /** @var UploadedFile $file */
        $file = $request->file('file');

        $this->commandBus->dispatch(new UploadImageCommand(
            uploadImageDTO: new UploadImageDTO(
                file_path: $file->getRealPath(),
                original_name: $file->getClientOriginalName(),
                uploader_name: $request->string('uploader_name')->toString(),
                uploader_email: $request->string('uploader_email')->toString(),
            ),
        ));

        return $this->responseMessage(
            message: __('image::messages.uploaded'),
            statusCode: Response::HTTP_CREATED,
        );
    }

    public function thumbnail(string $id, GetImageQuery $query, LaravelImageStorage $storage): StreamedResponse
    {
        return $storage->response(storagePath: $query->execute($id)->thumbnail_path, headers: [
            'Content-Type' => 'image/webp',
            'Cache-Control' => 'public, max-age=31536000, immutable',
        ]);
    }

    public function download(string $id, GetImageQuery $query, LaravelImageStorage $storage): StreamedResponse
    {
        $image = $query->execute($id);

        return $storage->download(
            storagePath: $image->storage_path,
            name: $image->original_name,
            headers: ['Content-Type' => $image->mime_type],
        );
    }

    public function destroy(string $id): HttpResponse
    {
        $this->commandBus->dispatch(new DeleteImageCommand(imageId: $id));

        return $this->responseFactory->noContent();
    }
}
