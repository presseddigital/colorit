<?php
namespace fruitstudios\colorit\web\twig;

use fruitstudios\colorit\Plugin;

use Craft;

class Extension extends \Twig_Extension
{
    // Public Methods
    // =========================================================================

    public function getName(): string
    {
        return 'Colorit Twig Extensions';
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
