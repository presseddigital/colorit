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

    private $_fieldTemplatesById = [];
    private $_fieldTemplatesByType = [];
    private $_fetchedAllFieldTemplates = false;

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
            return $this->_fieldTemplatesById;
        }

        $results = $this->_createFieldTemplatesQuery()->all();
        if($results)
        {
            foreach($results as $result)
            {
                $fieldTemplate = $this->createFieldTemplate($result);
                $this->_fieldTemplatesById[$result['id']] = $fieldTemplate;
                $this->_fieldTemplatesByType[$result['type']][$result['id']] = $fieldTemplate;
            }
        }
        $this->_fetchedAllFieldTemplates = true;
        return $this->_fieldTemplatesById;
    }

    public function getAllFieldTemplatesByType(string $type): array
    {
        if($this->_fetchedAllFieldTemplates || isset($this->_fieldTemplatesByType[$type]))
        {
            return $this->_fieldTemplatesByType[$type];
        }

        $results = $this->_createFieldTemplatesQuery()
            ->where(['type' => $type])
            ->all();

        if($results)
        {
            foreach($results as $result)
            {
                $fieldTemplate = $this->createFieldTemplate($result);
                $this->_fieldTemplatesByType[$result['type']][$result['id']] = $fieldTemplate;
            }
        }

        return $this->_fieldTemplatesByType[$type] ?? [];
    }

    public function getFieldTemplateById($id)
    {
        if($this->_fetchedAllFieldTemplates || isset($this->_fieldTemplatesById[$id]))
        {
            return $this->_fieldTemplatesById[$id] ?? null;
        }

        $result = $this->_createFieldTemplatesQuery()
            ->where(['id' => $id])
            ->one();

        if (!$result)
        {
            return null;
        }
        return $this->_fieldTemplatesById[$id] = $this->createFieldTemplate($result);
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
            ->from(['{{%fieldtemplates_colorit}}']);
    }
}
