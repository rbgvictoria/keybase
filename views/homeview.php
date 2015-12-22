<?php require_once('header.php'); ?>

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
                    <a class="project_link" href="<?=site_url()?>projects/show/<?=$project['ProjectsID']?>">
                        <span class="projectbutton"><img src="<?=base_url()?>images/projecticons/<?=($project['ProjectIcon']) ? $project['ProjectIcon'] : 'project_icon_default.png'; ?>" alt="" /></span>
                        <span class="projectdata">
                            <span class="pname"><?=$project['Name']?></span>
                            <span class="pstats">(<?=$project['NumUsers']?> contributor<?=($project['NumUsers']!=1) ? 's' : ''; ?>, <?=$project['NumKeys']?> 
                                key<?=($project['NumKeys']!=1) ? 's' : '';?> to <?=$project['NumTaxa']?> taxa)</span>
                        </span>
                    </a>
                </div>
                <?php endforeach; ?>
                <?php if (isset($this->session->userdata['id'])): ?>
                <div class="project">
                    <div id="new_project"><?=anchor('key/addproject', 'New project', array('class'=>'button-link'))?></div>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <div class="col-md-6">
            <div class="static-content">
                <?=$staticcontent['PageContent']?>
            </div>
        </div>
    </div> <!-- /.row -->
</div> <!-- /.container -->

<?php require_once('footer.php'); ?>
