<?php
/**
 * Palette plugin for Craft CMS 3.x
 *
 * A super simple field type which allows you toggle existing field types.
 *
 * @link      https://fruitstudios.co.uk
 * @copyright Copyright (c) 2018 Fruit Studios
 */

namespace fruitstudios\palette;

use fruitstudios\palette\models\Settings;
use fruitstudios\palette\fields\PaletteField;
use fruitstudios\palette\services\Colours;
use fruitstudios\palette\variables\PaletteVariable;

use Craft;
use craft\base\Plugin;
use craft\services\Fields;
use craft\events\RegisterComponentTypesEvent;
use craft\web\twig\variables\CraftVariable;

use yii\base\Event;

/**
 * Class Palette
 *
 * @author    Fruit Studios
 * @package   Palette
 * @since     1.0.0
 *
 */
class Palette extends Plugin
{
    // Static Properties
    // =========================================================================

    /**
     * @var Palette
     */
    public static $plugin;

    // Public Properties
    // =========================================================================

    /**
     * @var string
     */
    public $schemaVersion = '1.0.0';

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        self::$plugin = $this;

        Event::on(
            Fields::class,
            Fields::EVENT_REGISTER_FIELD_TYPES,
            function (RegisterComponentTypesEvent $event) {
                $event->types[] = PaletteField::class;
            }
        );


        Event::on(
            CraftVariable::class,
            CraftVariable::EVENT_INIT,
            function (Event $event) {
                $variable = $event->sender;
                $variable->set('palette', PaletteVariable::class);
            }
        );

        Craft::info(
            Craft::t(
                'palette',
                '{name} plugin loaded',
                ['name' => $this->name]
            ),
            __METHOD__
        );
    }

    // Protected Methods
    // =========================================================================

    protected function createSettingsModel()
    {
        return new Settings();
    }

    protected function settingsHtml()
    {
        $settings = $this->getSettings();

        $paletteField = new PaletteField();
        $paletteField = Craft::configure($paletteField, $settings->palette ?? []);
        $paletteField->setScenario('global');

        return Craft::$app->getView()->renderTemplate('palette/settings', [
            'settings' => $settings,
            'palette' => $paletteField,
        ]);
    }
}
