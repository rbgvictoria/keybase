<?php require_once('header.php'); ?>
<div class="container">
    <div class="row">
        <div class="col-md-6">
            <h2>Add project user</h2>
            <?php if (isset($messages)): ?>
            <ul>
            <?php foreach ($messages as $message): ?>
                <li class="message"><?=$message?></li>
            <?php endforeach; ?>
            </ul>
            <?php endif; ?>

            <?=form_open();?>
            <?=form_hidden('projectid', $projectid);?>

            <div class="form-group">
                <?php 
                    $dropdown = array();
                    $dropdown[] = 'Select user';
                    foreach ($users as $user) {
                        $dropdown[$user['UsersID']] = $user['LastName'] . ', ' . $user['FirstName'];
                    }
                ?>
                <?=form_label('Name', 'userid');?>
                <?=form_dropdown('userid', $dropdown, $this->input->post('userid'), 'class="form-control"');?>
            </div>
            <div class="form-group">
                <?php
                    $roles = array(
                        'User' => 'User',
                        'Manager' => 'Manager',
                    );
                ?>
                <?=form_label('Role', 'role');?>
                <?=form_dropdown('role', $roles, $this->input->post('role'), 'class="form-control"');?>
            </div>
            <div class="form-group">
                <?=form_submit('submit', 'Submit', 'class="btn btn-default"');?>
                <?=form_submit('cancel', 'Cancel', 'class="btn btn-default"');?>
            </div>
            <?=form_close()?>
 
        </div> <!-- /.col -->
    </div> <!-- /.row -->
</div> <!-- /.container -->

<?php require_once('footer.php'); ?>