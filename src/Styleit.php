<?php
/**
 * Styleit plugin for Craft CMS 3.x
 *
 * A super simple field type which allows you toggle existing field types.
 *
 * @link      https://fruitstudios.co.uk
 * @copyright Copyright (c) 2018 Fruit Studios
 */

namespace fruitstudios\styleit;

use fruitstudios\styleit\models\Settings;
use fruitstudios\styleit\fields\PaletteField;
use fruitstudios\styleit\services\Colours;
use fruitstudios\styleit\variables\StyleitVariable;

use Craft;
use craft\base\Plugin;
use craft\services\Fields;
use craft\events\RegisterComponentTypesEvent;
use craft\web\twig\variables\CraftVariable;

use yii\base\Event;

/**
 * Class Styleit
 *
 * @author    Fruit Studios
 * @package   Styleit
 * @since     1.0.0
 *
 */
class Styleit extends Plugin
{
    // Static Properties
    // =========================================================================

    /**
     * @var Styleit
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
                // $event->types[] = BackgroundField::class;
                // $event->types[] = AlignmentField::class;
                // $event->types[] = HeadingField::class;
            }
        );


        Event::on(
            CraftVariable::class,
            CraftVariable::EVENT_INIT,
            function (Event $event) {
                $variable = $event->sender;
                $variable->set('styleit', StyleitVariable::class);
            }
        );

        Craft::info(
            Craft::t(
                'styleit',
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

        return Craft::$app->getView()->renderTemplate('styleit/settings', [
            'settings' => $settings,
            'palette' => $paletteField,
        ]);
    }
}
