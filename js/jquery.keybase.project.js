$(function(){
    var href = location.href;
    var base_url;
    var site_url = href.substr(0, href.indexOf('/key/project'));
    if (site_url.indexOf('index.php') > 0) {
        base_url = site_url.substr(0, site_url.indexOf('index.php'));
    }
    else {
        base_url = site_url + '/';
    }
    var project = href.substr(href.indexOf('/project/')+9);
    
    var tab = $.QueryString['tab'];
    if (!tab || tab > 3) {
        tab = 1;
    }
    
    $(function() {
        $( "#project_tabs" ).tabs({
            active: tab,
            heightStyle: "auto" 
        });
    });
    
    $("#tree").dynatree({
        initAjax: {
            url: site_url + "/ajax/projectkeys_hierarchy/" + project
        },
        //autoCollapse: true,
        onActivate: function(node) {
            if (node.data.href) {
                    window.location.href=node.data.href;
            }
        },
        onCreate: function(node) {
            node.expand(true);
        }
    });
    
    var url = site_url + "/ajax/projectkeys_alphabetical/" + project;
    $.getJSON(url, function(data) {
        var list = [];
        $.each(data, function(index, item) {
            var entity;
            entity = "<li class=\"list\"><span class=\"keybase-dynatree-key\"><span class=\"dynatree-icon\"></span><a href=\"" + site_url + "/key/nothophoenix/" + item.KeysID + "\">" + item.Name + "</a></span>";
            if (item.Edit == 1) {
                entity += "&nbsp;<a class=\"edit-key\" href=\"" + site_url + "/key/editkey/" + item.KeysID + "\"><img src=\"" + base_url + "css/images/icon_edit.png\" width=\"10\" height=\"12\" alt=\"\"/>" + "</a>";
            }
            if (item.Delete == 1) {
                entity += "&nbsp;<a class=\"delete-key\" href=\"" + site_url + "/key/deletekey/" + item.KeysID + "\"><img src=\"" + base_url + "css/images/icon_delete.png\" width=\"10\" height=\"12\" alt=\"\"/>" + "</a>";
            }
            entity += "</li>";
            list.push(entity);
        });
        $('#list').html('<ul>' + list.join('') + '</ul>');

        $("a.edit-key").on('click', function () {
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
        });
        
        $("a.delete-key").on('click', function() {
            var cbox_href = $(this).attr('href');
            $(this).attr('href', cbox_href + '/cbox');
            $(this).colorbox({
                opacity: 0.40, 
                transition: 'elastic', 
                speed: 100,
                innerWidth: 400,
                innerHeight: 150,
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

    $(".add-key a").click(function () {
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
    });
    
    tabSize();
    $(window).resize(function() {
        tabSize();
    });
});

function tabSize() {
    var tabheight = window.innerHeight-345;
    if (tabheight > 300) {
        $('.content-right').css({'height': tabheight + 'px', 'overflow': 'auto'});
    }
    else {
        $('.content-right').css({'height': '300px', 'overflow': 'auto'});
    }
}



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

$(function(){
    $.extend({
        getValues: function(url) {
            var result = null;
            $.ajax({
                url: url,
                type: 'get',
                dataType: 'html',
                async: false,
                success: function(data) {
                    result = data;
                }
            });
           return result;
        }
    });    
});