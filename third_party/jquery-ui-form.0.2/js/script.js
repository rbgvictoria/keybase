window.log = function(){
	log.history = log.history || []; // store logs to an array for reference
	log.history.push(arguments);
	if (this.console) {
		console.log(Array.prototype.slice.call(arguments));
	}
};

$(function(){
	$('#tabs').tabs({
		cookie: {
			name: 'jquery-ui-form'
		}
	});
	
	$('form').webform();
	$('input, textarea, select, fieldset').webfield({
		required: function(evt, ui){
			$(this).webfield('labels').append(' <em title="' + ui.valueRequiredText + '"><img src="img/asterisk_red.png" /></em>');
		},
		optional: function(evt, ui){
			$(this).webfield('labels').find('em').remove();
		},
		invalid: function(evt, ui){
			$(this).addClass('invalid').webfield('labels').append(' <strong title="' + ui.validationMessage + '"><img src="img/error.png" /></strong>');
		},
		valid: function(evt, ui){
			$(this).removeClass('invalid').webfield('labels').find('strong').remove();
		}
	});
	
	function toggleSourceOther(){
		var elm = $('#example_source');
		if (elm.val() === 'Other') {
			$('#example_sourceOther').attr('disabled', false).attr('required', 'required');
		}
		else {
			$('#example_sourceOther').attr('disabled', true).removeAttr('required');
		}
	}
	
	$('#example_source').bind('change', toggleSourceOther);
	toggleSourceOther();
});
