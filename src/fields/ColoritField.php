<?php
namespace fruitstudios\colorit\fields;

use fruitstudios\colorit\Colorit;
use fruitstudios\colorit\models\Colour;
use fruitstudios\colorit\models\PaletteColour;
use fruitstudios\colorit\helpers\ColourHelper;
use fruitstudios\colorit\web\assets\colorit\ColoritAssetBundle;

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
class ColoritField extends Field
{
    // Public Properties
    // =========================================================================

    public $presetMode = false;

    public $presetId;
    public $paletteColours;
    public $paletteBaseColours;
    public $allowCustomColour = false;
    public $allowOpacity = false;
    public $colourFormat = 'auto';

    // Static Methods
    // =========================================================================

    public static function displayName(): string
    {
        return Craft::t('colorit', 'Colorit');
    }

    // Public Methods
    // =========================================================================

    public function init()
    {
        parent::init();
    }

    public function setPresetMode(bool $on)
    {
        $this->presetMode = $on;
    }

    public function rules()
    {
        $rules = [];
        $rules[] = ['paletteColours', 'validatePaletteColours'];
        $rules[] = ['paletteBaseColours', ArrayValidator::class];
        $rules[] = ['colourFormat', 'string'];
        $rules[] = ['colourFormat', 'default', 'value' => 'auto'];
        $rules[] = [['allowCustomColour', 'allowOpacity'], 'boolean'];
        $rules[] = [['allowCustomColour', 'allowOpacity'], 'default', 'value' => false];

        if($this->presetMode)
        {
            return $rules;
        }
        return array_merge(parent::rules(), $rules);
    }

    public function validatePaletteColours()
    {
        foreach ($this->paletteColours as $i => $paletteColour)
        {
            $_paletteColour = new PaletteColour($paletteColour);
            if(!$_paletteColour->validate())
            {
                $this->paletteColours[$i] = [
                    'label' => [
                        'value' => $_paletteColour->label ?? '',
                        'hasErrors' => $_paletteColour->hasErrors('label') ?? '',
                    ],
                    'handle' => [
                        'value' => $_paletteColour->handle ?? '',
                        'hasErrors' => $_paletteColour->hasErrors('handle') ?? '',
                    ],
                    'colour' => [
                        'value' => $_paletteColour->colour ?? '',
                        'hasErrors' => $_paletteColour->hasErrors('colour') ?? '',
                    ],
                ];
                foreach ($_paletteColour->getErrors() as $error)
                {
                    $this->addError('paletteColours', Craft::t('colorit', 'Row {row} {error}', [
                        'row' => ($i + 1),
                        'error' => lcfirst($error[0]),
                    ]));
                }
            }
        }
    }


    public function getType(): string
    {
        return get_class($this);
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

    public function getContentColumnType(): string
    {
        return Schema::TYPE_TEXT;
    }

    public function isValueEmpty($value, ElementInterface $element): bool
    {
        return empty($value->handle ?? '');
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
        $field = $this;
        $presets = [];
        $presetOptions[] =  [
            'value' => '',
            'label' => 'No Preset'
        ];

        if(!$this->presetMode)
        {
            $presets = Colorit::$plugin->getPresets()->getAllPresetsByType(self::class);
            if($presets)
            {
                foreach ($presets as $preset)
                {
                    $presetOptions[] = [
                        'value' => $preset->id,
                        'label' => $preset->name,
                    ];
                }
            }
        }

        return Craft::$app->getView()->renderTemplate('colorit/_fields/colorit/settings', compact(
            'field',
            'presets',
            'presetOptions'
        ));
    }

    public function getInputHtml($value, ElementInterface $element = null): string
    {
        $this->_populateWithPreset();

        $view = Craft::$app->getView();
        $id = $view->formatInputId($this->handle);
        $namespacedId = Craft::$app->view->namespaceInputId($id);

        $view->registerAssetBundle(ColoritAssetBundle::class);
        $js = Json::encode([
            'id' => $id,
            'namespacedId' => $namespacedId,
            'name' => $this->handle,
            'debug' => Craft::$app->getConfig()->getGeneral()->devMode,
        ]);
        $view->registerJs('new Colorit('.$js.');', View::POS_END);

        return $view->renderTemplate('colorit/_fields/colorit/input', [
            'id' => $id,
            'name' => $this->handle,
            'value' => $value,
            'field' => $this,
        ]);
    }

    public function getInputPreviewHtml(): string
    {
        $view = Craft::$app->getView();

        $view->registerAssetBundle(ColoritAssetBundle::class);

        return $view->renderTemplate('colorit/_fields/colorit/preview', [
            'field' => $this,
        ]);
    }

    public function getPalette()
    {
        $palette = ColourHelper::baseColours($this->paletteBaseColours);

        if($this->paletteColours)
        {
            foreach($this->paletteColours as $paletteColour)
            {
                $palette[$paletteColour['handle']] = $paletteColour;
            }
        }
        return $palette;
    }

    // Static Methods
    // =========================================================================

    private function _populateWithPreset()
    {
        if($this->presetId)
        {
            $preset = Colorit::$plugin->getPresets()->getPresetById($this->presetId);
            if($preset)
            {
                Craft::configure($this, $preset->getSettings());
            }
        }
    }

}
