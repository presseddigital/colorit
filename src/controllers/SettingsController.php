<?php
namespace fruitstudios\palette\controllers;

use fruitstudios\palette\Palette;
use fruitstudios\palette\fields\PaletteFieldTemplate;

use Craft;
use craft\web\Controller;
use craft\helpers\StringHelper;

use yii\web\Response;

class SettingsController extends Controller
{
    // Public Methods
    // =========================================================================

    public function actionGeneral()
    {
        return $this->renderTemplate('palette/settings/general', [
            'settings' => Palette::$settings,
        ]);
    }
}
