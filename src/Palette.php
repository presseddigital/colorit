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
use fruitstudios\palette\plugin\Routes as PaletteRoutes;
use fruitstudios\palette\plugin\Services as PaletteServices;
use fruitstudios\palette\web\twig\CraftVariableBehavior;

use Craft;
use craft\base\Plugin;
use craft\services\Plugins;
use craft\services\Fields;
use craft\helpers\UrlHelper;
use craft\events\RegisterComponentTypesEvent;
use craft\events\PluginEvent;
use craft\web\twig\variables\CraftVariable;

use craft\commerce\Plugin as CommercePlugin;

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

    public static $plugin;
    public static $settings;
    public static $devMode;
    public static $view;
    public static $commerceInstalled;

    // Public Properties
    // =========================================================================

    public $schemaVersion = '1.0.2';

    // Traits
    // =========================================================================

    use PaletteServices;
    use PaletteRoutes;

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        self::$plugin = $this;
        self::$settings = Palette::$plugin->getSettings();
        self::$devMode = Craft::$app->getConfig()->getGeneral()->devMode;
        self::$view = Craft::$app->getView();
        self::$commerceInstalled = class_exists(CommercePlugin::class);

        $this->name = Palette::$settings->pluginNameOverride;
        $this->hasCpSection = Palette::$settings->hasCpSectionOverride;

        $this->_setPluginComponents(); // See Trait
        $this->_registerCpRoutes(); // See Trait
        $this->_addTwigExtensions();
        $this->_registerFieldTypes();
        $this->_registerPermissions();
        $this->_registerEventListeners();
        $this->_registerWidgets();
        $this->_registerVariables();
        $this->_registerElementTypes();

        Craft::info(Craft::t('palette', '{name} plugin loaded', ['name' => $this->name]), __METHOD__);
    }

    public function beforeInstall(): bool
    {
        return true;
    }

    public function afterInstallPlugin(PluginEvent $event)
    {
        $isCpRequest = Craft::$app->getRequest()->isCpRequest;
        if ($event->plugin === $this && $isCpRequest)
        {
            Craft::$app->controller->redirect(UrlHelper::cpUrl('palette/about'))->send();
        }
    }

    public function getSettingsResponse()
    {
        return Craft::$app->controller->redirect(UrlHelper::cpUrl('palette/settings'));
    }

    public function getGitHubUrl(string $append = '')
    {
        return 'https://github.com/fruitstudios/craft-'.$this->handle.$append;
    }

    // Protected Methods
    // =========================================================================

    protected function createSettingsModel()
    {
        return new Settings();
    }

    // protected function settingsHtml()
    // {
    //     $settings = $this->getSettings();

    //     return Craft::$app->getView()->renderTemplate('palette/settings', [
    //         'settings' => $settings,
    //     ]);
    // }


    // Private Methods
    // =========================================================================

    private function _addTwigExtensions()
    {
        // Craft::$app->view->registerTwigExtension(new Extension);
    }

    private function _registerPermissions()
    {
        // Event::on(UserPermissions::class, UserPermissions::EVENT_REGISTER_PERMISSIONS, function(RegisterUserPermissionsEvent $event) {
        //     $productTypes = Plugin::getInstance()->getProductTypes()->getAllProductTypes();
        //     $productTypePermissions = [];
        //     foreach ($productTypes as $id => $productType) {
        //         $suffix = ':' . $id;
        //         $productTypePermissions['commerce-manageProductType' . $suffix] = ['label' => Craft::t('commerce', 'Manage “{type}” products', ['type' => $productType->name])];
        //     }
        //     $event->permissions[Craft::t('commerce', 'Craft Commerce')] = [
        //         'commerce-manageProducts' => ['label' => Craft::t('commerce', 'Manage products'), 'nested' => $productTypePermissions],
        //         'commerce-manageOrders' => ['label' => Craft::t('commerce', 'Manage orders')],
        //         'commerce-managePromotions' => ['label' => Craft::t('commerce', 'Manage promotions')],
        //         'commerce-manageSubscriptions' => ['label' => Craft::t('commerce', 'Manage subscriptions')],
        //         'commerce-manageShipping' => ['label' => Craft::t('commerce', 'Manage shipping')],
        //         'commerce-manageTaxes' => ['label' => Craft::t('commerce', 'Manage taxes')],
        //     ];
        // });
    }

    private function _registerEventListeners()
    {
        Event::on(Plugins::class, Plugins::EVENT_AFTER_INSTALL_PLUGIN, [$this, 'afterInstallPlugin']);

        // Event::on(Sites::class, Sites::EVENT_AFTER_SAVE_SITE, [$this->getServiceName(), 'functionToCall']);

        // if (!Craft::$app->getRequest()->getIsConsoleRequest()) {
        //     Event::on(UserElement::class, UserElement::EVENT_AFTER_SAVE, [$this->getFunction(), 'functionToCall']);
        //     Event::on(User::class, User::EVENT_AFTER_LOGIN, [$this->getCustomers(), 'loginHandler']);
        //     Event::on(User::class, User::EVENT_AFTER_LOGOUT, [$this->getCustomers(), 'logoutHandler']);
        // }
    }

    private function _registerFieldTypes()
    {
        Event::on(Fields::className(), Fields::EVENT_REGISTER_FIELD_TYPES, function(RegisterComponentTypesEvent $event) {
            $event->types[] = PaletteField::class;
        });
    }

    private function _registerWidgets()
    {
        // Event::on(Dashboard::class, Dashboard::EVENT_REGISTER_WIDGET_TYPES, function(RegisterComponentTypesEvent $event) {
        //     $event->types[] = Example::class;
        // });
    }

    private function _registerVariables()
    {
        Event::on(CraftVariable::class, CraftVariable::EVENT_INIT, function(Event $event) {
            /** @var CraftVariable $variable */
            $variable = $event->sender;
            $variable->attachBehavior('palette', CraftVariableBehavior::class);
        });
    }

    private function _registerElementTypes()
    {
        // Event::on(Elements::class, Elements::EVENT_REGISTER_ELEMENT_TYPES, function(RegisterComponentTypesEvent $e) {
        //     $e->types[] = Example::class;
        // });
    }
}
