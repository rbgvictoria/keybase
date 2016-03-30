CKEDITOR.config.customConfig = 'http://www.rbg.vic.gov.au/dbpages/dev/4fde7c4fa5200/js/ckeditor_customconfig.js';

CKEDITOR.editorConfig = function( config ) {
	// Define changes to default configuration here.
	// For the complete reference:
	// http://docs.ckeditor.com/#!/api/CKEDITOR.config

	// The toolbar groups arrangement, optimized for two toolbar rows.
	config.toolbarGroups = [
		{ name: 'clipboard',   groups: [ 'clipboard', 'undo' ] },
		{ name: 'editing',     groups: [ 'find', 'selection', 'spellchecker' ] },
		{ name: 'links' },
		{ name: 'insert' },
		{ name: 'forms' },
		{ name: 'tools' },
		{ name: 'document',	   groups: [ 'mode', 'document', 'doctools' ] },
		//{ name: 'others' },
		'/',
		{ name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ] },
		{ name: 'paragraph',   groups: [ 'list', 'indent', 'blocks', 'align' ] },
		{ name: 'styles' },
		{ name: 'colors' },
		{ name: 'about' }
	];

	// Remove some buttons, provided by the standard plugins, which we don't
	// need to have in the Standard(s) toolbar.
	// config.removeButtons = 'Underline,Subscript,Superscript';
    
    config.removePlugins = 'templates';
    
    // config.autoGrow_onStartup = true;
    config.autoGrow_maxHeight = 420;
    config.height = '420px';
    config.toolbarCanCollapse = true;
    config.toolbarStartupExpanded = true;
    
    config.stylesSet = 'custom_styles';
    config.contentsCss = base_url + 'css/ckeditor_styles.css';
    
    //config.pasteFromWordRemoveStyles = false;
};

/*
CKEDITOR.stylesSet.add( 'custom_styles', [
    // Block-level styles.
    { name: 'Description', element: 'p', attributes: { 'class': 'description' } },
    { name: 'Habitat',  element: 'p', attributes: { 'class': 'habitat' } },
    { name: 'Phenology',  element: 'p', attributes: { 'class': 'phenology' } },
    { name: 'Note',  element: 'p', attributes: { 'class': 'note' } },

    // Inline styles.
    { name: 'Scientific name', element: 'span', attributes: { 'class': 'scientific_name'} },
]);
*/