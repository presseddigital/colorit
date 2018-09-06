<?php
namespace fruitstudios\palette\plugin;

use fruitstudios\palette\services\Field;

trait Services
{
    // Public Methods
    // =========================================================================

    public function getField(): Field
    {
        return $this->get('field');
    }

    // Private Methods
    // =========================================================================

    private function _setPluginComponents()
    {
        $this->setComponents([
            'field' => Field::class,
        ]);
    }
}
