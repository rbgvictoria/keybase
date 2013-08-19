$(function() {
    $('#compkeys a').click(function(e) {
        e.preventDefault();
        
        var href = $(this).attr('href');
        
        $('#compare').load(href);
    });
});