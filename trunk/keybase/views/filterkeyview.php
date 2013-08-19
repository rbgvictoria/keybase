<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="en-US" xml:lang="en-US">
<head>
    <meta charset="UTF-8" />
    <title>KeyBase</title>
    <script type="text/javascript" src="<?=base_url()?>js/jquery.keybase.localfilter.js"></script>
</head>

<body>
    <div id="localfilter">
    
<?=form_open_multipart('key/localfilter/' . $key)?>
<?=form_hidden(array('referer'=>$_SERVER['HTTP_REFERER'], 'filteritems'=>''))?>
<div id="keyitems">
    <div>
        <?php
            if (isset($initems) && $initems) {
                $count = count($initems);
                $label = "Included taxa ($count)";
                echo form_label($label, 'initems');
                echo form_multiselect('initems', $initems, FALSE, 'id="initems"');
            }
            else {
                echo form_label('Included taxa (0)', 'initems');
                echo form_multiselect('initems', array(), FALSE), 'id="initems"';
            }
        ?>
    </div>
    <div id="buttons">
        <span><button id="excl"></button></span>
        <span><button id="exclall"></button></span>
        <span><button id="incl"></button></span>
        <span><button id="inclall"></button></span>
    </div>
    <div>
        <?php
            if (isset($outitems) && $outitems) {
                $count = count($outitems);
                $label = "Excluded taxa ($count)";
                echo form_label($label, 'outitems');
                echo form_multiselect('outitems', $outitems, FALSE, 'id="outitems"');
            }
            else {
                echo form_label('Excluded taxa (0)', 'outitems');
                echo form_multiselect('outitems', array(), FALSE, 'id="outitems"');
            }
        ?>
    </div>
</div>
<div id="formbuttons"><?=form_button(array(
    'name' => 'cancel',
    'id' => 'cancel',
    'content' => 'Cancel'
));?> <?=form_submit('submit', 'Apply');?></div>
<?=form_close()?>

    </div>
</body>