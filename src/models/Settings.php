<?php
namespace fruitstudios\styleit\models;

use fruitstudios\styleit\fields\PaletteField;

use Craft;
use craft\base\Model;

class Settings extends Model
{
    // Public Properties
    // =========================================================================

    public $palette;

    // Public Methods
    // =========================================================================

    public function rules()
    {
        $rules = parent::rules();
        return $rules;
    }
}
