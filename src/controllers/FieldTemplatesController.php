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

    public function actionEdit(int $fieldTemplateId = null, FieldTemplate $fieldTemplate = null): Response
    {
        if (!$fieldTemplate)
        {
            if ($fieldTemplateId)
            {
                $fieldTemplate = Palette::$plugin->getFieldTemplates()->getFieldTemplateById($fieldTemplateId);
                if (!$fieldTemplate)
                {
                    throw new HttpException(404);
                }
            }
            else
            {
                $fieldTemplate = new FieldTemplate();
            }
        }

        return $this->renderTemplate('palette/settings/fieldtemplates/_edit', [
            'fieldTemplate' => $fieldTemplate
        ]);
    }

    public function actionSave()
    {
        $this->requirePostRequest();

        $fieldTemplate = new FieldTemplate();

        // Shared attributes
        $fields = ['id', 'name', 'type', 'settings'];
        foreach ($fields as $field)
        {
            $fieldTemplate->$field = Craft::$app->getRequest()->getBodyParam($field);
        }

        if (!Palette::$plugin->getFieldTemplates()->saveFieldTemplate($fieldTemplate)) {
            Craft::$app->getSession()->setError(Craft::t('palette', 'Couldn’t save field template.'));

            // Send the plugin back to the template
            Craft::$app->getUrlManager()->setRouteParams([
                'fieldTemplate' => $fieldTemplate
            ]);

            return null;
        }

        Craft::$app->getSession()->setNotice(Craft::t('palette', 'Field template saved.'));

        return $this->redirectToPostedUrl();
    }

    public function actionDelete(): Response
    {
        $this->requirePostRequest();
        $this->requireAcceptsJson();

        $id = Craft::$app->getRequest()->getRequiredBodyParam('id');

        if (Palette::$plugin->getFieldTemplates()->deleteFieldTemplateById($id))
        {
            return $this->asJson(['success' => true]);
        }
        return $this->asErrorJson(Craft::t('palette', 'Could not delete field template'));
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
