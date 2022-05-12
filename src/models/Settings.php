<?php

namespace presseddigital\colorit\models;

use craft\base\Model;

class Settings extends Model
{
    // Public Properties
    // =========================================================================

    public string $pluginNameOverride = 'Colorit';
    public bool $hasCpSectionOverride = false;

    // Public Methods
    // =========================================================================
    /**
     * @return array<int, mixed[]>
     */
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
