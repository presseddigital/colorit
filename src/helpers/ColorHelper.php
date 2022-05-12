<?php

namespace presseddigital\colorit\helpers;

use Craft;

class ColorHelper
{
    // Public Methods
    // =========================================================================
    /**
     * @return array<int|string, mixed[]>
     */
    public static function baseColors(array $include = null): array
    {
        $baseColors = [
            'transparent' => [
                'label' => Craft::t('colorit', 'Transparent'),
                'handle' => 'transparent',
                'color' => 'transparent',
            ],
            'white' => [
                'label' => Craft::t('colorit', 'White'),
                'handle' => 'white',
                'color' => 'FFFFFF',
            ],
            'black' => [
                'label' => Craft::t('colorit', 'Black'),
                'handle' => 'black',
                'color' => '000000',
            ],
        ];

        if ($include) {
            $included = [];
            foreach ($include as $handle) {
                if (array_key_exists($handle, $baseColors)) {
                    $included[$handle] = $baseColors[$handle];
                }
            }
            return $included;
        }

        return $baseColors;
    }

    /**
     * @return array<int, array{label: mixed, value: mixed}>
     */
    public static function baseColorsAsOptions(): array
    {
        $options = [];
        $baseColors = static::baseColors();
        if ($baseColors) {
            foreach ($baseColors as $baseColor) {
                $options[] = [
                    'label' => $baseColor['label'],
                    'value' => $baseColor['handle'],
                ];
            }
        }

        return $options;
    }

    public static function hexIsWhite(string $color): bool
    {
        $isWhite = false;
        $color = strtoupper($color);
        $isWhite = match ($color) {
            'WHITE', '#FFF', '#FFFFFF', 'FFF', 'FFFFFF' => true,
            default => $isWhite,
        };
        return match ($color) {
            'WHITE', '#FFF', '#FFFFFF', 'FFF', 'FFFFFF' => true,
            default => $isWhite,
        };
    }

    public static function hexIsTransparent(string $color): bool
    {
        $isTransparent = false;
        $color = strtoupper($color);
        switch ($color) {
            case 'TRANSPARENT':
                $isTransparent = true;
                break;
        }
        return $isTransparent;
    }

    public static function hexIsBlack(string $color): bool
    {
        $isBlack = false;
        $color = strtoupper($color);
        $isBlack = match ($color) {
            'BLACK', '#000', '#000000', '000', '000000' => true,
            default => $isBlack,
        };
        return match ($color) {
            'BLACK', '#000', '#000000', '000', '000000' => true,
            default => $isBlack,
        };
    }

    public static function isValidHex($color): bool
    {
        return preg_match('/^#?[0-9a-f]{3}(?:[0-9a-f]{3})?$/i', $color) ? true : false;
    }

    public static function hexToRgba($color, $opacity = false, $asArray = false): bool|array|string
    {
        if (empty($color)) {
            return false;
        }

        if ($color[0] == '#') {
            $color = substr($color, 1);
        }

        if (strlen($color) == 6) {
            $hex = array( $color[0] . $color[1], $color[2] . $color[3], $color[4] . $color[5] );
        } elseif (strlen($color) == 3) {
            $hex = array( $color[0] . $color[0], $color[1] . $color[1], $color[2] . $color[2] );
        } else {
            return false;
        }

        $rgb = array_map('hexdec', $hex);

        if (is_numeric($opacity)) {
            $array = array(
                'r' => $rgb[0],
                'g' => $rgb[1],
                'b' => $rgb[2],
                'a' => ($opacity / 100),
            );
            $string = 'rgba(' . implode(',',$rgb) . ',' . $array['a'] . ')';
        } else {
            $array = array(
                'r' => $rgb[0],
                'g' => $rgb[1],
                'b' => $rgb[2],
            );
            $string = 'rgb(' . implode(',',$rgb) . ')';
        }

        return $asArray ? $array : $string;
    }


    public static function hexToRgb($color, $asArray = false)
    {
        return static::hexToRgba($color, false, $asArray);
    }


    // UK Versions
    public static function baseColours(array $include = null)
    {
        return self::baseColors($include);
    }

    public static function baseColoursAsOptions(array $include = null)
    {
        return self::baseColorsAsOptions();
    }
}
