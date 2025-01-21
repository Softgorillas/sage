<?php

namespace App\Services;

use Exception;

class SupportWebp
{
    /**
     * Constructor for the SupportWebp class.
     *
     * Registers a WordPress filter to generate a WebP version of an image
     * after uploading an image and generating its metadata,
     * and remove all webp when deleting an image
     */
    public function __construct()
    {
        add_filter('wp_generate_attachment_metadata', [$this, 'generateWebpOnUpload'], 10, 2);
        add_action('delete_attachment', [$this, 'deleteWebpFormatAfterDelete']);
    }

    /**
     * Generates a WebP image after upload
     *
     * @param array $metadata Metadata attachment
     * @param int $attachmentID ID attachment.
     * @return array
     * @throws Exception
     */
    public function generateWebpOnUpload(array $metadata, int $attachmentID): array
    {
        $filePath = get_attached_file($attachmentID);

        if (in_array(mime_content_type($filePath), ['image/jpeg', 'image/png'])) {
            try {
                $this->generateAllSizes($attachmentID);
            } catch (Exception $e) {
            }

            $metadata['webp'] = preg_replace('/\.(jpg|jpeg|png)$/i', '.webp', $filePath);

            foreach ($metadata['sizes'] as $size => $sizeInfo) {
                $sizeFilePath = str_replace(basename($filePath), $sizeInfo['file'], $filePath);
                $metadata['sizes'][$size]['webp'] = preg_replace('/\.(jpg|jpeg|png)$/i', '.webp', $sizeFilePath);
            }
        }

        return $metadata;
    }

    /**
     * Generates a WebP image from the given source image.
     *
     * @param string $sourcePath Path to source file (JPEG/PNG).
     * @param string $destinationPath Target path for WebP.
     * @param int $quality WebP image quality (default 90).
     * @return bool
     * @throws Exception
     */
    public function generate(string $sourcePath, string $destinationPath, int $quality = 90): bool
    {
        $info = getimagesize($sourcePath);

        if (!$info) {
            throw new Exception('Could not read image information.');
        }

        $mime = $info['mime'];

        switch ($mime) {
            case 'image/jpeg':
                $image = imagecreatefromjpeg($sourcePath);
                break;
            case 'image/png':
                $image = imagecreatefrompng($sourcePath);
                imagepalettetotruecolor($image);
                imagealphablending($image, true);
                imagesavealpha($image, true);
                break;
            default:
                throw new Exception(sprintf('Unsupported image format: %s', $mime));
        }

        if (!imagewebp($image, $destinationPath, $quality)) {
            throw new Exception('Failed to save WebP file.');
        }

        imagedestroy($image);

        return true;
    }

    /**
     * Generates WebP for all image sizes.
     *
     * @param int $attachmentID ID media.
     */
    public function generateAllSizes(int $attachmentID)
    {
        $metadata = wp_get_attachment_metadata($attachmentID);
        if (!$metadata) {
            return;
        }

        $filePath = get_attached_file($attachmentID);

        $this->generate($filePath, preg_replace('/\.(jpg|jpeg|png)$/i', '.webp', $filePath));

        foreach ($metadata['sizes'] as $size => $sizeInfo) {
            $sizeFilePath = str_replace(basename($filePath), $sizeInfo['file'], $filePath);
            $this->generate($sizeFilePath, preg_replace('/\.(jpg|jpeg|png)$/i', '.webp', $sizeFilePath));
        }
    }

    /**
     * Delete all webp images.
     *
     * @param int $attachmentID ID media.
     */
    public function deleteWebpFormatAfterDelete($attachmentID)
	{
		$filePath = get_attached_file($attachmentID);

		if (!$filePath) {
			return;
		}
		$fileDir = pathinfo($filePath, PATHINFO_DIRNAME);
		$fileName = pathinfo($filePath, PATHINFO_FILENAME);

		$allFiles = scandir($fileDir);

		foreach ($allFiles as $file) {
			if ($file === '.' || $file === '..') {
				continue;
			}

			if (strpos($file, $fileName) === 0) {
				$fullFilePath = "$fileDir/$file";

				if (file_exists($fullFilePath)) {
					@unlink($fullFilePath);
				}
			}
		}
	}
}
