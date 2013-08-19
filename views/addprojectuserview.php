<?php require_once('header.php'); ?>

<h2>Add project user</h2>
<?php if (isset($messages)): ?>
<ul>
<?php foreach ($messages as $message): ?>
    <li class="message"><?=$message?></li>
<?php endforeach; ?>
</ul>
<?php endif; ?>
<?php
    echo form_open();
    echo form_hidden('projectid', $projectid);
    
    echo '<p>';
    
    $dropdown = array();
    $dropdown[] = 'Select user';
    foreach ($users as $user) {
        $dropdown[$user['UsersID']] = $user['LastName'] . ', ' . $user['FirstName'];
    }
    echo '<span style="font-weight: bold">';
    echo form_label('Name: ', 'userid');
    echo '</span>';
    echo form_dropdown('userid', $dropdown, $this->input->post('userid'));
    echo '<span class="required">*</span></p>';
    
    $roles = array(
        'User' => 'User',
        'Manager' => 'Manager',
    );
    echo '<span style="font-weight: bold">';
    echo form_label('Role: ', 'role');
    echo '</span>';
    echo form_dropdown('role', $roles, $this->input->post('role'));
    echo '<span class="required">*</span></p>';
    
    echo form_submit('submit', 'Submit');
    
    
?>

<?php require_once('footer.php'); ?>