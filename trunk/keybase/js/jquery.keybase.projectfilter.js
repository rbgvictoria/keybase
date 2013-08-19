$(function(){
    $('a[href="#"]').parents('li.key').css('display', 'none');
    $('a[href!="#"]').parents('li.key').css('display', 'list-item');
    
    $('form[name="find_in_project"]').submit(function(event) {
        var string = $('#findkey').val();
        $('a:contains("' + string + '")').first().focus();
        return false;
    });
    
    $( "#findkey" ).autocomplete({
            source: "http://localhost/keybase/index.php/key/autocomplete/1",
            minLength: 2
    });
    

});

(function($) {
    $.QueryString = (function(a) {
        if (a == "") return {};
        var b = {};
        for (var i = 0; i < a.length; ++i)
        {
            var p=a[i].split('=');
            if (p.length != 2) continue;
            b[p[0]] = decodeURIComponent(p[1].replace(/\+/g, " "));
        }
        return b;
    })(window.location.search.substr(1).split('&'))
})(jQuery);