<?php
namespace fruitstudios\palette\fields;

use fruitstudios\palette\fields\PaletteField;

use Craft;

/**
 * @author    Fruit Studios
 * @package   Palette
 * @since     1.0.0
 */
class PaletteFieldTemplate extends PaletteField
{
    // Public Properties
    // =========================================================================

    // Static Methods
    // =========================================================================

    public static function isFieldTemplate(): bool
    {
        return true;
    }

    // Public Methods
    // =========================================================================

    public function init()
    {
        parent::init();
    }

    public function rules()
    {
        $rules = parent::rules();
        $rules[] = [['name', 'handle'], 'string'];
        $rules[] = [['name', 'handle'], 'required'];
        return $rules;
    }
}
