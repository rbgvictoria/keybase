/* 
 * Copyright 2017 Royal Botanic Gardens Victoria.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *      http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

$(function() {
    var project = $('[data-project-id]').data('project-id');
    
    
    var source = $('#source-id');
    if (source.length) {
        var sourceId = source.val();
        if (sourceId.length) {
            getSource(sourceId);
        }
    }
    
    if ($('#source-search').length) {
        $('#source-search').autocomplete({
            source: wsUrl + '/ws/source_autocomplete/project/' + project,
            minLength: 2,
            focus: function( event, ui ) {
                $( "#source-search" ).val( ui.item.label );
                return false;
            },
            select: function( event, ui ) {
                $( "#source-id" ).val( ui.item.value );
                $( "#source-search" ).val( ui.item.label );
                $( "#source-citation" ).html("<p><b>" + ui.item.label + '</b> ' + 
                    '<br/>' + ui.item.description + "</p>");
                $('#modified').prop('checked', false);
                return false;
            }
        }).autocomplete("instance")._renderItem = function(ul, item) {
            ul.addClass('keybase-source-lookup-autocomplete-list');
            return $( "<li>" )
                .append( "<a><b>" + item.label + "</b><br>" + item.description + "</a>" )
                .appendTo( ul );
        };

        $('#source-search').on('change', function(event) {
            if (!$(this).val().length) {
                $('#source-id').val('');
                $('#source-citation').html('');
                $('#modified').prop('checked', false);
            }
        });
    }
    
    $('#sourceModal').on('show.bs.modal', function(event) {
        var button = $(event.relatedTarget);
        var modalType;
        
        /*
         * Populate the Source fields, if the Edit Source button has been clicked
         * and a source ID has been set; otherwise empty them.
         */
        if (button.data('modal-type') === 'edit' && $('#source-id').val().length) {
            modalType = 'edit';
            var sourceId = $('#source-id').val();
            populateSourceFields(sourceId);
        }
        else {
            modalType = 'create';
            $('[data-source-field]').val("");
        }
        
        /*
         * Enable Save button if any field has changed
         */
        $('[data-source-field]').on('change', function() {
            $('#save-source-form').prop('disabled', false);
        });
        
        $('#save-source-form').off('click').on('click', function(event) {
            //event.preventDefault();
            var postData = getSourcePostData();
            if (modalType === 'edit') {
                console.log(modalType);
                postData.id = sourceId;
            }
            else {
                postData.project_id = project;
            }
            var url = wsUrl + '/ws/source_post';
            console.log(postData);
            $.ajax(url, {
                data: postData,
                method: "POST",
                success: function(data) {
                    console.log(data);
                    $('#source-id').val(data);
                    getSource(data);
                }
            });
            
            $('#sourceModal').modal('hide');
        });
    }).on('hide.bs.modal', function() {
        $('#save-source-form').prop('disabled', true);
        $('[data-source-field]').val("");
    });
    
    
    
});

var getSourcePostData = function() {
    var source = {};
    $('[data-source-field]').each(function() {
        source[$(this).data('source-field')] = $(this).val();
    });
    return source;
};

var populateSourceFields = function(id) {
    var url = wsUrl + '/ws/source_get/' + id;
    $.ajax({
        url: url,
        success: function(data) {
            $.each(data, function(key, value) {
                $('[data-source-field="' + key + '"]').val(value);
            });
        }
    });
};

var getSource = function(id) {
    var url = wsUrl + '/ws/source_get/' + id;
    $.ajax({
        url: url,
        success: function(source) {
            $('#source-search').val(source.author + ' (' + source.publication_year + ')');
            var cit = '';
            cit += '<p><b>' + source.author + ' (' + source.publication_year + ')</b><br/>';
            if (source.journal) {
                cit += source.title + '. <i>' + source.journal + '</i>';
                if (source.series)
                    cit += ', ser. ' + source.series;
                cit += ' <b>' + source.volume + '</b>';
                if (source.part) 
                    cit += '(' + source.part + ')';
                cit += ':' + source.page + '.';
            }
            else 
                {if (source.in_title) {
                    cit += source.title + '. In: ';
                    if (source.in_author) 
                        cit += source.in_author + ', ';
                    cit += '<i>' + source.in_title + '</i>';
                    if (source.volume) 
                        cit += ' <b>' + source.volume + '</b>';
                    if (source.page)
                        cit += ', pp. ' + source.page;
                    cit += '.';
                    if (source.publisher) {
                        cit += ' ' + source.publisher;
                        if (source.place_of_publication)
                            cit += ', ';
                        else
                            cit += '.';
                    }
                    if (source.place_of_publication)
                        cit += ' ' + source.place_of_publication + '.';
                }
                else {
                    cit += '<i>' + source.title + '</i>.';
                    if (source.publisher) {
                        cit += ' ' + source.publisher;
                        if (source.place_of_publication)
                            cit += ', ';
                        else
                            cit += '.';
                    }
                    if (source.place_of_publication)
                        cit += ' ' + source.place_of_publication + '.';
                }
            }
            $('#source-citation').html(cit);
        }
    });
};


