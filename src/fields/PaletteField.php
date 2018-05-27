<?php
/**
 * Validateit plugin for Craft CMS 3.x
 *
 * A super simple field type which allows you toggle existing field types.
 *
 * @link      https://fruitstudios.co.uk
 * @copyright Copyright (c) 2018 Fruit Studios
 */

namespace fruitstudios\validateit\fields;

use fruitstudios\validateit\Validateit;
use fruitstudios\validateit\validators\UrlValidator as ValidateitUrlValidator;

use Craft;
use craft\base\ElementInterface;
use craft\base\Field;
use craft\helpers\Db;
use craft\helpers\Json;

use yii\db\Schema;

/**
 * @author    Fruit Studios
 * @package   Validateit
 * @since     1.0.0
 */
class ValidateitField extends Field
{
    // Public Properties
    // =========================================================================

    public $type;
    public $regex;
    public $message;
    public $placeholder;

    // Static Methods
    // =========================================================================

    public static function displayName(): string
    {
        return Craft::t('validateit', 'Validateit');
    }

    // Public Methods
    // =========================================================================

    public function rules()
    {
        $rules = parent::rules();
        $rules[] = [['type'], 'required'];
        $rules[] = [['regex'], 'required', 'when' => [$this, 'isCustomType']];
        $rules[] = [['message', 'placeholder', 'regex'], 'string'];
        return $rules;
    }

    public function isCustomType(): bool
    {
        return $this->type == 'custom';
    }

    public function getTypeArray(): array
    {
        return $this->getTypes()[$this->type] ?? null;
    }

    public function getElementValidationRules(): array
    {
        $message = !empty($this->message) ? $this->message : ( $this->getTypeArray()['error'] ?? null );

        switch($this->type)
        {
            case('email'):
                $rule = ['email', 'message' => $message];
                break;
            case('url'):
                $rule = [ValidateitUrlValidator::class, 'defaultScheme' => 'http', 'message' => $message];
                break;
            case('phone'):
                $match = '/^(?:\+\d{1,3}|0\d{1,3}|00\d{1,2})?(?:\s?\(\d+\))?(?:[-\/\s.]|\d)+$/';
                $rule = ['match', 'pattern' => $match, 'message' => $message];
                break;
            case('ip'):
                $rule = ['ip', 'message' => $message];
                break;
            case('ipv4'):
                $rule = ['ip', 'ipv6' => false, 'message' => $message];
                break;
            case('ipv6'):
                $rule = ['ip', 'ipv4' => false, 'message' => $message];
                break;
            case('facebook'):
                $match = '/(?:(?:http|https):\/\/)?(?:www.)?facebook.com\/(?:(?:\w)*#!\/)?(?:pages\/)?(?:[?\w\-]*\/)?(?:profile.php\?id=(?=\d.*))?([\w\-]*)?/';
                $rule = ['match', 'pattern' => $match, 'message' => $message];
                break;
            case('twitter'):
                $match = '/^http(?:s)?:\/\/(?:www\.)?twitter\.com\/([a-zA-Z0-9_]+)/';
                $rule = ['match', 'pattern' => $match, 'message' => $message];
                break;
            case('instagram'):
                $match = '/(?:(?:http|https):\/\/)?(?:www.)?(?:instagram.com|instagr.am)\/([A-Za-z0-9-_]+)/i';
                $rule = ['match', 'pattern' => $match, 'message' => $message];
                break;
            case('linkedin'):
                $match = '/^http(?:s)?:\/\/[a-z]{2,3}\\.linkedin\\.com\\/.*$/';
                $rule = ['match', 'pattern' => $match, 'message' => $message];
                break;
            case('custom'):
                $rule = ['match', 'pattern' => $this->regex, 'message' => $message];
                break;
            default:
                $rule = null;
                break;
        }

        return [$rule];
    }

    public function getContentColumnType(): string
    {
        return Schema::TYPE_TEXT;
    }

    public function normalizeValue($value, ElementInterface $element = null)
    {
        return $value;
    }

    public function serializeValue($value, ElementInterface $element = null)
    {
        return parent::serializeValue($value, $element);
    }

    /**
     * @inheritdoc
     */
    public function getSettingsHtml()
    {
        // Render the settings template
        return Craft::$app->getView()->renderTemplate(
            'validateit/_settings',
            [
                'field' => $this,
            ]
        );
    }

    /**
     * @inheritdoc
     */
    public function getInputHtml($value, ElementInterface $element = null): string
    {
        // Get our id and namespace
        $id = Craft::$app->getView()->formatInputId($this->handle);

        // Render the input template
        return Craft::$app->getView()->renderTemplate(
            'validateit/_input',
            [
                'id' => $id,
                'name' => $this->handle,
                'value' => $value,
                'field' => $this,
            ]
        );
    }

    public function getTypes()
    {
        return [
            'email' => [
                'label' => Craft::t('validateit', 'Email Address'),
                'error' => Craft::t('validateit', 'Please provide a valid {type}.', [
                    'type' => Craft::t('validateit', 'email address')
                ]),
                'placeholder' => Craft::t('validateit', 'email@domain.com'),
                'handle' => 'email',
            ],
            'url' => [
                'label' => Craft::t('validateit', 'URL'),
                'error' => Craft::t('validateit', 'Please provide a valid {type}.', [
                    'type' => Craft::t('validateit', 'link')
                ]),
                'placeholder' => Craft::t('validateit', 'https://domain.com'),
                'handle' => 'url',
            ],
            'phone' => [
                'label' => Craft::t('validateit', 'Phone Number'),
                'error' => Craft::t('validateit', 'Please provide a valid {type}.', [
                    'type' => Craft::t('validateit', 'phone number')
                ]),
                'placeholder' => Craft::t('validateit', '+44(0)0000 000000'),
                'handle' => 'phone',
            ],
            'ip' => [
                'label' => Craft::t('validateit', 'IP Address'),
                'error' => Craft::t('validateit', 'Please provide a valid {type}.', [
                    'type' => Craft::t('validateit', 'IP address')
                ]),
                'placeholder' => Craft::t('validateit', '192.168.0.1, 2001:0db8:85a3:0000:0000:8a2e:0370:7334'),
                'handle' => 'ip',
            ],
            'ipv4' => [
                'label' => Craft::t('validateit', 'IP Address (IPv4)'),
                'error' => Craft::t('validateit', 'Please provide a valid {type}.', [
                    'type' => Craft::t('validateit', 'IPv4 address')
                ]),
                'placeholder' => Craft::t('validateit', '192.168.0.1'),
                'handle' => 'ipv4',
            ],
            'ipv6' => [
                'label' => Craft::t('validateit', 'IP Address (IPv6)'),
                'error' => Craft::t('validateit', 'Please provide a valid {type}.', [
                    'type' => Craft::t('validateit', 'IPv6 address')
                ]),
                'placeholder' => Craft::t('validateit', '2001:0db8:85a3:0000:0000:8a2e:0370:7334'),
                'handle' => 'ipv6',
            ],
            'facebook' => [
                'label' => Craft::t('validateit', 'Facebook Url'),
                'error' => Craft::t('validateit', 'Please provide a valid {type}.', [
                    'type' => Craft::t('validateit', 'Facebook link')
                ]),
                'placeholder' => Craft::t('validateit', 'https://www.facebook.com/username'),
                'handle' => 'facebook',
            ],
            'twitter' => [
                'label' => Craft::t('validateit', 'Twitter Url'),
                'error' => Craft::t('validateit', 'Please provide a valid {type}.', [
                    'type' => Craft::t('validateit', 'Twitter link')
                ]),
                'placeholder' => Craft::t('validateit', 'https://twitter.com/username'),
                'handle' => 'twitter',
            ],
            'instagram' => [
                'label' => Craft::t('validateit', 'Instagram Url'),
                'error' => Craft::t('validateit', 'Please provide a valid {type}.', [
                    'type' => Craft::t('validateit', 'Instagram link')
                ]),
                'placeholder' => Craft::t('validateit', 'https://www.instagram.com/username'),
                'handle' => 'instagram',
            ],
            'linkedin' => [
                'label' => Craft::t('validateit', 'LinkedIn Url'),
                'error' => Craft::t('validateit', 'Please provide a valid {type}.', [
                    'type' => Craft::t('validateit', 'LinkedIn link')
                ]),
                'placeholder' => Craft::t('validateit', 'https://www.linkedin.com/in/username'),
                'handle' => 'linkedin',
            ],
            'custom' => [
                'label' => Craft::t('validateit', 'Custom Regex'),
                'error' => Craft::t('validateit', 'Please provide a valid {type}.', [
                    'type' => Craft::t('validateit', 'value')
                ]),
                'placeholder' => Craft::t('validateit', $this->name),
                'handle' => 'custom',
            ]
        ];
    }

    public function getTypeOptions()
    {
        $options = [];
        foreach ($this->getTypes() as $type => $value)
        {
            $options[] = [
                'label' => $type->label,
                'value' => $type->handle,
            ];
        }
        return $options;
    }

}
