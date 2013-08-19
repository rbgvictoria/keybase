<?php require_once('header.php'); ?>
    
<h2>Filter</h2>

<?=form_open_multipart()?>
<div id="globalfilter-tabs">
    <div id="column_left">
        <p id="filters">
            <?=form_label('Filters: ', 'filter'); ?><br/>
            <?php $filters['0'] = 'New filter'; ?>
            <?=form_dropdown('filter', $filters, $this->input->post('filter'), 'id="filter" size="10"');?>
        </p>

        <div class="spacer">&nbsp;</div>
    </div>

    <div id="column_right">
        <ul>
            <li id="view"><a href="#tab1">View</a></li>
            <li id="manage"><a href="#tab2">Manage</a></li>
        </ul>

        <div id="tab1">
            <div id="globalfilter-keys"></div>
        </div>

        <div id="tab2">
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
    </div>
</div>
<?=form_close()?>

<?php require_once('footer.php'); ?>