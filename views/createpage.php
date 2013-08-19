<?php require_once('header.php') ?>

<h2>Create new static page</h2>

<?php
    echo form_open();

    $data = array(
        'name' => 'uri',
        'id' => 'uri',
        'value' => FALSE,
    );
    echo '<p>';
    echo form_label('URI: ', 'uri');
    echo form_input($data);
    echo '</p>';

    $data = array(
        'name' => 'title',
        'id' => 'title',
        'value' => FALSE,
    );
    echo '<p>';
    echo form_label('Title: ', 'title');
    echo form_input($data);
    echo '</p>';
    
    echo '<p>';
    echo form_submit('submit', 'Submit');
    echo '</p>';

?>

<?php require_once('footer.php') ?>
