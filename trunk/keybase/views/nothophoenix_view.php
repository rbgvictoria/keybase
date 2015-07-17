<?php require_once('header.php'); ?>
<?php require_once('keypageheader.php'); ?>
    
    <div id="keypanel">
        <div id="leftpane">
            <div id="currentnode">
                <h3>Current node<span id="curr"><?=$currentnode?></span>
                    <span id="keymenu">
                    <?php
                        $keymenu = array();
                        if ($parent) {
                            $keymenu[] = '<span id="startover"><a href="' . site_url() . "key/nothophoenix/$keyid/\"></a></span>";
                            if ($parent > 1) {
                                $keymenu[] = '<span id="back"><a href="' . site_url() . "key/nothophoenix/$keyid/$parent\"></a></span>";
                            }
                        }

                        if ($keymenu) {
                            echo implode('', $keymenu);
                        }
                        else echo '&nbsp;';
                    ?>
                    </span>
                
                </h3>
                <div>
                    <?php if(count($node) > 1): ?>
                    <?php foreach ($node as $lead): ?>
                    <a class="lead" href="<?=site_url()?>key/nothophoenix/<?=$keyid?>/<?=$lead['id']?>"><?=$lead['lead']?></a>
                    <?php if ($lead['media']): ?>
                    <div class="featureimg"><img src="<?=site_url() . 'images/' . $lead['media']?>" alt="<?=$lead['lead']?>" /></div>
                    <?php endif; ?>
                    <?php endforeach; ?>
                    <?php else: ?>
                        <p><span class="result">
                        <?php if(isset($remaining[0]['tokey'])): ?>
                            <?=$remaining[0]['name'];?>
                        <?php else: ?>
                            <?=$remaining[0]['name'];?>
                        <?php endif; ?>
                        </span></p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="drag_updown"></div>

            <div id="path">
                <h3>Steps</h3>
                <div>
                <?php if (isset($path) && $path) :?>
                    <ol>
                    <?php foreach ($path as $lead): ?>
                    <?php if ($lead['automatic']): ?>
                        <li style="color:gray"><?=$lead['lead']?></li>    
                    <?php else: ?>    
                        <li><a href="<?=site_url()?>key/nothophoenix/<?=$keyid?>/<?=$lead['parentid']?>"><?=$lead['lead']?></a></li>
                    <?php endif; ?>
                    <?php endforeach; ?>
                    <?php if (count($node) > 1): ?>
                    <li class="pending"><a href="<?=site_url()?>key/nothophoenix/<?=$keyid?>/<?=$currentnode?>">Pending question</a></li>
                    <?php endif; ?>
                    </ol>
                <?php else: ?>
                    &nbsp;
                <?php endif; ?>
                </div>
            </div>
        </div>
        
        <div class="drag_leftright"></div>
        
        <div id="rightpane">
            <?php 
                $vicfloraUrl = str_replace('keybase', 'vicflora', base_url());
                $vicfloraUrl = substr($vicfloraUrl, 0, strlen($vicfloraUrl)-1);
            
            ?>
            <div id="remaining">
                <h3>Remaining taxa (<span id="num_remaining"><?=count($remaining)?></span>)<span id="keypanel_filter"><a class="colorbox_ajax" href="<?=site_url()?>key/filterkey/<?=$keyid?>"></a></span></h3>
                <div>
                <?php 
                    if ($remaining) {
                        foreach ($remaining as $item) {
                            $entity = array();
                            $entity[] = '<span class="entity">';
                            if ($item['media'])
                                $entity[] = '<img src="' . site_url() . 'images/' . $item['media'] . '" alt="Image of ' . $item['name'] . '"/>';
                            
                            if ($item['url'])
                                $entity[] = '<a class="external" href="' . $item['url'] . '">';

                            $entity[] = $item['name'];
                            if ($item['url'])
                                $entity[] = '</a>';

                            if ($item['tokey'])
                                $entity[] = '&nbsp;<a href="' . site_url() . 'key/nothophoenix/' . $item['tokey'] . '">&#x25BA;</a>';
                            if ($item['LinkTo']) {
                                $entity[] = ': ';
                                if ($item['linkToUrl'])
                                    $entity[] = '<a class="external" href="' . $item['linkToUrl'] . '">';
                                $entity[] = $item['LinkTo'];
                                if ($item['linkToUrl'])
                                    $entity[] = '</a>';
                                if ($item['LinkToKey'])
                                    $entity[] = '&nbsp;<a href="' . site_url() . 'key/nothophoenix/' . $item['LinkToKey'] . '">&#x25BA;</a>';
                            }
                            $entity[] = '</span>';
                            echo implode('', $entity);
                        }
                    }
                    
                ?>    
                </div>
            </div>

            <div class="drag_updown"></div>

            <div id="discarded">
                <h3>Discarded taxa (<span id="num_discarded"><?=($discarded) ? count($discarded) : 0; ?></span>)</h3>
                <div>
                <?php 
                    if ($discarded) {
                        foreach ($discarded as $item) {
                            $entity = array();
                            $entity[] = '<span class="entity">';
                            if ($item['media'])
                                $entity[] = '<img src="' . site_url() . 'images/' . $item['media'] . '" alt="Image of ' . $item['name'] . '"/>';
                            
                            if ($item['url'])
                                $entity[] = '<a class="external" href="' . $item['url'] . '">';

                            $entity[] = $item['name'];
                            if ($item['url'])
                                $entity[] = '</a>';

                            if ($item['tokey'])
                                $entity[] = '&nbsp;<a href="' . site_url() . 'key/nothophoenix/' . $item['tokey'] . '">&#x25BA;</a>';
                            if ($item['LinkTo']) {
                                $entity[] = ': ';
                                if ($item['linkToUrl'])
                                    $entity[] = '<a class="external" href="' . $item['linkToUrl'] . '">';
                                $entity[] = $item['LinkTo'];
                                if ($item['linkToUrl'])
                                    $entity[] = '</a>';
                                if ($item['LinkToKey'])
                                    $entity[] = '&nbsp;<a href="' . site_url() . 'key/nothophoenix/' . $item['LinkToKey'] . '">&#x25BA;</a>';
                            }
                            $entity[] = '</span>';
                            echo implode('', $entity);
                        }
                    }
                ?>    
                </div>
            </div>
        </div>
    </div>
<?php require_once('footer.php'); ?>