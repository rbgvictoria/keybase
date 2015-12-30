/**
 * Created by NKlaze on 10/04/2015.
 */
var settings;

(function ( $ ) {
    var elem;

    $.fn.keybaseProject = function(action, options) {
        elem = $(this);

        settings = $.extend({
            baseUrl: "http://keybase.rbg.vic.gov.au/ws/projects",
            params: {
                project: 10,
                items: 'false'
            },
            defaultProjectIcon: "project_icon_default.png"
        }, $.fn.keybaseProject.defaults, options);


        /*
        * Project list
        **********************************************************************************/

        if (action === "projectList") {
            $.ajax({
                url: settings.baseUrl,
                jsonp: "callback",
                dataType: "jsonp",
                success:function(data){
                    projects = [];
                    $.each(data, function(index, item) {
                        var project = '<li>';
                        project += '<div><a href="/keybase/project/show/' + item.project_id + '" data-project_id="' + item.project_id + '">' + item.project_name + '</a></div>';

                        project += '</li>';
                        projects.push(project);
                    });

                    elem.html('<ul>' + projects.join('') + '</ul>');
                }
            });
        }


        /*
        * Project list for home page
        **********************************************************************************/

        if (action === "projectListHome") {
            $.ajax({
                url: settings.baseUrl,
                jsonp: "callback",
                dataType: "jsonp",
                success:function(data){
                    projects = [];
                    $.each(data, function(index, item) {
                        var project = '<div class="col-lg-4 col-sm-6">';
                        project += '<div class="project">';
                        project += '<a class="project-link" href="/keybase/project/show/' + item.project_id + '" data-project_id="' + item.project_id + '">';

                        var icon = item.project_icon ? item.project_icon : settings.defaultProjectIcon;
                        project += '<span class="project-button">';
                        project += '<img alt="Keybase project icon" src="' + settings.projectIconBaseUrl + icon + '" />';
                        project += '</span>';

                        project += '<span class="project-details">';
                        project += '<span class="project-name">' + item.project_name + '</span>';
                        project += '<span class="project-stats">' + item.number_of_keys + ' keys to ' + item.number_of_items + ' taxa</span>';
                        project += '</span>';

                        project += '</a>';
                        project += '</div>';
                        project += '</div>';
                        projects.push(project);
                    });

                    elem.html(projects.join(''));
                    $('.project-details').css('width', $('#project-box .project').width()-$('#project-box .project-button').width()-10 + "px");
                }
            });
        }


        if (action === "keysHierarchical" || action === "keysAlphabetical") {
            //

            if (!json) {

                params = $.extend({
                    'project': 10,
                    'items': 'false'
                }, settings.params);

                $.ajax({
                    url: settings.baseUrl,
                    data: params,
                    dataType: "jsonp",
                    success:function(data){
                        json = data;

                        $('<div>', {class: 'keybase-project-icon'})
                            .append('<img src="' + settings.projectIconBaseUrl + json[0].project_icon + '" alt="" />')
                            .appendTo('.keybase-project-metadata');

                        $('<h1>', {
                            class: "keybase-project-name",
                            html: json[0].project_name
                        }).appendTo('.keybase-project-metadata');

                        $('<div>', {
                            class: "keybase-project-stats",
                            html: "This project contains " + json[0].number_of_keys + " keys to " + json[0].number_of_items + " taxa."
                        }).appendTo('.keybase-project-metadata');

                        searchList();

                        if (action === "keysHierarchical") {
                            hierarchical();
                        }
                        else {
                            if (action === "keysAlphabetical") {
                                alphabeticalList();
                            }
                        }
                    }
                });

            }
            else {
                if (action === "keysHierarchical") {
                    hierarchical();
                }
                else {
                    if (action === "keysAlphabetical") {
                        alphabeticalList();
                    }
                }
            }
            return elem;
        }

        if (action === "keysAlphabetical") {
            //
        }

        if (action === "items") {
            //
        }

    };

    $.fn.keybaseProject.defaults = {
        projectIconBaseUrl: "/keybase/images/projecticons/",
        filter: []
    };

    $.fn.keybaseProject.defaults.keyLinkClick = function(keyID) {
        location.href = '/keybase/keys/show/' + keyID;
    };

    var hierarchical = function() {
        if (undefined === hierarchy || !hierarchy) {
            hierarchy = [];
            root = {};
            root.title = json[0].project_name;
            root.isFolder = true;
            root.expand = true;
            root.children = [];

            if (json[0].first_key.id !== null) {
                first_key = JSPath('.keys{.id==' + json[0].first_key.id + '}', json)[0];
                var first = $.extend({}, first_key);
                first.title = first_key.name;
                first.href = '#' + first_key.id;
                first.expand = true;
                delete first.id;
                delete first.name;
                delete first.parent_id;
                root.children.push(first);
                hierarchicalListNode(first_key.id, first);

                orphan_keys = JSPath('.keys{!.parent_id && .id!=' + first_key.id + '}', json);
            }
            else {
                orphan_keys = JSPath('.keys{!.parent_id}', json);
            }

            if (orphan_keys) {
                $.each(orphan_keys, function (index, item) {
                    var child = $.extend({}, item);
                    child.title = item.name;
                    child.href = '#' + item.id;
                    child.expand = true;
                    if (settings.filter.length > 0 && settings.filter.indexOf(item.id) === -1) {
                        child.addClass = 'collapse';
                    }
                    delete child.id;
                    delete child.name;
                    delete child.name;
                    delete child.parent_id;
                    root.children.push(child);
                    hierarchicalListNode(item.id, child);
                });
            }

            hierarchy.push(root);

            elem.dynatree({
                children: hierarchy,
                data: {mode: "all"},
                expand: true
            });

            elem.on('click', 'a.dynatree-title', function (e) {
                e.preventDefault();
                var key_id = $(this).attr('href').substr(1);
                settings.keyLinkClick(key_id);
            });
        }
    };

    var hierarchicalListNode = function(parent_id, parent) {
        var children = JSPath('.keys{.parent_id==' + parent_id + '}', json);
        parent.children = [];
        if (children.length > 0) {

            //parent.children = children;
            $.each(children, function(index, key) {
                if (settings.filter.length === 0 || settings.filter.indexOf(key.id) > -1) {
                    var child = $.extend({}, key);
                    child.title = key.name;
                    child.href = "#" + key.id;
                    child.expand = true;
                    delete child.id;
                    delete child.name;
                    delete child.parent_id;
                    parent.children.push(child);
                    hierarchicalListNode(key.id, child);
                }
            });
        }
    };

    var  alphabeticalList = function() {
        alphabetical = [];
        var root = {};
        root.title = json[0].project_name;
        root.isFolder = true;
        root.expand = true;
        root.children = [];

        $.each(JSPath('.keys', json), function(index, item) {
            var key = $.extend({}, item);
            key.title = item.name;
            delete key.name;
            key.href = '#' + item.id;
            delete key.id;
            delete key.parent_id;
            key.expand = true;
            root.children.push(key);
        });

        alphabetical.push(root);

        elem.dynatree({
            children: alphabetical,
            data: {mode: "all"},
            expand: true
        });

        elem.on('click', 'a.dynatree-title', function(e) {
            e.preventDefault();
            var key_id = $(this).attr('href').substr(1);
            settings.keyLinkClick(key_id);
        });
    };

    var  searchList = function() {
        autocomplete = [];
        $.each(JSPath('.keys', json), function(index, item) {
            var term = $.extend({}, item);
            term.value = item.id;
            term.label = item.taxonomic_scope.name;
            delete term.id;
            delete term.parent_id;
            delete term.name;
            delete term.taxonomic_scope;
            autocomplete.push(term);
        });

        autocomplete.sort(function(a, b) {
            if (a.label < b.label)
                return -1;
            if (a.label > b.label)
                return 1;
            return 0;
        });


        $( "#taxonomicScopeSearch" ).autocomplete({
            minLength: 1,
            source: autocomplete,
            focus: function( event, ui ) {
                $( "#taxonomicScopeSearch" ).val( ui.item.label );
                return false;
            },
            select: function( event, ui ) {
                $( "#taxonomicScopeSearch" ).val( ui.item.label );
                $( "#search-project-id" ).val( ui.item.value );
                return false;
            }
        });
    };


}( jQuery ));