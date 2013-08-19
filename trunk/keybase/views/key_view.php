<?php require_once('header.php'); ?>
    
    <?php if ($ItemName): ?>
        <h2>Available keys for <span class="itemhl"><?=$ItemName?></span></h2>
        <p style="text-align:right">&nbsp;</p>
    <?php else: ?>
        <h2>Keys in KeyBase</h2>
    <?php endif; ?>
    <table id="keylist" style="width: 100%">
        <tr>
            <th>Project</th>
            <th>Key</th>
            <th>Taxonomic scope</th>
            <th>Geographic scope</th>
        </tr>
        <?php foreach ($keys as $key):?>
        <tr class="key <?=$key['rank']?>">
            <td>
                <?php 
                    if ($key['projectid'])
                        echo anchor('/key/project/' . $key['projectid'], $key['projectname']);
                    else
                        echo '&nbsp;';
                ?>
            </td>
            <td><?=anchor('/key/nothophoenix/' . $key['id'], $key['name']); ?></td>
            <td><?=$key['taxonomicscope']?></td>
            <td><?=$key['geographicscope']?></td>
        </tr>
        <?php endforeach;?>
    </table>
        
    <?php if ($ItemName && count($keys)>1):?>
        <p id="compkeys" style="text-align: right"><a href="<?=site_url()?>key/compare/<?=$ItemsID?>">Compare taxa</a></p>
        <div id="compare">&nbsp;</div>
    <?php endif; ?>

<?php require_once('footer.php'); ?>