<?php
namespace fruitstudios\colorit\services;

use fruitstudios\colorit\Colorit;
use fruitstudios\colorit\models\FieldTemplate;
use fruitstudios\colorit\records\FieldTemplate as FieldTemplateRecord;

use fruitstudios\colorit\fields\ColoritField;

use Craft;
use craft\base\Component;
use craft\db\Query;

class FieldTemplates extends Component
{
    // Properties
    // =========================================================================

    private $_fetchedAllFieldTemplates = false;

    private $_allFieldTemplates = [];
    private $_allFieldTemplatesByType = [];

    private $_allFieldsUsingTemplates;
    private $_allFieldsUsingTemplatesById;

    // Public Methods
    // =========================================================================

    public function getAllFieldTemplateTypes(): array
    {
        return [
            ColoritField::class,
        ];
    }

    public function getAllFieldTemplates(): array
    {
        if($this->_fetchedAllFieldTemplates)
        {
            return $this->_allFieldTemplates;
        }

        $results = $this->_createFieldTemplatesQuery()->all();
        foreach($results as $result)
        {
            $fieldTemplate = $this->createFieldTemplate($result);
            $this->_allFieldTemplates[$result['id']] = $fieldTemplate;
            $this->_allFieldTemplatesByType[$result['type']][$result['id']] = $fieldTemplate;
        }
        $this->_fetchedAllFieldTemplates = true;
        return $this->_allFieldTemplates;
    }

    public function getAllFieldTemplatesByType(string $type): array
    {
        if(!$this->_fetchedAllFieldTemplates)
        {
            $this->getAllFieldTemplates();
        }
        return $this->_allFieldTemplatesByType[$type] ?? [];
    }

    public function getFieldTemplateById($id)
    {
        if(isset($this->_allFieldTemplates[$id]))
        {
            return $this->_allFieldTemplates[$id];
        }

        if ($this->_fetchedAllFieldTemplates)
        {
            return null;
        }

        $result = $this->_createFieldTemplatesQuery()
            ->where(['id' => $id])
            ->one();

        if (!$result)
        {
            return null;
        }
        return $this->_allFieldTemplates[$id] = $this->createFieldTemplate($result);
    }

    public function saveFieldTemplate(FieldTemplate $model, bool $runValidation = true): bool
    {
        if ($model->id)
        {
            $record = FieldTemplateRecord::findOne($model->id);
            if (!$record)
            {
                throw new Exception(Craft::t('colorit', 'No field template exists with the ID “{id}”', ['id' => $model->id]));
            }
        }
        else
        {
            $record = new FieldTemplateRecord();
        }

        if ($runValidation && !$model->validate())
        {
            Craft::info('Field template not saved due to validation error.', __METHOD__);
            return false;
        }

        $record->name = $model->name;
        $record->type = $model->type;
        $record->settings = $model->settings;

        // Save it!
        $record->save(false);

        // Now that we have a record ID, save it on the model
        $model->id = $record->id;

        return true;
    }

    public function deleteFieldTemplateById($id): bool
    {
        $record = FieldTemplateRecord::findOne($id);
        if ($record)
        {
            return (bool)$record->delete();
        }
        return false;
    }

    public function getFieldTemplateField(string $type)
    {
        $field = Craft::$app->getFields()->createField($type);
        if(!$field)
        {
            return false;
        }
        return $field;
    }

    public function createFieldTemplate($config): FieldTemplate
    {
        if(is_string($config))
        {
            $config = [
                'type' => $config
            ];
        }
        return new FieldTemplate($config);
    }

    // public function getAllFieldsUsingTemplate(): array
    // {
    //     if(is_null($this->_allFieldsUsingTemplates))
    //     {
    //         $fields = [];
    //         if($fields)
    //         {
    //             foreach($fields as $field)
    //             {
    //                 $this->_allFieldsUsingTemplates[] = $field;
    //                 $this->_allFieldsUsingTemplatesById[$field['id']][] = $field;
    //             }
    //         }
    //     }
    //     return $this->_allFieldTemplates;
    // }

    // public function getFieldsUsingTemplateById(int $id)
    // {
    //     $this->getAllFieldsUsingTemplates();
    //     return $this->_allFieldsUsingTemplatesById[$id] ?? [];
    // }

    // Private Methods
    // =========================================================================

    private function _createFieldTemplatesQuery(): Query
    {
        return (new Query())
            ->select([
                'id',
                'name',
                'type',
                'settings',
            ])
            ->from(['{{%colorit_fieldtemplates}}']);
    }
}
