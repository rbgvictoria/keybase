<?php require_once('header.php'); ?>
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h2>Projects in KeyBase</h2>
            <?php foreach ($projects as $project):?>
            <p><?=anchor('key/project/'. $project['id'], $project['name']); ?></p>
            <?php endforeach;?>
        </div>
    </div>
</div>
        
<?php require_once('footer.php'); ?>