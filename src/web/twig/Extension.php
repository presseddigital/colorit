<?php
namespace fruitstudios\palette\web\twig;

use fruitstudios\palette\Plugin;

use Craft;

class Extension extends \Twig_Extension
{
    // Public Methods
    // =========================================================================

    public function getName(): string
    {
        return 'Palette Twig Extension';
    }

    public function getFilters(): array
    {
        return [
            new \Twig_SimpleFilter('customFilter', [$this, 'customFilterFunction'])
        ];
    }

    public function customFilterFunction($string): string
    {
        return $string;
    }

}
