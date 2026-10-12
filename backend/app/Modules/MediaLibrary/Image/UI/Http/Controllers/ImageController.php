<?php

declare(strict_types=1);

namespace App\Modules\MediaLibrary\Image\UI\Http\Controllers;

use App\Core\UI\Http\Controllers\ApiController;
use App\Modules\MediaLibrary\Image\Application\Commands\DeleteImageCommand;
use App\Modules\MediaLibrary\Image\Application\Commands\UploadImageCommand;
use App\Modules\MediaLibrary\Image\Application\Queries\GetImageQuery;
use App\Modules\MediaLibrary\Image\Application\Queries\ListImagesQuery;
use App\Modules\MediaLibrary\Image\Application\Services\ImageFileService;
use App\Modules\MediaLibrary\Image\Domain\DTO\UploadImageDTO;
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

    public function thumbnail(string $id, GetImageQuery $query, ImageFileService $imageFileService): StreamedResponse
    {
        return $imageFileService->thumbnailResponse($query->execute($id));
    }

    public function download(string $id, GetImageQuery $query, ImageFileService $imageFileService): StreamedResponse
    {
        return $imageFileService->downloadResponse($query->execute($id));
    }

    public function destroy(string $id): HttpResponse
    {
        $this->commandBus->dispatch(new DeleteImageCommand(imageId: $id));

        return $this->responseFactory->noContent();
    }
}
