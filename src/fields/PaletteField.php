<?php
namespace fruitstudios\styleit\fields;

use fruitstudios\styleit\Styleit;
use fruitstudios\styleit\models\Colour;
use fruitstudios\styleit\helpers\ColourHelper;
use fruitstudios\styleit\assetbundles\styleit\PaletteAssetBundle;

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
 * @package   Styleit
 * @since     1.0.0
 */
class PaletteField extends Field
{
    // Public Properties
    // =========================================================================

    const SCENARIO_GLOBAl = 'global';

    // Public Properties
    // =========================================================================

    public $useGlobalSettings = false;
    public $paletteColours;
    public $paletteBaseColours;
    public $allowCustomColour = false;
    public $allowOpacity = false;
    public $colourFormat = 'auto';

    // Static Methods
    // =========================================================================

    public static function displayName(): string
    {
        return Craft::t('styleit', 'Palette');
    }

    // Public Methods
    // =========================================================================

    public function init()
    {
        parent::init();
        if($this->useGlobalSettings && $this->getScenario() != self::SCENARIO_GLOBAl)
        {
            $globalSettings = Styleit::$plugin->getSettings()->palette ?? [];
            Craft::configure($this, $globalSettings);
            $this->useGlobalSettings = true;
        }
    }
    public function rules()
    {
        $rules = parent::rules();
        $rules[] = ['paletteColours', 'validatePaletteColours'];
        $rules[] = ['paletteBaseColours', ArrayValidator::class];
        $rules[] = ['colourFormat', 'string'];
        $rules[] = ['useGlobalSettings', 'default', 'value' => false];
        $rules[] = ['colourFormat', 'default', 'value' => 'auto'];
        $rules[] = [['allowCustomColour', 'allowOpacity'], 'boolean'];
        $rules[] = [['allowCustomColour', 'allowOpacity'], 'default', 'value' => false];
        return $rules;
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

    public function getSettingsHtml()
    {
        return Craft::$app->getView()->renderTemplate(
            'styleit/fields/palette/settings',
            [
                'field' => $this,
            ]
        );
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

        return $view->renderTemplate(
            'styleit/fields/palette/input',
            [
                'id' => $id,
                'name' => $this->handle,
                'value' => $value,
                'field' => $this,
            ]
        );
    }

    public function getInputPreviewHtml(): string
    {
        $view = Craft::$app->getView();

        $view->registerAssetBundle(PaletteAssetBundle::class);

        return $view->renderTemplate(
            'styleit/fields/palette/preview',
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
