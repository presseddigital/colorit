<?php

namespace presseddigital\colorit\web\assets\colorit;

use yii\web\AssetBundle;

class ColoritAssetBundle extends AssetBundle
{
    // Public Methods
    // =========================================================================

    public function init(): void
    {
        $this->sourcePath = "@presseddigital/colorit/web/assets/colorit/build";

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
