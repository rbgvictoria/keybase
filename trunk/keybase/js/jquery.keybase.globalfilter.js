var href = location.href;
var base_url;
var site_url = href.substr(0, href.indexOf('/key/filter'));
if (site_url.indexOf('index.php') > 0) {
    base_url = site_url.substr(0, site_url.indexOf('/index.php'));
}
else {
    base_url = site_url + '/';
}

var uri = href.substr(href.indexOf('key/filter')).split('/');
var filterid;

$(function() {
    $('a#import').button();
    
    $('#globalfilter-tabs').tabs();
    
    $('select#filter').val(uri[2]);
    if (uri.length > 2) {
        filterid = uri[2];
        $('#globalfilter-tabs').tabs({active: 0});
        getStuff();
        $('li#view').css('display', 'inline');
        $('input#import').css('display', 'none');
    }
    else {
        $('#globalfilter-tabs').tabs({active: 1});
        $('input[name="update"]').val('Create filter');
        $('input#export, input#delete').css('display', 'none');
        $('li#view').css('display', 'none');
        $('li#manage').css('margin-left', '518px');
    }
    
    $('select#filter').change(function(e) {
        filterid = $(this).val();
        
        if (filterid != 0 ) {
            $('#globalfilter-tabs').tabs({active: 0});
            getStuff();
            $('input[name="update"]').val('Update filter');
            $('li#view').css('display', 'list-item');
            $('li#manage').css('margin-left', '0px');
            $('input#export, input#delete').css('display', 'inline');
            $('input#import').css('display', 'none');
        }
        else {
            $('#globalfilter-tabs').tabs({active: 1});
            $('textarea#taxa').html('');
            $('input#filterid').val(null);
            $('input#filtername').val(null);
            $('select#projects').val(null);
            $('input[name="update"]').val('Create filter');
            $('input#export, input#delete').css('display', 'none');
            $('input#import').css({'display': 'inline'});
            $('li#view').css('display', 'none');
            $('li#manage').css('margin-left', '518px');
        }
        
        $('select#filter').focus();
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
    
    tabSize();
    $(window).resize(function() {
        tabSize();
    });
    
    
    $('select#filter').focus();
});

function getStuff() {
    $.getJSON(site_url + '/ajax/getGlobalFilterMetadata/' + filterid, function(data) {
        $('input#filterid').val(data.FilterID);
        $('input#filtername').val(data.FilterName);
    });
    
    $('div#globalfilter-keys').dynatree({
        initAjax: {
            url: site_url + "/ajax/getGlobalfilterKeys/" + filterid
        },
        //autoCollapse: true,
        onActivate: function(node) {
            if (node.data.href) {
                    window.location.href=node.data.href;
            }
        },
        onCreate: function(node) {
            if (node.data.addClass !== "keybase-dynatree-items-folder") {
                node.expand(true);
            }
        }
    });
    $('div#globalfilter-keys').dynatree("getTree").reload();

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
