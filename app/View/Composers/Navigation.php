<?php

namespace App\View\Composers;

use Roots\Acorn\View\Composer;
use Log1x\Navi\Navi;

class Navigation extends Composer
{
    /**
     * List of views served by this composer.
     *
     * @var array
     */
    protected static $views = [
        'sections.header',
        'sections.footer',
    ];

    /**
     * Data to be passed to view before rendering.
     *
     * @return array
     */
    public function with()
    {
        return [
            'header_navigation' => $this->headerNavigation(),
            'footer_navigation' => $this->footerNavigation(),
        ];
    }

    /**
     * Returns the header navigation.
     *
     * @return array
     */
    public function headerNavigation()
    {
        return (new Navi())->build('primary_navigation');
    }

    /**
     * Returns the footer navigation.
     *
     * @return array
     */
    public function footerNavigation()
    {
        return (new Navi())->build('footer_navigation');
    }
}
