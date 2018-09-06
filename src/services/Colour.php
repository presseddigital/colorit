<?php
namespace fruitstudios\palette\services;

use fruitstudios\palette\Palette;
use fruitstudios\palette\helpers\ColourHelper;

use Craft;
use craft\base\Component;

class ColourService extends Component
{
    // Public Methods
    // =========================================================================

    public function getBaseColours()
    {
        return ColourHelper::baseColours();
    }

    public function getBaseColoursAsOptions()
    {
        return ColourHelper::baseColoursAsOptions();
    }

    public function hexIsWhite(string $colour)
    {
        return ColourHelper::hexIsWhite($colour);
    }

    public function hexIsBlack(string $colour)
    {
        return ColourHelper::hexIsBlack($colour);
    }

    public function hexIsTransparent(string $colour)
    {
        return ColourHelper::hexIsTransparent($colour);
    }

    public function hexToRgba($colour, $opacity = false, $asArray = false)
    {
        return ColourHelper::hexToRgba($colour, $opacity, $asArray);
    }

    public function hexToRgb($colour, $asArray = false)
    {
        return ColourHelper::hexToRgb($colour, $asArray);
    }

}
