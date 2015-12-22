var json;
var hierarchy;

$(function(){
    var project = href.substr(href.indexOf('/projects/show')+15);
    
    $.fn.keybaseProject.defaults.keyLinkClick = function(keyID) {
        location.href = site_url + '/keys/show/' + keyID;
    };
    
    $.fn.keybaseProject.defaults.projectIconBaseUrl = base_url + "images/projecticons/";
    
    $.fn.keybaseProject.defaults.baseUrl = site_url + '/ws/projects/';
    
    $('#tree').keybaseProject('keysHierarchical', {
        params: {
            project: project
        }
    });
    
    $('#tree li:gt(0)>span').addClass('keybase-dynatree-key');
    
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
                    href = site_url + '/keys/edit/' + hash + '/cbox';
                   $.colorbox({
                        href: href,
                        opacity: 0.40, 
                        transition: 'elastic', 
                        speed: 100,
                        innerWidth: 860,
                        innerHeight: "80%",
                        close: 'close',
                        onLoad: function() {
                            $('#cboxClose').hide();
                        },
                        onComplete: function() {
                            $('#colorbox').addClass('edit-project');
                            $('#colorbox input[name="cancel"]').click(function(e) {
                                e.preventDefault();
                                $.colorbox.close();
                            });
                            $('input[type="submit"]').button();
                        }
                    });
                
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
    
    var url = site_url + "/ajax/projectkeys_alphabetical/" + project;
    $.getJSON(url, function(data) {
        var list = [];
        $.each(data, function(index, item) {
            var entity;
            entity = "<li class=\"list\"><span class=\"keybase-dynatree-key\"><span class=\"dynatree-icon\"></span><a href=\"" + site_url + "/keys/show/" + item.KeysID + "\">" + item.Name + "</a></span>";
            if (item.Edit == 1) {
                entity += "&nbsp;<a class=\"edit-key\" href=\"" + site_url + "/keys/edit/" + item.KeysID + "\"><img src=\"" + base_url + "css/images/icon_edit.png\" width=\"10\" height=\"12\" alt=\"\"/>" + "</a>";
            }
            if (item.Delete == 1) {
                entity += "&nbsp;<a class=\"delete-key\" href=\"" + site_url + "/keys/delete/" + item.KeysID + "\"><img src=\"" + base_url + "css/images/icon_delete.png\" width=\"10\" height=\"12\" alt=\"\"/>" + "</a>";
            }
            entity += "</li>";
            list.push(entity);
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
    
    $('a[href="#"]').parents('li.key').css('display', 'none');
    $('a[href!="#"]').parents('li.key').css('display', 'list-item');
    
    $('form[name="find_in_tree"]').submit(function(event) {
        var string = $('#findkey_h').val();
        $('a.dynatree-title:contains("' + string + '")').first().focus();
        return false;
    });
    
    $('form[name="find_in_list"]').submit(function(event) {
        var string = $('#findkey_a').val();
        $('.list a:contains("' + string + '")').first().focus();
        return false;
    });
    
    $( "#findkey_h, #findkey_a" ).autocomplete({
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

    /*$(".add-key a").click(function () {
        var cbox_href = $(this).attr('href');
        $(this).attr('href', cbox_href + '/cbox');
        $(this).colorbox({
            opacity: 0.40, 
            transition: 'elastic', 
            speed: 100,
            innerWidth: 860,
            innerHeight: "80%",
            close: 'close',
            onLoad: function() {
                $('#cboxClose').hide();
            },
            onComplete: function() {
                $('#colorbox').addClass('edit-project');
                $('#colorbox input[name="cancel"]').click(function(e) {
                    e.preventDefault();
                    $.colorbox.close();
                });
                $('input[type="submit"]').button();
            }
        });
    });*/
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