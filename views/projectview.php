<?php require_once('header.php'); ?>
    <?php if (isset($project)): ?>
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

    <div id="breadcrumbs">&nbsp;</div>

<?php require_once('includes/globalfilter.php'); ?>

    <div id="project-page-header">
        <?php if($project): ?>
        <div id="projecticon">
            <img src="<?=base_url()?>images/projecticons/<?=($project['ProjectIcon']) ? $project['ProjectIcon'] : 'project_icon_default.png'?>" alt=""/>
        </div>
        <?php endif; ?>
        <div id="title">
            <h2><?=$project['Name']?></h2>
            <div class="project-summary">This project currently includes <?=$project['NumKeys']?> keys to <?=$project['NumTaxa']?> taxa</div>
        </div>
    </div>

    <div id="project_tabs">
        <ul>
            <li><a href="#about">About</a></li>
            <li><a href="#keys_hierarchy">Keys (tree)</a></li>
            <li><a href="#keys_alphabetical">Keys (list)</a></li>
            <li><a href="#projectusers">Contributors</a></li>
        </ul>
        
        <div id="about">
            <div class="content-left">
                <?php if (isset($this->session->userdata['id'])  && (in_array($userid, $prmanagers))):?>
                <div id="edit-project">
                    <a class="button-link" href="<?=site_url()?>key/editproject/<?=$project['ProjectsID']?>">Edit project</a>
                </div>
                <?php endif;?>
            </div>
            <div class="content-right">
                <div class="textarea"><?=$project['Description']?></div>
            </div>
        </div>

        <div id="keys_hierarchy">
            <div class="content-left">
                <?php
                    $data = array(
                        'name' => 'findkey_h',
                        'id' => 'findkey_h',
                    );

                    echo form_open('', array('name' => 'find_in_tree', 'id' => 'find_in_tree'));
                    echo '<div>';
                    echo form_label('Find a key in this project', 'findkey_h') . '<br/>';
                    echo form_input($data);
                    echo '<span class="search">';
                    echo form_submit('submith', '');
                    echo '</span>';
                    echo '</div>';
                    echo form_close();
                ?>
                <?php if ($userid && in_array($userid, $prusers)):?>
                <div class="add-key"><a href="<?=site_url()?>key/addKey/<?=$project['ProjectsID']?>" class="button-link">Add new key</a></div>
                <?php endif; ?>
            </div>
            <div class="content-right">
                <div id="tree">
                    <noscript><?=anchor(site_url() . 'ajax/projectkeys_hierarchy/' . $project['ProjectsID'], 'Hierarchy of trees in JSON format'); ?></noscript>
                </div>
            </div>
        </div>    

        <div id="keys_alphabetical">
            <div class="content-left">
                <?php
                    $data = array(
                        'name' => 'findkey_a',
                        'id' => 'findkey_a',
                    );

                    echo form_open('', array('name' => 'find_in_list', 'id' => 'find_in_list'));
                    echo '<div>';
                    echo form_label('Find a key in this project', 'findkey_a');
                    echo form_input($data);
                    echo '<span class="search">';
                    echo form_submit('submita', '');
                    echo '</span>';
                    echo '</div>';
                    echo form_close();
                ?>
                <?php if ($userid && in_array($userid, $prusers)):?>
                <div class="add-key"><a href="<?=site_url()?>key/addKey/<?=$project['ProjectsID']?>" class="button-link">Add new key</a></div>
                <?php endif; ?>
            </div>
            <div class="content-right">
                <div id="list">
                    <noscript><?=anchor(site_url() . 'ajax/projectkeys_alphabetical/' . $project['ProjectsID'], 'Alphabetical list of trees in JSON format'); ?></noscript>
                </div>
            </div>
        </div>

        <div id="projectusers">
            <div class="content-left">
                <?php if ($userid && in_array($userid, $prmanagers)): ?>
                <p>
                    <?=anchor('key/addprojectuser/' . $project['ProjectsID'], 'Add another user', array('class'=>'button-link')); ?>
                </p>
                <?php endif; ?>
            </div>
            <div class="content-right">
                <table>
                    <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?=$user['FullName']?></td>
                        <td><?=$user['Role']?></td>
                        <?php if (in_array($userid, $prmanagers)): ?>
                        <td><?=anchor('key/deleteprojectuser/' . $user['ProjectsUsersID'], '<img src="' . base_url() . '/css/images/icon_delete.png" width="10" height="12" alt="" />', array('title' => 'Remove ' . $user['FullName'])); ?></td>
                        <?php endif; ?>
                    </tr>
                    <?php endforeach; ?>
                </table>
            </div>
        </div>
    </div>
    <p>&nbsp;</p>
    
    <?php endif; ?>

<?php require_once('footer.php'); ?>