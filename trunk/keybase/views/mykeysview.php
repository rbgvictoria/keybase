<?php require_once('header.php'); ?>
    
    <h2>My keys</h2>
    <?php if (isset($this->session->userdata['id'])): ?>
    <?php if ($keys): ?>
    <table id="keylist" style="width: 100%">
        <tr>
            <th>Project</th>
            <th>Key</th>
            <th>Taxonomic scope</th>
            <th>Geographic scope</th>
            <th>&nbsp;</th>
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
            <td><?=anchor('/key/keydetail/' . $key['id'], $key['name']); ?></td>
            <td><?=$key['taxonomicscope']?></td>
            <td><?=$key['geographicscope']?></td>
            <td><a href="<?=site_url()?>key/nothophoenix/<?=$key['id']?>/">player</a></td>
        </tr>
        <?php endforeach;?>
    </table>
    <?php else: ?>
    <h3>Press the 'Add new key' button to upload your first key.</h3>
    <?php endif; ?>
    <?php else: ?>
    <h3>Your session has expired. Please log in again.</h3>
    <?php endif; ?>
        
<?php require_once('footer.php'); ?>