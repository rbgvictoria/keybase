var json;
var hierarchy;

$(function(){
    var project = href.substr(href.indexOf('/projects/show')+15);
    if (project.indexOf('?') > -1) {
        project = project.substr(0, project.indexOf('?'));
    }
    
    $.fn.keybaseProject.defaults.keyLinkClick = function(keyID) {
        location.href = site_url + '/keys/show/' + keyID;
    };
    
    $.fn.keybaseProject.defaults.projectIconBaseUrl = base_url + "images/projecticons/";
    
    $.fn.keybaseProject.defaults.baseUrl = site_url + '/ws/projects/';
    
    var keysAlphabetical = function(filter) {
        if (filter === undefined) {
            filter = [];
        }
        if ($.QueryString.filter_id !== undefined) {
            var qstring = '?filter_id=' + $.QueryString.filter_id;
        }
        else {
            var qstring = '';
        }
        var url = site_url + "/ajax/projectkeys_alphabetical/" + project;
        $.getJSON(url, function(data) {
            var list = [];
            $.each(data, function(index, item) {
                if (filter.length === 0 || filter.indexOf(item.KeysID) > -1) {
                    var entity;
                    entity = "<li class=\"list\"><span class=\"keybase-dynatree-key\"><span class=\"dynatree-icon\"></span><a href=\"" + site_url + "/keys/show/" + item.KeysID + qstring + "\">" + item.Name + "</a></span>";
                    if (item.Edit == 1) {
                        entity += "&nbsp;<a class=\"edit-key\" href=\"" + site_url + "/keys/edit/" + item.KeysID + "\"><img src=\"" + base_url + "css/images/icon_edit.png\" width=\"10\" height=\"12\" alt=\"\"/>" + "</a>";
                    }
                    if (item.Delete == 1) {
                        entity += "&nbsp;<a class=\"delete-key\" href=\"" + site_url + "/keys/delete/" + item.KeysID + "\"><img src=\"" + base_url + "css/images/icon_delete.png\" width=\"10\" height=\"12\" alt=\"\"/>" + "</a>";
                    }
                    entity += "</li>";
                    list.push(entity);
                }    
            });
            $('#list').html('<ul>' + list.join('') + '</ul>');

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
                        /*$('input.ok').click(function(e) {
                            $('form').submit();
                        });*/
                    }
                });

                return FALSE;

            });
        });
    }
    
    var keysHierarchical = function(filter) {
        $('#tree').keybaseProject('keysHierarchical', {
            params: {
                project: project
            },
            filter: filter,
            keyLinkClick: function(keyID) {
                if ($.QueryString.filter_id === undefined) {
                    location.href = site_url + '/keys/show/' + keyID;
                }
                else {
                    location.href = site_url + '/keys/show/' + keyID + '?filter_id=' + $.QueryString.filter_id;
                }
            }
        });
    };

    var keysInFilter = [];
    
    var findKey = function() {
        var string = $('#find-key').val();
        var target = '#' + $('#keys-control-panel').parents('#keys_hierarchy, #keys_alphabetical').eq(0).attr('id') + ' a';
        if (string.length > 0) {
            $(target + ':contains("' + string + '")').first().focus();
        }
        return false;
    };
    
    if ($.QueryString.filter_id !== undefined && $.QueryString.filter_id.length > 0) {
        $.ajax({
            'url': site_url + '/ajax/getGlobalFilterKeys/' + $.QueryString.filter_id,
            'success': function(data) {
                var fjson = data;
                keysInFilter = JSPath.apply('.keyID', fjson.keys);
                
                keysHierarchical(keysInFilter);
                keysAlphabetical(keysInFilter);
                
                $('[name=filter-id]').val($.QueryString.filter_id).after('<span class="input-group-addon" id="fdelete"><i class="fa fa-trash"></i></span>');
                $('#apply-filter').css('background-color', '#ff9900');
                $('#fdelete').click(function(e) {
                    location.href = site_url + '/projects/show/' + project;
                });
            }
        });
    }
    else {
        keysHierarchical(keysInFilter);
        keysAlphabetical(keysInFilter);
    }
    $('#tree li:gt(0)>span').addClass('keybase-dynatree-key');
    
    $('[data-toggle=tab]:lt(2)').on('shown.bs.tab', function() {
        var target = $(this).attr('href') + ' .left-pane';
        $('#keys-control-panel').appendTo(target);
        //$('#find-key').val('');
    });
    
    $('#tree').contextMenu({
        selector: 'a', 
        items: {
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
                            //$('input.ok').click(function(e) {
                            //    $('form').submit();
                            //});
                        }
                    });
                }
            },
        }
    });
    
        
    $('a[href="#"]').parents('li.key').css('display', 'none');
    $('a[href!="#"]').parents('li.key').css('display', 'list-item');
    
    $('#find-key').on('change', function(event) {
        findKey();
    });
    
    $('#find-key~span>button').on('click', function(event) {
        findKey();
    });

    
    $( "#find-key" ).autocomplete({
            source: site_url + "/autocomplete/findprojecttaxa/" + project,
            minLength: 2
    });
    
    $("#edit-project a").click(function () {
        var cbox_href = $(this).attr('href');
        $(this).attr('href', cbox_href + '/cbox');
        $(this).colorbox({
            opacity: 0.40, 
            transition: 'elastic', 
            speed: 100,
            innerWidth: 860,
            innerHeight: 580,
            close: 'close',
            onLoad: function() {
                $('#cboxClose').hide();
            },
            onComplete: function() {
                CKEDITOR.replace("description", {
                    toolbarGroups: [
                            { groups: ['undo'] },
                            { items:['Bold', 'Italic', 'RemoveFormat']},
                            { name: 'links' },
                            { name: 'document',	   groups: [ 'mode', 'document' ] }
                    ],
                    height: 230,
                    contentsCss: '<?=base_url();?>css/ckeditor_styles.css',
                    removePlugins: 'autogrow'
                });
        
                $('#colorbox').addClass('edit-project');
                $('#colorbox input[name="cancel"]').click(function(e) {
                    e.preventDefault();
                    $.colorbox.close();
                });
                $('input[type="submit"]').button();
            }
        });
    });
    
    $('[name=filter-id]').on('change', function(e) {
        if ($(this).val().length > 0) {
            location.href = site_url + '/projects/show/' + project + '?filter_id=' + $(this).val();
        }
        else {
            location.href = site_url + '/projects/show/' + project;
        }
    });
    
    $('.is-project-filter').on('click', function(e) {
        var postData = {
            filter_id: $(this).parents('tr').eq(0).attr('data-keybase-filter-id'),
            is_project_filter: ($(this).prop('checked')) ? true : null
        };
        console.log(postData);
        $.ajax({
            url: site_url + '/filters/setProjectFilter',
            method: 'POST',
            data: postData,
            success: function(data) {
                console.log(data);
            }
        });
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