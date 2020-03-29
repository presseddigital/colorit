<?php
namespace presseddigital\colorit\models;

use presseddigital\colorit\fields\ColoritField;

use Craft;
use craft\base\Model;

class Settings extends Model
{
    // Public Properties
    // =========================================================================

    public $pluginNameOverride = 'Colorit';
	public $hasCpSectionOverride = false;

    // Public Methods
    // =========================================================================

	public function rules(): array
    {
        return [
            ['pluginNameOverride', 'string'],
            ['pluginNameOverride', 'default', 'value' => 'Colorit'],
            ['hasCpSectionOverride', 'boolean'],
            ['hasCpSectionOverride', 'default', 'value' => false],
        ];
    }
}
