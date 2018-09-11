<?php
namespace fruitstudios\palette\fields;

use fruitstudios\palette\fields\PaletteAlternativeField;

use Craft;

class PaletteAlternativeFieldTemplate extends PaletteAlternativeField
{

    // Static Methods
    // =========================================================================

    public static function displayName(): string
    {
        return Craft::t('palette', 'Palette Alternative');
    }

    public static function isFieldTemplate(): bool
    {
        return true;
    }

    // Public Methods
    // =========================================================================

    public function rules()
    {
        return parent::fieldRules();
    }
}
