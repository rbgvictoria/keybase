/**
 * Created by NKlaze on 10/04/2015.
 */
;(function ( $ ) {
    var json;
    var settings;
    var hierarchy;
    var alphabetical;
    
    $.fn.keybaseProject = function(action, options) {
        $.fn.keybaseProject.getters = {
            projectKeyTree: function() { return hierarchy; },
            projectKeyList: function() { return alphabetical; }
        };

        settings = $.extend({}, $.fn.keybaseProject.defaults, options);

        if (!json) {
            var url = settings.baseUrl + settings.project;
            console.log(url);
            $.ajax({
                url: url,
                success:function(data){
                    json = data;

                    searchList();

                    if (action === "keysHierarchical") {
                        hierarchical();
                    }
                    else {
                        if (action === "keysAlphabetical") {
                            alphabeticalList();
                        }
                    }

                },
                complete: function() {
                    settings.onComplete(json);
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
    };

    $.fn.keybaseProject.defaults = {
        baseUrl: "http://data.rbg.vic.gov.au/dev/keybase-ws/ws/project_get/",
        filter: [],
        treeDiv: 'tree',
        listDiv: 'list',
        onComplete: function() {}
    };

    $.fn.keybaseProject.defaults.keyLinkClick = function(keyID) {
        location.href = '/keybase/keys/show/' + keyID;
    };

    var hierarchical = function() {
        if (undefined === hierarchy || !hierarchy) {
            hierarchy = [];
            root = {};
            root.title = json.project_name;
            root.isFolder = true;
            root.expand = true;
            root.children = [];

            if (json.first_key.id !== null) {
                first_key = JSPath('.keys{.id==' + json.first_key.id + '}', json)[0];
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
            settings.hierarchyDisplay();
        }
    };
    
    $.fn.keybaseProject.defaults.hierarchyDisplay = function() {
        elem = $('#' + settings.treeDiv);
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

    var hierarchicalListNode = function(parent_id, parent) {
        var children = JSPath('.keys{.parent_id==' + parent_id + '}', json);
        parent.children = [];
        if (children.length > 0) {
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
        root.title = json.project_name;
        root.isFolder = true;
        root.expand = true;
        root.children = [];

        $.each(JSPath('.keys', json), function(index, item) {
            if (settings.filter.length === 0 || settings.filter.indexOf(item.id) > -1) {
                var key = $.extend({}, item);
                key.title = item.name;
                delete key.name;
                key.href = '#' + item.id;
                delete key.id;
                delete key.parent_id;
                key.expand = true;
                root.children.push(key);
            }
        });

        alphabetical.push(root);
        settings.alphabeticalListDisplay();
    };
    
    $.fn.keybaseProject.defaults.alphabeticalListDisplay = function() {
        elem = $('#' + settings.listDiv);
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
    }

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