<?php
namespace fruitstudios\palette\assetbundles\palette;

use Craft;

use yii\web\AssetBundle;
use craft\web\assets\cp\CpAsset;

class PaletteAssetBundle extends AssetBundle
{
    // Public Methods
    // =========================================================================

    public function init()
    {
        $this->sourcePath = "@fruitstudios/palette/assetbundles/palette/build";

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
