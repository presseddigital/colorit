<?php
namespace fruitstudios\palette\models;

use Craft;
use craft\base\Model;

class FieldTemplate extends Model
{
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
        return [
            [['name', 'type'], 'string'],
            [['name', 'type'], 'required'],
            ['settings', 'validateSettings'],
            ['settings', 'default', 'value' => []],
        ];
    }

    public function validateSettings()
    {
        return true;

        // //  Function to push validation onto this link
        // public function validateLinkValue(ElementInterface $element)
        // {
        //     $fieldValue = $element->getFieldValue($this->handle);
        //     if($fieldValue && !$fieldValue->validate())
        //     {
        //         $element->addModelErrors($fieldValue, $this->handle);
        //     }
        // }
    }

    public function getField()
    {
        if(!$this->type)
        {
            return false;
        }

        try {
            $field = Craft::createObject($this->type);
            $field->setAttributes($this->settings, false);
            return $field;
            Craft::dd($this->settings);
            return Craft::configure($field, $this->settings ?? []);
        } catch(ErrorException $exception) {
            $error = $exception->getMessage();
            return false;
        }
    }
}
