<?php
namespace fruitstudios\colorit\models;

use Craft;
use craft\base\Model;
use craft\validators\ColorValidator;

class PaletteColour extends Model
{
    // Public Properties
    // =========================================================================

    public $label;
	public $handle;
    public $colour;

    // Public Methods
    // =========================================================================

	public function rules(): array
    {
        return [
            [['label', 'handle'], 'string'],
            [['label', 'handle', 'colour'], 'required'],
            [
                ['colour'],
                ColorValidator::class,
                // 'message' => Craft::t('colorit', 'Invalid HEX Colour')
            ],
        ];
    }
}
