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

    // Public Methods
    // =========================================================================

	public function rules(): array
    {
        return [
            [['pluginName', 'fieldTemplates'], 'string'],
            ['pluginName', 'default', 'value' => 'Palette'],
            ['showInCpNav', 'boolean'],
            ['showInCpNav', 'default', 'value' => false],
        ];
    }
}
