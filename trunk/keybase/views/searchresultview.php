<?php require_once('header.php'); ?>

<h2>Search result</h2>

<?php if ($search_result): ?>
<div class="search-string">Your search for "<?=$search_string?>" returned 
    <?=(count($search_result) == 1) ? '1 key.' : count($search_result) . ' keys.';?>
</div>

    <?php foreach ($search_result as $key): ?>
    <div class="search-result">
        <div class="project-icon">
            <img src="<?=base_url()?>images/projecticons/<?=($key['ProjectIcon']) ? $key['ProjectIcon'] : 'project_icon_default.png'; ?>" alt=""
                 height="60" width="60"/>
        </div>
        <div class="title">
            <h3>
                <span class="project">
                <?=$key['ProjectName']?>:
                </span>
                <?=anchor(site_url() . 'key/nothophoenix/' . $key['KeysID'], $key['KeyName']); ?>
            </h3>
            <div class="geographic-scope"><b>Geographic scope:</b> <?=$key['GeographicScope']?></div>
        </div>
    </div>
    <?php endforeach; ?>

<?php else: ?>
<div class="search-string">Your search for "<?=$search_string?>" returned no keys.</div>
<?php endif; ?>

<?php require_once('footer.php'); ?>
