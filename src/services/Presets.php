<?php

namespace presseddigital\colorit\services;

use Craft;
use craft\base\Component;

use craft\db\Query;

use presseddigital\colorit\fields\ColoritField;
use presseddigital\colorit\models\Preset;
use presseddigital\colorit\records\Preset as PresetRecord;

class Presets extends Component
{
    // Properties
    // =========================================================================

    private array $_presetsById = [];
    private array $_presetsByType = [];
    private bool $_fetchedAllPresets = false;

    // Public Methods
    // =========================================================================
    /**
     * @return array<class-string<\presseddigital\colorit\fields\ColoritField>>
     */
    public function getAllPresetTypes(): array
    {
        return [
            ColoritField::class,
        ];
    }

    /**
     * @return \presseddigital\colorit\models\Preset[]
     */
    public function getAllPresets(): array
    {
        if ($this->_fetchedAllPresets) {
            return $this->_presetsById;
        }

        $results = $this->_createPresetsQuery()->all();
        if ($results) {
            foreach ($results as $result) {
                $preset = $this->createPreset($result);
                $this->_presetsById[$result['id']] = $preset;
                $this->_presetsByType[$result['type']][$result['id']] = $preset;
            }
        }
        $this->_fetchedAllPresets = true;
        return $this->_presetsById;
    }

    /**
     * @return mixed[]
     */
    public function getAllPresetsByType(string $type): array
    {
        if ($this->_fetchedAllPresets || isset($this->_presetsByType[$type])) {
            return $this->_presetsByType[$type];
        }

        $results = $this->_createPresetsQuery()
            ->where(['type' => $type])
            ->all();

        if ($results) {
            foreach ($results as $result) {
                $preset = $this->createPreset($result);
                $this->_presetsByType[$result['type']][$result['id']] = $preset;
            }
        }

        return $this->_presetsByType[$type] ?? [];
    }

    public function getPresetById($id)
    {
        if ($this->_fetchedAllPresets || isset($this->_presetsById[$id])) {
            return $this->_presetsById[$id] ?? null;
        }

        $result = $this->_createPresetsQuery()
            ->where(['id' => $id])
            ->one();

        if (!$result) {
            return null;
        }
        return $this->_presetsById[$id] = $this->createPreset($result);
    }

    public function savePreset(Preset $model, bool $runValidation = true): bool
    {
        if ($model->id) {
            $record = PresetRecord::findOne($model->id);
            if (!$record) {
                throw new \Exception(Craft::t('colorit', 'No preset exists with the ID “{id}”', ['id' => $model->id]));
            }
        } else {
            $record = new PresetRecord();
        }

        if ($runValidation && !$model->validate()) {
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

    public function deletePresetById($id): bool
    {
        $record = PresetRecord::findOne($id);
        if ($record) {
            return (bool)$record->delete();
        }
        return false;
    }

    public function getPresetField(string $type)
    {
        $field = Craft::$app->getFields()->createField($type);
        if (!$field) {
            return false;
        }
        return $field;
    }

    public function createPreset($config): Preset
    {
        if (is_string($config)) {
            $config = [
                'type' => $config,
            ];
        }
        return new Preset($config);
    }

    // Private Methods
    // =========================================================================

    private function _createPresetsQuery(): Query
    {
        return (new Query())
            ->select([
                'id',
                'name',
                'type',
                'settings',
            ])
            ->from(['{{%colorit_presets}}']);
    }
}
