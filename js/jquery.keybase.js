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


$(function() {
    /*
     * File upload for Bootstrap 3
     */
    $(document).on('change', '.btn-file :file', function() {
    var input = $(this),
        numFiles = input.get(0).files ? input.get(0).files.length : 1,
        label = input.val().replace(/\\/g, '/').replace(/.*\//, '');

        input.trigger('fileselect', [numFiles, label]);
    });


    $('.btn-file :file').on('fileselect', function(event, numFiles, label) {
        $('#selected-file').html(label);
    });

    
    /*
     * Autocomplete on search box
     */
    $('#searchbox').autocomplete({
        source: function( request, response ) {
            $.ajax({
                url: wsUrl + '/ws/autocomplete_item_name',
                data: {
                    term: request.term
                },
                success: function( data ) {
                    response( data );
                }
            });
        },
        minLength: 2
    });
    
    
    /*
     * Tabs for key and project pages
     */
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
     * KEY
     */
    if (location.href.indexOf('keys/show/') > -1) {
        $.fn.keybaseProject.defaults.projectIconBaseUrl = base_url + "images/projecticons/";
        $.fn.keybaseProject.defaults.baseUrl = 'http://data.rbg.vic.gov.au/dev/keybase-ws/ws/project_get/';
        
        var key = new Key();
        
        key.checkFilter();
        
        $('[href=#player]').on('show.bs.tab', function() {
            if ($('.keybase-player-window').length === 0) {
                key.interactiveKey();
            }
            else {
                $.fn.keybase.defaults.resizePlayerWindow();
            }
            key.windowHeight('.keybase-player-window');
        });
        
        $('[href=#bracketed]').on('show.bs.tab', function() {
            if ($('.keybase-bracketed-key').length === 0) {
                key.bracketedKey();
            }
        });
        
        $('[href=#indented]').on('show.bs.tab', function() {
            if ($('.keybase-indented-key').length === 0) {
                key.indentedKey();
            }
        });
    }
    
    /*
     * PROJECT
     */
    if (location.href.substr(base_url.length, 8) === 'projects') {
        var proj = new Project();
        if ($.isNumeric(proj.project)) {
            if ($('[name=keybase-user-id]').length > 0) {
                proj.keybase_user_id = $('[name=keybase-user-id]').val();
                var url = 'http://data.rbg.vic.gov.au/dev/keybase-ws/ws/project_user_get/' + proj.project;
                $.ajax({
                    url: url,
                    success: function(data) {
                        proj.project_users = [];
                        proj.project_managers = [];
                        $.each(data, function(index, item) {
                            proj.project_users.push(item.user_id);
                            if (item.role === 'Manager') {
                                proj.project_managers.push(item.user_id);
                            }
                        });
                    },
                    complete: function() {
                        proj.getProjectKeys();
                    }
                });
            }
            else {
                proj.getProjectKeys();
            }

            $('#tree li:gt(0)>span').addClass('keybase-dynatree-key');

            $('[data-toggle=tab]:lt(2)').on('shown.bs.tab', function() {
                var target = $(this).attr('href') + ' .left-pane';
                $('#keys-control-panel').appendTo(target);
            });

            $('a[href="#"]').parents('li.key').css('display', 'none');
            $('a[href!="#"]').parents('li.key').css('display', 'list-item');

            $('#find-key').on('change', function(event) {
                proj.findKey();
            });

            $('#find-key~span>button').on('click', function(event) {
                proj.findKey();
            });

            $('[name=filter-id]').on('change', function(e) {
                if ($(this).val().length > 0) {
                    location.href = site_url + '/projects/show/' + proj.project + '?filter_id=' + $(this).val();
                }
                else {
                    location.href = site_url + '/projects/show/' + proj.project;
                }
            });

            $('.is-project-filter').on('click', function(e) {
                var filter_id = $(this).parents('tr').eq(0).attr('data-keybase-filter-id');
                var postData = {
                    is_project_filter: ($(this).prop('checked')) ? true : null
                };
                $.ajax({
                    url: wsUrl + '/ws/set_project_filter/' + filter_id,
                    method: 'POST',
                    data: postData,
                    contentType: 'application/x-www-form-urlencoded; charset=utf-8'
                });
            });
        }
    }
    
    /*
     * FILTER
     */
    if (location.href.substr(base_url.length, 7) === 'filters') {
        var filter = new Filter();

        $('#filter').attr('size', $('#filter option').length);

        var uri = href.substr(href.indexOf('filters')).split('/');
        if (uri.length > 2 && uri[2].length > 0) {
            $('[data-toggle=tab]:eq(0)').tab('show');
            $('li#view').css('display', 'block');
            filter.filterid = uri[2];

            $('[href$=' + filter.filterid + ']').css({'font-weight': 'bold'}).prev('i').removeClass('fa-check-square').addClass('fa-check-square-o');

            filter.getStuff();
        }
        else {

            $('[data-toggle=tab]:eq(1)').tab('show');
            $('li#view').css('display', 'none');

            $('input[name="update"]').val('Create filter');
            $('input#export, input#delete').css('display', 'none');
        }

        $('#project-filters').on('click', '.fa-plus-square-o', function(e) {
            $(e.target).removeClass('fa-plus-square-o').addClass('fa-minus-square-o').nextAll('ul').show();
        });

        $('#project-filters').on('click', '.fa-minus-square-o', function(e) {
            $(e.target).removeClass('fa-minus-square-o').addClass('fa-plus-square-o').nextAll('ul').hide();
        });

        $('#globalfilter-keys').on('click', '.toggle', function(e) {
            if ($(this).html() == '[+]') {
                $(this).parents('div.key').find('ul').css('display', 'block');
                $(this).html('[-]');
            }
            else {
                $(this).parents('div.key').find('ul').css('display', 'none');
                $(this).html('[+]');
            }
        });

        $('input#import').click(function(e) {
            e.preventDefault();
            $(this).colorbox({
                opacity: 0.40, 
                transition: 'elastic', 
                speed: 100,
                href: site_url + '/key/importglobalfilter',
                innerWidth: 860,
                innerHeight: 580,
                close: 'close',
                onLoad: function() {
                    $('#cboxClose').hide();
                }
            });

        });

        $('select#filter').focus();
    }
    
});


var Key = function() {
    var that = this;
    
    this.keyId = location.href.substr(location.href.lastIndexOf('/') + 1);
    if (this.keyId.indexOf('?') > -1) {
        this.keyId = this.keyId.substring(0, this.keyId.indexOf('?'));
    }
    
    /**
     * @function checkFilter
     * @description see if an external filter has been set in the query string and, 
     *     if so, retrieve the filter, before running the KeyBase plugin.
     * @returns {undefined}
     */
    this.checkFilter = function() {
        if ($.QueryString.filter_id !== undefined) {
            $.ajax({
                url: site_url + '/ws/filterItems',
                data: {
                    "key_id": that.keyId,
                    "filter_id": $.QueryString.filter_id
                },
                success: function(data) {
                    that.doTheKeyThing(that.keyId, data);
                }
            });
        }
        else {
            that.doTheKeyThing(that.keyId);
        }
    };
    
    this.doTheKeyThing = function(keyId, filter) {
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
            key: keyId,
            title: false,
            source: false,
            reset: true,
            filter_items: filter,
            onLoad: that.keybaseOnLoad,
            onIndentedKeyComplete: that.onIndentedKeyComplete,
            onFilterWindowOpen: that.onFilterWindowOpen,
            renderItemLink: that.renderItemLink,
            onBracketedKeyComplete: that.onBracketedKeyComplete
        });

        var contentHeight = $('#keybase-player').offset().top + 600;
        if (contentHeight > $(window).height()-60) {
            $('body').css('height', contentHeight);
        }

        that.windowHeightTabClick('#player');
        that.windowHeightTabClick('#bracketed');
        that.windowHeightTabClick('#indented');
        that.windowHeightTabClick('#about');
        that.windowHeightTabClick('#items');
        that.windowHeightTabClick('#changes');
        that.windowHeightTabClick('#metadata');
    };

    this.windowHeightTabClick = function(element) {
        $('a[href=' + element + ']').click(function() {
            that.windowHeight(element);
        });
    };

    this.windowHeight = function(element) {
        var contentHeight = $(element).offset().top + $(element).height();
        if (contentHeight > $(window).height()-60) {
            $('body').css('height', contentHeight);
        }
        else {
            $('body').css('height', $(window).height()-80);
        }
    };

    this.onFilterWindowOpen = function() {
        if ($('.external-filters').length === 0) {
            $.ajax({
                url: site_url + '/ws/projectFilters',
                data: {
                    key_id: that.keyId
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
                                        key_id: keyId,
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
                                        that.sortOptions('includedItems');
                                        that.sortOptions('excludedItems');
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
                                that.sortOptions('includedItems');
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
                            that.sortOptions('includedItems');
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
                        var newHref = that.updateHref(href);
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

    this.updateHref = function(href) {
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

    this.sortOptions = function(select) {
        var options = $('[name=' + select + ']>option');
        options.sort(function(a,b) {
            if (a.text > b.text) return 1;
            else if (a.text < b.text) return -1;
            else return 0;
        });
        $('[name=' + select + ']').html(options);
    };


    this.keybaseItemsDisplay = function(items) {
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
            entity += that.renderItemLink(item);
            entity += '</li>';
            list.push(entity);
        });
        return list;
    };

    this.interactiveKey = function () {
        $.fn.keybase('player', {
            bracketedKeyDiv: '#keybase-player',
            renderItemLink: that.enderItemLink
        });
    };

    this.bracketedKey = function () {
        $.fn.keybase('bracketedKey', {
            bracketedKeyDiv: '#keybase-bracketed',
            renderItemLink: that.renderItemLink,
            onBracketedKeyComplete: that.onBracketedKeyComplete
        });
    };

    this.indentedKey = function () {
        $.fn.keybase('indentedKey', {
            indentedKeyDiv: '#keybase-indented',
            renderItemLink: that.renderItemLink,
            onIndentedKeyComplete: that.onIndentedKeyComplete
        });
    };

    this.renderItemLink = function(item) {
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

    this.onIndentedKeyComplete = function() {
        var contentHeight = $('#keybase-indented').offset().top + $('#keybase-indented').height();
        if (contentHeight > $(window).height()-60) {
            $('body').css('height', contentHeight);
        }
    };

    this.onBracketedKeyComplete = function() {
        var contentHeight = $('#keybase-bracketed').offset().top + $('#keybase-bracketed').height();
        if (contentHeight > $(window).height()-60) {
            $('body').css('height', contentHeight);
        }
    };

    this.keybaseOnLoad = function(json) {
        that.showItems(json);
        if ($.QueryString.filter_id !== undefined) {
            $.fn.keybase.setActiveFilter($.QueryString.filter_id);
            $('.keybase-player-filter').css('background-color', '#ffcc00');
        }
    };

    this.showItems = function(json) {
        var list = this.keybaseItemsDisplay(json.items);
        $('#items').html('<ul>' + list.join('') + '</ul>');

    };    
};

var Project = function() {
    var that = this;
    
    this.keysInFilter = [];
    this.project = href.substr(href.indexOf('/projects/show')+15);
    if (this.project.indexOf('?') > -1) {
        this.project = this.project.substr(0, this.project.indexOf('?'));
    }
    this.keybase_user_id;
    this.project_users;
    this.project_managers;
    this.auto_complete_list;
    
    this.getProjectKeys = function() {
        if ($.QueryString.filter_id !== undefined && $.QueryString.filter_id.length > 0) {
            $.ajax({
                'url': wsUrl + '/ws/filter_keys_get/' + $.QueryString.filter_id,
                'success': function(data) {
                    that.keysInFilter = JSPath.apply('.keyID', data.keys);
                },
                complete: function() {
                    that.keysHierarchical(that.keysInFilter);
                    that.keysAlphabetical(that.keysInFilter);
                    $('[name=filter-id]').val($.QueryString.filter_id).after('<span class="input-group-addon" id="fdelete"><i class="fa fa-trash"></i></span>');
                    $('#apply-filter').css('background-color', '#ff9900');
                    $('#fdelete').click(function(e) {
                        location.href = site_url + '/projects/show/' + that.project;
                    });
                }
            });
        }
        else {
            that.keysHierarchical(that.keysInFilter);
            that.keysAlphabetical(that.keysInFilter);
        }
    };

    this.keysHierarchical = function(filter) {
        $.fn.keybaseProject('keysHierarchical', {
            project: that.project,
            filter: filter,
            keyLinkClick: that.keyLinkClick
        });
        that.contextMenu();
    };

    this.keysAlphabetical = function(filter) {
        $.fn.keybaseProject('keysAlphabetical', {
            project: that.project,
            filter: filter,
            keyLinkClick: that.keyLinkClick,
            alphabeticalListDisplay: that.projectListDisplay,
            onComplete: that.onProjectGetComplete
        });
    };
    
    this.keyLinkClick = function(keyID) {
        if ($.QueryString.filter_id === undefined) {
            location.href = site_url + '/keys/show/' + keyID;
        }
        else {
            location.href = site_url + '/keys/show/' + keyID + '?filter_id=' + $.QueryString.filter_id;
        }
    };

    this.projectListDisplay = function() {
        if ($.QueryString.filter_id !== undefined) {
            var qstring = '?filter_id=' + $.QueryString.filter_id;
        }
        else {
            var qstring = '';
        }
        var list = [];
        var data = $.fn.keybaseProject.getters.projectKeyList()[0].children;
        $.each(data, function(index, item) {
            var entity;
            entity = "<li><a href=\"" + 
                site_url + "/keys/show/" + item.href.substr(1) + qstring + "\">" + item.title + "</a>";
            if (that.keybase_user_id !== undefined && that.keybase_user_id) {
                if (that.project_users.indexOf(that.keybase_user_id) > -1) {
                    entity += "&nbsp;<a class=\"edit-key\" href=\"" + site_url + "/keys/edit/" + item.href.substr(1) + "\"><i class=\"fa fa-edit\"></i></a>";
                }
                if (that.project_managers.indexOf(that.keybase_user_id) > -1 || item.created_by.id === that.keybase_user_id) {
                    entity += "&nbsp;<a class=\"delete-key\" href=\"" + site_url + "/keys/delete/" + item.href.substr(1) + "\"><i class=\"fa fa-trash\"></i></a>";
                }
            }
            entity += "</li>";
            list.push(entity);
        });
        $('#list').html('<ul>' + list.join('') + '</ul>');
        that.deleteKey();
    };

    this.onProjectGetComplete = function(json) {
        var data = JSPath.apply('.keys', json);
        that.auto_complete_list = [];
        $.each(data, function(index, item) {
            that.auto_complete_list.push(item.taxonomic_scope.name);
        });
        that.auto_complete_list.sort();
        that.searchAutoComplete();
    };

    this.searchAutoComplete = function() {
        $( "#find-key" ).autocomplete({
            source: this.autoCompleteSource,
            minLength: 2
        });
    };

    /**
     * autoCompleteSource
     * 
     * @description Custom function to create the source for the search auto-complete in order for it to only return values
     * for which the first letters match the searc term.
     * @param {type} request
     * @param {type} response
     * @returns {undefined}
     */
    this.autoCompleteSource = function (request, response) {
        var items = [];
        $.each(that.auto_complete_list, function(index, item) {
            if (item.substr(0, request.term.length).toLowerCase() === request.term.toLowerCase()) {
                items.push(item);
            }
        });
        response(items);
    };

    this.findKey = function() {
        var string = $('#find-key').val();
        var target = '#' + $('#keys-control-panel').parents('#keys_hierarchy, #keys_alphabetical').eq(0).attr('id') + ' a';
        if (string.length > 0) {
            $(target + ':contains("' + string + '")').first().focus();
        }
        return false;
    };

    this.contextMenu = function() {
        /*
         * At the moment, the items in the context menu can only be an object, not a
         * function, so only project managers can delete keys. Hopefully better luck 
         * when I've updated the context menu plugin.
         */
        var items = this.contextMenuItems();
        $('#tree').contextMenu({
            selector: 'a', 
            items: items
        });
    };

    this.contextMenuItems = function() {
        var items = {
            "player": {
                name: "Key player",
                callback: function(key, options) {
                    var hash = $(this).attr('href').substr(1);
                    window.location.href = site_url + '/keys/show/' + hash;
                }
            },
            "bracketed": {
                name: "Bracketed key",
                callback: function(key, options) {
                    var hash = $(this).attr('href').substr(1);
                    window.location.href = site_url + '/keys/show/' + hash + '?mode=bracketed';
                }
            },
            "indented": {
                name: "Indented key",
                callback: function(key, options) {
                    var hash = $(this).attr('href').substr(1);
                    window.location.href = site_url + '/keys/show/' + hash + '?mode=indented';
                }
            },
            "about": {
                name: "About",
                callback: function(key, options) {
                    var hash = $(this).attr('href').substr(1);
                    window.location.href = site_url + '/keys/show/' + hash + '?tab=3';
                }
            },
            "sep1": "---------",
            "edit": {
                name: "Edit", 
                icon: "edit", 
                callback: function(key, options) {
                    var hash = $(this).attr('href').substr(1);
                    href = site_url + '/keys/edit/' + hash;
                    window.location.href = href;
                }
            },
            "delete": {
                name: "Delete", 
                icon: "delete",
                callback: function(key, options) {
                    var hash = $(this).attr('href').substr(1);
                    href = site_url + '/keys/delete/' + hash + '/cbox';
                    $.colorbox({
                        href: href,
                        opacity: 0.40, 
                        transition: 'elastic', 
                        speed: 100,
                        innerWidth: 400,
                        innerHeight: 165,
                        close: 'close',
                        onLoad: function() {
                            $('#cboxClose').hide();
                        },
                        onComplete: function() {
                            $('#colorbox').addClass('edit-project');
                            $('#colorbox button.cancel').click(function(e) {
                                e.preventDefault();
                                $.colorbox.close();
                            });
                            $('input[type="submit"], button').button();
                            $('input.ok').focus();
                        }
                    });
                }
            },
        };

        var menuItems = {};
        menuItems.player = items.player;
        menuItems.bracketed = items.bracketed;
        menuItems.indented = items.indented;
        menuItems.about = items.about;
        if (that.keybase_user_id !== undefined && that.keybase_user_id) {
            menuItems.sep1 = items.sep1;
            if (that.project_users.indexOf(that.keybase_user_id) > -1) {
                menuItems.edit = items.edit;
            }
            if (that.project_managers.indexOf(that.keybase_user_id) > -1) {
                menuItems.delete = items.delete;
            }
        }
        return menuItems;
    };

    this.deleteKey = function() {
        /*
         * This is the only usage of Colorbox left in KeyBase, I
         * think. Should see if it can be replaced by a Bootstrap 
         * modal
         */
        $("a.delete-key").on('click', function() {
            var cbox_href = $(this).attr('href');
            $(this).attr('href', cbox_href + '/cbox');
            $(this).colorbox({
                opacity: 0.40, 
                transition: 'elastic', 
                speed: 100,
                innerWidth: 400,
                innerHeight: 165,
                close: 'close',
                onLoad: function() {
                    $('#cboxClose').hide();
                },
                onComplete: function() {
                    $('#colorbox').addClass('edit-project');
                    $('#colorbox button.cancel').click(function(e) {
                        e.preventDefault();
                        $.colorbox.close();
                    });
                    $('input[type="submit"], button').button();
                    $('input.ok').focus();
                }
            });
            return FALSE;
        });
    };
};

var Filter = function() {
    var that = this;
    
    this.filterid;
    this.json;
    this.filterHtml;
    
    this.getStuff = function() {
        $.getJSON(wsUrl + '/ws/filter_meta_get/' + that.filterid, function(data) {
            $('input#filterid').val(data.FilterID);
            $('input#filtername').val(data.FilterName);
        });

        $('#globalfilter-keys').html('<i class="fa fa-spinner fa-spin fa-lg"></i>');

        $.ajax({
            url: wsUrl +  "/ws/filter_keys_get/" + that.filterid,
            success: function(data) {
                that.json = data;
                that.filter();
                $('div#globalfilter-keys').html(that.filterHtml);
                $('#globalfilter-keys').on('click', '.fa-folder-open-o, .fa-minus-square-o', function(e) {
                    $(e.target).removeClass('fa-minus-square-o').addClass('fa-plus-square-o');
                    $(e.target).parents('li').eq(0).children('.fa-folder-open-o').removeClass('fa-folder-open-o').addClass('fa-folder-o');
                    $(e.target).parents('li').eq(0).children('ul').hide();
                });
                $('#globalfilter-keys').on('click', '.fa-folder-o, .fa-plus-square-o', function(e) {
                    $(e.target).removeClass('fa-plus-square-o').addClass('fa-minus-square-o');
                    $(e.target).parents('li').eq(0).children('.fa-folder-o').removeClass('fa-folder-o').addClass('fa-folder-open-o');
                    $(e.target).parents('li').eq(0).children('ul').show();
                });

                $('.keybase-filter-key').prepend('<span class="keybase-key-icon dynatree-icon"></span>');
                $('.keybase-filter-project, .keybase-filter-first').prepend('<i class="fa fa-folder-open-o fa-fw"></i>');

                $('.keybase-filter li').each(function() {
                    $(this).prepend('<i class="fa fa-minus-square-o fa-fw"></i>');
                });


                $('.keybase-filter-key-num-items').addClass('expand');
                $('#globalfilter-keys').on('click', '.keybase-filter-key-num-items.expand', function(e) {
                    $(e.target).removeClass('expand').addClass('collapse').children('.fa').eq(0).removeClass('fa-caret-right').addClass('fa-caret-down');
                    $(e.target).parents('li').eq(0).find('.keybase-filter-key-items').eq(0).css('display', 'block');
                });
                $('#globalfilter-keys').on('click', '.keybase-filter-key-num-items.collapse', function(e) {
                    $(e.target).removeClass('collapse').addClass('expand').children('.fa').eq(0).removeClass('fa-caret-down').addClass('fa-caret-right');
                    $(e.target).parents('li').eq(0).find('.keybase-filter-key-items').eq(0).hide();
                });

                $('#globalfilter-keys').prepend("<div id=\"keybase-filter-item-toggle\"><div class=\"btn-group\" data-toggle=\"buttons\"> \
                    <label class=\"btn btn-default\"> \
                      <input type=\"radio\" name=\"filter-item-toggle\" id=\"filter-item-expand\" checked>Expand items \
                    </label> \
                    <label class=\"btn btn-default active\"> \
                      <input type=\"radio\" name=\"filter-item-toggle\" id=\"filter-item-collapse\">Collapse items \
                    </label> \
                  </div></div>");

                $('#globalfilter-keys').on('click', '.btn', function(e) {
                    if (!$(e.target).hasClass('active')) {
                        if ($(e.target).children('input').eq(0).attr('id') === 'filter-item-expand') {
                            $('.keybase-filter-key-num-items').removeClass('expand').removeClass('collapse').addClass('collapse').each(function() {
                                $(this).children('.fa').removeClass('fa-caret-right').addClass('fa-caret-down');
                            });
                            $('.keybase-filter-key-items').css('display', 'block');
                        }
                        else {
                            $('.keybase-filter-key-num-items').removeClass('expand').removeClass('collapse').addClass('expand').each(function() {
                                $(this).children('.fa').removeClass('fa-caret-down').addClass('fa-caret-right');
                            });
                            $('.keybase-filter-key-items').hide();
                        }
                    }
                });
            }
        });

        $.getJSON(wsUrl + '/ws/filter_projects_get/' + that.filterid, function(data) {
            $('select#projects').val(data);
        });

        $('textarea#taxa').load(wsUrl + '/ws/filter_keys_get/' + that.filterid);
    }

    this.filter = function() {
        that.filterHtml = '<ul class="keybase-filter">';
        that.filterHtml += '<li class="keybase-filter-first">';
        that.filterHtml += '<span>' + that.json.filterName + ' [ID: ' + that.json.filterID + ']</span>';
        $.each(that.json.projects, function(index, project ) {
            that.filterHtml += '<ul>';
            that.filterHtml += '<li class="keybase-filter-project">';
            that.filterHtml += '<span><a href="' + site_url + '/projects/show/' + project.projectID + '?filter_id=' + that.json.filterID + '">' + project.projectName + '</a></span>';
            var projectKeys = JSPath.apply('.{.projectID===$projectID}', that.json.keys, {projectID: project.projectID});
            var itemIDs = JSPath.apply('.items', projectKeys);

            var rootKey = JSPath.apply('.{.taxonomicScopeID==="' + project.taxonomicScopeID + '"}', projectKeys);
            that.filterKey(rootKey[0]);

            var orphans = [];
            $.each(projectKeys, function(index,key) {
                if (itemIDs.indexOf(key.taxonomicScopeID) === -1 && key.taxonomicScopeID !== project.taxonomicScopeID) {
                    orphans.push(key);
                }
            });
            orphans.sort(function(a, b) {
                if (a.keyName < b.keyName) {
                    return -1;
                }
                if (a.keyName > b.keyName) {
                    return 1;
                }
                return 0;
            });

            $.each(orphans, function (index, key) {
                that.filterKey(key);
            });

            that.filterHtml += '</li> <!-- /.keybase-filter-project -->';
            that.filterHtml += '</ul>';
        });
        that.filterHtml += '</li> <!-- /.keybasefilter-first -->';
        that.filterHtml += '</ul> <!-- /.keybase-filter -->';
    }

    this.filterKey = function(key) {
        that.filterHtml += '<ul>';
        that.filterHtml += '<li class="keybase-filter-key">';
        that.filterHtml += '<span class="keybase-filter-key-name"><a href="' + site_url + '/keys/show/' + key.keyID + '?filter_id=' + that.json.filterID + '">' + key.keyName + '</a> <span class="keybase-filter-key-num-items">' + key.items.length + ' items <i class="fa fa-caret-right"></i></span></span>';

        var items = JSPath.apply('.{.itemID==$itemID}', that.json.items, {itemID: key.items});

        var itemNames = JSPath.apply('.itemName', items);

        itemNames = itemNames.join(', ').replace(/{/g, '(').replace(/}/g, ')');
        that.filterHtml += '<span class="keybase-filter-key-items">' + itemNames + '</span>';

        var itemIDs = [];
        $.each(items, function(index, item) {
            itemIDs.push(item.linkedItemID || item.itemID);
        });

        var keys = JSPath.apply('.{.taxonomicScopeID==$itemID}', that.json.keys, {itemID: itemIDs});
        keys.sort(function(a, b) {
            if (a.keyName < b.keyName) {
                return -1;
            }
            if (b.keyName < a.keyName) {
                return 1;
            }
            return 0;
        });

        $.each(keys, function (index, key) {
            that.filterKey(key);
        });

        that.filterHtml += '</li> <!-- /.keybase-filter-key -->';
        that.filterHtml += '</ul>';
    };
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
