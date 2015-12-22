<?php require_once('header.php'); ?>

<div class="container">
    <div class="row">
        <?php if (isset($pages)): ?>
        <div class="col-md-12">
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
        </div> <!-- /.col -->
        <?php endif; ?>
        
        <div class="col-md-12">
            <h1><?=$staticcontent['PageTitle']?></h1>
            <div class="staticcontent"><?=$staticcontent['PageContent']?></div>
        </div> <!-- /.col -->
    </div> <!-- /.row -->
</div> <!-- /.container -->



<?php require_once('footer.php'); ?>
