<?php
namespace fruitstudios\styleit\variables;

use fruitstudios\styleit\Styleit;
use fruitstudios\styleit\helpers\ColourHelper;

class StyleitVariable
{
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
