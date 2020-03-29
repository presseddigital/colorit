<?php
namespace presseddigital\colorit\web\twig;

use presseddigital\colorit\Colorit;

use Craft;
use yii\base\Behavior;

class CraftVariableBehavior extends Behavior
{
    public $colorit;

    public function init()
    {
        parent::init();
        // Point `craft.colorit` to the craft\colorit\Plugin instance
        $this->colorit = Colorit::getInstance();
    }

}
