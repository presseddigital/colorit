<?php
namespace fruitstudios\palette\models;

use fruitstudios\palette\Palette;
use fruitstudios\palette\helpers\FieldHelper;

use Craft;
use craft\base\Model;
use craft\helpers\Json;
use craft\helpers\UrlHelper;
use craft\db\Query;

class FieldTemplate extends Model
{
    private $_fieldType;
    private $_fieldTypeTemplate;

    private $_fieldsUsing;

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
        $fieldType = $this->getFieldTypeTemplate();
        if($fieldType && !$fieldType->validate())
        {
            $this->addError('settings', $fieldType->getErrors());
        }
    }

    public function getFieldSettingsHtml()
    {
        // Get field type for this template and add any errors to it
        $fieldType = $this->getFieldTypeTemplate();

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
        $fieldType = $this->getFieldTypeTemplate();
        return $fieldType ? $fieldType->getInputPreviewHtml() : '';
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

    public function getFieldsUsing()
    {
        if(!is_null($this->_fieldsUsing))
        {
            return $this->_fieldsUsing;
        }

        $fieldsOfType = FieldHelper::getFieldsByType($this->type);
        foreach ($fieldsOfType as $fieldOfType)
        {
            if($this->id == ($this->normalizeSettings($fieldOfType['settings'])['fieldTemplateId'] ?? false))
            {
                $this->_fieldsUsing[] = $fieldOfType;
            }
        }
        return $this->_fieldsUsing;
    }

    public function getFieldsUsingHtml()
    {
        $fields = $this->getFieldsUsing();
        if(!$fields)
        {
            return Craft::t('palette', 'Not In Use');
        }

        $links = [];
        foreach($fields as $field)
        {
            $isPartOfMatrix = $field['context'] != 'global';
            $_field = $isPartOfMatrix ? FieldHelper::getMatrixFieldByChildFieldId($field['id']) : FieldHelper::getFieldById($field['id']);
            Craft::dd($field);
            if($_field)
            {
                //$links[] = '<a href="'.UrlHelper::cpUrl('settings/fields/edit/'.$_field->id).'">'.$_field->name.($isPartOfMatrix ? ' ('.$field['name'].')' : '').'</a>';
            }
        }
        return '<p>'.implode(', ', $links).'</p>';
    }

    public function getFieldType()
    {
        if(!is_null($this->_fieldType))
        {
            return $this->_fieldType;
        }

        if(!$this->type)
        {
            return false;
        }

        $this->_fieldType = Craft::$app->getFields()->createField([
            'type' => $this->type,
            'settings' => $this->getSettings(),
        ]);
        return $this->_fieldType;
    }

    public function getFieldTypeTemplate()
    {
        if(!is_null($this->_fieldTypeTemplate))
        {
            return $this->_fieldTypeTemplate;
        }

        if(!$this->type)
        {
            return false;
        }

        $this->_fieldTypeTemplate = Craft::$app->getFields()->createField([
            'type' => $this->type,
            'settings' => array_merge($this->getSettings(), [ 'fieldTemplateMode' => true ]),
        ]);
        return $this->_fieldTypeTemplate;
    }

    // Private Methods
    // =========================================================================



}
