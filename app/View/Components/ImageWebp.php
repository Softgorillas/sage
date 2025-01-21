<?php

namespace App\View\Components;

use Roots\Acorn\View\Component;

class ImageWebp extends Component
{

    /**
     * ID attachment.
     *
     * @var int
     */
    protected int $imageId;

    /**
     * Array breakpoints.
     *
     * @var array
     */
    public array $breakpoints = [];

    /**
     * Array sizes.
     *
     * @var array
     */
    public array $sizes = [];

    /**
     * Array image elements.
     *
     * @var array
     */
    public array $imageElements;

    /**
     * Array upload dir.
     *
     * @var array
     */
    public array $uploadDir;

    /**
     * Media Eager loading.
     *
     * @var bool
     */
    public bool $eagerLoading;

    /**
     * Media alt text.
     *
     * @var string
     */
    public string $alt;

    public function __construct(int $imageId = null, array $breakpoints = [], array $sizes = [], bool $eagerLoading = false, string $alt = '')
    {
        $this->imageId = $imageId;

        if ($this->imageId) {
            $this->breakpoints = $breakpoints;
            $this->sizes = $sizes;
            $this->eagerLoading = $eagerLoading ? true : false;
            $this->alt = $alt ?: get_post_meta($this->imageId, '_wp_attachment_image_alt', true);
            $this->uploadDir = wp_upload_dir();
            $this->imageElements = $this->prepareImageData($this->imageId, $this->sizes, $this->breakpoints);
        }
    }

    /**
     * Component file name.
     *
     * @return string
     */
    public function render()
    {
        return view('components.image');
    }

    /**
     * Compare two arrays.
     *
     * @param array $a array a to comapre.
     * @param array $b array b to comapre.
     * @return bool
     *
     */
    public function compareArrayCounts(array $a, array $b)
    {
        return count($a) === count($b);
    }

    /**
     * Get url image.
     *
     * @param string $scr attachment source.
     * @return string
     */
    public function getImageUrl($src): string
    {
        return $this->uploadDir['url'] . $src;
    }

    /**
     * Get url webp image.
     *
     * @param string $scr attachment path.
     * @return string
     */
    public function getWebpUrl($src): string
    {
        return str_replace($this->uploadDir['path'], $this->uploadDir['url'], $src);
    }

    /**
     * Get array of url images.
     *
     * @param int $id id attachment.
     * @param array $sizes array sizes.
     * @param array $breakpoints array brakpoints.
     * @return array
     */
    public function prepareImageData($imageId, $sizes, $breakpoints): array
    {
        if (!$this->compareArrayCounts($breakpoints, $sizes)) {
            return [];
        }

        $metadata = wp_get_attachment_metadata($imageId);


        if (empty($metadata)) {
            return [];
        }

        if (!array_key_exists('sizes', $metadata) || empty($metadata['sizes'])) {
            return [];
        }

        $sources = [];

        foreach ($sizes as $key => $size) {
            $element = array_key_exists($size, $metadata['sizes']) ? $metadata['sizes'][$size] : null;

            if (!empty($element)) {
                $sources[] = [
                    'src' => array_key_exists('file', $element)
                        ? $this->getImageUrl($element['file']) :
                        (array_key_exists('file', $metadata) ? $this->getImageUrl($metadata['file']) : null),
                    'srcWebp' => array_key_exists('webp', $element) ?
                        $this->getWebpUrl($element['webp']) :
                        (array_key_exists('webp', $metadata) ? $this->getWebpUrl($metadata['webp']) : null),
                    'width' => $element['width'],
                    'height' => $element['height'],
                ];

                continue;
            }

            $sources[] = [
                'src' => array_key_exists('file', $metadata) ? $this->getImageUrl($metadata['file']) : null,
                'srcWebp' => array_key_exists('webp', $metadata) ? $this->getWebpUrl($metadata['webp']) : null,
                'width' => $metadata['width'],
                'height' => $metadata['height'],
            ];
        }

        return array_reverse($sources);
    }
}
