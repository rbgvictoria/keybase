var href = location.href;

var site_url = href.substr(0, href.indexOf('/key/nothophoenix'))
if (site_url.indexOf('index.php') > 0) {
    base_url = site_url.substr(0, site_url.indexOf('index.php'));
}
else {
    base_url = site_url + '/';
}


var str = '/nothophoenix/'

var uriString;
var uri;

var remainingTaxa;

$(function(){
    
    var position;

    var contentheight = $('#keypanel').position().top-$('#content').position().top+$('#keypanel').height();
    $('#content').css('height', contentheight);

    $('#currentnode, #path, #remaining, #discarded').each(function() {
        $(this).children('div').css('height', $(this).height()-37);
    });

    $('#keypanel .drag_leftright').mousedown(function(e){
        e.preventDefault();
        position = $('#keypanel').position();
        $(document).mousemove(function(e){
            if (e.pageX > $('#keypanel').position().left+220 &&
                    e.pageX < $('#keypanel').position().left+$('#keypanel').width()-210) {
            /*if (e.pageX > $('#keypanel').position().left-5 &&
                    e.pageX < $('#keypanel').position().left+$('#keypanel').width()) {*/
                $('.drag_leftright').css('left', e.pageX-position.left);
                $('#leftpane').css("width",e.pageX-position.left);
                $('#rightpane').css("left",e.pageX-position.left+7);
                $('#leftpane>div').css('width', $('#leftpane').width()-2);
            }
        })
    });

    $('#leftpane .drag_updown').mousedown(function(e) {
        e.preventDefault();
        position = $('#leftpane').offset();
        $(document).mousemove(function(e) {
            if (e.pageY>$('#keypanel').position().top+25
                    && e.pageY<$('#keypanel').position().top+$('#keypanel').height()-32) {
                $('#leftpane .drag_updown').css('top', e.pageY-position.top+2);
                $('#currentnode').css("height", e.pageY-position.top);
                $('#currentnode>div').css("height", e.pageY-position.top-37);
                $('#path').css('top', e.pageY-position.top+7);
                $('#path>div').css('height', $('#path').height()-37);
            }
        })
    });

    $('#rightpane .drag_updown').mousedown(function(e) {
        e.preventDefault();
        position = $('#rightpane').offset();
        $(document).mousemove(function(e) {
            if (e.pageY>$('#keypanel').position().top+25
                    && e.pageY<$('#keypanel').position().top+$('#keypanel').height()-32) {
                $('#rightpane .drag_updown').css('top', e.pageY-position.top+2);
                $('#remaining').css("height", e.pageY-position.top);
                $('#remaining>div').css("height", e.pageY-position.top-37);
                $('#discarded').css('top', e.pageY-position.top+7);
                $('#discarded>div').css('height', $('#discarded').height()-37);
            }
        })
    });

    $(document).mouseup(function(e){
        $(document).unbind('mousemove');
    })
    
    
    // Nothophoenix player using AJAX
    $('#currentnode').on('click', 'a.lead', function(e) {
        e.preventDefault();
        
        var currentnode = $('#curr').val();
        href = $(this).attr('href');
        uri = $(this).Player('uri');
        
        // Get the content for the different panels
        $.fn.Player('couplet');
        $.fn.Player('path');
        $.fn.Player('remaining');
        $.fn.Player('discarded');
        
        // Create the start-over button
        var html = '<span id="startover"><a href="' + base_url + 'index.php/key/nothophoenix/' + uri[2] + '">&nbsp</a></span>';
        $('#keymenu').html(html);
        
        // Create the step-back button
        html = '<span id="back"><a href="' + site_url + '/key/nothophoenix/' + uri[2] + '/' + $("#curr").html() + '">&nbsp;</a></span>';
        $('#keymenu').append(html);
        
        $('#curr').html(uri[3]);
        
    });
    
    $('#keymenu').on('click', '#back a', function(e) {
        e.preventDefault();
        uri = $(this).Player('uri');

        // Get the content for the different panels
        $.fn.Player('couplet');
        $.fn.Player('remaining');
        $.fn.Player('discarded');
        
        // Differentially color the list items of the path
        $('#path li, #path li a').css({'color': '#000000', 'font-weight': 'normal'});
        var p = $('#path a[href$="/' + uri[3] + '"]');
        p.css('color', '#cc0000').parent('li').css({'color': '#cc0000', 'font-weight': 'bold'});
        p.parent('li').nextAll().css('color', '#aaaaaa').children().css('color', '#aaaaaa');
        
        // Create the start-over button
        var html = '<span id="startover"><a href="' + site_url + '/key/nothophoenix/' + uri[2] + '">&nbsp;</a></span>';
        $('#keymenu').html(html);
        
        // Create the step-back button
        url = site_url + '/ajax/parent/' + uri[2] + '/' + uri[3];
        $.get(url, function(parentnode) {
            if (parentnode) {
                html = '<span id="back"><a href="' + site_url + '/key/nothophoenix/' + uri[2] + '/' + parentnode + '">&nbsp;</a></span>';
                $('#keymenu').append(html);
            }
        });
        
        $('#curr').html(uri[3]);
    });
    
    $('#path').on('click', 'a', function(e) {
        e.preventDefault();
        uri = $(this).Player('uri');
        
        // Get the content for the different panels
        $.fn.Player('couplet');
        $.fn.Player('remaining');
        $.fn.Player('discarded');
        
        // Differentially color the list items of the path
        $('#path li, #path li a').css({'color': '#000000', 'font-weight': 'normal'});
        $(this).css('color', '#cc0000').parent('li').css({'color': '#cc0000', 'font-weight': 'bold'});
        $(this).parent('li').nextAll().css('color', '#aaaaaa').children().css('color', '#aaaaaa');
        
        // Create the start-over button
        var html = '<span id="startover"><a href="' + site_url + '/key/nothophoenix/' + uri[2] + '">&nbsp;</a></span>';
        $('#keymenu').html(html);
        
        // Create the step-back button
        url = site_url + '/ajax/parent/' + uri[2] + uri[3];
        $.get(url, function(parentnode) {
            if (parentnode) {
                html = '<span id="back"><a href="' + base_url + 'index.php/key/nothophoenix/' + uri[2] + '/' + parentnode + '">back one step</a></span>';
                $('#keymenu').append(html);
            }
        });
        
        $('#curr').html(uri[3]);
    });

});

(function( $ ) {
    $.fn.URI = function() {

        var href = this.attr('href');
        return href.substr(href.indexOf('key/nothophoenix')).split('/');

    };
    
    var methods = {
        uri: function() {
            var href = this.attr('href');
            return href.substr(href.indexOf('key/nothophoenix')).split('/');
            
        },
        parent: function() {
            url = url.replace('key/nothophoenix', 'ajax/parent');
            $.get(url, function(data) {
                $('#curr').html(data);
            });
        },
        couplet: function() {
            var url = site_url + '/ajax/nextCouplet/' + uri[2] + '/' + uri[3];
            $('#currentnode div').load(url);
        },
        path: function() {
            url = site_url + '/ajax/path/' + uri[2] + '/' + uri[3];
            $('#path div').load(url);
        },
        remaining: function() {
            url = site_url + '/ajax/remainingItemsJSON/' + uri[2] + '/' + uri[3];
            remainingTaxa = $.getJSON(url, function(data) {
                var items = [];

                $('#num_remaining').html(data.length);
                $.each(data, function(index, item) {
                    var entity;
                    entity = '<span class="entity">';

                    if (item.media) {
                        entity += '<img src="' + base_url + 'images/' + item.media + '" alt="Image of ' + item.name + '"/>';
                    }

                    if (item.url) {
                        entity += '<a class="external" href="' + item.url + '" target="_blank">';
                    }
                    entity += item.name;
                    if (item.url) {
                        entity += '</a>';
                    }
                    if (item.tokey) {
                        entity += '&nbsp;<a href="' +  site_url + '/key/nothophoenix/' + item.tokey + '">';
                        entity += '&#x25BA;';
                        entity += '</a>';
                    }
                    if (item.LinkTo) {
                        entity += ': ';
                        if (item.linkToUrl) {
                            entity += '<a class="external" href="' + item.linkToUrl + '">';
                        }
                        entity += item.LinkTo;
                        if (item.linkToUrl) {
                            entity += '</a>';
                        }
                        if (item.LinkToItemsID) {
                            entity += '&nbsp;<a href="' + site_url + '/key/nothophoenix/' + item.linkToItemsID + '">';
                            entity += '&#x25BA;';
                            entity += '</a>';
                        }
                    }
                    entity += '</span>';

                    items.push(entity);
                });

                $('#remaining div').html(items.join(''));
            });
        },
        discarded: function() {
            url = site_url + '/ajax/discardedItemsJSON/' + uri[2] + '/' + uri[3];
            $.getJSON(url, function(data) {
                var items = [];
                $('#num_discarded').html(data.length);
                $.each(data, function(index, item) {
                    var entity;
                    entity = '<span class="entity">';

                    if (item.media) {
                        entity += '<img src="' + base_url + 'images/' + item.media + '" alt="Image of ' + item.name + '"/>';
                    }

                    if (item.url) {
                        entity += '<a class="external" href="' + item.url + '" target="_blank">';
                    }
                    entity += item.name;
                    if (item.url) {
                        entity += '</a>';
                    }
                    if (item.tokey) {
                        entity += '&nbsp;<a href="' +  site_url + '/key/nothophoenix/' + item.tokey + '">';
                        entity += '&#x25BA;';
                        entity += '</a>';
                    }
                    if (item.LinkTo) {
                        entity += ': ';
                        if (item.linkToUrl) {
                            entity += '<a class="external" href="' + item.linkToUrl + '">';
                        }
                        entity += item.LinkTo;
                        if (item.linkToUrl) {
                            entity += '</a>';
                        }
                        if (item.LinkToItemsID) {
                            entity += '&nbsp;<a href="' + site_url + '/key/nothophoenix/' + item.linkToItemsID + '">';
                            entity += '&#x25BA;';
                            entity += '</a>';
                        }
                    }
                    entity += '</span>';

                    items.push(entity);
                });

                $('#discarded div').html(items.join(''));
            });
            
        }
    };
  
    $.fn.Player = function(method) {
        if ( methods[method] ) {
            return methods[ method ].apply( this, Array.prototype.slice.call( arguments, 1 ));
        }
        else if ( typeof method === 'object' || ! method ) {
            return methods.init.apply( this, arguments );
        }
        else {
            $.error( 'Method ' +  method + ' does not exist on jQuery.tooltip' );
        }    
    }
  
})( jQuery );
