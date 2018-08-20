<?php
namespace fruitstudios\palette\helpers;

use Craft;

class ColourHelper
{
    // Public Methods
    // =========================================================================

    public static function baseColours(array $include = null)
    {
        $baseColours = [
            'transparent' => [
                'label' => Craft::t('palette', 'Transparent'),
                'handle' => 'transparent',
                'colour' => 'transparent'
            ],
            'white' => [
                'label' => Craft::t('palette', 'White'),
                'handle' => 'white',
                'colour' => '#FFFFFF'
            ],
            'black' => [
                'label' => Craft::t('palette', 'Black'),
                'handle' => 'black',
                'colour' => '#000000'
            ],
        ];

        if($include)
        {
            $included = [];
            foreach ($include as $handle)
            {
                if(array_key_exists($handle, $baseColours))
                {
                    $included[$handle] = $baseColours[$handle];
                }
            }
            return $included;
        }

        return $baseColours;
    }

    public static function baseColoursAsOptions()
    {
        $options = [];
        $baseColours = static::baseColours();
        if($baseColours)
        {
            foreach ($baseColours as $baseColour)
            {
                $options[] = [
                    'label' => $baseColour['label'],
                    'value' => $baseColour['handle'],
                ];
            }
        }

        return $options;
    }

    public static function hexIsWhite(string $colour)
    {
        $isWhite = false;
        $colour = strtoupper($colour);
        switch($colour)
        {
            case 'WHITE':
            case '#FFF':
            case '#FFFFFF':
                $isWhite = true;
                break;
        }
        return $isWhite;
    }

    public static function hexIsTransparent(string $colour)
    {
        $isTransparent = false;
        $colour = strtoupper($colour);
        switch($colour)
        {
            case 'TRANSPARENT':
                $isTransparent = true;
                break;
        }
        return $isTransparent;
    }

    public static function hexIsBlack(string $colour)
    {
        $isBlack = false;
        $colour = strtoupper($colour);
        switch($colour)
        {
            case 'BLACK':
            case '#000':
            case '#000000':
                $isBlack = true;
                break;
        }
        return $isBlack;
    }

    public static function isValidHex($colour)
    {
        return preg_match('/^#[0-9a-f]{3}(?:[0-9a-f]{3})?$/i', $colour) ? true : false;
    }

    public static function hexToRgba($colour, $opacity = false, $asArray = false)
    {

        if( empty($colour) )
        {
            return false;
        }

        if( $colour[0] == '#' )
        {
            $colour = substr( $colour, 1 );
        }

        if( strlen($colour) == 6 )
        {
            $hex = array( $colour[0] . $colour[1], $colour[2] . $colour[3], $colour[4] . $colour[5] );
        }
        elseif( strlen( $colour ) == 3 )
        {
            $hex = array( $colour[0] . $colour[0], $colour[1] . $colour[1], $colour[2] . $colour[2] );
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


    public static function hexToRgb($colour, $asArray = false)
    {
        return static::hexToRgba($colour, false, $asArray);
    }

}
