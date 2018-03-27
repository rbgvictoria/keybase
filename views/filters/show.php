<?php require_once('views/header.php'); ?>

<div class="container">
    <div class="row">
        
        <div class="col-md-12">
            <h2>Filter</h2>
        </div>

        <?=form_open_multipart()?>
            <div class="col-md-4">
                <?php if ($filters): ?>
                <h3>My filters</h3>
                <ul id="my-filters">
                    <?php foreach($filters as $filter): ?>
                    <li><i class="fa fa-check-square"></i><?=anchor(site_url() . 'filters/show/' . $filter->filter_id, $filter->filter_name)?></li>
                    <?php endforeach; ?>
                </ul>
                <?php endif; ?>
                <?php if(isset($projectFilters) && $projectFilters): ?>
                <h3>Project filters</h3>
                <ul id="project-filters">
                <?php foreach ($projectFilters as $index => $filter): ?>
                    <?php if ($index == 0 || $filter->project_id != $projectFilters[$index-1]->project_id):?>
                    <li><i class="fa fa-minus-square-o"></i><?=anchor(site_url() . 'projects/show/' . $filter->project_id, $filter->project_name)?>
                        <ul>
                    <?php endif; ?>
                            <li><i class="fa fa-check-square"></i><?=anchor(site_url() . 'filters/show/' . $filter->filter_id, $filter->filter_name); ?></li>
                    <?php if ($index == count($projectFilters)-1 || $filter->project_id != $projectFilters[$index+1]->project_id):?>
                        </ul>
                    </li>
                    <?php endif; ?>
                <?php endforeach; ?>
                </ul>
                <?php endif; ?>
                <?=anchor(site_url() . 'filters', 'Create new filter', array('class' => 'btn btn-default'));?>
            </div> <!-- /.col -->

            <div class="col-md-8">
                <div id="globalfilter-tabs">
                    <ul class="nav nav-tabs" role="tablist">
                        <li id="view" role="presentation" class="active"><a href="#tab1" aria-controls="view" role="tab" data-toggle="tab">View</a></li>
                        <li id="manage" role="presentation"><a href="#tab2" aria-controls="manage" role="tab" data-toggle="tab">Manage</a></li>
                    </ul>

                    <div class="tab-content">
                        <div id="tab1" role="tabpanel" class="tab-pane active">
                            <div id="globalfilter-keys"></div>
                        </div>

                        <div id="tab2" role="tabpanel" class="tab-pane">
                            <div class="form-horizontal">
                                <div class="form-group">
                                    <?=form_label('Name', 'filtername', array('class' => 'col-sm-2 form-label'))?>
                                    <div class="col-sm-10">
                                        <?=form_input(array('name'=>'filtername', 'id'=>'filtername', 'class' => 'form-control', 'placeholder' => 'Filter name'))?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <?=form_label('ID', 'filterid', array('class' => 'col-sm-2 form-label'))?>
                                    <div class="col-sm-10">
                                        <?php if (isset($filterid)):?>
                                        <?=form_hidden('filterid', $filterid); ?>
                                        <?php endif;?>
                                        <?=form_input(array('id'=>'filterid', 'disabled'=>'disabled', 'class' => 'form-control', 'placeholder' => 'Filter ID'))?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <?php 
                                        $options = array();
                                        $options[] = 'Select project';
                                        foreach ($projects as $project) {
                                            $options[$project->project_id] = $project->project_name;
                                        }
                                    ?>
                                    
                                    <?=form_label('Project', 'project', array('class' => 'col-sm-2 form-label')); ?>
                                    <div class="col-md-10">
                                         <?=form_dropdown('project', $options, false, "id=\"project\" class=\"form-control\"");?>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <span class="col-md-2"></span>
                                    <div class="checkbox col-md-10">
                                        <label>
                                            <input type="checkbox" name="isProjectFilter" value="1"/>
                                            Is project filter
                                        </label>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <?php
                                        $data = array(
                                            'name' => 'taxa',
                                            'id' => 'taxa',
                                            'value' => '',
                                            'rows' => 10,
                                            'placeholder' => 'Enter taxon names, one per line, like you would expect to find them in KeyBase, i.e. without authorship',
                                            'class' => 'form-control'
                                        );
                                    ?>
                                    <?=form_label('Taxa', 'taxa', array('class' => 'col-sm-2 form-label'))?>
                                    <div class="col-sm-10">
                                        <?=form_textarea($data)?>
                                    </div>

                                    <?php if (isset($filterid) && $filterid): ?>
                                    <?php
                                        $data = array(
                                            'name' => 'items_not_found',
                                            'id' => 'items_not_found',
                                            'value' => '',
                                            'rows' => 10,
                                            'placeholder' => 'Not in any key in selected project(s)',
                                            'class' => 'form-control'
                                        );
                                    ?>
                                    <?=form_label('Not found', 'items_not_found', array('class' => 'col-sm-2 form-label'))?>
                                    <div class="col-sm-10">
                                        <?=form_textarea($data)?>
                                    </div>
                                    <?php endif; ?>
                                </div>

                                <div class="text-right">
                                    <?=form_submit('update', 'Update filter', 'id="update" class="btn btn-default"'); ?>
                                    <?=form_submit('delete', 'Delete filter', 'id="delete" class="btn btn-default"'); ?>
                                </div>
                            </div> <!-- /.form-horizontal -->
                        </div>
                    </div> <!-- /.tab-content -->
                </div>
            </div>
        <?=form_close()?>
    </div> <!-- /.row -->
</div> <!-- /.container -->

<?php require_once('views/footer.php'); ?>