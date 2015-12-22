<?php require_once('views/header.php'); ?>
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h2>Projects in KeyBase</h2>
            <?php foreach ($projects as $project):?>
            <p><?=anchor('projects/show/'. $project['id'], $project['name']); ?></p>
            <?php endforeach;?>
            <?php if (isset($this->session->userdata['id'])): ?>
            <div>
                <a class="btn btn-default" href="projects/add">Create new project</a>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>
        
<?php require_once('views/footer.php'); ?>