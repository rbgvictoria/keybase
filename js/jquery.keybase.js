var href = location.href;
var base_url;
if (href.indexOf('index.php') > -1) {
    base_url = href.substr(0, href.indexOf('index.php'));
    site_url = base_url + '/index.php';
}
else {
    if (href.indexOf('/localhost') > -1) {
        base_url = href.substr(0, href.indexOf('keybase/')+8);
    }
    else {
        if (href.indexOf('/dev/') > -1 || href.substr(href.length-4) === '/dev') {
            base_url = href.substr(0, href.indexOf('/', 9)) + '/dev/';
        }
        else {
            base_url = href.substr(0, href.indexOf('/', 9)) + '/';
        }
    }
    site_url = base_url.substr(0, base_url.length -1);
}

/*
 * File upload for Bootstrap 3
 */
$(document).on('change', '.btn-file :file', function() {
var input = $(this),
    numFiles = input.get(0).files ? input.get(0).files.length : 1,
    label = input.val().replace(/\\/g, '/').replace(/.*\//, '');

    input.trigger('fileselect', [numFiles, label]);
});


$(function() {
    /*
     * Autocomple on search box
     */
    $('#searchbox').autocomplete({
            source: base_url + 'autocomplete/searchtaxon',
            minLength: 2
    });
    
    /*
     * Tabs
     */
    /*var tab = $.QueryString['tab'];
    if (!tab || tab > 3) {
        tab = 0;
    }
    
    $(function() {
        $( "#project_tabs" ).tabs({
            active: tab,
            heightStyle: "auto" 
        });
    });*/
    var tab;
    if ($.QueryString.mode !== undefined) {
        switch($.QueryString.mode) {
            case 'interactive':
                tab = 0;
                break;
            case 'bracketed':
                tab = 1;
                break;
            case 'indented':
                tab = 2;
                break;
            
            case 'hierarchical':
                tab = 0;
                break;
            case 'alphabetical':
                tab = 1;
                break;
                
            default:
                tab = 0;
                break;
        };        
    }
    else if ($.QueryString.tab !== undefined) {
        tab = $.QueryString.tab;
    }
    else {
        tab = 0;
    }
    
    $( "#project_tabs, #key_tabs" ).tabs({
        active: tab,
        heightStyle: "auto" 
    });
    $('.nav-tabs>li>a').eq(tab).tab('show');
    
    
    /*
     * 
     */
    $('.btn-file :file').on('fileselect', function(event, numFiles, label) {
        $('#selected-file').html(label);
    });
    
    /*
     * 
     */
    if (location.href.indexOf('keys/show/') > -1) {
        var key = location.href.substr(location.href.lastIndexOf('/') + 1);
        if (key.indexOf('?') > -1) {
            key = key.substring(0, key.indexOf('?'));
        }
        $.fn.keybase({
            baseUrl: base_url + "ws/keyJSON",
            playerDiv: '#keybase-player',
            key: key,
            title: false,
            source: false,
            reset: true,
            remainingItemsDisplay: remainingItemsDisplay,
            discardedItemsDisplay: discardedItemsDisplay,
            onLoad: showItems
        });
        var contentHeight = $('#keybase-player').offset().top + 600;
        if (contentHeight > $(window).height()-60) {
            $('body').css('height', contentHeight);
        }
        bracketedKey();
        indentedKey();
        
        $('a[href=#player]').click(function() {
            if (($.QueryString.mode !== undefined && $.QueryString.mode !== 'interactive') || 
                    ($.QueryString.tab !== undefined && $.QueryString.tab > 0)) {
                $('.keybase-player-window').remove();
                $.fn.keybase({
                    baseUrl: base_url + "ws/keyJSON",
                    playerDiv: '#keybase-player',
                    key: key,
                    title: false,
                    source: false,
                    reset: false,
                    remainingItemsDisplay: remainingItemsDisplay,
                    discardedItemsDisplay: discardedItemsDisplay,
                    onLoad: showItems
                });
                
            }    
            
        });
        
        $('a[href=#player]').click(function() {
            var contentHeight = $('#keybase-player').offset().top + $('#keybase-player').height();
            if (contentHeight > $(window).height()-60) {
                $('body').css('height', contentHeight);
            }
        });
        $('a[href=#bracketed]').click(function() {
            var contentHeight = $('#keybase-bracketed').offset().top + $('#keybase-bracketed').height();
            if (contentHeight > $(window).height()-60) {
                $('body').css('height', contentHeight);
            }
            else {
                $('body').css('height', $(window).height()-80);
            }
        });
        $('a[href=#indented]').click(function() {
            var contentHeight = $('#keybase-indented').offset().top + $('#keybase-indented').height();
            if (contentHeight > $(window).height()-60) {
                $('body').css('height', contentHeight);
            }
            else {
                $('body').css('height', $(window).height()-80);
            }
        });
        $('a[href=#about]').click(function() {
            var contentHeight = $('#about').offset().top + $('#about').height();
            if (contentHeight > $(window).height()-60) {
                $('body').css('height', contentHeight);
            }
            else {
                $('body').css('height', $(window).height()-80);
            }
        });
        $('a[href=#items]').click(function() {
            var contentHeight = $('#items').parent().offset().top + $('#items').height();
            if (contentHeight > $(window).height()-60) {
                $('body').css('height', contentHeight);
            }
            else {
                $('body').css('height', $(window).height()-80);
            }
        });
        
    }
});

    var remainingItemsDisplay = function(items, itemsDiv) {
        var list = keybaseItemsDisplay(items);
        $(itemsDiv).eq(0).find('h3 .keybase-num-remaining').eq(0).html(items.length);
        $(itemsDiv).eq(0).children('div').eq(0).html('<ul>' + list.join('') + '</ul>');
    };
    
    var discardedItemsDisplay = function(items, itemsDiv) {
        var list = keybaseItemsDisplay(items);
        $(itemsDiv).eq(0).find('h3 .keybase-num-discarded').eq(0).html(items.length);
        $(itemsDiv).eq(0).children('div').eq(0).html('<ul>' + list.join('') + '</ul>');
        
        // filter
    };
    
    var keybaseItemsDisplay = function(items) {
        var list = [];
        $.each(items, function(index, item) {
            var entity;
            entity = '<li>';
            if (item.url) {
                entity += '<a href="' + item.url + '">' + item.item_name + '</a>';
            }
            else {
                entity += item.item_name;
            }
            if (item.to_key) {
                entity += '<a href="' + base_url + 'keys/show/' + item.to_key + '"><span class="keybase-player-tokey"></span></a>';
            }
            if (item.link_to_item_name) {
                entity += ': ';
                if (item.link_to_url) {
                    entity += '<a href="' + item.link_to_url + '">' + item.link_to_item_name + '</a>';
                }
                else {
                    entity += item.link_to_item_name;
                }
                if (item.link_to_key) {
                    entity += '<a href="' + base_url + 'keys/show/' + item.link_to_key + '"><span class="keybase-player-tokey"></span></a>';
                }
            }
            entity += '</li>';
            list.push(entity);
        });
        return list;
    };
    
    var bracketedKey = function () {
        $.fn.keybase('bracketedKey', {
            bracketedKeyDiv: '#keybase-bracketed',
            bracketedKeyDisplay: bracketedKeyDisplay
        });
    };

    var indentedKey = function () {
        $.fn.keybase('indentedKey', {
            indentedKeyDiv: '#keybase-indented'
        });
    };

    var showItems = function(json) {
        var list = keybaseItemsDisplay(json.items);
        $('#items').html('<ul>' + list.join('') + '</ul>');
    };
    
    var bracketedKeyDisplay = function(json) {
        /*$(settings.bracketedKeyDiv).dynatree({
            children: bracketed_key,
            data: {mode: "all"},
            expand: true
        });*/
        var html = '<div class="keybase-bracketed-key">';
        var couplets = bracketed_key[0].children;
        for (var i = 0; i < couplets.length; i++)  {
            var couplet = couplets[i];
            var leads = couplet.children;
            html += '<div class="keybase-couplet" id="l_' + leads[0].parent_id + '">';
            //html += '<span class="test">' + JSON.stringify(couplet.children) + '</span>';
            for (var j = 0; j < leads.length; j++) {
                var lead = leads[j];
                var items = lead.children;
                html += '<div class="keybase-lead">';
                html += '<span class="keybase-from-node">' + lead.fromNode + '</span>';
                html += '<span class="keybase-lead-text">' + lead.title;
                if (lead.toNode !== undefined) {
                    html += '<span class="keybase-to-node"><a href="#l_' + lead.lead_id + '">' + lead.toNode + '</a></span>';
                }
                else {
                    var toItem = items[0].children[0];
                    var item = JSPath.apply('.items{.item_id==' + toItem.item_id + '}', json)[0];
                    html += '<span class="keybase-to-item">';
                    if (item.url) {
                        html += '<a href="' + item.url + '">' + item.item_name + '</a>';
                    }
                    else {
                        html += item.item_name;
                    }
                    if (item.to_key) {
                        html += '<a href="' + item.to_key + '?mode=bracketed"><span class="keybase-player-tokey"></span></a>';
                    }

                    if (item.link_to_id) {
                        html += ': ';
                        if (item.link_to_url) {
                            html += '<a href="' + item.link_to_url + '">' + item.link_to_item_name + '</a>';
                        }
                        else {
                            html += item.link_to_item_name;
                        }
                        if (item.link_to_key) {
                            html += '<a href="' + item.link_to_key + '?mode=bracketed"><span class="keybase-player-tokey"></span></a>';
                        }

                    }

                    html += '</span> <!-- /.to-item -->';
                }
                html += '</div> <!-- /.keybase-lead -->';
            }
            html += '</span> <!-- /.keybase-lead-text -->';
            html += '</div> <!-- /.keybase-couplet -->';

        }
        html += '</div> <!-- /.keybase-bracketed_key -->';
        $(settings.bracketedKeyDiv).html(html);
        
        var contentHeight = $('#keybase-bracketed').offset().top + $('#keybase-bracketed').height();
        if (contentHeight > $(window).height()-60) {
            $('body').css('height', contentHeight);
        }

        /*$('.keybase-bracketed-key .keybase-lead').each(function() {
            var divHeight = $(this).height();
            $(this).find('.keybase-from-node').eq(0).css({'height': divHeight + 'px'});
        });*/
    };
    

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
