<?php require_once('header.php'); ?>

<h2>Add project</h2>
<?php
    echo form_open();
    echo form_hidden('userid', $this->session->userdata['id']);
    
    echo '<p>';
    $data = array(
        'name' => 'name',
        'id' => 'name',
        'value' => FALSE,
        'size' => 80,
    );
    echo '<span style="font-weight: bold">';
    echo form_label('Name: ', 'name');
    echo '</span>';
    echo form_input($data);
    echo '<span class="required">*</span></p>';
    
    echo '<p>';
    $data = array(
        'name' => 'description',
        'id' => 'description',
        'value' => FALSE,
        'rows' => 3,
        'cols' => 80,
        'style' => 'vertical-align: top;',
    );
    echo '<span style="font-weight: bold">';
    echo form_label('Description: ', 'description');
    echo '</span>';
    echo form_textarea($data);
    echo '</p>';
    
    echo '<p>';
    echo form_submit('submit', 'Submit');
    echo form_close();
    echo '</p>';
?>

<?php require_once('footer.php'); ?>