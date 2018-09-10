<?php
namespace fruitstudios\palette\fields;

use fruitstudios\palette\fields\PaletteField;

use Craft;

class PaletteAlternativeFieldTemplate extends PaletteField
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
