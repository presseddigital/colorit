<?php
namespace fruitstudios\colorit\plugin;

use fruitstudios\colorit\services\Colors;
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

    public function getColors(): Colors
    {
        return $this->get('colors');
    }

    public function getColours(): Colors
    {
        return $this->getColors();
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
            'colors' => Colors::class,
            'colours' => Colors::class,
            'presets' => Presets::class,
            'fields' => Fields::class,
        ]);
    }
}
