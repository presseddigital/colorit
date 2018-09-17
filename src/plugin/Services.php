<?php
namespace fruitstudios\palette\plugin;

use fruitstudios\palette\services\Colours;
use fruitstudios\palette\services\Fields;
use fruitstudios\palette\services\FieldTemplates;

trait Services
{
    // Public Methods
    // =========================================================================

    public function getFieldTemplates(): FieldTemplates
    {
        return $this->get('fieldTemplates');
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
            'fieldTemplates' => FieldTemplates::class,
            'fields' => Fields::class,
        ]);
    }
}
