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

<?php require_once('views/header.php'); ?>


<?php if($project): ?>
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div id="breadcrumbs">&nbsp;</div>
            <?php //require_once('views/includes/globalfilter.php'); ?>
        </div> <!-- /.col -->
        <div class="col-md-12">
            <div id="project-page-header" class="clearfix">
                <div id="projecticon">
                    <img src="<?=base_url()?>images/projecticons/<?=($project['ProjectIcon']) ? $project['ProjectIcon'] : 'project_icon_default.png'?>" alt=""/>
                </div>
                <?php endif; ?>
                <div id="title">
                    <h2><?=$project['Name']?></h2>
                    <div class="project-summary">This project currently includes <?=$project['NumKeys']?> keys to <?=$project['NumTaxa']?> taxa</div>
                </div>
            </div>
        </div> <!-- /.col -->
        
        <div class="col-md-12">
            <div id="project_tabs">
                <ul class="nav nav-tabs navbar-right" role="tablist">
                    <li role="presentation" class="active"><a href="#keys_hierarchy" aria-controls="hierarchy" role="tab" data-toggle="tab">Keys (tree)</a></li>
                    <li role="presentation"><a href="#keys_alphabetical" aria-controls="alphabetical" role="tab" data-toggle="tab">Keys (list)</a></li>
                    <li role="presentation"><a href="#about" aria-controls="about" role="tab" data-toggle="tab">About</a></li>
                    <li role="presentation"><a href="#projectusers" aria-controls="users" role="tab" data-toggle="tab">Contributors</a></li>
                </ul>

                <div class="tab-content clearfix">
                    <div role="tabpanel" class="tab-pane" id="about">
                        <div class="row">
                            <div class="col-md-4">
                                <?php if (isset($this->session->userdata['id'])  && (in_array($userid, $prmanagers))):?>
                                <div id="edit-project">
                                    <a class="btn btn-default" href="<?=site_url()?>projects/edit/<?=$project['ProjectsID']?>">Edit project</a>
                                </div>
                                <?php endif;?>
                            </div>
                            <div class="col-md-8">
                                <div class="textarea"><?=$project['Description']?></div>
                            </div>
                        </div> <!-- /.row -->
                    </div>

                    <div role="tabpanel" class="tab-pane active" id="keys_hierarchy">
                        <div class="row">
                            <div class="col-md-4">
                                <?php
                                    $data = array(
                                        'name' => 'findkey_h',
                                        'id' => 'findkey_h',
                                        'class' => 'form-control'
                                    );
                                ?>
                                <div>Find a key in this project</div>
                                <?=form_open('', array('name' => 'find_in_tree', 'id' => 'find_in_tree', 'class' => 'form-inline'));?>
                                <div class="form-group">
                                <?=form_input($data);?>
                                <button class="btn btn-default" type="submit" name="submith"><i class="fa fa-search"></i></button>
                                </div>
                                <?=form_close();?>
                                
                                <?php if ($projectFilters): ?>
                                <?php 
                                    $options = array();
                                    $options[''] = 'Select filter...';
                                    foreach ($projectFilters as $filter) {
                                        $options[$filter['FilterID']] = $filter['FilterName'];
                                    }
                                ?>
                                <br/>
                                <div class="input-group">
                                    <span class="input-group-addon" id="apply-filter"><i class="fa fa-filter"></i></span>
                                    <?=form_dropdown('project-filter', $options, array(), 'class="form-control"'); ?>
                                </div>
                                <br/>
                                <?php endif; ?>

                                <?php if ($userid && in_array($userid, $prusers)):?>
                                <div class="add-key">
                                    <a href="<?=site_url()?>key/addKey/<?=$project['ProjectsID']?>" class="btn btn-default">Add new key</a>
                                </div>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-8">
                                <div id="tree">
                                    <i class="fa fa-spinner fa-spin"></i>
                                    <noscript><?=anchor(site_url() . 'ajax/projectkeys_hierarchy/' . $project['ProjectsID'], 'Hierarchy of trees in JSON format'); ?></noscript>
                                </div>
                            </div>
                        </div>
                    </div>    

                    <div role="tabpanel" class="tab-pane" id="keys_alphabetical">
                        <div class="row">
                            <div class="col-md-4">
                                <?php
                                    $data = array(
                                        'name' => 'findkey_a',
                                        'id' => 'findkey_a',
                                        'class' => 'form-control'
                                    );
                                ?>
                                
                                <div>Find a key in this project</div>
                                <?=form_open('', array('name' => 'find_in_list', 'id' => 'find_in_list', 'class' => 'form-inline'));?>
                                <div class="form-group">
                                <?=form_input($data);?>
                                <button class="btn btn-default" type="submit" name="submita"><i class="fa fa-search"></i></button>
                                </div>
                                <?=form_close();?>

                                
                                <?php if ($userid && in_array($userid, $prusers)):?>
                                <div class="add-key">
                                    <a href="<?=site_url()?>key/addKey/<?=$project['ProjectsID']?>" class="btn btn-default">Add new key</a>
                                </div>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-8">
                                <div id="list">
                                    <i class="fa fa-spinner fa-spin"></i>
                                    <noscript><?=anchor(site_url() . 'ajax/projectkeys_alphabetical/' . $project['ProjectsID'], 'Alphabetical list of trees in JSON format'); ?></noscript>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div role="tabpanel" class="tab-pane" id="projectusers">
                        <div class="row">
                            <div class="col-md-4">
                                <?php if ($userid && in_array($userid, $prmanagers)): ?>
                                <p>
                                    <?=anchor('key/addprojectuser/' . $project['ProjectsID'], 'Add another user', array('class'=>'btn btn-default')); ?>
                                </p>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-8">
                                <table class="table">
                                    <?php foreach ($users as $user): ?>
                                    <tr>
                                        <td><?=$user['FullName']?></td>
                                        <td><?=$user['Role']?></td>
                                        <?php if (in_array($userid, $prmanagers)): ?>
                                        <td><?=anchor('key/deleteprojectuser/' . $user['ProjectsUsersID'], '<i class="fa fa-trash-o"></i>', array('title' => 'Remove ' . $user['FullName'])); ?></td>
                                        <?php endif; ?>
                                    </tr>
                                    <?php endforeach; ?>
                                </table>
                            </div>
                        </div>
                    </div>
                </div> <!-- /.tab-content -->
            </div>
        </div> <!-- /.col -->
    </div> <!-- /.row -->
</div> <!-- /.container -->
<?php endif;?>

<?php require_once('views/footer.php'); ?>