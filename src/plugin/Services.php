<?php
namespace fruitstudios\colorit\plugin;

use fruitstudios\colorit\services\Colours;
use fruitstudios\colorit\services\Fields;
use fruitstudios\colorit\services\FieldTemplates;

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
