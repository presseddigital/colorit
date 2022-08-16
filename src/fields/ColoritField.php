<?php

namespace presseddigital\colorit\fields;

use Craft;
use craft\base\Element;
use craft\base\ElementInterface;
use craft\base\Field;
use craft\base\PreviewableFieldInterface;

use craft\helpers\Json;
use craft\validators\ArrayValidator;
use craft\web\View;
use GraphQL\Type\Definition\Type;
use presseddigital\colorit\Colorit;
use presseddigital\colorit\helpers\ColorHelper;
use presseddigital\colorit\models\Color;
use presseddigital\colorit\models\PaletteColor;
use presseddigital\colorit\web\assets\colorit\ColoritAssetBundle;

use yii\db\Schema;

/**
 * @author    Pressed Digital
 * @package   Palette
 * @since     1.0.0
 */
class ColoritField extends Field implements PreviewableFieldInterface
{
    // Public Properties
    // =========================================================================

    public bool $presetMode = false;

    public $presetId;
    public $paletteColors;
    public $paletteBaseColors;
    public bool $allowCustomColor = false;
    public bool $allowCustomColorPicker = false;
    public bool $allowOpacity = false;
    public bool $fieldDescriptions = false;
    public string $colorFormat = 'auto';

    public $defaultColorHandle;
    public int $defaultOpacity = 100;

    // Static Methods
    // =========================================================================

    public static function displayName(): string
    {
        return Craft::t('colorit', 'Colorit');
    }

    // Public Methods
    // =========================================================================

    public function init(): void
    {
        parent::init();
    }

    public function setPresetMode(bool $on): void
    {
        $this->presetMode = $on;
    }

    /**
     * @return array<int, mixed[]>
     */
    public function rules(): array
    {
        $rules = [];
        $rules[] = [['paletteColors'], 'validatePaletteColors'];
        $rules[] = [['paletteBaseColors'], ArrayValidator::class];
        $rules[] = [['colorFormat', 'defaultColorHandle'], 'string'];
        $rules[] = [['defaultColorHandle'], 'validateDefaultColorHandle'];
        $rules[] = [['colorFormat'], 'default', 'value' => 'auto'];
        $rules[] = [['allowCustomColor', 'allowCustomColorPicker', 'allowOpacity', 'fieldDescriptions'], 'boolean'];
        $rules[] = [['allowCustomColor', 'allowCustomColorPicker', 'allowOpacity', 'fieldDescriptions'], 'default', 'value' => false];
        $rules[] = [['defaultOpacity'], 'integer', 'min' => 0, 'max' => 100];
        $rules[] = [['defaultOpacity'], 'default', 'value' => 100];

        if ($this->presetMode) {
            return $rules;
        }
        return array_merge(parent::rules(), $rules);
    }

    public function validateDefaultColorHandle(): void
    {
        if (!in_array($this->defaultColorHandle, array_keys($this->getPalette()))) {
            $this->addError('defaultColorHandle', Craft::t('colorit', 'Color handle not in use'));
        }
    }

    public function validatePaletteColors(): void
    {
        $usedHandles = [];
        foreach ($this->paletteColors as $i => $paletteColor) {
            $_paletteColor = new PaletteColor($paletteColor);

            if ($_paletteColor->handle ?? false) {
                $isHandleUnique = !in_array($_paletteColor->handle, $usedHandles);
                if (!$isHandleUnique) {
                    $this->addError('paletteColors', Craft::t('colorit', 'Row {row} {error}', [
                        'row' => ($i + 1),
                        'error' => Craft::t('colorit', 'handle must be unique'),
                    ]));
                }
                $usedHandles[] = $_paletteColor->handle;
            }

            if (!$_paletteColor->validate() || !$isHandleUnique) {
                $this->paletteColors[$i] = [
                    'label' => [
                        'value' => $_paletteColor->label ?? '',
                        'hasErrors' => $_paletteColor->hasErrors('label') ?? '',
                    ],
                    'handle' => [
                        'value' => $_paletteColor->handle ?? '',
                        'hasErrors' => !$isHandleUnique ? true : $_paletteColor->hasErrors('handle') ?? '',
                    ],
                    'color' => [
                        'value' => $_paletteColor->color ?? '',
                        'hasErrors' => $_paletteColor->hasErrors('color') ?? '',
                    ],
                ];
                foreach ($_paletteColor->getErrors() as $error) {
                    $this->addError('paletteColors', Craft::t('colorit', 'Row {row} {error}', [
                        'row' => ($i + 1),
                        'error' => lcfirst($error[0]),
                    ]));
                }
            }
        }
    }


    public function getType(): string
    {
        return $this::class;
    }

    /**
     * @return string[]
     */
    public function getElementValidationRules(): array
    {
        return ['validateColorValue'];
    }

    public function validateColorValue(ElementInterface $element): void
    {
        if ($element->getScenario() === Element::SCENARIO_LIVE) {
            $fieldValue = $element->getFieldValue($this->handle);
            if ($fieldValue && !$fieldValue->validate()) {
                $element->addModelErrors($fieldValue, $this->handle);
            }
        }
    }

    public function getContentColumnType(): string
    {
        return Schema::TYPE_TEXT;
    }

    public function getContentGqlType(): Type|array
    {
        return Type::string();
    }

    public function isValueEmpty(mixed $value, ElementInterface $element): bool
    {
        return empty($value->handle ?? '');
    }

    public function normalizeValue(mixed $value, ?\craft\base\ElementInterface $element = null): mixed
    {
        if ($value instanceof Color) {
            return $value;
        }

        if (is_string($value)) {
            $value = Json::decodeIfJson($value);
        }

        if (isset($value['handle'])) {
            return $this->_createColor($value);
        } else {
            return $this->defaultColor();
        }

        return null;
    }

    public function serializeValue(mixed $value, ?\craft\base\ElementInterface $element = null): mixed
    {
        $serialized = [];
        if ($value instanceof Color) {
            $serialized = [
                'handle' => $value->handle,
                'custom' => $value->custom,
                'opacity' => $value->opacity,
            ];
        }
        return parent::serializeValue($serialized, $element);
    }

    public function getSettingsHtml(): ?string
    {
        $presetOptions = [];
        $field = $this;
        $presets = [];
        $presetOptions[] = [
            'value' => '',
            'label' => 'No Preset',
        ];

        if (!$this->presetMode) {
            $presets = Colorit::$plugin->getPresets()->getAllPresetsByType(self::class);
            if ($presets) {
                foreach ($presets as $preset) {
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

    public function getInputHtml(mixed $value, ?\craft\base\ElementInterface $element = null): string
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
        $view->registerJs('new Colorit(' . $js . ');', View::POS_END);

        return $view->renderTemplate('colorit/_fields/colorit/input', [
            'id' => $id,
            'name' => $this->handle,
            'value' => $value,
            'field' => $this,
        ]);
    }

    public function getTableAttributeHtml(mixed $value, ElementInterface $element): string
    {
        if (!$value) {
            return '<div class="color small static"><div class="color-preview"></div></div>';
        }

        return '<div class="color small static"><div class="color-preview" style="background-color: ' . $value->getColor() . ';"></div></div>
            <div class="colorhex code">' . $value->getColor() . '</div>';
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
        $palette = [];
        if ($this->paletteBaseColors) {
            $palette = ColorHelper::baseColors($this->paletteBaseColors);
        }

        if ($this->paletteColors) {
            foreach ($this->paletteColors as $paletteColor) {
                if (is_string($paletteColor['handle'])) {
                    $palette[$paletteColor['handle']] = $paletteColor;
                }
            }
        }
        return $palette;
    }

    protected function defaultColor()
    {
        $this->_populateWithPreset();
        if ($this->defaultColorHandle) {
            return $this->_createColor([
                'handle' => $this->defaultColorHandle,
                'opacity' => $this->defaultOpacity,
            ]);
        }
        return null;
    }

    // Private Methods
    // =========================================================================

    private function _createColor($value)
    {
        $color = new Color($value);
        $this->_populateWithPreset();
        $color->field = $this;
        return $color;
    }

    private function _populateWithPreset(): void
    {
        if ($this->presetId) {
            $preset = Colorit::$plugin->getPresets()->getPresetById($this->presetId);
            if ($preset) {
                Craft::configure($this, $preset->getSettings());
            }
        }
    }
}

class_alias(ColoritField::class, \fruitstudios\colorit\fields\ColoritField::class);
