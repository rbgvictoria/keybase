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

var wsUrl = 'http://data.rbg.vic.gov.au/dev/keybase-ws';

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
            source: wsUrl + '/autocomplete_item_name',
            minLength: 2
    });
    
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
        
        // get the key ID from the URL
        var key = location.href.substr(location.href.lastIndexOf('/') + 1);
        if (key.indexOf('?') > -1) {
            key = key.substring(0, key.indexOf('?'));
        }
        
        // see if an external filter has been set in the query string and, 
        // if so, retrieve the filter, before running the KeyBase plugin.
        if ($.QueryString.filter_id !== undefined) {
            $.ajax({
                url: site_url + '/ws/filterItems',
                data: {
                    "key_id": key,
                    "filter_id": $.QueryString.filter_id
                },
                success: function(data) {
                    doTheKeyThing(key, data);
                }
            });
        }
        else {
            doTheKeyThing(key);
        }
        
        $('[href=#player]').on('show.bs.tab', function() {
            if ($('.keybase-player-window').length === 0) {
                interactiveKey();
            }
            else {
                $.fn.keybase.defaults.resizePlayerWindow();
            }
            console.log($('#player').offset().top);
            windowHeight('.keybase-player-window');
        });
        
        $('[href=#bracketed]').on('show.bs.tab', function() {
            if ($('.keybase-bracketed-key').length === 0) {
                bracketedKey();
            }
        });
        
        $('[href=#indented]').on('show.bs.tab', function() {
            if ($('.keybase-indented-key').length === 0) {
                indentedKey();
            }
        });
    }
});

var doTheKeyThing = function(key, filter) {
    if (filter === undefined) {
        filter = [];
    }

    var mode = $.QueryString.mode;
    var action;
    if (mode == 'bracketed') {
        action = 'bracketedKey';
    }
    else {
        if (mode == 'indented') {
            action = 'indentedKey';
        }
        else {
            action = 'player';
        }
    }
    
    $.fn.keybase(action, {
        baseUrl: base_url + "ws/keyJSON",
        playerDiv: '#keybase-player',
        indentedKeyDiv: '#keybase-indented',
        bracketedKeyDiv: '#keybase-bracketed',
        key: key,
        title: false,
        source: false,
        reset: true,
        filter_items: filter,
        onLoad: keybaseOnLoad,
        onIndentedKeyComplete: onIndentedKeyComplete,
        onFilterWindowOpen: onFilterWindowOpen,
        renderItemLink: renderItemLink,
        onBracketedKeyComplete: onBracketedKeyComplete
    });

    var contentHeight = $('#keybase-player').offset().top + 600;
    if (contentHeight > $(window).height()-60) {
        $('body').css('height', contentHeight);
    }

    windowHeightTabClick('#player');
    windowHeightTabClick('#bracketed');
    windowHeightTabClick('#indented');
    windowHeightTabClick('#about');
    windowHeightTabClick('#items');
    windowHeightTabClick('#changes');
    windowHeightTabClick('#metadata');
};

var windowHeightTabClick = function(element) {
    $('a[href=' + element + ']').click(function() {
        windowHeight(element);
    });
};

var windowHeight = function(element) {
    var contentHeight = $(element).offset().top + $(element).height();
    if (contentHeight > $(window).height()-60) {
        $('body').css('height', contentHeight);
    }
    else {
        $('body').css('height', $(window).height()-80);
    }
};

var onFilterWindowOpen = function() {
    var key = location.href.substr(location.href.lastIndexOf('/') + 1);
    if (key.indexOf('?') > -1) {
        key = key.substring(0, key.indexOf('?'));
    }

    if ($('.external-filters').length === 0) {
        $.ajax({
            url: site_url + '/ws/projectFilters',
            data: {
                key_id: key
            },
            success: function(data) {
                if (data.myFilters.length > 0 || data.projectFilters.length > 0) {
                    var options;
                    options += '<option value="">Select filter...</option>';
                    if (data.myFilters.length > 0) {
                        options += '<optgroup label="My filters">';
                        $.each(data.myFilters, function(index, item) {
                            options += '<option value="' + item.id + '">' + item.name + '</option>';
                        });
                        options += '</optgroup>';
                    }
                    if (data.projectFilters.length > 0) {
                        options += '<optgroup label="Project filters">';
                        $.each(data.projectFilters, function(index, item) {
                            options += '<option value="' + item.id + '">' + item.name + '</option>';
                        });
                        options += '</optgroup>';
                    }


                    var html = '<div class="external-filters"> \
                        <div class="input-group"> \
                          <span class="input-group-addon" id="apply-filter"><i class="fa fa-filter"></i></span> \
                          <select class="form-control" name="filter-id"></select>\
                        </div> \
                      </div>';
                    if ($('.external-filters').length === 0) {
                        $('.keybase-local-filter').before(html);
                    }
                    $('.external-filters [name=filter-id]').html(options);
                    if ($.fn.keybase.getters.activeFilter !== undefined || $.QueryString.filter_id !== undefined) {
                        var activeFilter = ($.fn.keybase.getters.activeFilter() !== undefined) ? $.fn.keybase.getters.activeFilter() : false;
                        if (activeFilter.length === false) {
                            $.fn.keybase.setActiveFilter($.QueryString.filter_id);
                            activeFilter = $.QueryString.filter_id;
                        }
                        if ($('#fdelete').length === 0) {
                            $('.external-filters [name=filter-id]').val(activeFilter).after('<span class="input-group-addon" id="fdelete"><i class="fa fa-trash"></i></span>');
                        }
                    }
                    $('.external-filters [name=filter-id]').on('change', function() {
                        // set active filter to the new filter
                        $.fn.keybase.setActiveFilter($(this).val());
                        var items = $.fn.keybase.getters.jsonKey().items;

                        if ($(this).val().length > 0) {
                            $.fn.keybase.setActiveFilter($(this).val());
                            $(this).parent().append('<span class="input-group-addon" id="fspinner"><i class="fa fa-spinner fa-spin"></i></span>')
                            $.ajax({
                                url: site_url + '/ws/filterItems',
                                data: {
                                    key_id: key,
                                    filter_id: $(this).val()
                                },
                                success: function(data) {
                                    var filterItems = (data !== null) ? data : [];

                                    $.fn.keybase.setFilterItems(filterItems);

                                    var excl = [];
                                    var incl = [];
                                    $.each(items, function(index, item) {
                                        var option = '<option value="' + item.item_id + '">' + item.item_name + '</option>';
                                        if (filterItems.indexOf(item.item_id) > -1 || filterItems.indexOf(Number(item.item_id)) > -1) {
                                            incl.push(option);
                                        }
                                        else {
                                            excl.push(option);
                                        }
                                    });
                                    $('select[name=includedItems]').html(incl);
                                    $('select[name=excludedItems]').html(excl);
                                    sortOptions('includedItems');
                                    sortOptions('excludedItems');
                                    $('label[for="initems"]').text('Included taxa (' + $('#initems>option').length + ')');
                                    $('label[for="outitems"]').text('Excluded taxa (' + $('#outitems>option').length + ')');

                                    $('#fspinner').remove();
                                }
                            });

                        }
                        else {
                            var incl = [];
                            $.fn.keybase.setFilterItems([]);
                            $.each(items, function(index, item) {
                                var option = '<option value="' + item.item_id + '">' + item.item_name + '</option>';
                                incl.push(option);
                            });
                            $('select[name=includedItems]').html(incl);
                            $('select[name=excludedItems]').html('');
                            sortOptions('includedItems');
                            $('label[for="initems"]').text('Included taxa (' + $('#initems>option').length + ')');
                            $('label[for="outitems"]').text('Excluded taxa (' + $('#outitems>option').length + ')');
                            $('#fdelete').remove();
                        }

                    });

                    $('#fdelete').on('click', function() {
                        $.fn.keybase.setActiveFilter('');
                        $.fn.keybase.setFilterItems([]);
                        $('select[name=filter-id]').val('');
                        var items = $.fn.keybase.getters.jsonKey().items;
                        var incl = [];
                        $.each(items, function(index, item) {
                            var option = '<option value="' + item.item_id + '">' + item.item_name + '</option>';
                            incl.push(option);
                        });
                        $('select[name=includedItems]').html(incl);
                        $('select[name=excludedItems]').html('');
                        sortOptions('includedItems');
                        $('label[for="initems"]').text('Included taxa (' + $('#initems>option').length + ')');
                        $('label[for="outitems"]').text('Excluded taxa (' + $('#outitems>option').length + ')');
                        $('#fdelete').remove();
                    });

                    $('.keybase-filter-buttons').on('click', 'button', function() {
                        $('select[name=filter-id]').val('');
                        $.fn.keybase.setActiveFilter('');
                    });
                }
            }
        });
    }

    $('.keybase-local-filter-ok').on('click', function() {
        if ($.fn.keybase.getters.filterItems().length > 0) {
            if ($('[name=filter-id]').length > 0 && $('[name=filter-id]').val().length > 0) {
                $('.project a, #breadcrumbs a').each(function() {
                    var href = $(this).attr('href');
                    var newHref = updateHref(href);
                    $(this).attr('href', newHref);
                });

                $('.keybase-player-filter').css('background-color', '#ffcc00');
            }
            else {
                $('.keybase-player-filter').css('background-color', '#33ee33');
            }
        }
    });
};

var updateHref = function(href) {
    var qstr;
    var path;
    if (href.indexOf('?') > -1) {
        qstr = href.substr(href.indexOf('?')+1);
        path = href.substr(0, href.indexOf('?'));
    }
    else {
        qstr = '';
        path = href;
    }

    var qobj = {};
    if (qstr.length > 0) {
        qobj = $.QueryStringHREF(qstr.split('&'));
        delete qobj.filter_id;
    }

    var qstring = '';
    filter_id = $.fn.keybase.getters.activeFilter();
    if (filter_id !== undefined && filter_id.length > 0) {
        qobj.filter_id = filter_id;
    }

    qstring = $.ObjToString(qobj);

    if (qstring.length > 0) {
        return path + '?' + qstring;
    }
    else {
        return path;
    }
};

var sortOptions = function(select) {
    var options = $('[name=' + select + ']>option');
    options.sort(function(a,b) {
        if (a.text > b.text) return 1;
        else if (a.text < b.text) return -1;
        else return 0;
    });
    $('[name=' + select + ']').html(options);
};


var keybaseItemsDisplay = function(items) {
    var qstring;
    var filter = $.fn.keybase.getters.activeFilter();
    if (filter !== undefined && filter.length > 0) {
        qstring = '?filter_id=' + filter;
    }
    else {
        qstring = '';
    }

    var list = [];
    $.each(items, function(index, item) {
        var entity;
        entity = '<li>';
        entity += renderItemLink(item);
        entity += '</li>';
        list.push(entity);
    });
    return list;
};

var interactiveKey = function () {
    $.fn.keybase('player', {
        bracketedKeyDiv: '#keybase-player',
        renderItemLink: renderItemLink
    });
};

var bracketedKey = function () {
    $.fn.keybase('bracketedKey', {
        bracketedKeyDiv: '#keybase-bracketed',
        renderItemLink: renderItemLink,
        onBracketedKeyComplete: onBracketedKeyComplete
    });
};

var indentedKey = function () {
    $.fn.keybase('indentedKey', {
        indentedKeyDiv: '#keybase-indented',
        renderItemLink: renderItemLink,
        onIndentedKeyComplete: onIndentedKeyComplete
    });
};

var renderItemLink = function(item) {
    var mode = '';
    if ($('.nav-tabs .active').text() === 'Bracketed') {
        mode = '?mode=bracketed';
    }
    if ($('.nav-tabs .active').text() === 'Indented') {
        mode = '?mode=indented';
    }

    var link = '';
    if (item.url) {
        link += '<a href="' + item.url + '">' + item.item_name + '</a>';
    }
    else {
        link += item.item_name;
    }
    if (item.to_key) {
        link += '<a href="' + item.to_key + mode + '"><span class="keybase-player-tokey"></span></a>';
    }

    if (item.link_to_item_id) {
        link += ': ';
        if (item.link_to_url) {
            link += '<a href="' + item.link_to_url + '">' + item.link_to_item_name + '</a>';
        }
        else {
            link += item.link_to_item_name;
        }
        if (item.link_to_key) {
            link += '<a href="' + item.link_to_key + mode + '"><span class="keybase-player-tokey"></span></a>';
        }
    }
    return link;
};

var onIndentedKeyComplete = function() {
    var contentHeight = $('#keybase-indented').offset().top + $('#keybase-indented').height();
    if (contentHeight > $(window).height()-60) {
        $('body').css('height', contentHeight);
    }
};

var onBracketedKeyComplete = function() {
    var contentHeight = $('#keybase-bracketed').offset().top + $('#keybase-bracketed').height();
    if (contentHeight > $(window).height()-60) {
        $('body').css('height', contentHeight);
    }
};

var keybaseOnLoad = function(json) {
    showItems(json);
    if ($.QueryString.filter_id !== undefined) {
        $.fn.keybase.setActiveFilter($.QueryString.filter_id);
        $('.keybase-player-filter').css('background-color', '#ffcc00');
    }
};

var showItems = function(json) {
    var list = keybaseItemsDisplay(json.items);
    $('#items').html('<ul>' + list.join('') + '</ul>');

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
    })(window.location.search.substr(1).split('&'));
    
    $.QueryStringHREF = function(a) {
        if (a == "") return {};
        var b = {};
        for (var i = 0; i < a.length; ++i)
        {
            var p=a[i].split('=');
            if (p.length !== 2) continue;
            b[p[0]] = decodeURIComponent(p[1].replace(/\+/g, " "));
        }
        return b;
    };
    
    $.ObjToString = function(obj) {
        var arr = [];
        for (var p in obj) {
            if (obj.hasOwnProperty(p)) {
                arr.push(p + '=' + obj[p]);
            }
        }
        return arr.join('&');
    };
})(jQuery);
