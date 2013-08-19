<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="en-US" xml:lang="en-US">
    <head>
        <title>KeyBase</title>
        <meta charset="UTF-8" />
        <!--base href="<?=base_url()?>" /-->
        <link rel="stylesheet" type="text/css" href="<?=base_url()?>css/jquery-ui-1.10.2.custom.css"/>
        <link rel="stylesheet" type="text/css" href="<?=base_url()?>css/colorbox/colorbox.css"/>
        <?php if (isset($css)): ?>
            <?php foreach ($css as $link): ?>
        <link rel="stylesheet" type="text/css" href="<?=$link?>" />
            <?php endforeach; ?>
        <?php endif; ?>
        <link rel="stylesheet" type="text/css" href="<?=base_url()?>css/keybase.css" />
        <!--script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script-->
        <script type="text/javascript" src="<?=base_url()?>js/jquery-1.9.1.min.js"></script>
        <script type="text/javascript" src="<?=base_url()?>js/jquery.window.js"></script>
        <script type="text/javascript" src="<?=base_url()?>js/jquery-ui-1.10.2.custom.min.js"></script>
        <script type="text/javascript" src="<?=base_url()?>js/colorbox/jquery.colorbox.js"></script>
        <script type="text/javascript" src="<?=base_url()?>js/colorbox/jquery.keybase.colorbox.js"></script>
        <?php if (isset($js)): ?>
            <?php foreach ($js as $file): ?>
        <script type="text/javascript" src="<?=$file?>"></script>
            <?php endforeach; ?>
        <?php endif; ?>
        <?php
            if (isset($iehack) && $iehack) require_once('iehack.php');
        ?>
        <?php if (isset($script) && $script): ?>
        <script type="text/javascript">
        <?=$script; ?>
        </script>
        <?php endif; ?>
    </head>

    <body class="colorbox-upload-image">
        <div>

            <h2>Upload image</h2>

            <?=form_open_multipart()?>
            <div>
                <?=form_label('Choose image: ', 'st_image');?>
                <?=form_upload(array('name'=>'st_image', 'id'=>'st_image', 'value'=>''));?>
            </div>
            <div id="loaded-images"></div>
            <div>&nbsp;</div>
            <div style="width: 100%; text-align: right;">
                <?=form_submit('submit', 'Upload image'); ?>
            </div>
            
            <?php if (isset($loadedimages)): ?>
            <?=form_hidden('loadedimages', serialize($loadedimages))?>
            <table>
                <tr>
                    <th>Path</th>
                    <th>File type</th>
                    <th>File size</th>
                </tr>
                <?php foreach ($loadedimages as $file): ?>
                <tr>
                    <td><?=anchor(base_url() . 'images/st/' . $file['name'], base_url() . 'images/st/' . $file['name'])?></td>
                    <td><?=$file['type']?></td>
                    <td><?=$file['size']?></td>
                </tr>
                <?php endforeach; ?>
            </table>
            
            <?php endif; ?>
            <?=form_close()?>
            
        </div>
    </body>
</html>


