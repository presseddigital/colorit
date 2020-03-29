<?php
namespace presseddigital\colorit\web\twig;

use presseddigital\colorit\Colorit;

use Craft;

class Extension extends \Twig_Extension
{
    protected $colors;

    // Public Methods
    // =========================================================================

    public function __construct()
    {
        $this->colors = Colorit::$plugin->getColors();
    }

    public function getName(): string
    {
        return 'Colorit Twig Extensions';
    }

    public function getFilters(): array
    {
        return [
            new \Twig_SimpleFilter('hexIsWhite', [$this->colors, 'hexIsWhite']),
            new \Twig_SimpleFilter('hexIsBlack', [$this->colors, 'hexIsBlack']),
            new \Twig_SimpleFilter('hexIsTransparent', [$this->colors, 'hexIsTransparent']),
            new \Twig_SimpleFilter('hexToRgb', [$this->colors, 'hexToRgb']),
            new \Twig_SimpleFilter('hexToRgba', [$this->colors, 'hexToRgba']),
        ];
    }
}
