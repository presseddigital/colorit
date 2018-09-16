<?php
namespace fruitstudios\palette\helpers;

use Craft;
use craft\web\View;
use craft\db\Query;

class FieldHelper
{
    private static $_fieldsMap;

    private static $_fieldsByType;
    private static $_fieldsInMatrixById;

    // Public Methods
    // =========================================================================

    public static function getFieldsMap()
    {
        self::_buildFieldsMap();
        return self::$_fieldsMap;
    }

    public static function getFieldsByType(string $type)
    {
        if(isset(self::$_fieldsByType[$type]))
        {
            return self::$_fieldsByType[$type];
        }

        $fields = self::_createFieldQuery()
            ->where(['type' => $type])
            ->all();

        if(!$fields)
        {
            return false;
        }

        foreach ($fields as $field)
        {
            self::$_fieldsByType[$field['type']][$field['id']] = $field;
        }

        return self::$_fieldsByType[$type];
    }

    public static function getFieldByHandle(string $handle)
    {
        $id = self::getFieldIdByHandle();
        if(!$id)
        {
            return false;
        }
        return Craft::$app->getFields()->getFieldById($id);
    }

    public static function getFieldIdByHandle(string $handle)
    {
        self::_buildFieldsMap();
        return self::$_fieldsMap[$handle] ?? false;
    }

    public static function getMatrixFieldByChildFieldId($id)
    {
        self::_buildFieldsMap();
        return self::$_fieldsInMatrixById[$id] ?? false;
    }

    public static function getMatrixFieldIdByChildFieldId($id)
    {
        self::_buildFieldsMap();
        if(!self::$_fieldsInMatrixById[$id] ?? false)
        {
            return false;
        }
        return Craft::$app->getFields()->getFieldById(self::$_fieldsInMatrixById[$id]);
    }

    // Private Methods
    // =========================================================================

    private static function _buildFieldsMap()
    {
        if (is_null(self::$_fieldsMap))
        {
            $fields = Craft::$app->getFields()->getAllFields();

            $matrixFieldTypes = (new Query())
                ->select([
                    'matrixblocktypes.id',
                    'matrixblocktypes.handle',
                    'matrixblocktypes.fieldId',
                    'fields.handle as fieldHandle'
                ])
                ->from(['{{%matrixblocktypes}} matrixblocktypes'])
                ->orderBy('matrixblocktypes.id')
                ->innerJoin('{{%fields}} fields', '[[fields.id]] = [[matrixblocktypes.fieldId]]')
                ->all();

            $matrixFieldHandlesByContext = [];
            $matrixFieldIdsByContext = [];
            foreach ($matrixFieldTypes as $matrixFieldType)
            {
                $matrixFieldHandlesByContext['matrixBlockType:'.$matrixFieldType['id']] = $matrixFieldType['fieldHandle'].':'.$matrixFieldType['handle'].':';
                $matrixFieldIdsByContext['matrixBlockType:'.$matrixFieldType['id']] = $matrixFieldType['fieldId'];
            }

            foreach ($fields as $field)
            {
                self::$_fieldsByType[$field->type][$field->id] = $field;

                if(array_key_exists($field['context'], $matrixFieldHandlesByContext))
                {
                    $handle = $matrixFieldHandlesByContext[$field['context']].$field['handle'];
                    self::$_fieldsMap[$handle] = $field['id'];
                    self::$_fieldsInMatrixById[$field['id']] = $matrixFieldIdsByContext[$field['context']];
                }
                else
                {
                    self::$_fieldsMap[$field['handle']] = $field['id'];
                }
            }
        }
    }

    private static function _createFieldQuery(): Query
    {
        return (new Query())
            ->select([
                'fields.id',
                'fields.dateCreated',
                'fields.dateUpdated',
                'fields.groupId',
                'fields.name',
                'fields.handle',
                'fields.context',
                'fields.instructions',
                'fields.translationMethod',
                'fields.translationKeyFormat',
                'fields.type',
                'fields.settings'
            ])
            ->from(['{{%fields}} fields'])
            ->orderBy(['fields.name' => SORT_ASC, 'fields.handle' => SORT_ASC]);
    }

}
