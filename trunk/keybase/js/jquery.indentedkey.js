$(function() {
    $('#indentedkey').css('cursor', 'default');
    
    $('#indentedkey td.text, #indentedkey td.from').click(function() {
        $('#indentedkey .selected').removeClass('selected');
        $('#indentedkey .alternative').removeClass('alternative');
        
        var stepid = $(this).closest('tr').attr('id');
        var substr = stepid.substring(0, stepid.indexOf('l')+1);
        
        $(this).closest('tr').addClass('selected');
        $('tr[id^="' + substr + '"]').not('#' + stepid).addClass('alternative');
        //$(this).closest('tr').children('td.text').css({'background-color': 'blue', 'color': 'white'});
        //$('tr[id^="' + stepid + '"]').children('td from, td.text').css({'background-color': '#eeeeee', 'color': 'blue'});
    });
});