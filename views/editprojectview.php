<?php 
    if (!$cbox)
        require_once('header.php');
    else
        echo '<!-- start colorbox --><div class="cbox-edit-project">';
?>

<?=form_open();?>
<?=form_hidden('userid', $this->session->userdata['id']);?>
<?php if (isset($project['ProjectsID'])): ?>
    <?=form_hidden('projectid', $project['ProjectsID']);?>
    <h2>Edit project: <?=$project['Name']?></h2>
<?php else: ?>
    <h2>Add project</h2>
<?php endif; ?>
<p>
    <?php
        $data = array(
            'name' => 'name',
            'id' => 'name',
            'value' => (isset($project['Name'])) ? $project['Name'] : FALSE,
            'size' => 80,
        );
    ?>
    <?=form_label('Name', 'name');?><br/>
    <?=form_input($data)?>
    <span class="required">*</span>
</p>

<p>
    <?php
        $data = array(
            'name' => 'taxonomicscope',
            'id' => 'taxonomicscope',
            'value' => (isset($project['TaxonomicScope'])) ? $project['TaxonomicScope'] : FALSE,
            'size' => 40,
        );
    ?>
    <?=form_label('Taxonomic scope', 'taxonomicscope');?><br/>
    <?=form_input($data);?>
    <span class="required">*</span>
</p>

<p>
    <?php
        $data = array(
            'name' => 'geographicscope',
            'id' => 'geographicscope',
            'value' => (isset($project['GeographicScope'])) ? $project['GeographicScope'] : FALSE,
            'size' => 40,
        );
    ?>
    <?=form_label('Geographic scope', 'geographicscope');?><br/>
    <?=form_input($data);?>
    <span class="required">*</span>
</p>

<div>
    <?php
        $data = array(
            'name' => 'description',
            'id' => 'description',
            'class' => 'ckeditor',
            'value' => (isset($project['Description'])) ? $project['Description'] : FALSE,
            'rows' => 10,
            'cols' => 80,
            'style' => 'vertical-align: top',
        );
    ?>
    <?=form_label('Description', 'description');?><br/>
    <?=form_textarea($data);?>
</div>

<div class="submit">
<?=form_submit('submit', 'Submit');?>
<?=form_submit('cancel', 'Cancel');?>
</div>
<?=form_close();?>

<?php 
    if (!$cbox) 
        require_once('footer.php'); 
    else 
        echo '</div><!-- end colorbox -->';
?>
