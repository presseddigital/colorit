<?php
namespace fruitstudios\colorit\web\assets\colorit;

use Craft;

use yii\web\AssetBundle;
use craft\web\assets\cp\CpAsset;

class ColoritAssetBundle extends AssetBundle
{
    // Public Methods
    // =========================================================================

    public function init()
    {
        $this->sourcePath = "@fruitstudios/colorit/web/assets/colorit/build";

        $this->depends = [];

        $this->js = [
            'js/vendor/polyfill.js',
            'js/vendor/extend.js',
            'js/Colorit.js',
        ];

        $this->css = [
            'css/cp.css',
            'css/colorit.css',
        ];

        parent::init();
    }
}
