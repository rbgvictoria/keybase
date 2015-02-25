<?php require_once('header.php'); ?>
<?php if (isset($project)): ?>
<h2><span class="project"><?=$project['Name']?></span></h2>
<?=form_open_multipart()?>
    <p>
        <?=form_label('Load items file:', 'itemsfile')?>
        <?=form_upload('itemsfile', '') ?>
        <?=form_submit('submit', 'Submit')?>
    </p>
<?=form_close()?>
    
<?php if (isset($table)): ?>
    <table>
<?php foreach ($table as $i => $row): ?>
        <?php if ($row): ?>
        <tr>
            <?php foreach ($row as $j => $cell): ?>
            <?php if ($i == 0 || $j == 0): ?>
            <th><?=$cell?></th>
            <?php else: ?>
            <td><?=$cell?></td>
            <?php endif; ?>
            <?php endforeach; ?>
        </tr>
        <?php endif; ?>
<?php endforeach; ?>
    </table>
<?php endif; ?>
<?php endif; ?>
<?php require_once('footer.php'); ?>

