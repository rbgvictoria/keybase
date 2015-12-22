<?php require_once('header.php'); ?>

<div class="container">
    <div class="row">
        
        <div class="col-md-12">
            <h2>Filter</h2>
        </div>

        <?=form_open_multipart()?>
            <div class="col-md-4">
                <p id="filters">
                    <?=form_label('Filters: ', 'filter'); ?><br/>
                    <?php $filters['0'] = 'New filter'; ?>
                    <?=form_dropdown('filter', $filters, $this->input->post('filter'), 'id="filter" size="2"');?>
                </p>
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
                            <p>
                                <?=form_label('Name: ', 'filtername')?>
                                <?=form_input(array('name'=>'filtername', 'id'=>'filtername', 'size'=>'60'))?>
                                <?=form_label('ID: ', 'filterid')?>
                                <?=form_input(array('name'=>'filterid', 'id'=>'filterid', 'disabled'=>'disabled'))?>
                            </p>
                            <p>
                                <?=form_label('Project(s): ', 'project'); ?><br/>
                                <?=form_multiselect('projects[]', $projects, $this->input->post('projects'), 'id="projects"');?>
                            </p>

                            <p>
                            <?php
                                $data = array(
                                    'name' => 'taxa',
                                    'id' => 'taxa',
                                    'value' => '',
                                    'rows' => 10,
                                    'cols' => 80,
                                    'placeholder' => 'Enter taxon names, one per line, like you would expect to find them in KeyBase, i.e. without authorship'
                                );
                                echo form_label('Taxa', 'taxa') . '<br/>';
                                echo form_textarea($data);
                            ?>
                            </p>

                            <p>
                                <?=form_submit('update', 'Update filter', 'id="update"'); ?>
                                <?=form_submit('delete', 'Delete filter', 'id="delete"'); ?>
                            </p>
                        </div>
                    </div> <!-- /.tab-content -->
                </div>
            </div>
        <?=form_close()?>
    </div> <!-- /.row -->
</div> <!-- /.container -->

<?php require_once('footer.php'); ?>