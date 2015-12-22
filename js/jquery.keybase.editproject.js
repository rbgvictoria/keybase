$(function() {
    CKEDITOR.replace("description", {
	toolbarGroups: [
 		{ groups: ['undo'] },
 		{ items:['Bold', 'Italic', 'RemoveFormat']},
 		{ name: 'links' },
		{ name: 'document',	   groups: [ 'mode', 'document' ] }
	],
        height: 230,
        contentsCss: base_url + 'css/ckeditor_styles.css',
        removePlugins: 'autogrow'
    });
    
});