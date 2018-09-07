<?php
namespace fruitstudios\palette\plugin;

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

            $event->rules['palette'] = ['template' => 'palette/index'];

            $event->rules['palette/settings/general'] = 'palette/settings/general';

            $event->rules['palette/settings/fieldtemplates'] = 'palette/field-templates/index';
            $event->rules['palette/settings/fieldtemplates/<fieldTemplateId:\d+>'] = 'palette/field-templates/edit';
            $event->rules['palette/settings/fieldtemplates/new'] = 'palette/field-templates/edit';

        });
    }
}
