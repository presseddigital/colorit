<?php
namespace fruitstudios\styleit\assetbundles\styleit;

use Craft;

use yii\web\AssetBundle;
use craft\web\assets\cp\CpAsset;

class PaletteAssetBundle extends AssetBundle
{
    // Public Methods
    // =========================================================================

    public function init()
    {
        $this->sourcePath = "@fruitstudios/styleit/assetbundles/styleit/build";

        $this->depends = [];

        $this->js = [
            'js/vendor/polyfill.js',
            'js/vendor/extend.js',
            'js/Palette.js',
        ];

        $this->css = [
            'css/styles.css',
        ];

        parent::init();
    }
}
