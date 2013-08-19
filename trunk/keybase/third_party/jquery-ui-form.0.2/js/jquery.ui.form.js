/*
 * jQuery DOMAttrModified 0.1
 *
 * An event triggered upon attribute change should really be in jQuery Core.
 * This is extremely powerful and useful.
 */
(function($, $attr){

	// override core $.attr() to mimic DOMAttrModified so we can bind to it
	// http://www.w3.org/TR/DOM-Level-2-Events/events.html#Events-MutationEvent
	$.attr = function(elem, name, value, pass){
		if (value === undefined) {// getter
			return $attr(elem, name, value, pass);
		}
		else {// setter
			// store original value
			var prevValue = $attr(elem, name, undefined, pass);
			
			// call original fn
			$attr(elem, name, value, pass);
			
			// get value from orig again in case of special handling
			var newValue = $attr(elem, name, undefined, pass);
			
			// attrChange
			// MODIFICATION = 1;
			// ADDITION = 2;
			// REMOVAL = 3;
			var event = $.extend(new $.Event('attrchange'), {
				relatedNode: elem,
				prevValue: prevValue || '',
				newValue: newValue || '',
				attrName: name,
				attrChange: newValue === '' ? 3 : prevValue === undefined ? 2 : 1
			});
			
			$(elem).trigger(event);
			
			return newValue;
		}
	};
	
}(jQuery, jQuery.attr));

/*
 * jQuery UI Form 0.1
 *
 * Much credit goes to Web Forms by Scott Gonzalez
 * (http://plugins.jquery.com/node/1178). Scott's work has been reorganized into
 * a jQuery-UI widget and I have made many changes from there.
 *
 * Depends: jquery.ui.widget.js
 */
(function($){

	$.widget('ui.webform', {
		options: {},
		
		_create: function(){
			var self = this;
			
			if (!self.element.is('form')) {
				return false;
			}
			
			// TODO: verify this is working
			self.element.addClass('ui-form').bind('submit', function(){
				return self._submit();
			});
		},
		
		_submit: function(){
			return this.checkValidity();
		},
		
		// TODO: verify this is working
		checkValidity: function(){
			if (this.element.length && typeof this.element.attr('novalidate') === 'undefined') {
				self._trigger('beforeValidity');
				
				var elem = this[0];
				var valid = true;
				
				elem.find(':input:not([type=hidden]):not(:button):not(:reset):not(:image):not(:submit):not(:disabled):not([readonly])').each(function(){
					valid = $(this).webfield('checkValidity') && valid;
				});
				if (!valid) {
					elem.find(':invalid:eq(0)')[0].focus();
				}
				
				self._trigger('afterValidity');
				return valid;
			}
		}
		
	});
	
}(jQuery));

/*
 * jQuery UI Field 0.1
 *
 * Much credit goes to Web Forms by Scott Gonzalez
 * (http://plugins.jquery.com/node/1178). Scott's work has been reorganized into
 * a jQuery-UI widget and I have made many changes from there.
 *
 * Depends: jquery.ui.widget.js
 */
(function($){

	// compliance checks
	
	var support = {
		placeholder: function(){
			var elm = document.createElement('input');
			var ret = ('placeholder' in elm);
			elm = null;
			return ret;
		}(),
		selection: !!HTMLInputElement.prototype.setSelectionRange,
		stepDown: !!HTMLInputElement.prototype.stepDown,
		stepUp: !!HTMLInputElement.prototype.stepUp
	};
	
	// setup
	
	var re = {
		color: /^#([0-9a-f]{2,2}){3}$/i,
		date: /^(\d{4})-(\d{2,2})-(\d{2,2})$/,
		// http://projects.scottsplayground.com/email_address_validation/
		email: /^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|(\x22((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?\x22))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)*(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i,
		number: /^-?\d*\.?\d+(e-?\d+)?$/,
		// http://projects.scottsplayground.com/iri/
		url: /^([a-z]([a-z]|\d|\+|-|\.)*):(\/\/(((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:)*@)?((\[(|(v[\da-f]{1,}\.(([a-z]|\d|-|\.|_|~)|[!\$&'\(\)\*\+,;=]|:)+))\])|((\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5]))|(([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=])*)(:\d*)?)(\/(([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)*)*|(\/((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)+(\/(([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)*)*)?)|((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)+(\/(([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)*)*)|((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)){0})(\?((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|[\uE000-\uF8FF]|\/|\?)*)?(\#((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|\/|\?)*)?$/i
	};
	
	function isColor(val){
		return re.color.test(val);
	}
	
	function isDate(val){
		return re.date.test(val);
	}
	
	function isEmail(val){
		return re.email.test(val);
	}
	
	function isNumber(val){
		return re.number.test(val);
	}
	
	function isURL(val){
		return re.url.test(val);
	}
	
	function toDate(val){
		if (isDate(val)) {
			var parsed = val.match(re.date);
			return new Date(parsed[1], parsed[2] - 1, parsed[3]);
		}
		return null;
	}
	
	function toNumber(val){
		if (isNumber(val)) {
			return Number(val);
		}
		return null;
	}
	
	function getCheckedCount(element_name){
		return $('input[name=' + element_name + ']:checked').length;
	}
	
	function getSelectedCount(elem){
		return elem.find('option:selected').length;
	}
	
	function getType(elem){
		// DOM does not return unsupported types so try HTML attribute first
		return elem[0].getAttribute('type') || elem.attr('type');
	}
	
	function getWebForms(elem){
		var webForms = $.data(elem, 'webForms');
		if (webForms === undefined) {
			webForms = {
				willValidate: $(elem).webfield('willValidate'),
				validity: $.extend({}, validityState),
				customErrorMessage: ''
			};
			$.data(elem, 'webForms', webForms);
		}
		return webForms;
	}
	
	function validate(elem, webForms){
		webForms.validity.valid = !webForms.validity.customError;
		$.each(validator, function(e, f){
			webForms.validity.valid = !(webForms.validity[e] = !f(elem)) && webForms.validity.valid;
		});
	}
	
	function getValidationMessage(elem, webForms){
		var validity = $.extend({}, webForms.validity);
		delete validity.valid;
		
		var message = '';
		$.each(validity, function(e, v){
			if (v) {
				if (typeof validationMessages[e] == 'string') {
					message += validationMessages[e];
					return false;
				}
				else if ($.isFunction(validationMessages[e])) {
					message += validationMessages[e](elem);
					return false;
				}
			}
		});
		return $.trim(message);
	}
	
	var validityState = {
		typeMismatch: false,
		rangeUnderflow: false,
		rangeOverflow: false,
		stepMismatch: false,
		tooShort: false,
		tooLong: false,
		patternMismatch: false,
		valueMissing: false,
		customError: false,
		valid: true
	};
	
	var validationMessages = {
		typeMismatch: function(elem){
			switch (getType(elem)) {
				case 'color':
					return 'Value must be a hexadecimal color in format of #rrggbb.';
				case 'date':
					return 'Value must be a date in format of yyyy-mm-dd.';
				case 'email':
					return 'Value must be an email address.';
				case 'number':
				case 'range':
					return 'Value must be a number.';
				case 'url':
					return 'Value must be a URL.';
			}
		},
		rangeUnderflow: function(elem){
			switch (getType(elem)) {
				case 'select-multiple':
					return 'Value may not be less than ' + $(elem).attr('data-min') + '.';
				default:
					return 'Value may not be less than ' + $(elem).attr('min') + '.';
			}
		},
		rangeOverflow: function(elem){
			switch (getType(elem)) {
				case 'select-multiple':
					return 'Value may not be more than ' + $(elem).attr('data-max') + '.';
				default:
					return 'Value may not be more than ' + $(elem).attr('max') + '.';
			}
		},
		stepMismatch: 'Step mismatch.',
		tooShort: function(elem){
			return 'Value may not be less than ' + $(elem).attr('data-minlength') + ' characters.';
		},
		tooLong: function(elem){
			return 'Value may not be more than ' + $(elem).attr('maxlength') + ' characters.';
		},
		patternMismatch: 'Value does not match the expected pattern.',
		valueMissing: 'Value is required.',
		customError: function(elem){
			return getWebForms(elem).customErrorMessage;
		}
	};
	
	var validator = {
		typeMismatch: function(elem){
			var val = elem.val();
			if (val !== '') {
				switch (getType(elem)) {
					case 'color':
						return isColor(val);
					case 'date':
						return isDate(val);
					case 'email':
						return isEmail(val);
					case 'number':
					case 'range':
						return isNumber(val);
					case 'url':
						return isURL(val);
				}
			}
			return true;
		},
		
		rangeUnderflow: function(elem){
			switch (getType(elem)) {
				// case 'datetime':
				case 'date':
					// TODO
					/*
				 * var min = toDate(elem.attr('min'));
				 * if (min) {
				 * 	var val = toDate(elem.val());
				 * 	if (val) {
				 * 		return (min <= val);
				 * 	}
				 * }
				 */
					break;
				// case 'month':
				// case 'week':
				// case 'time':
				// case 'datetime-local':
				case 'number':
				case 'range':
					var min = toNumber(elem.attr('min'));
					if (typeof min === 'number') {
						var val = toNumber(elem.val());
						if (typeof val === 'number') {
							return (min <= val);
						}
					}
					break;
				case 'select-multiple':
					var min = toNumber(elem.attr('data-min'));
					if (typeof min === 'number') {
						return (min <= getSelectedCount(elem));
					}
					break;
			}
			return true;
		},
		
		rangeOverflow: function(elem){
			switch (getType(elem)) {
				// case 'datetime':
				case 'date':
					// TODO
					break;
				// case 'month':
				// case 'week':
				// case 'time':
				// case 'datetime-local':
				case 'number':
				case 'range':
					var max = elem.attr('max');
					if ((max !== '') && isNumber(max)) {
						var val = elem.val();
						if (isNumber(val)) {
							return (Number(max) >= Number(val));
						}
					}
					break;
				case 'select-multiple':
					var max = elem.attr('data-max');
					if ((max !== '') && isNumber(max)) {
						return (Number(max) >= getSelectedCount(elem));
					}
					break;
			}
			return true;
		},
		
		stepMismatch: function(elem){
			switch (getType(elem)) {
				// case 'datetime':
				case 'date':
					// TODO
					break;
				// case 'month':
				// case 'week':
				// case 'time':
				// case 'datetime-local':
				case 'number':
				case 'range':
					var step = elem.attr('step');
					if (step && isNumber(step)) {
						var base = elem.attr('min');
						if ((base === '') || !isNumber(base)) {
							base = elem.attr('max');
						}
						if ((base !== '') && isNumber(base)) {
							var val = elem.val();
							if (isNumber(val)) {
								return (parseInt((val - base) / step, 10) == ((val - base) / step));
							}
						}
					}
					break;
			}
			return true;
		},
		
		tooShort: function(elem){
			switch (getType(elem)) {
				case 'text':
				case 'url':
				case 'search':
				case 'tel':
				case 'email':
				case 'password':
				case 'textarea':
					var minlength = elem.attr('data-minlength');
					var val = elem.val();
					// only check if there is something in val
					if (minlength && (minlength > 0) && (val !== '')) {
						return (minlength <= val.length);
					}
					break;
			}
			return true;
		},
		
		tooLong: function(elem){
			switch (getType(elem)) {
				case 'text':
				case 'url':
				case 'search':
				case 'tel':
				case 'email':
				case 'password':
				case 'textarea':
					var maxlength = elem.attr('maxlength');
					if (maxlength && (maxlength > 0)) {
						return (maxlength >= elem.val().length);
					}
			}
			return true;
		},
		
		patternMismatch: function(elem){
			switch (getType(elem)) {
				case 'text':
				case 'url':
				case 'search':
				case 'tel':
				case 'email':
				case 'password':
					var pattern = elem.attr('pattern');
					var val = elem.val();
					if ((pattern || (pattern === 0)) && (val !== '')) {
						var regex = new RegExp('^(?:' + pattern + ')$');
						if (!regex.test(val)) {
							return false;
						}
					}
			}
			return true;
		},
		
		valueMissing: function(elem){
			if (elem.attr('required')) {
				switch (getType(elem)) {
					case 'text':
					case 'url':
					case 'search':
					case 'tel':
					case 'email':
					case 'password':
					case 'datetime':
					case 'date':
					case 'month':
					case 'week':
					case 'time':
					case 'datetime-local':
					case 'number':
					case 'file':
						if (elem.val() === '') {
							return false;
						}
						break;
					case 'radio':
					case 'checkbox':
						var checked_count = getCheckedCount(elem.attr('name'));
						if (elem.is(':checkbox')) {
							return (checked_count >= 1);
						}
						else {
							return (checked_count == 1);
						}
						break;
					case 'select-one':
					case 'select-multiple':
						var selected_count = getSelectedCount(elem);
						if (elem.is('[multiple]')) {
							return (selected_count >= 1);
						}
						else {
							// Browsers default select-ones to 1st option so they will always be selected.
							// return (selected_count == 1);
							
							// for now, just assume option w/ blank value until we have more info on this
							if (elem.val() === '') {
								return false;
							}
						}
						break;
				}
			}
			return true;
		}
	};
	
	// selectors - http://www.w3.org/TR/css3-ui/#pseudo-validity
	$.extend($.expr[':'], {
		'default': function(elem){
			return elem === $(elem.form).find(':submit:first')[0] || elem.defaultChecked || elem.defaultSelected;
		},
		'valid': function(elem){
			return $(elem).webfield('validity').valid;
		},
		'invalid': function(elem){
			return !$(elem).webfield('validity').valid;
		},
		'in-range': function(elem){
			var validity = $(elem).webfield('validity');
			return !validity.typeMismatch && !validity.rangeUnderflow && !validity.rangeOverflow;
		},
		'out-of-range': function(elem){
			var validity = $(elem).webfield('validity');
			return validity.rangeUnderflow || validity.rangeOverflow;
		},
		'required': function(elem){
			return $(elem).is('fieldset') ? $(elem).attr('data-required') : $(elem).attr('required');
		},
		'optional': function(elem){
			return /input|textarea|select|fieldset/i.test(elem.nodeName) &&
			!/hidden|image|reset|submit|button/i.test(elem.type) &&
			!($(elem).is('fieldset') ? $(elem).attr('data-required') : $(elem).attr('required'));
		},
		'read-only': function(elem){
			return $(elem).is('[readonly]');
		},
		'read-write': function(elem){
			return !$(elem).is('[readonly]');
		}
	});
	
	$.widget('ui.webfield', {
		options: {},
		
		_create: function(){
			var self = this;
			if (!self.element.is(':input:not([type=hidden]):not(:button):not(:reset):not(:image):not(:submit), fieldset')) {
				return false;
			}
			
			self.element.addClass('ui-field ui-corner-all').bind('attrchange.webfield', function(event){
				// List any visuals that need to change if the attribute does.
				// Changing an attribute may mean altering constraints so we re-validate.
				switch (event.attrName) {
					case 'readonly':
						// readonly change fires event and has validity updates
						self._readonly();
						self.checkValidity();
						break;
					case 'required':
					case 'data-required':
						// required change fires event and has validity updates
						self._required();
						self.checkValidity();
						break;
					case 'placeholder':
						// placeholder only affects visual
						self._placeholder();
						break;
					case 'accept':
					case 'max':
					case 'data-max':
					case 'maxlength':
					case 'data-minlength':
					case 'min':
					case 'data-min':
					case 'multiple':
					case 'pattern':
					case 'step':
						// no visual, just validity
						self.checkValidity();
						break;
				}
			});
			
			// newer browsers support input and change events, older ones need keyup
			self.element.filter('input, textarea').bind('input.webfield change.webfield keyup.webfield', function(){
				self.checkValidity();
			});
			
			// TODO: only allow numeric input in number/range fields
			//self.element.filter('input[type=number], input[type=range]').bind('keyup.webfield', function(evt){
			//});
			
			// TODO: do not allow textarea with maxlength to exceed that length (just like input)
			//self.element.filter('textarea').bind('keydown.webfield', function(){
			//});
			
			self.element.filter('select').bind('input.webfield change.webfield', function(){
				self.checkValidity();
			});
			
			// TODO: fieldsets need to validate based on radio/checkboxes they have
			//self.element.filter('fieldset').find('input[type=radio], input[type=checkbox]').bind('change.webfield', function(){
			//});
			
			self._readonly();
			self._required();
			self._placeholder();
			self._autofocus();// save this for last
		},
		
		_init: function(){
			this.checkValidity();
		},
		
		destroy: function(){
			// TODO: verify this is working
			this.element.unbind('.webfield').removeClass('ui-field ui-corner-all');
		},
		
		// validation
		
		checkValidity: function(){
			var self = this, elm = self.element;
			if (elm.length) {
				var webForms = getWebForms(elm);
				if (webForms.willValidate) {
					// TODO: verfiy beforeValidity is working
					self._trigger('beforeValidity');
					
					validate(elm, webForms);
					var validity = webForms.validity;
					
					// TODO: verify outofrange is working
					if (validity.rangeUnderflow || validity.rangeOverflow) {
						self._trigger('outofrange');
					}
					// TODO: verify inrange is working
					else if (!validity.typeMismatch && !validity.rangeUnderflow && !validity.rangeOverflow) {
						self._trigger('inrange');
					}
					
					if (!validity.valid) {
						self._trigger('invalid', null, {
							validationMessage: self.validationMessage()
						});
					}
					else {
						self._trigger('valid');
					}
					
					// TODO: verfiy afterValidity is working
					self._trigger('afterValidity');
					return validity.valid;
				}
				return true;
			}
		},
		
		setCustomValidity: function(message){
			message = message || '';
			var flag = !!message;
			return this.each(function(){
				var webForms = getWebForms(this);
				webForms.customErrorMessage = message;
				webForms.validity.valid = !(webForms.validity.customError = flag);
				for (e in validator) {
					webForms.validity.valid = webForms.validity.valid &&
					!webForms.validity[e];
				}
				$.data(this, 'webForms', webForms);
			});
		},
		
		validationMessage: function(){
			var message = '';
			if (this.element.length) {
				var webForms = getWebForms(this.element);
				if (!webForms.validity.valid) {
					message = getValidationMessage(this.element, webForms);
				}
			}
			return message;
		},
		
		validity: function(){
			if (this.element.length) {
				return getWebForms(this[0]).validity;
			}
		},
		
		willValidate: function(){
			return this.element.is(':enabled:not([readonly])');
		},
		
		// informational
		
		labels: function(){
			if (this.element.is('fieldset')) {
				return this.element.find('legend');
			}
			else {
				var id = this.element.attr('id');
				if (id.length) {
					return $('label[for=' + id + ']');
				}
			}
			return [];
		},
		
		valueAsDate: function(){
			switch (getType(this.element)) {
				case 'datetime':
				case 'date':
				case 'month':
				case 'week':
				case 'time':
					return toDate(this.element.val());
			}
		},
		
		valueAsNumber: function(){
			switch (getType(this.element)) {
				case 'datetime':
				case 'date':
				case 'month':
				case 'week':
				case 'time':
				case 'datetime-local':
				case 'number':
				case 'range':
					return toNumber(this.element.val());
			}
		},
		
		// interaction
		
		_autofocus: function(){
			if (this.element.attr('autofocus')) {
				this.element.focus();
			}
		},
		
		_placeholder: support.placeholder ? function(){
			// uses native support
		}
 : function(){
			var self = this;
			if (self.element.attr('placeholder')) {
				self.element.bind('blur focus', function(evt){
					self._placeholderCheck(evt.type);
				});
			}
			else {
				self.element.unbind('blur focus', function(evt){
					self._placeholderCheck(evt.type);
				});
			}
			self._placeholderCheck();
		},
		
		_placeholderCheck: function(type){
			var self = this;
			var placeholderEl = self.labels().find('span');
			if (self.element.attr('placeholder')) {
				if (type && type === 'focus') {
					placeholderEl.remove();
				}
				else if (self.element.val().length === 0 && !placeholderEl.length) {
					placeholderEl = $('<span>' + self.element.attr('placeholder') + '</span>').appendTo(self.labels());
					placeholderEl.css({
						'border': '1px solid transparent',
						'color': '#999',
						'display': 'block',
						'line-height': '1.1em',
						'padding': '4px',
						'position': 'absolute'
					});
					// BUG: position incorrect when field is hidden at time of calculation
					placeholderEl.position({
						of: self.element,
						my: 'left center',
						at: 'left center'
					});
				}
			}
			else {
				placeholderEl.remove();
			}
		},
		
		_readonly: function(){
			if (this.element.attr('readonly')) {
				this._trigger('readonly');
			}
			else if (!this.element.attr('readonly')) {
				this._trigger('readwrite');
			}
		},
		
		_required: function(){
			var requiredAttr = this.element.is('fieldset') ? 'data-required' : 'required';
			if (this.element.attr(requiredAttr)) {
				this._trigger('required', null, {
					valueRequiredText: validationMessages.valueMissing
				});
			}
			else if (!this.element.attr(requiredAttr)) {
				this._trigger('optional');
			}
		},
		
		stepDown: support.stepDown ? function(n){
			// use native
			return this.element[0].stepDown(n);
		}
 : function(n){
 			n = n || 1;
			switch (getType(this.element)) {
				case 'datetime':
				case 'date':
				case 'month':
				case 'week':
				case 'time':
				case 'datetime-local':
				case 'number':
				case 'range':
					// TODO
					break;
			}
		},
		
		stepUp: support.stepUp ? function(n){
			// use native
			return this.element[0].stepUp(n);
		}
 : function(n){
 			n = n || 1;
			switch (getType(this.element)) {
				case 'datetime':
				case 'date':
				case 'month':
				case 'week':
				case 'time':
				case 'datetime-local':
				case 'number':
				case 'range':
					// TODO
					break;
			}
		},
		
		// selection module
		
		select: function(){
			// jQuery Core already supports this
			return this.element.trigger('select');
		},
		
		selectionStart: support.selection ? function(value){
			// use native
			// TODO
		}
 : function(value){
			// TODO
		},
		
		selectionEnd: support.selection ? function(value){
			// use native
			// TODO
		}
 : function(value){
			// TODO
		},
		
		setSelectionRange: support.selection ? function(start, end){
			// use native
			// TODO
		}
 : function(start, end){
			// TODO
		}
		
		// TODO: form* attributes for submit/image buttons
	
	});
	
}(jQuery));
