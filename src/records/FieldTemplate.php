<?php
namespace fruitstudios\palette\records;

use craft\db\ActiveRecord;
use yii\db\ActiveQueryInterface;

class FieldTemplate extends ActiveRecord
{
    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     * @return string
     */
    public static function tableName(): string
    {
        return '{{%palette_fieldtemplates}}';
    }

}
