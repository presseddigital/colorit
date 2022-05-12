<?php

namespace presseddigital\colorit\web\twig;

use Craft;

use presseddigital\colorit\Colorit;
use yii\base\Behavior;

class CraftVariableBehavior extends Behavior
{
    public $colorit;

    public function init(): void
    {
        parent::init();
        // Point `craft.colorit` to the craft\colorit\Plugin instance
        $this->colorit = Colorit::getInstance();
    }
}
