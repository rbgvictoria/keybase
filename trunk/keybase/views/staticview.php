<?php require_once('header.php'); ?>

<?php if (isset($pages)): ?>
<h1>Static pages</h1>
<ul>
    <?php foreach ($pages as $page): ?>
    <?php if ($page['PageTitle'] == 'Home'):?>
    <li><?=anchor(base_url(), 'Home'); ?> (partly static only)</li>
    <?php else: ?>
    <li><?=anchor('key/st/' . $page['Uri'], $page['PageTitle'])?></li>
    <?php endif; ?>
    <?php endforeach; ?>
</ul>
<?php endif; ?>

<h1><?=$staticcontent['PageTitle']?></h1>
<div id="staticcontent"><?=$staticcontent['PageContent']?></div>

<?php require_once('footer.php'); ?>
