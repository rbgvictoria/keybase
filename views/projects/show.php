<?php if (isset($project)): ?>
<?php 
    $userid = FALSE; 
    if (isset($this->session->userdata['id'])) {
        $userid = $this->session->userdata['id'];
    }
    $prusers = array();
    $prmanagers = array();
    foreach ($users as $user) {
        $prusers[] = $user->user_id;
        if ($user->role == 'Manager') {
            $prmanagers[] = $user->user_id;
        }
    }
?>
<?php endif; ?>

<?php require_once('views/header.php'); ?>


<?php if($project): ?>
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div id="breadcrumbs">&nbsp;</div>
        </div> <!-- /.col -->
        <div class="col-md-12">
            <div id="project-page-header" class="clearfix">
                <div id="projecticon">
                    <img src="<?=base_url()?>images/projecticons/<?=($project->project_icon) ? $project->project_icon : 'project_icon_default.png'?>" alt=""/>
                </div>
                <div id="title">
                    <h2><?=$project->project_name?></h2>
                    <div class="project-summary">This project currently includes <?=$project->num_keys?> keys to <?=$project->num_items?> items</div>
                </div>
            </div>
        </div> <!-- /.col -->
        
        <div class="col-md-12">
            <div id="project_tabs">
                <ul class="nav nav-tabs navbar-right" role="tablist">
                    <li role="presentation" class="active"><a href="#keys_hierarchy" aria-controls="hierarchy" role="tab" data-toggle="tab">Keys (tree)</a></li>
                    <li role="presentation"><a href="#keys_alphabetical" aria-controls="alphabetical" role="tab" data-toggle="tab">Keys (list)</a></li>
                    <?php if ($userid && in_array($userid, $prmanagers)): ?>
                        <li role="presentation"><a href="#filters" aria-controls="filters" rol="tab" data-toggle="tab">Filters</a></li>
                    <?php endif; ?>
                    <li role="presentation"><a href="#about" aria-controls="about" role="tab" data-toggle="tab">About</a></li>
                    <li role="presentation"><a href="#projectusers" aria-controls="users" role="tab" data-toggle="tab">Contributors</a></li>
                </ul>

                <div class="tab-content clearfix">
                    <?php if ($userid && in_array($userid, $prmanagers)): ?>
                    <div role="tabpanel" class="tab-pane" id="filters">
                        <?php if ($manageFilters): ?>
                        <table class="table-bordered table-condensed">
                            <thead>
                                <tr>
                                    <th>Filter</th>
                                    <th>Username</th>
                                    <th>Is project filter</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($manageFilters as $filter): ?>
                                <tr data-keybase-filter-id="<?=$filter->filter_id; ?>">
                                    <td><?=$filter->filter_name; ?></td>
                                    <td><?=$filter->user_name; ?></td>
                                    <td>
                                        <input type="checkbox" class="is-project-filter"<?=($filter->is_project_filter) ? ' checked' : '';?>/>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        <?php endif; ?>
                        
                        <div style="margin-top: 10px">
                            <?=anchor('filters', 'Create new filter', array('class' => 'btn btn-default')); ?>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <div role="tabpanel" class="tab-pane" id="about">
                        <div class="row">
                            <div class="col-md-4">
                                <?php if ($this->session->userdata('id')  && (in_array($userid, $prmanagers))):?>
                                <div id="edit-project">
                                    <a class="btn btn-default" href="<?=site_url()?>projects/edit/<?=$project->project_id?>">Edit project</a>
                                </div>
                                <?php endif;?>
                                <?php if ($this->session->userdata('id') == 1): ?>
                                <div id="delete-project">
                                    <a class="btn btn-default" href="<?=site_url()?>projects/delete/<?=$project->project_id?>">Delete project</a>
                                </div>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-8">
                                <div class="textarea"><?=$project->project_description?></div>
                            </div>
                        </div> <!-- /.row -->
                    </div>

                    <div role="tabpanel" class="tab-pane active" id="keys_hierarchy">
                        <div class="row">
                            <div class="col-md-4 left-pane">
                                <div id="keys-control-panel">
                                    <div>Find a key in this project</div>
                                    <div class="input-group">
                                    <input name="find-key" id="find-key" class="form-control" />
                                    <span class="input-group-btn">
                                        <button class="btn btn-default" type="submit" name="submith"><i class="fa fa-search"></i></button>
                                    </span>
                                    </div>

                                    <?php if ($projectFilters || $myFilters): ?>
                                    <div id="project-filters">
                                        <div class="input-group">
                                            <span class="input-group-addon" id="apply-filter"><i class="fa fa-filter"></i></span>
                                            <select class="form-control" name="filter-id">
                                                <option value=''>Select filter...</option>
                                                <?php if ($myFilters): ?>
                                                <optgroup label="My filters">
                                                <?php foreach ($myFilters as $key => $value): ?>
                                                    <option value="<?=$key?>"><?=$value?></option>
                                                <?php endforeach; ?>
                                                </optgroup>
                                                <?php endif; ?>
                                                <?php if ($projectFilters): ?>
                                                <optgroup label="Project filters">
                                                <?php foreach ($projectFilters as $filter): ?>
                                                    <option value="<?=$filter->filter_id?>"><?=$filter->filter_name?></option>
                                                <?php endforeach; ?>
                                                </optgroup>
                                                <?php endif; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <?php endif; ?>

                                    <?php if ($userid && in_array($userid, $prusers)):?>
                                    <div class="add-key">
                                        <a href="<?=site_url()?>keys/create/<?=$project->project_id?>" class="btn btn-default">Create new key</a>
                                    </div>
                                    <?php endif; ?>
                                </div> <!-- /#keys-control-panel -->
                            </div>
                            <div class="col-md-8">
                                <div id="tree">
                                    <i class="fa fa-spinner fa-spin"></i>
                                    <noscript><?=anchor(site_url() . 'ajax/projectkeys_hierarchy/' . $project->project_id, 'Hierarchy of trees in JSON format'); ?></noscript>
                                </div>
                            </div>
                        </div>
                    </div>    

                    <div role="tabpanel" class="tab-pane" id="keys_alphabetical">
                        <div class="row">
                            <div class="col-md-4 left-pane"></div>
                            <div class="col-md-8">
                                <div id="list">
                                    <i class="fa fa-spinner fa-spin"></i>
                                    <noscript><?=anchor(site_url() . 'ajax/projectkeys_alphabetical/' . $project->project_id, 'Alphabetical list of trees in JSON format'); ?></noscript>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div role="tabpanel" class="tab-pane" id="projectusers">
                        <div class="row">
                            <div class="col-md-4">
                                <?php if ($userid && in_array($userid, $prmanagers)): ?>
                                <p>
                                    <?=anchor('key/addprojectuser/' . $project->project_id, 'Add another user', array('class'=>'btn btn-default')); ?>
                                </p>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-8">
                                <table class="table">
                                    <?php foreach ($users as $user): ?>
                                    <tr>
                                        <td><?=$user->full_name?></td>
                                        <td><?=$user->role?></td>
                                        <?php if (in_array($userid, $prmanagers)): ?>
                                        <td><?=anchor('key/deleteprojectuser/' . $user->project_user_id, '<i class="fa fa-trash-o"></i>', array('title' => 'Remove ' . $user->full_name)); ?></td>
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