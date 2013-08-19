$(function(){

    $('button#excl').click(function(e) {
        e.preventDefault();
        $('#initems>option:selected').each(function() {
            $(this).remove();
            $(this).removeAttr('selected')
            $('#outitems').append($(this));
        });
        sortOptions('outitems');
        filterItems();
        boxHeaders();
    });
    
    $('button#exclall').click(function(e) {
        e.preventDefault();
        $('#initems>option').each(function() {
            $(this).remove();
            $('#outitems').append($(this));
        });
        sortOptions('outitems');
        $('input[name="filteritems"]').val('');
        boxHeaders();
    });

    $('button#incl').click(function(e) {
        e.preventDefault();
        $('#outitems>option:selected').each(function() {
            $(this).remove();
            $(this).removeAttr('selected')
            $('#initems').append($(this));
        });
        sortOptions('initems');
        filterItems();
        boxHeaders();
    });

    $('button#inclall').click(function(e) {
        e.preventDefault();
        $('#outitems>option').each(function() {
            $(this).remove();
            $('#initems').append($(this));
        });
        sortOptions('initems');
        $('input[name="filteritems"]').val('');
        boxHeaders();
    });
    
    $('button, a.button-link, input[type="submit"]').button();

});

sortOptions = function(selectid) {
        var options = $('#' + selectid + '>option');
        options.sort(function(a,b) {
            if (a.text > b.text) return 1;
            else if (a.text < b.text) return -1;
            else return 0
        });
        $('#' + selectid).html(options);
}

boxHeaders = function() {
    $('label[for="initems"]').text('Included taxa (' + $('#initems>option').length + ')');
    $('label[for="outitems"]').text('Excluded taxa (' + $('#outitems>option').length + ')');
}

filterItems = function() {
    var array = [];
    $('#initems>option').each(function() {
        array.push($(this).val());
    });
    var filteritems = array.join(',');
    $('input[name="filteritems"]').val(filteritems);
}




