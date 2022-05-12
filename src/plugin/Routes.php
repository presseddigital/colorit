<?php

namespace presseddigital\colorit\plugin;

use craft\events\RegisterUrlRulesEvent;
use craft\web\UrlManager;
use yii\base\Event;

trait Routes
{
    // Private Methods
    // =========================================================================

    private function _registerCpRoutes()
    {
        Event::on(UrlManager::class, UrlManager::EVENT_REGISTER_CP_URL_RULES, function(RegisterUrlRulesEvent $event): void {
            $event->rules['colorit'] = ['template' => 'colorit/index'];
            $event->rules['colorit/settings'] = 'colorit/settings';
            $event->rules['colorit/presets'] = 'colorit/presets/index';
            $event->rules['colorit/presets/<presetId:\d+>'] = 'colorit/presets/edit';
            $event->rules['colorit/presets/new'] = 'colorit/presets/edit';
        });
    }
}
