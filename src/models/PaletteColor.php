<?php
namespace fruitstudios\colorit\models;

use Craft;
use craft\base\Model;
use craft\validators\ColorValidator;

class PaletteColor extends Model
{
    // Public Properties
    // =========================================================================

    public $label;
	public $handle;
    public $color;

    // Public Methods
    // =========================================================================

	public function rules(): array
    {
        return [
            [['label', 'handle'], 'string'],
            [['label', 'handle', 'color'], 'required'],
            [['color'], ColorValidator::class],
        ];
    }
}
