var json;

var href = location.href;
var base_url;
var site_url = href.substr(0, href.indexOf('/filters'));
if (site_url.indexOf('index.php') > 0) {
    base_url = site_url.substr(0, site_url.indexOf('/index.php'));
}
else {
    base_url = site_url + '/';
}

var uri = href.substr(href.indexOf('filters')).split('/');
var filterid;

$(function() {
    var filterHtml;
    
    $('#filter').attr('size', $('#filter option').length);
    
    if (uri.length > 2 && uri[2].length > 0) {
        $('[data-toggle=tab]:eq(0)').tab('show');
        $('li#view').css('display', 'block');
        filterid = uri[2];
        
        $('[href$=' + filterid + ']').css({'font-weight': 'bold'}).prev('i').removeClass('fa-check-square').addClass('fa-check-square-o');
        
        getStuff();
        //$('input#import').css('display', 'none');
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
    
    /*tabSize();
    $(window).resize(function() {
        tabSize();
    });*/
    
    
    $('select#filter').focus();
    
    
    
});

function getStuff() {
    $.getJSON(site_url + '/ajax/getGlobalFilterMetadata/' + filterid, function(data) {
        $('input#filterid').val(data.FilterID);
        $('input#filtername').val(data.FilterName);
    });
    
    $('#globalfilter-keys').html('<i class="fa fa-spinner fa-spin fa-lg"></i>');
    
    $.ajax({
        url: site_url +  "/ajax/getGlobalfilterKeys/" + filterid,
        success: function(data) {
            json = data;
            //var taxonomicScopeIDs = JSPath.apply('.taxonomicScopeID', json.keys);
            filter();
            $('div#globalfilter-keys').html(filterHtml);
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
    
    $.getJSON(site_url + '/ajax/getGlobalFilterProjects/' + filterid, function(data) {
        $('select#projects').val(data);
    });

    $('textarea#taxa').load(site_url + '/ajax/getGlobalFilterTaxa/' + filterid);
}

function tabSize() {
    var tabheight = window.innerHeight-312;
    if (tabheight > 400) {
        $('#globalfilter-keys').css({'height': tabheight + 'px', 'overflow': 'visible'});
    }
    else {
        $('#globalfilter-keys').css({'height': '400px', 'overflow': 'visible'});
    }
}

function filter() {
    filterHtml = '<ul class="keybase-filter">';
    filterHtml += '<li class="keybase-filter-first">';
    filterHtml += '<span>' + json.filterName + ' [ID: ' + json.filterID + ']</span>';
    $.each(json.projects, function(index, project ) {
        filterHtml += '<ul>';
        filterHtml += '<li class="keybase-filter-project">';
        filterHtml += '<span><a href="' + site_url + '/projects/show/' + project.projectID + '?filter_id=' + json.filterID + '">' + project.projectName + '</a></span>';
        var projectKeys = JSPath.apply('.{.projectID===$projectID}', json.keys, {projectID: project.projectID});
        var itemIDs = JSPath.apply('.items', projectKeys);
        
        var rootKey = JSPath.apply('.{.taxonomicScopeID==="' + project.taxonomicScopeID + '"}', projectKeys);
        filterKey(rootKey[0]);
        
        
        
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
            filterKey(key);
        });

        filterHtml += '</li> <!-- /.keybase-filter-project -->';
        filterHtml += '</ul>';
    });
    filterHtml += '</li> <!-- /.keybasefilter-first -->';
    filterHtml += '</ul> <!-- /.keybase-filter -->';
}

function filterKey(key) {
    filterHtml += '<ul>';
    filterHtml += '<li class="keybase-filter-key">';
    filterHtml += '<span class="keybase-filter-key-name"><a href="' + site_url + '/keys/show/' + key.keyID + '?filter_id=' + json.filterID + '">' + key.keyName + '</a> <span class="keybase-filter-key-num-items">' + key.items.length + ' items <i class="fa fa-caret-right"></i></span></span>';
    
    var items = JSPath.apply('.{.itemID==$itemID}', json.items, {itemID: key.items});
    
    var itemNames = JSPath.apply('.itemName', items);
    
    itemNames = itemNames.join(', ').replace(/{/g, '(').replace(/}/g, ')');
    filterHtml += '<span class="keybase-filter-key-items">' + itemNames + '</span>';
    
    var itemIDs = [];
    $.each(items, function(index, item) {
        itemIDs.push(item.linkedItemID || item.itemID);
    });
    
    var keys = JSPath.apply('.{.taxonomicScopeID==$itemID}', json.keys, {itemID: itemIDs});
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
        filterKey(key);
    });
    
    filterHtml += '</li> <!-- /.keybase-filter-key -->';
    filterHtml += '</ul>';
}