<?php

namespace App\Filters;

class UploadMimes {
    /**
     * Allow SVG files to be uploaded to the WordPress media library.
     *
     * This function adds the MIME type for SVG files to the list of allowed upload types.
     *
     * @param array $mimeTypes Current list of allowed MIME types.
     * @return array Modified list of allowed MIME types.
     */
    public static function allowSvg($mimeTypes) {
        // Add SVG MIME type to the list of allowed file types
        $mimeTypes['svg'] = 'image/svg+xml';

        return $mimeTypes;
    }
}
