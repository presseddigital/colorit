<?php
namespace fruitstudios\palette\web\twig;

use fruitstudios\palette\Palette;

use Craft;
use yii\base\Behavior;

class CraftVariableBehavior extends Behavior
{
    public $palette;

    public function init()
    {
        parent::init();
        // Point `craft.palette` to the craft\palette\Plugin instance
        $this->palette = Palette::getInstance();
    }

}
