<?php
namespace fruitstudios\colorit\web\twig;

use fruitstudios\colorit\Colorit;

use Craft;

class Extension extends \Twig_Extension
{
    protected $colours;

    // Public Methods
    // =========================================================================

    public function __construct()
    {
        $this->colours = Colorit::$plugin->getColours();
    }

    public function getName(): string
    {
        return 'Colorit Twig Extensions';
    }

    public function getFilters(): array
    {
        return [
            new \Twig_SimpleFilter('hexIsWhite', [$this->colours, 'hexIsWhite']),
            new \Twig_SimpleFilter('hexIsBlack', [$this->colours, 'hexIsBlack']),
            new \Twig_SimpleFilter('hexIsTransparent', [$this->colours, 'hexIsTransparent']),
            new \Twig_SimpleFilter('hexToRgb', [$this->colours, 'hexToRgb']),
            new \Twig_SimpleFilter('hexToRgba', [$this->colours, 'hexToRgba']),
        ];
    }
}
