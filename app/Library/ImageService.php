<?php

namespace App\Library;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ImageService
{

    public static function uploadProfileImage(UploadedFile $fileToUpload, $existingFile = null)
    {
        $fileName = self::generateFileName($fileToUpload);

        self::oldFileCleanUp($existingFile);
        self::saveImage($fileToUpload, $fileName);

        return $fileName;
    }


    private static function saveImage(UploadedFile $imageToUpload, $fileName = null, $subDirectory = null)
    {
        if (empty($fileName)) {
            $fileName = Storage::putFile(
                $subDirectory ? $subDirectory : '',
                $imageToUpload,
                'public'
            );
        } else {
            Storage::putFileAs(
                $subDirectory ? $subDirectory : '',
                $imageToUpload,
                $fileName,
                'public'
            );
        }

        return $fileName;
    }

    public static function oldFileCleanUp($existingFile)
    {
        if ($existingFile) {
            /**
             * If the old image exists
             * @var boolean $exists
             */
            $exists = Storage::exists($existingFile);

            if ($exists) {
                return Storage::delete($existingFile);
            }

            return false;
        }
    }

    private static function generateFileName(UploadedFile $file)
    {
        return time() . '-' . $file->getClientOriginalName();
    }
}
