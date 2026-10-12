<?php

declare(strict_types=1);

namespace App\Modules\MediaLibrary\Image\UI\Http\Controllers;

use App\Core\UI\Http\Controllers\ApiController;
use App\Modules\MediaLibrary\Image\Application\Commands\UploadImageCommand;
use App\Modules\MediaLibrary\Image\Domain\DTO\UploadImageDTO;
use App\Modules\MediaLibrary\Image\UI\Http\Requests\ImageStoreRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\UploadedFile;
use Symfony\Component\HttpFoundation\Response;

class ImageController extends ApiController
{
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
}
