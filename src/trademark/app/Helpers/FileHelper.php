<?php

namespace App\Helpers;

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

class FileHelper
{
    /**
     * Get Storage
     *
     * @return  Filesystem
     */
    public static function getStorage(): Filesystem
    {
        return Storage::disk(env('FILE_STORAGE_DISK', 'local'));
    }

    /**
     * Upload File
     *
     * @param   mixed  $file
     * @param   array  $resize
     * @param   string $publicPath
     * @return  array|null
     */
    public static function uploads($file, array $resize = [], string $publicPath = LOCAL_PUBLIC_FOLDER, $hasCheckEtx = true): ?array
    {
        $fileName = [];
        if (is_array($file)) {
            foreach ($file as $f) {
                $fileName[] = self::saveFile($f, $resize, $publicPath, $hasCheckEtx);
            }
        } else {
            $fileName[] = self::saveFile($file, $resize, $publicPath, $hasCheckEtx);
        }

        return $fileName;
    }

    /**
     * Save file
     *
     * @param   mixed  $file
     * @param   array  $resize
     * @param   string $publicPath
     * @return  array|null
     */
    public static function saveFile($file, array $resize = [], string $publicPath = LOCAL_PUBLIC_FOLDER, $hasCheckEtx = true): ?array
    {
        try {
            if (is_file($file)) {
                $storage = self::getStorage();

                // Allow config upload file
                $allowedFilesize = config('filesystems.upload_max_filesize');
                $allowedExtensionImage = config('filesystems.allowed_extension_image');
                $allowedExtensionFile = config('filesystems.allowed_extension_file');

                // Get File info
                $fileInfo = explode('.', $file->getClientOriginalName());
                $fileInfo = [
                    'filename' => $fileInfo[0] ?? null,
                    'extension' => $fileInfo[1] ?? null,
                ];

                $fileName = $fileInfo['filename'] ?? null;
                $fileName = str_replace([' ', '　'], '-', $fileName);
                $fileName = strtolower($fileName);

                $fileExtension = $fileInfo['extension'];
                $fileSize = filesize($file);
                $filenameWithExtension = $fileName . '.' . $fileExtension;

                if ($fileSize >= $allowedFilesize) {
                    Log::error('Filesize is larger than required size. Filename: ' . $filenameWithExtension);
                }

                // Check namesake
                $i = 0;
                while ($storage->exists($publicPath . '/' . $filenameWithExtension)) {
                    $i++;
                    $filenameWithExtension = $fileName . '-' . $i . '.' . $fileExtension;
                }

                // Full path of file
                $filePath = $publicPath . '/' . $filenameWithExtension;

                // Check Allowed Extension Image
                if (in_array(strtolower($fileExtension), $allowedExtensionImage)) {
                    $image = $file->storeAs('', $filePath, 'local');
                    // Resize
                    $resizeImage = [];
                    foreach ($resize as $size) {
                        $imageResize = Image::make($storage->get($image))->widen($size);
                        $imageResize = $imageResize->stream($fileExtension, 100);
                        $imageResizePath = str_replace('.', '-w' . $size . '.', $filePath);
                        $storage->put($imageResizePath, $imageResize->__toString(), 'public');
                        $resizeImage[] = $imageResizePath;
                    }

                    // Return
                    return [
                        'filepath' => $filePath,
                        'resize' => $resizeImage,
                    ];
                } elseif (in_array(strtolower($fileExtension), $allowedExtensionFile) || $hasCheckEtx == false) {
                    $storage->put($filePath, file_get_contents($file), 'public');
                    return [
                        'filepath' => $filePath,
                    ];
                } else {
                    Log::error('File format not allowed. Filename: ' . $filenameWithExtension);
                }
            }
            return null;
        } catch (\Exception $e) {
            Log::error($e);
            return null;
        }
    }

    /**
     * Save file
     *
     * @param   string $path
     * @param   array  $resize
     * @return  void
     */
    public static function unlink(string $path, array $resize = [])
    {
        // Load image and image resize
        $unlinkImage = [
            $path
        ];
        foreach ($resize as $size) {
            $unlinkImage[] = str_replace('.', '-w' . $size . '.', $path);
        }
        // Start Unlink
        $storage = self::getStorage();
        foreach ($unlinkImage as $image) {
            $storage->delete($image);
        }
    }

    /**
     * Get image with resize
     *
     * @param string|null $img
     * @param string $resize
     * @return  string|null
     */
    public static function getImage(?string $img, string $resize = ''): ?string
    {
        if (empty($img)) {
            return self::getImageDefault();
        }

        $disk = env('FILE_STORAGE_DISK', 'local');

        // Start resize
        if (!empty($resize)) {
            $image = explode('.', $img);
            $image[count($image) - 2] = $image[count($image) - 2] . '-w' . $resize;
            $img = implode('.', $image);
        }

        switch ($disk) {
            case 'local':
                return asset($img);
            case 's3':
                return str_replace(
                    config('filesystems.disks.' . $disk . '.endpoint'),
                    config('filesystems.disks.' . $disk . '.url'),
                    $img
                );
        }

        return null;
    }

    /**
     * Get file url
     *
     * @param string|null $fileUrl
     * @return  string|null
     */
    public function getFileUrl(?string $fileUrl): ?string
    {
        if (empty($fileUrl)) {
            return null;
        }

        $disk = env('FILE_STORAGE_DISK', 'local');

        switch ($disk) {
            case 'local':
                return asset($fileUrl);
            case 's3':
                return str_replace(
                    config('filesystems.disks.' . $disk . '.endpoint'),
                    config('filesystems.disks.' . $disk . '.url'),
                    $fileUrl
                );
        }

        return null;
    }

    /**
     * Get image default
     *
     * @return  string
     */
    public static function getImageDefault(): string
    {
        return asset('admin_assets/core/images/default_image.png');
    }

    /**
     * Get image default
     *
     * @param string $filepath
     * @param string $path
     * @return ?string
     */
    public static function moveTempToPath(string $filepath, string $path): ?string
    {
        $storage = self::getStorage();
        if ($storage->exists($filepath)) {
            $newFile = Str::replace(FOLDER_TEMP, $path, $filepath);

            $explodeFilepath = explode('/', $newFile);
            $explodeFilename = explode('.', $explodeFilepath[3]);

            // Check namesake
            $i = 0;
            while ($storage->exists($newFile)) {
                $i++;
                $newFile = $path . '/' . $explodeFilename[0] . '-' . $i . '.' . $explodeFilename[1];
            }

            $storage->move($filepath, $newFile);

            return $newFile;
        }

        return null;
    }
}
