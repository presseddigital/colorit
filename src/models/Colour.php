<?php
namespace fruitstudios\styleit\models;

use fruitstudios\styleit\helpers\ColourHelper;

use Craft;
use craft\base\Model;
use craft\validators\ColorValidator as ColourValidator;

class Colour extends Model
{

    // Constants
    // =========================================================================

    const TRANSPARENT_STRING = 'transparent';

    // Public Properties
    // =========================================================================

    private $_palette;
    private $_hex;

    // Public Properties
    // =========================================================================

    public function init()
    {
        parent::init();
    }

    public $handle;
    public $custom;
    public $opacity = 100;
    public $field;


    // Public Methods
    // =========================================================================

    public function __toString(): string
    {
        return $this->getColour();
    }

    public function rules()
    {
        $rules = parent::rules();
        $rules[] = ['handle', 'string'];
        $rules[] = ['custom', ColourValidator::class, 'when' => [$this, 'isCustomColour'], 'message' => Craft::t('styleit', 'A valid custom colour hex is required')];
        $rules[] = ['opacity', 'required'];
        $rules[] = ['opacity', 'default', 'value' => 100];
        $rules[] = ['opacity', 'integer', 'min' => 0, 'max' => 100];
        return $rules;
    }

    public function isCustomColour(): bool
    {
        return $this->handle == '_custom_';
    }

    public function isTransparent(): bool
    {
        return $this->handle == self::TRANSPARENT_STRING;
    }

    public function getColour(string $format = null)
    {
        $colour = '';
        if ($this->isTransparent())
        {
            return self::TRANSPARENT_STRING;
        }

        $format = $format ?? $this->field->colourFormat;
        switch ($format)
        {
            case 'hex':
                $colour = $this->getHex();
                break;
            case 'rgb':
                $colour = $this->getRgb();
                break;
            case 'rgba':
                $colour = $this->getRgba();
                break;
            default:
                $colour = $this->opacity < 100 ? $this->getRgba() : $this->getHex();
                break;
        }
        return $colour ? $colour : '';
    }

    public function hasColour()
    {
        return !empty($this->getColour());
    }

    public function getHex()
    {
        if ($this->isTransparent())
        {
            return self::TRANSPARENT_STRING;
        }

        if(is_null($this->_hex))
        {
            $hex = '';
            if ($this->handle)
            {
                switch ($this->handle)
                {
                    case '_custom_':
                        $hex = $this->custom;
                        break;

                    default:
                        $hex = $this->_inPalette($this->handle) ? $this->_palette[$this->handle]['colour'] : '';
                        break;
                }
            }

            if(empty($hex) || !ColourHelper::isValidHex($hex))
            {
                return '';
            }

            $this->_hex = $hex;
        }
        return $this->_hex;
    }

    public function getRgb()
    {
        if ($this->isTransparent())
        {
            return self::TRANSPARENT_STRING;
        }

        $hex = $this->getHex();
        if($hex)
        {
            $rgb = ColourHelper::hexToRgb($hex);
            if($rgb)
            {
                return $rgb;
            }
        }
        return false;
    }

    public function getRgba()
    {
        if ($this->isTransparent())
        {
            return self::TRANSPARENT_STRING;
        }

        $hex = $this->getHex();
        if($hex)
        {
            $rgba = ColourHelper::hexToRgba($hex, $this->opacity);
            if($rgba)
            {
                return $rgba;
            }
        }
        return false;
    }

    public function getR()
    {
        $hex = $this->getHex();
        if($hex)
        {
            $rgb = ColourHelper::hexToRgb($hex, true);
            if($rgb)
            {
                return $rgb['r'];
            }
        }
        return false;
    }

    public function getG()
    {
        $hex = $this->getHex();
        if($hex)
        {
            $rgb = ColourHelper::hexToRgb($hex, true);
            if($rgb)
            {
                return $rgb['g'];
            }
        }
        return false;
    }

    public function getB()
    {
        $hex = $this->getHex();
        if($hex)
        {
            $rgb = ColourHelper::hexToRgb($hex, true);
            if($rgb)
            {
                return $rgb['b'];
            }
        }
        return false;
    }

    public function getA()
    {
        if ($this->isTransparent())
        {
            return 0;
        }

        return $this->opacity / 100;
    }

    // US Versions
    // =========================================================================

    public function getColor(string $format = null)
    {
        return $this->getColour($format);
    }

    public function hasColor(string $format = null)
    {
        return $this->hasColour($format);
    }

    // Private Methods
    // =========================================================================

    private function _getPalette()
    {
        if(is_null($this->_palette))
        {
            $this->_palette = $this->field->getPalette() ?? [];
        }
        return $this->_palette;
    }

    private function _inPalette(string $handle)
    {
        return array_key_exists($handle, $this->_getPalette());
    }

}
