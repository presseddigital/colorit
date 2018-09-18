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

            $event->rules['colorit/settings/fieldtemplates'] = 'colorit/field-templates/index';
            $event->rules['colorit/settings/fieldtemplates/<fieldTemplateId:\d+>'] = 'colorit/field-templates/edit';
            $event->rules['colorit/settings/fieldtemplates/new'] = 'colorit/field-templates/edit';

        });
    }
}
