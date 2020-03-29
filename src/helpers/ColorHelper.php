<?php
namespace presseddigital\colorit\helpers;

use Craft;

class ColorHelper
{
    // Public Methods
    // =========================================================================

    public static function baseColors(array $include = null)
    {
        $baseColors = [
            'transparent' => [
                'label' => Craft::t('colorit', 'Transparent'),
                'handle' => 'transparent',
                'color' => 'transparent'
            ],
            'white' => [
                'label' => Craft::t('colorit', 'White'),
                'handle' => 'white',
                'color' => '#FFFFFF'
            ],
            'black' => [
                'label' => Craft::t('colorit', 'Black'),
                'handle' => 'black',
                'color' => '#000000'
            ],
        ];

        if($include)
        {
            $included = [];
            foreach ($include as $handle)
            {
                if(array_key_exists($handle, $baseColors))
                {
                    $included[$handle] = $baseColors[$handle];
                }
            }
            return $included;
        }

        return $baseColors;
    }

    public static function baseColorsAsOptions()
    {
        $options = [];
        $baseColors = static::baseColors();
        if($baseColors)
        {
            foreach ($baseColors as $baseColor)
            {
                $options[] = [
                    'label' => $baseColor['label'],
                    'value' => $baseColor['handle'],
                ];
            }
        }

        return $options;
    }

    public static function hexIsWhite(string $color)
    {
        $isWhite = false;
        $color = strtoupper($color);
        switch($color)
        {
            case 'WHITE':
            case '#FFF':
            case '#FFFFFF':
                $isWhite = true;
                break;
        }
        return $isWhite;
    }

    public static function hexIsTransparent(string $color)
    {
        $isTransparent = false;
        $color = strtoupper($color);
        switch($color)
        {
            case 'TRANSPARENT':
                $isTransparent = true;
                break;
        }
        return $isTransparent;
    }

    public static function hexIsBlack(string $color)
    {
        $isBlack = false;
        $color = strtoupper($color);
        switch($color)
        {
            case 'BLACK':
            case '#000':
            case '#000000':
                $isBlack = true;
                break;
        }
        return $isBlack;
    }

    public static function isValidHex($color)
    {
        return preg_match('/^#[0-9a-f]{3}(?:[0-9a-f]{3})?$/i', $color) ? true : false;
    }

    public static function hexToRgba($color, $opacity = false, $asArray = false)
    {
        if( empty($color) )
        {
            return false;
        }

        if( $color[0] == '#' )
        {
            $color = substr( $color, 1 );
        }

        if( strlen($color) == 6 )
        {
            $hex = array( $color[0] . $color[1], $color[2] . $color[3], $color[4] . $color[5] );
        }
        elseif( strlen( $color ) == 3 )
        {
            $hex = array( $color[0] . $color[0], $color[1] . $color[1], $color[2] . $color[2] );
        }
        else
        {
            return false;
        }

        $rgb =  array_map('hexdec', $hex);

        if(is_numeric($opacity))
        {
            $array = array(
                'r' => $rgb[0],
                'g' => $rgb[1],
                'b' => $rgb[2],
                'a' => ( $opacity / 100 )
            );
            $string = 'rgba('.implode(',',$rgb).','.$array['a'].')';
        }
        else
        {
            $array = array(
                'r' => $rgb[0],
                'g' => $rgb[1],
                'b' => $rgb[2]
            );
            $string = 'rgb('.implode(',',$rgb).')';
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
        return self::baseColorsAsOptions($include);
    }
}
