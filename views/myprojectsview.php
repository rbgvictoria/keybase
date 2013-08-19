<?php require_once('header.php'); ?>
    
    <h2>My projects</h2>
    <table id="keylist" style="width: 100%">
        <tr>
            <th>Project name</th>
            <th>My role</th>
        </tr>
        <?php foreach ($projects as $project):?>
        <tr class="project">
            <td><?=anchor('key/project/'. $project['id'], $project['name']); ?></td>
            <td><?=$project['myRole']?></td>
        </tr>
        <?php endforeach;?>
    </table>
    <p>
        <?=anchor('key/addproject', 'Add project'); ?>
    </p>
        
<?php require_once('footer.php'); ?>