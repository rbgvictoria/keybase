<?php require_once('header.php'); ?>
<?php require_once('keypageheader.php'); ?>

    <?php if (isset($key) && $key): ?>
    <?php 
        $userid = FALSE; 
        if (isset($this->session->userdata['id']))
            $userid = $this->session->userdata['id'];
        $prusers = array();
        $prmanagers = array();
        foreach ($users as $user) {
            $prusers[] = $user['UsersID'];
            if ($user['Role'] == 'Manager')
                $prmanagers[] = $user['UsersID'];
        }
    ?>
    <?php if (isset($this->session->userdata['id'])  && (in_array($userid, $prmanagers))):?>
    <div id="edit-key">
        <a class="button-link" href="<?=site_url()?>key/editkey/<?=$keyid?>">Edit key</a>
    </div>
    <?php endif;?>

    <h3>Key metadata</h3>
    <p><span class="label">Taxonomic scope: </span><?=$key['TaxonomicScope']?></p>
    <p><span class="label">Geographic scope: </span><?=$key['GeographicScope']?></p>
    
    <?php if ($key['Description']): ?>
    <p><span class="label">Description:</span><span class="textarea"><?=$key['Description']?></span></p>
    <?php endif; ?>
    
    <?php if ($key['Notes']): ?>
    <p><span class="label">Notes:</span><span class="textarea"><?=$key['Notes']?></span></p>
    <?php endif; ?>
    
    
    <?php if ($citation): ?>
    <h3>Source/attribution</h3>
    <p><?=substr($citation, strpos($citation, ':')+2)?>
    <?php if ($key['Url']): ?>
        [<?=anchor($key['Url'], $key['Url']); ?>]
    <?php endif; ?>    
    </p>
    <?php endif; ?>
    
    <h3>Cite this key</h3>
    <p><b>KeyBase</b> (<?=date('Y')?>). <?=$project['Name']?>: <?=$keyname?>. <?=anchor(site_url() . 'key/uid/' . $key['UID'], site_url() . 'key/uid/' . $key['UID'])?> [Seen: <?=date('d-m-Y')?>].</p>
    
    <h3>Taxa (<?=count($taxa)?>)</h3>
    <div class="taxa">
        <ul>
            <?php foreach ($taxa as $item): ?>
            <li class="keyitem">
                <?php if ($item['LSID']): ?>
                <?php
                    $uri = str_replace(':', '/', $item['LSID']);
                    $uri = str_replace('urn/lsid/biodiversity.org.au/', 'http://biodiversity.org.au/', $uri);
                    echo anchor($uri, $item['Name'], array('class'=>'external', 'target'=>'_blank'));
                ?>
                <?php else: ?>
                <?=$item['Name']?>
                <?php endif; ?>
            </li>
            <?php endforeach; ?>
        </ul>
    </div>
    
    <?php if(isset($changes) && $changes):?>
    <h3>Changes (<?=count($changes)?>)</h3>
    <table>
        <tr>
            <th>Time modified</th>
            <th>Modified by</th>
            <th>Comment</th>
        </tr>
    <?php foreach ($changes as $change): ?>
        <tr>
            <td><?=$change['TimestampModified']?></td>
            <td><?=$change['FullName']?></td>
            <td><?=$change['Comment']?></td>
        </tr>
    <?php endforeach; ?>
    </table>
    <?php endif;?>
    
    <h3>Export</h3>
    <p><span class="label">Format: </span>
        <a href="<?=site_url()?>key/export/lpxk/<?=$key['KeysID']?>/">LPXK</a> |
        <a href="<?=site_url()?>key/export/csv/<?=$key['KeysID']?>/">CSV</a> | 
        <a href="<?=site_url()?>key/export/txt/<?=$key['KeysID']?>/">TXT</a> | 
        <a href="<?=site_url()?>key/export/sdd/<?=$key['KeysID']?>/">SDD</a>
    </p>
    <?php endif; ?>

<?php require_once('footer.php'); ?>