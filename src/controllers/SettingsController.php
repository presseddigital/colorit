<?php
namespace fruitstudios\palette\controllers;

use fruitstudios\palette\Palette;

use Craft;
use craft\web\Controller;

class SettingsController extends Controller
{
    // Public Methods
    // =========================================================================

    public function actionGeneral()
    {
        $settings = Palette::$plugin->getSettings();

        return $this->renderTemplate('palette/settings/general', [
            'settings' => $settings,
        ]);
    }
}
