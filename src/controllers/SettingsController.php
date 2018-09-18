<?php
namespace fruitstudios\colorit\controllers;

use fruitstudios\colorit\Colorit;

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
        return $this->renderTemplate('colorit/settings/general', [
            'settings' => Colorit::$settings,
        ]);
    }
}
