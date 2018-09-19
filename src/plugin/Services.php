<?php
namespace fruitstudios\colorit\plugin;

use fruitstudios\colorit\services\Colours;
use fruitstudios\colorit\services\Fields;
use fruitstudios\colorit\services\Presets;

trait Services
{
    // Public Methods
    // =========================================================================

    public function getPresets(): Presets
    {
        return $this->get('presets');
    }

    public function getColours(): Colours
    {
        return $this->get('colours');
    }

    public function getFields(): Fields
    {
        return $this->get('fields');
    }

    // Private Methods
    // =========================================================================

    private function _setPluginComponents()
    {
        $this->setComponents([
            'colours' => Colours::class,
            'colors' => Colours::class,
            'presets' => Presets::class,
            'fields' => Fields::class,
        ]);
    }
}
