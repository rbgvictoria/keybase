<?php require_once('header.php'); ?>

<?php require_once('keypageheader.php'); ?>
    <?php if(isset($key) && $key): ?>
    <div id="bracketedkey">
        <h3><span id="keypanel_filter"><a class="colorbox_ajax" href="<?=site_url()?>key/filterkey/<?=$keyid?>"></a></span></h3>
        <table>
            <?php foreach ($key as $i => $node): ?>
            <tbody id="s<?=$node['StepID']?>" class="<?=($i % 2 == 0) ? 'odd' : 'even';?>">
                <?php foreach ($node['Leads'] as $j => $lead): ?>
                <tr id="l<?=$lead['LeadID']?>">
                    <td class="from"><?=$lead['FromNode']?><?=($j == 1) ? ':' : '';?></td>
                    <td class="text"><?=$lead['Text']?></td>
                    <td class="to">
                        <?php if ($lead['ToNode']): ?>
                        <a href="<?=site_url()?>key/bracketedkey/<?=$keyid?>#s<?=$lead['LeadID']?>"><?=$lead['ToNode']?></a>
                        <?php else: ?>
                        
                        <?php 
                            if ($lead['URL']) {
                                $toname = anchor($lead['URL'], $lead['ToName']);
                            }
                            else {
                                $toname = $lead['ToName'];
                            }
                        ?>
                        
                        <?=$toname?><?php if ($lead['NextKey']): ?>&nbsp;<a href="<?=site_url()?>key/bracketedkey/<?=$lead['NextKey']?>">&#x25BA;</a><?php endif; ?>
                        
                        <?php if ($lead['LinkToName']):?>
                        
                        <?php 
                            if ($lead['LinkToURL']) {
                                $linktoname = anchor($lead['LinkToURL'], $lead['LinkToName']);
                            }
                            else {
                                $linktoname = $lead['LinkToName'];
                            }
                        ?>
                        
                        : <?=$linktoname?><?php if ($lead['LinkToNextKey']): ?>&nbsp;<a href="<?=site_url()?>key/bracketedkey/<?=$lead['LinkToNextKey']?>">&#x25BA;</a><?php endif; ?>
                        <?php endif; ?>
                        
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
            <?php endforeach; ?>
        </table>
    </div>
    <?php endif; ?>

<?php require_once('footer.php'); ?>