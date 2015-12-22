<?php 
    // File: edit.php
    // Location: views/projects/edit.php
    
    if (!$cbox) {
        require_once 'views/header.php';
        echo '<div class="container">';
    }
    else {
        echo '<!-- start colorbox --><div class="cbox-edit-project">';
        echo '<div class="container-fluid">';
    }
?>
    <div class="row">
        <div class="col-md-6">
            
            <?=form_open();?>
            <?=form_hidden('userid', $this->session->userdata['id']);?>
            <?php if (isset($project['ProjectsID'])): ?>
                <?=form_hidden('projectid', $project['ProjectsID']);?>
                <h2>Edit project: <?=$project['Name']?></h2>
            <?php else: ?>
                <h2>Add project</h2>
            <?php endif; ?>
            <div class="form-group is-required">
                <?php
                    $data = array(
                        'name' => 'name',
                        'id' => 'name',
                        'value' => (isset($project['Name'])) ? $project['Name'] : FALSE,
                        'class' => 'form-control',
                    );
                ?>
                <?=form_label('Name', 'name');?><br/>
                <?=form_input($data)?>
                <span class="form-control-required" aria-hidden="true"><i class="fa fa-asterisk"></i></span>
                <span class="sr-only">(required)</span>
            </div>

            <div class="form-group is-required">
                <?php
                    $data = array(
                        'name' => 'taxonomicscope',
                        'id' => 'taxonomicscope',
                        'value' => (isset($project['TaxonomicScope'])) ? $project['TaxonomicScope'] : FALSE,
                        'class' => 'form-control',
                    );
                ?>
                <?=form_label('Taxonomic scope', 'taxonomicscope');?><br/>
                <?=form_input($data);?>
                <span class="form-control-required" aria-hidden="true"><i class="fa fa-asterisk"></i></span>
                <span class="sr-only">(required)</span>
            </div>

            <div class="form-group is-required">
                <?php
                    $data = array(
                        'name' => 'geographicscope',
                        'id' => 'geographicscope',
                        'value' => (isset($project['GeographicScope'])) ? $project['GeographicScope'] : FALSE,
                        'class' => 'form-control',
                    );
                ?>
                <?=form_label('Geographic scope', 'geographicscope');?><br/>
                <?=form_input($data);?>
                <span class="form-control-required" aria-hidden="true"><i class="fa fa-asterisk"></i></span>
                <span class="sr-only">(required)</span>
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                <?php
                    $data = array(
                        'name' => 'description',
                        'id' => 'description',
                        'class' => 'ckeditor form-control',
                        'value' => (isset($project['Description'])) ? $project['Description'] : FALSE,
                        'rows' => 10,
                        'cols' => 80,
                        'style' => 'vertical-align: top',
                    );
                ?>
                <?=form_label('Description', 'description');?><br/>
                <?=form_textarea($data);?>
            </div>

            <div class="form-group">
            <?=form_submit('submit', 'Submit', 'class="btn btn-default"');?>
            <?=form_submit('cancel', 'Cancel', 'class="btn btn-default"');?>
            </div>
            <?=form_close();?>
        </div> <!-- /.col -->
    </div> <!-- /.row -->
</div> <!-- /.container -->

<?php 
    if (!$cbox) 
        require_once 'views/footer.php'; 
    else 
        echo '</div><!-- end colorbox -->';
?>
