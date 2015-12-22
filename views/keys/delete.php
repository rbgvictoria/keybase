<?php 
    // File: delete.php
    // Location: views/keys/delete

    if (!$cbox) {
        require_once('views/header.php');
    }
    else
        echo '<!-- start colorbox --><div id="cbox-delete-key">';
?>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <p>You are about to delete:</p>
            <div id="key-to-delete" class="text-center"><span><?=$key['Name']?></span><br/>(<?=$key['ProjectName']?>)</div>

            <?=form_open()?>
            <?=form_hidden('projectid', $key['ProjectsID'])?>
            <div id="delete-key-form-buttons" class="text-right">
                <?=form_button(array('content' => 'Cancel', 'class' => 'cancel btn btn-default'))?>
                <?=form_submit(array('name' => 'ok', 'value' => 'OK', 'class' => 'ok btn btn-default'))?>
            </div>
            <?=form_close()?>
        </div>
    </div>
</div>

<?php 
    if (!$cbox) 
        require_once('views/footer.php'); 
    else 
        echo '</div><!-- end colorbox -->';
?>
