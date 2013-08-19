<?php 
    if (!$cbox) {
        require_once('header.php');
    }
    else
        echo '<!-- start colorbox --><div id="cbox-delete-key">';
?>

<p>You are about to delete:</p>
<div id="key-to-delete"><span><?=$key['Name']?></span><br/>(<?=$key['ProjectName']?>)</div>

<?=form_open()?>
<?=form_hidden('projectid', $key['ProjectsID'])?>
<div id="delete-key-form-buttons">
    <?=form_button(array('content' => 'Cancel', 'class' => 'cancel'))?>
    <?=form_submit(array('name' => 'ok', 'value' => 'OK', 'class' => 'ok'))?>
</div>
<?=form_close()?>

<?php 
    if (!$cbox) 
        require_once('footer.php'); 
    else 
        echo '</div><!-- end colorbox -->';
?>
