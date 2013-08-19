$(function() {
    //$('div#content').prepend('<div id="console"></div>')
    //$('div#console').css({'border': 'solid 1px #dddddd', 'height': '100px'});
    
    $('label[id="playkeylabel"]').css('width', 'auto');
    
    if ($('input#playkey').attr('checked')) {
        $('#bracketedkey td').css('cursor', 'default');
        $('#bracketedkey tbody:eq(0)').addClass('selected');
    }
    
    $('input#playkey').click(function() {
        if ($('input#playkey').attr('checked')) {
            $('#bracketedkey td').css('cursor', 'default');
            $('#bracketedkey tbody:eq(0)').addClass('selected');
            $('#bracketedkey tbody:eq(0) td.text').each(function() {
                var link = $(this).closest('tr').find('a').attr('href');
                $(this).wrapInner('<a href="' + link + '" />');
            })
        }
        else {
            $('#bracketedkey td').css('cursor', 'auto');
            $('.selected').removeClass('selected');
            $('.followed').removeClass('followed');
            $('#bracketedkey td.text').each(function() {
                var html = $(this).children('a').html();
                if (html) {
                    $(this).replaceWith('<td class="text">' + html + '</text>');
                }
            });
        }
    });
    
    $('#bracketedkey').delegate('.selected td a', 'click', function() {
        $('.selected').removeClass('selected');
        //$('.followed').removeClass('followed');
        $('tr.backlinked').removeClass('backlinked');
        $('span.backlink').remove();
        $(this).closest('tr').addClass('followed');
        var link = $(this).closest('tr').find('a').attr('href');
        $(link).addClass('selected');
        $(link + ' td.text').each(function() {
            var href = $(this).closest('tr').find('td.to a').attr('href');
            if (href) {
                $(this).wrapInner('<a href="' + href + '" />');
            }
        });
        //var backlink = link.replace('s', 'l');
        //$(link + ' tr:first td:first').prepend('<span class="backlink"><a href="' + backlink + '">&#8593;</a></span>');
        $(this).closest('tbody').find('td.text').each(function() {
            var html = $(this).children('a').html();
            if (html) {
                $(this).replaceWith('<td class="text">' + html + '</td>');
            }
        });
    });
    
    $('#bracketedkey').delegate('.backlink a', 'click', function() {
        var link = $(this).attr('href');
        $(link).addClass('backlinked');
        var previous = $(link).closest('tbody').attr('id');
        previous = $('td.to a[href="#' + previous + '"]').closest('tr').attr('id');
        $(link + ' td:first').prepend('<span class="backlink"><a href="#' + previous + '">&#8593;</a></span>');
    });
});