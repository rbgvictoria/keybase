$(function() {
    $('span.button').not('.openpage').click(function() {
        var href = $(this).children('a').attr('href');
        $(location).attr('href', href);
    }).css('cursor', 'pointer');
});