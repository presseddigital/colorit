var palettes = {};

var Colorit = (function() {
	"use strict";

	var defaults = {
		id: null,
		namespacedId: null,
		name: null,
		debug: false,
	};

	var selectors = {
		palette: '[data]',
		paletteColors: '[data-colors]',
		paletteColor: '[data-color]',
		opacity: '[data-opacity]',
		custom: '[data-custom]',
		customColor: '[data-custom-color]',
		customColorPicker: '[data-custom-color-picker]',
		customColorPickerTrigger: '[data-custom-color-picker-trigger]',
	};

	var classes = {
		selectedColor: 'colorit--palette-colorIsSelected',
	};

	var constructor = function(options) {
		// Public
		// =========================================================================

		var api = {};

		// Private
		// =========================================================================

		var settings;
		var dom = {
			field: null,
			palette: null,
			paleteColors: null,
			customColor: null,
			handleInput: null,
			opacityInput: null,
			customColorPicker: null,
			customColorPickerTrigger: null,
		};

		// Private Methods
		// =========================================================================

		var isValidHex = function(value) {
			return /^#?[0-9a-f]{3}(?:[0-9a-f]{3})?$/i.test(value);
		};

		var hexToRgb = function(hex) {
		    var shorthandRegex = /^#?([a-f\d])([a-f\d])([a-f\d])$/i;
		    hex = hex.replace(shorthandRegex, function(m, r, g, b) {
		        return r + r + g + g + b + b;
		    });

		    var result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
		    return result ? {
		        r: parseInt(result[1], 16),
		        g: parseInt(result[2], 16),
		        b: parseInt(result[3], 16)
		    } : null;
		};

		var rgbToHex = function(r, g, b) {
		    return '#' + ((1 << 24) + (r << 16) + (g << 8) + b).toString(16).slice(1);
		};

		var setOpacity = function(color) {
			var opacity = dom.opacityInput.value / 100;
			var hex = color.getAttribute('data-color');
			if(isValidHex(hex)) {
				var rgb = hexToRgb(hex);
				color.style.backgroundColor = 'rgba(' + rgb.r + ',' + rgb.g + ',' + rgb.b + ',' + (opacity || 1) + ')';
			}
		};

		var clearPaletteColorSelection = function() {
			dom.paletteColors.forEach(function (color, index) {
				color.classList.remove(classes.selectedColor);
	        });
	        dom.handleInput.value = '';
		};

		var clearCustomColorSelection = function(clearValue) {
			if (isValidHex(dom.customColorInput.value)) {
				dom.customColorInput.setAttribute('data-last-color', dom.customColorInput.value);
			}
	        if(clearValue) {
	        	dom.customColorInput.value = '';
	        }
	        if(dom.customColor) {
	        	dom.customColor.setAttribute('data-color', '');
	        	dom.customColor.style.backgroundColor = null;
	        }
	        if (dom.custom) {
		        dom.custom.classList.remove(classes.selectedColor);
	        }
		};

		// Event Handlers
		// =========================================================================

		var colorHandler = function(event) {

			var color = event.target.closest(selectors.paletteColor);
			if (!color) return;

			event.preventDefault();
			event.stopPropagation();

			var isSelected = color.classList.contains(classes.selectedColor);

			clearPaletteColorSelection();
			clearCustomColorSelection(true);

			if(!isSelected) {
				color.classList.add(classes.selectedColor);
				dom.handleInput.value = color.getAttribute('data-handle');
			}
		};

		var customColorPickerHandler = function(event) {
			dom.customColorInput.value = event.target.value;
			dom.customColorInput.dispatchEvent(new Event('keyup'));
		}

		var customColorPickerTriggerHandler = function(event) {
			event.preventDefault();
			event.stopPropagation();
			dom.customColorPicker.click();
		}

		var customColorHandler = function(event) {

			event.preventDefault();
			event.stopPropagation();

			clearPaletteColorSelection();

			if (event.type === 'focus') {
				var lastCustomColor = dom.customColorInput.getAttribute('data-last-color');
				if(lastCustomColor) {
					dom.customColorInput.value = lastCustomColor;
					dom.customColorInput.removeAttribute('data-last-color');
				}
			}

			var color = dom.customColorInput.value;

			dom.handleInput.value = '_custom_';
			if(color == '') {
				dom.handleInput.value = '';
			}

			if(!color.match('^#') && color != '#' && color != '') {
				color = '#' + color;
				dom.customColorInput.value = color;
			}

			if(isValidHex(color)) {
				dom.customColor.setAttribute('data-color', color);
				setOpacity(dom.customColor);
				dom.custom.classList.add(classes.selectedColor);
			} else {
				clearCustomColorSelection();
			}
		};

		var opacityHandler = function(event) {

			event.preventDefault();
			event.stopPropagation();

			dom.paletteColors.forEach(function (color, index) {
				setOpacity(color);
	        });

			setOpacity(dom.customColor);

		};

		// Public Methods
		// =========================================================================

		api.init = function(options) {

			settings = extend(defaults, options || {});

			if (settings.debug) {
				console.log("[COLORIT][" + settings.namespacedId + "]", settings);
			}

			dom.field = document.getElementById(settings.namespacedId);
			if(dom.field) {

				dom.palette = dom.field.querySelector(selectors.paletteColors);
				dom.paletteColors = dom.field.querySelectorAll(selectors.paletteColor);
				if(dom.palette && dom.paletteColors) {
					dom.palette.addEventListener("click", colorHandler, false);
				}

				dom.handleInput = document.getElementById(settings.namespacedId + '-handle');

				dom.opacityInput = document.getElementById(settings.namespacedId + '-opacity');
				if(dom.opacityInput) {
					dom.opacityInput.addEventListener("change", opacityHandler, false);
					dom.opacityInput.addEventListener("keyup", opacityHandler, false);
				}

				dom.custom = dom.field.querySelector(selectors.custom);
				dom.customColor = dom.field.querySelector(selectors.customColor);
				dom.customColorInput = document.getElementById(settings.namespacedId + '-custom');
				if(dom.customColorInput) {
					dom.customColorInput.addEventListener("keyup", customColorHandler, false);
					dom.customColorInput.addEventListener("focus", customColorHandler, false);
				}

				dom.customColorPicker = dom.field.querySelector(selectors.customColorPicker);
				dom.customColorPickerTrigger = dom.field.querySelector(selectors.customColorPickerTrigger);

				if(dom.customColorPicker && dom.customColorPickerTrigger) {
					dom.customColorPicker.addEventListener("input", customColorPickerHandler, false);
					dom.customColorPickerTrigger.addEventListener("click", customColorPickerTriggerHandler, false);
				}

			}
		};

		api.init(options);
		return api;
	};

	return constructor;
})();
