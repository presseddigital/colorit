<?php
namespace fruitstudios\palette\models;

use fruitstudios\palette\fields\PaletteField;

use Craft;
use craft\base\Model;

class Settings extends Model
{
    // Public Properties
    // =========================================================================

	public $pluginName = 'Palette';
	public $showInCpNav = false;

    public $presets;
    public $palette;

    // Public Methods
    // =========================================================================

	public function rules(): array
    {
        return [
            ['pluginName', 'string'],
            ['pluginName', 'default', 'value' => 'Palette'],
            ['showInCpNav', 'boolean'],
            ['showInCpNav', 'default', 'value' => false],
        ];
    }
}
