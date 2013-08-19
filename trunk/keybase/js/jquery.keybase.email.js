$(function(){
	// email addresses
	$('a[href^="http://www.rbg.vic.gov.au/email"]').each(function() {
		var emailStr = $(this).attr('href');
		emailStr = emailStr.substr(emailStr.indexOf('#'));
		emailStr = emailStr.substr(1);
		var at = '@';
		var dot = '.'
		var domain = 'rbg' + dot + 'vic' + dot + 'gov' + dot + 'au';
		var emailAddr = emailStr + at + domain;
		$(this).attr('href', 'mailto:' + emailAddr);
		
		var emailHtml = $(this).html();
		if (emailHtml.indexOf('.')>-1 || emailHtml=='email') {
			$(this).html(emailAddr);
		}
	});
});