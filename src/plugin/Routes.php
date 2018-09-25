<?php
namespace fruitstudios\colorit\plugin;

use craft\events\RegisterUrlRulesEvent;
use craft\web\UrlManager;
use yii\base\Event;

trait Routes
{
    // Private Methods
    // =========================================================================

    private function _registerCpRoutes()
    {
        Event::on(UrlManager::class, UrlManager::EVENT_REGISTER_CP_URL_RULES, function(RegisterUrlRulesEvent $event) {
            $event->rules['colorit'] = ['template' => 'colorit/index'];
            $event->rules['colorit/settings/general'] = 'colorit/settings/general';
            $event->rules['colorit/settings/presets'] = 'colorit/presets/index';
            $event->rules['colorit/settings/presets/<presetId:\d+>'] = 'colorit/presets/edit';
            $event->rules['colorit/settings/presets/new'] = 'colorit/presets/edit';
        });
    }
}
