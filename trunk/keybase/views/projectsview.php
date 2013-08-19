<?php require_once('header.php'); ?>
    
    <h2>Projects in KeyBase</h2>
    <?php foreach ($projects as $project):?>
    <p><?=anchor('key/project/'. $project['id'], $project['name']); ?></p>
    <?php endforeach;?>
        
<?php require_once('footer.php'); ?>