<?php require_once('views/header.php'); ?>
<div class="container">
    <div class="row">
        <div class="col-md-12">
            
            <h2>Search result</h2>

            <?php if ($result): ?>
            <div class="search-string">Your search for "<?=$search_string?>" returned 
                <?=(count($result) == 1) ? '1 key.' : count($result) . ' keys.';?>
            </div>

                <?php foreach ($result as $key): ?>
                <div class="search-result">
                    <div class="project-icon">
                        <img src="<?=base_url()?>images/projecticons/<?=($key->project->project_icon) ? $key->project->project_icon : 'project_icon_default.png'; ?>" alt=""
                             height="60" width="60"/>
                    </div>
                    <div class="title">
                        <h3>
                            <span class="project">
                            <?=$key->project->project_name?>:
                            </span>
                            <?=anchor(site_url() . 'keys/show/' . $key->key_id, $key->key_name); ?>
                        </h3>
                        <div class="geographic-scope"><b>Geographic scope:</b> <?=$key->geographic_scope?></div>
                    </div>
                </div>
                <?php endforeach; ?>

            <?php else: ?>
            <div class="search-string">Your search for "<?=$search_string?>" returned no keys.</div>
            <?php endif; ?>
        </div> <!-- /.col -->
    </div> <!-- /.row -->
</div> <!-- /.container -->

<?php require_once('views/footer.php'); ?>
