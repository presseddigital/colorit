<?php
/**
 * Validateit plugin for Craft CMS 3.x
 *
 * A super simple field type which allows you toggle existing field types.
 *
 * @link      https://fruitstudios.co.uk
 * @copyright Copyright (c) 2018 Fruit Studios
 */

namespace fruitstudios\validateit;

use fruitstudios\validateit\fields\ValidateitField;

use Craft;
use craft\base\Plugin;
use craft\services\Fields;
use craft\events\RegisterComponentTypesEvent;

use yii\base\Event;

/**
 * Class Validateit
 *
 * @author    Fruit Studios
 * @package   Validateit
 * @since     1.0.0
 *
 */
class Validateit extends Plugin
{
    // Static Properties
    // =========================================================================

    /**
     * @var Validateit
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
                $event->types[] = ValidateitField::class;
            }
        );

        Craft::info(
            Craft::t(
                'validateit',
                '{name} plugin loaded',
                ['name' => $this->name]
            ),
            __METHOD__
        );
    }

}
