<?php require_once('header.php'); ?>


<div id="column-box">
    <div id="column1">
        <h1>Welcome to KeyBase</h1>
        <p>KeyBase currently contains <?=$NumKeys?> keys to <?=$NumTaxa?> taxa.</p>
        <hr/>
        <div id="project-box">
            <?php foreach ($ProjectStats as $project): ?>
            <div class="project">
                <a class="project_link" href="<?=site_url()?>key/project/<?=$project['ProjectsID']?>">
                    <span class="projectbutton"><img src="<?=base_url()?>images/projecticons/<?=($project['ProjectIcon']) ? $project['ProjectIcon'] : 'project_icon_default.png'; ?>" alt="" /></span>
                    <span class="projectdata">
                        <span class="pname"><?=$project['Name']?></span>
                        <span class="pstats">(<?=$project['NumUsers']?> contributor<?=($project['NumUsers']!=1) ? 's' : ''; ?>, <?=$project['NumKeys']?> 
                            key<?=($project['NumKeys']!=1) ? 's' : '';?> to <?=$project['NumTaxa']?> taxa)</span>
                    </span>
                </a>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <div id="column2">
        <?=$staticcontent['PageContent']?>
    </div>
</div>
<div class="spacer">&nbsp;</div>

<?php require_once('footer.php'); ?>
