<?php

declare(strict_types=1);

namespace App\Modules\MediaLibrary\Image\UI\Http\Controllers;

use App\Core\UI\Http\Controllers\ApiController;
use App\Modules\MediaLibrary\Image\Application\Commands\UploadImageCommand;
use App\Modules\MediaLibrary\Image\UI\Http\Requests\ImageStoreRequest;
use App\Modules\MediaLibrary\Image\UI\Http\Resources\ImageResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\UploadedFile;
use Symfony\Component\HttpFoundation\Response;

class ImageController extends ApiController
{
    public function store(ImageStoreRequest $request): JsonResponse
    {
        /** @var UploadedFile $file */
        $file = $request->file('file');

        $command = new UploadImageCommand(
            filePath: $file->getRealPath(),
            originalName: $file->getClientOriginalName(),
            uploaderName: $request->string('uploader_name')->toString(),
            uploaderEmail: $request->string('uploader_email')->toString(),
        );

        $this->commandBus->dispatch($command);

        return $this->responseData(
            data: (new ImageResource($command->image))->resolve($request),
            message: __('image::messages.uploaded'),
            statusCode: Response::HTTP_CREATED,
        );
    }
}
