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

use fruitstudios\styleit\fields\StyleitField;

use Craft;
use craft\base\Plugin;
use craft\services\Fields;
use craft\events\RegisterComponentTypesEvent;

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
                $event->types[] = StyleitField::class;
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

}
