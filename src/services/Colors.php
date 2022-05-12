<?php

namespace presseddigital\colorit\services;

use craft\base\Component;

use presseddigital\colorit\helpers\ColorHelper;

class Colors extends Component
{
    // Public Methods
    // =========================================================================

    public function getBaseColors()
    {
        return ColorHelper::baseColors();
    }

    public function getBaseColorsAsOptions()
    {
        return ColorHelper::baseColorsAsOptions();
    }

    public function hexIsWhite(string $color)
    {
        return ColorHelper::hexIsWhite($color);
    }

    public function hexIsBlack(string $color)
    {
        return ColorHelper::hexIsBlack($color);
    }

    public function hexIsTransparent(string $color)
    {
        return ColorHelper::hexIsTransparent($color);
    }

    public function hexToRgba($color, $opacity = 100, $asArray = false)
    {
        return ColorHelper::hexToRgba($color, $opacity, $asArray);
    }

    public function hexToRgb($color, $asArray = false)
    {
        return ColorHelper::hexToRgb($color, $asArray);
    }

    // UK Versions

    public function getBaseColours()
    {
        return $this->getBaseColors();
    }

    public function getBaseColoursAsOptions()
    {
        return $this->getBaseColorsAsOptions();
    }
}
