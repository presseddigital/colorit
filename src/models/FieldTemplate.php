<?php
namespace fruitstudios\palette\models;

use Craft;
use craft\base\Model;
use craft\helpers\Json;

class FieldTemplate extends Model
{
    private $_field;

    // Public Properties
    // =========================================================================

    public $id;
    public $name;
    public $type;
	public $settings;

    // Public Methods
    // =========================================================================

	public function rules(): array
    {
        $rules = parent::rules();
        $rules[] = [['name', 'type'], 'string'];
        $rules[] = [['name', 'type'], 'required'];
        $rules[] = [['settings'], 'validateFieldTypeSettings'];
        return $rules;
    }

    public function validateFieldTypeSettings()
    {
        $fieldType = $this->getFieldType();
        if($fieldType && !$fieldType->validate())
        {
            $this->addError('settings', $fieldType->getErrors());
        }
    }

    public function getFieldSettingsHtml()
    {
        // Get field type for this template and add any errors to it
        $fieldType = $this->getFieldType();
        $fieldTypeErrors = $this->getFirstError('settings');
        if($fieldTypeErrors)
        {
            foreach ($fieldTypeErrors as $handle => $errors)
            {
                foreach ($errors as $error)
                {
                   $fieldType->addError($handle, $error);
                }
            }
        }
        return $fieldType ? $fieldType->getSettingsHtml() : '';
    }

    public function getFieldInputPreviewHtml()
    {
        // Get field type for this template and add any errors to it
        $fieldType = $this->getFieldType();
        return $fieldType ? $fieldType->getInputPreviewHtml() : '';
    }

    // TODO: Move this into the service, it needs to be used in multiple places
    public function getFieldType()
    {
        if(!$this->type)
        {
            return false;
        }

        try {
            $fieldType = Craft::createObject($this->type);
            $fieldType->setAttributes($this->normalizeSettings($this->settings), false);
            return $fieldType;
        } catch(ErrorException $exception) {
            $error = $exception->getMessage();
            return false;
        }
    }

    public function getSettings()
    {
        return $this->normalizeSettings($this->settings);
    }

    public function normalizeSettings($settings)
    {
        if(is_array($settings))
        {
            return $settings;
        }

        return is_string($settings) ? Json::decodeIfJson($settings) : ($settings ?? []);
    }




}
