<?php require_once('views/header.php'); ?>
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h2>Projects in KeyBase</h2>
            <p>KeyBase currently has <?=count($projects)?> projects, together comprising <?=$totalKeys?> keys to <?=$totalItems?> items.</p>
            <table class="table table-bordered keybase-project-table">
                <thead>
                    <tr>
                        <th>&nbsp;</th>
                        <th>Project</th>
                        <th># Keys</th>
                        <th># Items</th>
                        <th># Contributors</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($projects as $project):?>
                    <tr>
                        <td><img src="<?=base_url()?>images/projecticons/<?=($project->project_icon) ? $project->project_icon : 'project_icon_default.png'?>" 
                                 alt=""/></td>
                        <td><?=anchor('projects/show/'. $project->project_id, $project->project_name); ?></td>
                        <td><?=$project->num_keys?></td>
                        <td><?=$project->num_items?></td>
                        <td><?=$project->num_users?></td>
                    </tr>
                    <?php endforeach;?>
                </tbody>
                
            </table>
            <?php if (isset($this->session->userdata['id'])): ?>
            <div>
                <a class="btn btn-default" href="projects/create">Create new project</a>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>
        
<?php require_once('views/footer.php'); ?>