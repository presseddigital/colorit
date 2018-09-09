<?php
namespace fruitstudios\palette\models;

use fruitstudios\palette\fields\PaletteField;

use Craft;
use craft\base\Model;

class Settings extends Model
{
    // Public Properties
    // =========================================================================

    public $pluginNameOverride = 'Palette';
	public $hasCpSectionOverride = false;

    // Public Methods
    // =========================================================================

	public function rules(): array
    {
        return [
            ['pluginNameOverride', 'string'],
            ['pluginNameOverride', 'default', 'value' => 'Palette'],
            ['hasCpSectionOverride', 'boolean'],
            ['hasCpSectionOverride', 'default', 'value' => false],
        ];
    }
}
