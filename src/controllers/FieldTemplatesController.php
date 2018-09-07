<?php
namespace fruitstudios\palette\controllers;

use fruitstudios\palette\Palette;
use fruitstudios\palette\models\FieldTemplate;

use Craft;
use craft\web\Controller;
use craft\helpers\StringHelper;

use yii\web\Response;

class FieldTemplatesController extends Controller
{
    // Public Methods
    // =========================================================================

    public function actionIndex(): Response
    {
        $fieldTemplates = Palette::$plugin->getFieldTemplates()->getAllFieldTemplates();

        return $this->renderTemplate('palette/settings/fieldtemplates/index', compact('fieldTemplates'));
    }

    public function actionEdit(int $id = null, FieldTemplate $fieldTemplate = null): Response
    {

        $variables = [
            'id' => $id,
            'fieldTemplate' => $fieldTemplate,
        ];

        if (!$variables['fieldTemplate'])
        {
            if ($variables['id'])
            {
                $variables['fieldTemplate'] = Palette::$plugin->getFieldTemplates()->getFieldTemplateById($variables['id']);
                if (!$variables['fieldTemplate'])
                {
                    throw new HttpException(404);
                }
            }
            else
            {
                $variables['fieldTemplate'] = new FieldTemplate();
            }
        }

        return $this->renderTemplate('palette/settings/fieldtemplates/_edit', $variables);
    }

    public function actionSave()
    {
        $this->requirePostRequest();

        $type = Craft::$app->getRequest()->getBodyParam('type', false);
        $attributes = Craft::$app->getRequest()->getBodyParam('settings', []);

        $isNew = !$handle;
        if($isNew)
        {
            $fieldTemplate = Palette::$plugin->getFieldTemplates()->createFieldTemplate($type);
        }
        if(!$isNew)
        {
            $fieldTemplate = Palette::$plugin->getFieldTemplates()->getFieldTemplate($type, $handle);
        }




        $fieldTemplate->validate();

        Craft::dd($fieldTemplate);

        if ($plugin === null) {
            throw new NotFoundHttpException('Plugin not found');
        }

        if (!Craft::$app->getPlugins()->savePluginSettings($plugin, $settings)) {
            Craft::$app->getSession()->setError(Craft::t('app', 'Couldn’t save plugin settings.'));

            // Send the plugin back to the template
            Craft::$app->getUrlManager()->setRouteParams([
                'plugin' => $plugin
            ]);

            return null;
        }

        Craft::$app->getSession()->setNotice(Craft::t('app', 'Plugin settings saved.'));

        return $this->redirectToPostedUrl();
    }

    public function actionDelete(): Response
    {
        $settings = Palette::$settings;

        return $this->renderTemplate('palette/settings/fieldtemplates/_edit', [
            'fieldTemplate' => false,
        ]);
    }

    // Private Methods
    // =========================================================================

    private function _getFieldTemplateModel(string $type, array $attributes = [])
    {
        try {
            $fieldTemplate = Craft::createObject($type);
            return Craft::configure($fieldTemplate, $attributes);
        } catch(ErrorException $exception) {
            $error = $exception->getMessage();
            return false;
        }
    }

}
