$(function() {
    contentSize();
    $(window).resize(function() {
        contentSize();
    });
    
    $('button, a.button-link, input[type="submit"]').button();
    
    $('input[type!="hidden"]').first().focus();
    
    $('#searchbox').autocomplete({
            source: 'http://keybase.rbg.vic.gov.au/autocomplete/searchtaxon',
            minLength: 2
    });
    
    
});

function contentSize() {
        var w = window.innerHeight;
        var b = $('#banner').height();
        var m = $('#menu').height();
        var f = $('#footer').height();
        var pt = Number($('#content').css('padding-top').substr(0, $('#content').css('padding-top').indexOf('px')));
        var pb = Number($('#content').css('padding-bottom').substr(0, $('#content').css('padding-bottom').indexOf('px')));
        var cmt = Number($('#container').css('margin-top').substr(0, $('#container').css('margin-top').indexOf('px')))
        var cmb = Number($('#container').css('margin-bottom').substr(0, $('#container').css('margin-bottom').indexOf('px')))
	var height = w-(b+m+f+pt+pb+cmt+cmb);
	$('#content').css('min-height', height + 'px');
}