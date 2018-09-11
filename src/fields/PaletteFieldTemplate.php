<?php
namespace fruitstudios\palette\fields;

use fruitstudios\palette\fields\PaletteField;

use Craft;

class PaletteFieldTemplate extends PaletteField
{
    // Static Methods
    // =========================================================================

    public static function isFieldTemplate(): bool
    {
        return true;
    }

    public function ignoreErrors()
    {
        return [
            'name',
            'handle'
        ];
    }

    // Public Methods
    // =========================================================================

    public function init()
    {
        parent::init();
    }

    public function rules()
    {
        return parent::fieldRules();
    }
}
