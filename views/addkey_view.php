<?php require_once('header.php'); ?>

    <h2>Add new key</h2>
    
    <?=form_open_multipart() ?>
    <p>
        <?=form_label('Title:', 'title')?>
        <?=form_input(array('name'=>'title', 'id'=>'title', 'value'=>'', 'style'=>'width:400px', 'tabindex'=>'0')) ?>
    </p>
    
    <?=form_fieldset('Lucid Phoenix Key (LPXK)')?>
    <p>
    <?=form_label('Load file:', 'loadfile')?>
    <?=form_upload('loadfile', '') ?>
    </p>

    <p>
    <?=form_label('Load from URL:', 'loadurl')?>
    <?=form_input(array('name'=>'loadurl', 'value'=>'', 'style'=>'width:400px')) ?>
    <?=form_checkbox(array('name'=>'loadimages', 'id'=>'loadimages',  'value'=>'loadimages', 'checked'=>'checked')) ?>
    <?=form_label('load images', 'loadimages') ?>
    </p>
    <?=form_fieldset_close()?>
    
    <?=form_fieldset('Delimited text')?>
    <p>
        <?=form_label('Load file:', 'delimitedtext')?>
        <?=form_upload('delimitedtext', '') ?>
        Delimiter: <?=form_radio(array('name'=>'delimiter', 'id'=>'comma', 'value'=>'comma', 'checked'=>'checked')) ?>
        <?=form_label('comma', 'comma', array('style'=>'width: auto'))?>
        <?=form_radio(array('name'=>'delimiter', 'id'=>'tab', 'value'=>'tab')) ?>
        <?=form_label('tab', 'tab')?>
    </p>
    <?=form_fieldset_close()?>
    
    <p>
        <?=form_label('Taxonomic scope', 'taxscope')?>
        <?=form_input('taxscope', '')?>
    </p>
    
    <p>
        <?=form_label('Geographic scope', 'geoscope')?>
        <?=form_input('geoscope', '')?>
    </p>
    
    <p>
        <?=form_submit('submit', 'Submit')?>
    </p>

    <?=form_close() ?>

<?php require_once('footer.php'); ?>