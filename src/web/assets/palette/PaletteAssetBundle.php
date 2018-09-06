<?php
namespace fruitstudios\palette\web\assets\palette;

use Craft;

use yii\web\AssetBundle;
use craft\web\assets\cp\CpAsset;

class PaletteAssetBundle extends AssetBundle
{
    // Public Methods
    // =========================================================================

    public function init()
    {
        $this->sourcePath = "@fruitstudios/palette/web/assets/palette/build";

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
