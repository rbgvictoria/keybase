<?php 
    //File: edit.php
    //Loction: views/keys/edit.php

    require_once 'views/header.php';
?>
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h2>
                <?php
                    if ($key->project->project_id || $key->project->project_name) {
                        echo '<span class="project">';
                        echo anchor('key/project/' . $key->project->project_id, ($key->project->project_name) ? $key->project->project_name : $key->project->project_name) . ': ';
                        echo '</span>';
                    }
                    if (isset($key->key_name)) {
                        echo anchor(site_url() . 'keys/show/' . $key->key_id . '?tab=3', $key->key_name, array('class'=>'keydetaillink'));
                    }
                    else
                        echo 'Add a new key';
                ?>

            </h2>

            <?php if (isset($message)) : ?>
            <ul>
                <?php foreach($message as $m): ?>
                <li class="message"><?=$m?></li>
                <?php endforeach; ?>
            </ul>
            <?php endif; ?>

            <?php if (isset($input_key) && $input_key): ?>
            <?php require_once('views/keys/edit_2.php'); ?>

            <?php elseif(isset($error_key) && $error_key): ?>
            <?php require_once('views/keys/edit_3.php'); ?>

            <?php else: ?>
            <?php require_once('views/keys/edit_1.php'); ?>

            <?php endif; ?>
        </div> <!-- /.col-* -->
    </div> <!-- /.row -->
</div> <!-- /.container -->

<?php require_once 'views/footer.php'; ?>
