<?php
namespace fruitstudios\palette\fields;

use fruitstudios\palette\Palette;
use fruitstudios\palette\fields\PaletteFieldTemplate;
use fruitstudios\palette\models\Colour;
use fruitstudios\palette\helpers\ColourHelper;
use fruitstudios\palette\web\assets\palette\PaletteAssetBundle;

use Craft;
use craft\web\View;
use craft\base\ElementInterface;
use craft\base\Element;
use craft\base\Field;
use craft\helpers\Db;
use craft\helpers\Json;
use craft\validators\ColourValidator;
use craft\validators\ArrayValidator;

use yii\db\Schema;

/**
 * @author    Fruit Studios
 * @package   Palette
 * @since     1.0.0
 */
class PaletteField extends Field
{
    // Public Properties
    // =========================================================================

    public $fieldTemplateId;
    public $paletteColours;
    public $paletteBaseColours;
    public $allowCustomColour = false;
    public $allowOpacity = false;
    public $colourFormat = 'auto';

    // Static Methods
    // =========================================================================

    public static function displayName(): string
    {
        return Craft::t('palette', 'Palette');
    }

    public static function isFieldTemplate(): bool
    {
        return false;
    }

    public static function fieldRules(): array
    {
        $rules = [];
        $rules[] = ['paletteColours', 'validatePaletteColours'];
        $rules[] = ['paletteBaseColours', ArrayValidator::class];
        $rules[] = ['colourFormat', 'string'];
        $rules[] = ['colourFormat', 'default', 'value' => 'auto'];
        $rules[] = [['allowCustomColour', 'allowOpacity'], 'boolean'];
        $rules[] = [['allowCustomColour', 'allowOpacity'], 'default', 'value' => false];
        return $rules;
    }


    // Public Methods
    // =========================================================================

    public function init()
    {
        parent::init();
        if($this->fieldTemplateId)
        {
            $fieldTemplate = Palette::$plugin->getFieldTemplates()->getFieldTemplateById($this->fieldTemplateId);
            Craft::configure($this, $fieldTemplate->getSettings());
        }
    }

    public function rules()
    {
        return array_merge(parent::rules(), self::fieldRules());
    }

    public function getType(): string
    {
        return get_class($this);
    }

    public function validatePaletteColours()
    {
        return true;
    }

    public function getContentColumnType(): string
    {
        return Schema::TYPE_TEXT;
    }

    public function isValueEmpty($value, ElementInterface $element): bool
    {
        return empty($value->handle ?? '');
    }

    public function getElementValidationRules(): array
    {
        return ['validateColourValue'];
    }

    public function validateColourValue(ElementInterface $element)
    {
        if ($element->getScenario() === Element::SCENARIO_LIVE)
        {
            $fieldValue = $element->getFieldValue($this->handle);
            if($fieldValue && !$fieldValue->validate())
            {
                $element->addModelErrors($fieldValue, $this->handle);
            }
        }
    }

    public function normalizeValue($value, ElementInterface $element = null)
    {
        if($value instanceof Colour)
        {
            return $value;
        }

        if(is_string($value))
        {
            $value = Json::decodeIfJson($value);
        }

        if (isset($value['handle']))
        {
            $colour = new Colour();
            $colour = Craft::configure($colour, $value);
            $colour->field = $this;
            return $colour;
        }
        return $value;
    }

    public function serializeValue($value, ElementInterface $element = null)
    {
        $serialized = [];
        if($value instanceof Colour)
        {
            $serialized = [
                'handle' => $value->handle,
                'custom' => $value->custom,
                'opacity' => $value->opacity,
            ];
        }

        return parent::serializeValue($serialized, $element);
    }

    // public function getSettings(): array
    // {
    //     $settings = [];
    //     $fieldTemplate;

    //     if($this->fieldTemplateId)
    //     {
    //         $fieldTemplate = Palette::$plugin->getFieldTemplates()->getFieldTemplateById($this->fieldTemplateId);
    //     }

    //     foreach ($this->settingsAttributes() as $attribute) {
    //         $settings[$attribute] = $fieldTemplate ? $fieldTemplate->settings->$attribute : $this->$attribute;
    //     }

    //     return $settings;
    // }

    public function getSettingsHtml()
    {
        $field = $this;
        $fieldTemplates = [];
        $fieldTemplateOptions = [];
        if(!self::isFieldTemplate())
        {
            $fieldTemplates = Palette::$plugin->getFieldTemplates()->getAllFieldTemplatesByType(PaletteFieldTemplate::class);
            if($fieldTemplates)
            {
                $fieldTemplateOptions[] = [ 'value' => '', 'label' => 'Inline Settings' ];
                foreach ($fieldTemplates as $fieldTemplate)
                {
                    $fieldTemplateOptions[] = [
                        'value' => $fieldTemplate->id,
                        'label' => $fieldTemplate->name,
                    ];
                }
            }
        }

        return Craft::$app->getView()->renderTemplate('palette/_fields/palette/settings', compact(
            'field',
            'fieldTemplates',
            'fieldTemplateOptions'
        ));
    }

    public function getInputHtml($value, ElementInterface $element = null): string
    {
        $view = Craft::$app->getView();
        $id = $view->formatInputId($this->handle);
        $namespacedId = Craft::$app->view->namespaceInputId($id);

        $view->registerAssetBundle(PaletteAssetBundle::class);
        $js = Json::encode([
            'id' => $id,
            'namespacedId' => $namespacedId,
            'name' => $this->handle,
            'debug' => Craft::$app->getConfig()->getGeneral()->devMode,
        ]);
        $view->registerJs('new Palette('.$js.');', View::POS_END);

        // TODO: Replace with settings set on init
        $field = $this;
        // if($this->fieldTemplateId)
        // {
        //     $fieldTemplate = Palette::$plugin->getFieldTemplates()->getFieldTemplateById($this->fieldTemplateId);
        //     $field = $fieldTemplate->getFieldType();
        // }

        return $view->renderTemplate(
            'palette/_fields/palette/input',
            [
                'id' => $id,
                'name' => $this->handle,
                'value' => $value,
                'field' => $field,
            ]
        );
    }

    public function getInputPreviewHtml(): string
    {
        $view = Craft::$app->getView();

        $view->registerAssetBundle(PaletteAssetBundle::class);

        return $view->renderTemplate(
            'palette/_fields/palette/preview',
            [
                'field' => $this,
            ]
        );
    }

    public function getPalette()
    {
        $palette = ColourHelper::baseColours($this->paletteBaseColours);

        if ($this->paletteColours)
        {
            foreach($this->paletteColours as $paletteColour)
            {
                $palette[$paletteColour['handle']] = $paletteColour;
            }
        }

        return $palette;
    }
}
