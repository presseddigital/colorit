<?php

namespace presseddigital\colorit\web\twig;

use presseddigital\colorit\Colorit;

class Extension extends \Twig\Extension\AbstractExtension
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

    /**
     * @return \Twig\TwigFilter[]
     */
    public function getFilters(): array
    {
        return [
            new \Twig\TwigFilter('hexIsWhite', [$this->colors, 'hexIsWhite']),
            new \Twig\TwigFilter('hexIsBlack', [$this->colors, 'hexIsBlack']),
            new \Twig\TwigFilter('hexIsTransparent', [$this->colors, 'hexIsTransparent']),
            new \Twig\TwigFilter('hexToRgb', [$this->colors, 'hexToRgb']),
            new \Twig\TwigFilter('hexToRgba', [$this->colors, 'hexToRgba']),
        ];
    }
}
