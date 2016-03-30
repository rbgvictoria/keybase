<?php require_once('views/header.php'); ?>

<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h1>Welcome to KeyBase</h1>
            <p>KeyBase currently contains <?=$NumKeys?> keys to <?=$NumTaxa?> taxa.</p>
            <hr/>
        </div>
        <div class="col-md-6">
            <div id="project-box">
                <?php foreach ($ProjectStats as $project): ?>
                <div class="project">
                    <a class="project_link" href="<?=site_url()?>projects/show/<?=$project->project_id?>">
                        <span class="projectbutton"><img src="<?=base_url()?>images/projecticons/<?=($project->project_icon) ? $project->project_icon : 'project_icon_default.png'; ?>" alt="" /></span>
                        <span class="projectdata">
                            <span class="pname"><?=$project->project_name?></span>
                            <span class="pstats">(<?=$project->num_users?> contributor<?=($project->num_users!=1) ? 's' : ''; ?>, <?=$project->num_keys?> 
                                key<?=($project->num_keys!=1) ? 's' : '';?> to <?=$project->num_items?> taxa)</span>
                        </span>
                    </a>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="col-md-6">
            <div class="static-content">
                <?=$staticcontent['PageContent']?>
            </div>
        </div>
    </div> <!-- /.row -->
</div> <!-- /.container -->

<?php require_once('views/footer.php'); ?>
