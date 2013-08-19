<?php require_once('header.php'); ?>
<?php require_once('keypageheader.php'); ?>

<?php if (isset($key) && $key): ?>
    <div id="indentedkey">
        <h3><span id="keypanel_filter"><a class="colorbox_ajax" href="<?=site_url()?>key/filterkey/<?=$keyid?>"></a></span></h3>
        <?=$key?>
    </div>
    <?php endif; ?>

<?php require_once('footer.php'); ?>