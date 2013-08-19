<div id="import-global-filter-view">
    <?=form_open_multipart(); ?>
    
    <p>
        <?=form_label('Choose file: ', 'file'); ?><br/>
        <?=form_upload('file', '', 'id="file"'); ?>
        <?=form_submit('submit', 'Upload'); ?>
    </p>
    
    <?=form_close(); ?>
    
</div>



