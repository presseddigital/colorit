<?php
/**
 * Colorit plugin for Craft CMS 3.x
 *
 * A super simple field type which allows you toggle existing field types.
 *
 * @link      https://presseddigital.co.uk
 * @copyright Copyright (c) 2020 Pressed Digital
 */

namespace presseddigital\colorit;

use presseddigital\colorit\models\Settings;
use presseddigital\colorit\fields\ColoritField;
use presseddigital\colorit\plugin\Routes as ColoritRoutes;
use presseddigital\colorit\plugin\Services as ColoritServices;
use presseddigital\colorit\web\twig\CraftVariableBehavior;
use presseddigital\colorit\web\twig\Extension;

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
 * Class Colorit
 *
 * @author    Pressed Digital
 * @package   Colorit
 * @since     1.0.0
 *
 */
class Colorit extends Plugin
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

    public $schemaVersion = '1.0.3';

    // Traits
    // =========================================================================

    use ColoritServices;
    use ColoritRoutes;

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        self::$plugin = $this;
        self::$settings = Colorit::$plugin->getSettings();
        self::$devMode = Craft::$app->getConfig()->getGeneral()->devMode;
        self::$view = Craft::$app->getView();
        self::$commerceInstalled = class_exists(CommercePlugin::class);

        $this->name = Colorit::$settings->pluginNameOverride;
        $this->hasCpSection = Colorit::$settings->hasCpSectionOverride;

        $this->_setPluginComponents(); // See Trait
        $this->_registerCpRoutes(); // See Trait
        $this->_addTwigExtensions();
        $this->_registerFieldTypes();
        $this->_registerPermissions();
        $this->_registerEventListeners();
        $this->_registerWidgets();
        $this->_registerVariables();
        $this->_registerElementTypes();
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
            Craft::$app->controller->redirect(UrlHelper::cpUrl('colorit/about'))->send();
        }
    }

    public function getSettingsResponse()
    {
        return Craft::$app->controller->redirect(UrlHelper::cpUrl('colorit/settings'));
    }

    public function getGitHubUrl(string $append = '')
    {
        return 'https://github.com/presseddigital/craft-'.$this->handle.$append;
    }

    // Protected Methods
    // =========================================================================

    protected function createSettingsModel()
    {
        return new Settings();
    }

    // Private Methods
    // =========================================================================

    private function _addTwigExtensions()
    {
        Craft::$app->view->registerTwigExtension(new Extension);
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
            $event->types[] = ColoritField::class;
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
            $variable->attachBehavior('colorit', CraftVariableBehavior::class);
        });
    }

    private function _registerElementTypes()
    {
        // Event::on(Elements::class, Elements::EVENT_REGISTER_ELEMENT_TYPES, function(RegisterComponentTypesEvent $e) {
        //     $e->types[] = Example::class;
        // });
    }
}
