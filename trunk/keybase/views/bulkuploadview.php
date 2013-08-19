<?php require_once('header.php'); ?>

    <h2>Bulk upload</h2>
    <?php 
        echo form_open_multipart();
        
        $dropdown = array();
        foreach ($projects as $project) {
            $dropdown[$project['id']] = $project['name'];
        }
        echo '<p>';
        echo form_label('Project: ', 'project');
        echo form_dropdown('project', $dropdown);
        echo '</p>';
        
        $data = array(
            'name' => 'upload',
            'id' => 'upload',
        );
        echo '<p>';
        echo form_label('Upload ZIP archive: ', 'upload');
        echo form_upload($data);
        echo '</p>';
        
        echo '<p>';
        echo form_submit('submit', 'Submit');
        echo '</p>';
        echo form_close();
        
    ?>

<?php require_once('footer.php'); ?>