<p align="left"><a href="https://github.com/fruitstudios/craft-colorit" target="_blank"><img width="100" height="100" src="resources/img/colorit.svg" alt="Colorit"></a></p>

# Colorit plugin for Craft 3

Plugin under development, not for production, docs coming soon...

## Template Variables

Each Colorit fieldtype returns a Colour model.

    {{ entry.coloritField }} - Returns the colour string in the format.

    {{ entry.coloritField.colour }} - (string) gets the colour
    {{ entry.coloritField.colour(format) }} - (string) with an optinal foramt (hex, rgb, rgba)
    {{ entry.coloritField.opacity }} - (int) the opacity value
    {{ entry.coloritField.handle }} - (string) the colour handle

    {{ entry.coloritField.isCustomColour }} - (bool) whether this is a custom colour
    {{ entry.coloritField.isTransparent }} - (bool) whether this is transparent
    {{ entry.coloritField.hasColour }} - (bool) whether the field has a colour set

    {{ entry.coloritField.palette }} - (array) get all available colours in this fields palette

    {{ entry.coloritField.hex }} - (string) get the hex value
    {{ entry.coloritField.rgb }} - (string) get the rgb value
    {{ entry.coloritField.rgba }} - (string) get the rgba value
    {{ entry.coloritField.r }} or {{ entry.coloritField.red }} - (string) get the red value
    {{ entry.coloritField.g }} or {{ entry.coloritField.green }} - (string) get the gredd value
    {{ entry.coloritField.b }} or {{ entry.coloritField.blue }} - (string) get the blue value
    {{ entry.coloritField.a }} or {{ entry.coloritField.alpha }} - (string) get the alpha value

## Utilities

Colorit makes a few utilities avaiable in your templates.

	{{ craft.colorit.colours.baseColours }} - (array)
	{{ craft.colorit.colours.baseColoursAsOptions }} - (array)

	{{ craft.colorit.colours.hexIsWhite(hex) }} - (bool)
	{{ craft.colorit.colours.hexIsBlack(hex) }} - (bool)
	{{ craft.colorit.colours.hexIsTransparent(hex) }} - (bool)
	{{ craft.colorit.colours.hexToRgb(hex) }} - (string)
	{{ craft.colorit.colours.hexToRgba(hex, opacity) }} - (string)


## Twig Extensions

	{{ ('#555')|hexIsWhite }} - (bool)
	{{ ('#555')|hexIsBlack }} - (bool)
	{{ ('#555')|hexIsTransparent }} - (bool)

	{{ ('#555')|hexToRgb }} - (string)
	{{ ('#555')|hexToRgba(opacity = 100) }} - (string)

Brought to you by [Fruit Studios](https://fruitstudios.co.uk)
