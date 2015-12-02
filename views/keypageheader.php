    <div id="breadcrumbs">
    <?php 
        if (isset($breadcrumbs) && $breadcrumbs) {
            $crumbs = array();
            foreach (array_reverse($breadcrumbs) as $crumb) {
                switch ($keyformat) {
                    case 'player':
                        $urisegm = 'nothophoenix';
                        break;
                    case 'about':
                        $urisegm = 'keydetail';
                        break;

                    default:
                        $urisegm = $keyformat . 'key';
                        break;
                }
                $crumbs[] = anchor(site_url() . 'key/' . $urisegm . '/' . $crumb['KeysID'], $crumb['Name']);
            }
            echo '&gt; ' . implode(' &gt; ', $crumbs);
        }
        else
            echo '&nbsp;';
    ?>
    </div>
    <?php require_once('includes/globalfilter.php'); ?>

    <div id="keypage_header">
        <?php if($project): ?>
        <div id="projecticon">
            <img src="<?=base_url()?>images/projecticons/<?=($project['ProjectIcon']) ? $project['ProjectIcon'] : 'project_icon_default.png'?>" alt=""/>
        </div>
        <?php endif; ?>
        <div id="title">
            <h2>
                <?php
                    if ($project) {
                        echo '<span class="project">';
                        echo anchor(site_url() . 'key/project/' . $project['ProjectsID'], $project['Name']) . ':';
                        echo '</span>';
                    }
                ?>
                <?=$keyname?> <span id="keyid"><?=$keyid?></span>
            </h2>

            <?php if ($citation): ?>
            <div class="citation"><?=$citation?></div>
            <?php endif; ?>
        </div>
    </div>
    
    
    <div id="keymainmenu">
        <?php if($keyformat == 'player'): ?>
        <span class="buttoncurrent" id="tab_player"><span>Interactive</span></span>
        <?php else: ?>
        <span class="button" id="tab_player"><?=anchor("/key/nothophoenix/$keyid", 'Player')?></span>
        <?php endif; ?>
        <?php if ($keyformat == 'bracketed'): ?>
        <span class="buttoncurrent" id="tab_bracketed"><span>Bracketed</span></span>
        <?php else: ?>
        <span class="button" id="tab_bracketed"><?=anchor("/key/bracketedkey/$keyid", 'Bracketed')?></span>
        <?php endif; ?>
        <?php if ($keyformat == 'indented'): ?>
        <span class="buttoncurrent" id="tab_indented"><span>Indented</span></span>
        <?php else: ?>
        <span class="button" id="tab_indented"><?=anchor("/key/indentedkey/$keyid", 'Indented')?></span>
        <?php endif; ?>
        <?php if ($keyformat == 'about'): ?>
        <span class="buttoncurrent" id="tab_about"><span>About</span></span>
        <?php else: ?>
        <span class="button" id="tab_about"><?=anchor("/key/keydetail/$keyid", 'About')?></span>
        <?php endif; ?>
    </div>
