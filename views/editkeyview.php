<?php 
    if (!$cbox)
        require_once('header.php');
    else
        echo '<!-- start colorbox --><div class="cbox-edit-key">';
?>
    <h2>
        <?php
            if (isset($key['Name'])) {
                if ($key['ProjectsID']) {
                    echo '<span class="project">';
                    echo anchor('key/project/' . $key['ProjectsID'], $key['ProjectName']) . ': ';
                    echo '</span>';
                }
                echo anchor(site_url() . 'key/keydetail/' . $key['KeysID'], $key['Name'], array('class'=>'keydetaillink'));
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

    <?=form_open_multipart()?>
    <h3>Key metadata</h3>
    <hr />
    <?php 
        if (isset($key)) {
            echo form_hidden(array(
                'keyid'=>$key['KeysID'], 
                //'name'=>$key['Name'], 
                'projectid'=>$key['ProjectsID'],
                'createdbyid'=>$key['CreatedByID'],
                'referer'=>$referer
            ));
            $data = array(
                'name' => 'name',
                'id' => 'name',
                'value' => $key['Name'],
                'size' => 80,
                'tabindex' => 0,
            );
        }
        else {
            echo form_hidden(array('projectid'=>$projectid, 'createdbyid'=>$this->session->userdata['id']));
            $data = array(
                'name' => 'name',
                'id' => 'name',
                'value' => FALSE,
                'size' => 80,
                'tabindex' => 0,
            );
        }
        echo '<p><span style="font-weight: bold;">';
        echo form_label('Name', 'name');
        echo '</span>';
        echo form_input($data);
        echo '<span class="required">*</span></p>';
    ?>
    <p>
        <span style="font-weight: bold;">
        <?php
            $data = array(
                'name' => 'description',
                'id' => 'description',
                'value' => (isset($key['Description'])) ? $key['Description'] : FALSE,
                'rows' => 3,
                'cols' => 90,
                'style' => 'vertical-align: top',
            );
            echo form_label('Description: ', 'description');
            echo form_textarea($data);
        
        ?>
        </span>
    </p>
    <p>
        <span style="font-weight: bold;">
        <?php
            $data = array(
                'name' => 'taxonomicscope',
                'id' => 'taxonomicscope',
                'value' => (isset($key['TaxonomicScope'])) ? $key['TaxonomicScope'] : FALSE,
            );
            echo form_label('Taxonomic scope: ', 'taxonomicscope');
            echo form_input($data);
            echo '<span class="required">*</span>';
        ?>
        </span>
    </p>
    <p>
        <span style="font-weight: bold;">
        <?php
            $data = array(
                'name' => 'geographicscope',
                'id' => 'geographicscope',
                'value' => (isset($key['GeographicScope'])) ? $key['GeographicScope'] : FALSE,
            );
            echo form_label('Geographic scope: ', 'geographicscope');
            echo form_input($data);
            echo '<span class="required">*</span>';
        ?>
        </span>
    </p>
    <?php if (isset($key)): ?>
    <p>
        <span style="font-weight: bold;">
        <?php
            $data = array(
                'name' => 'changecomment',
                'id' => 'changecomment',
            );
            echo form_label('Change comment: ', 'changecomment');
            echo form_input($data);
        ?>
        </span>
    </p>
    
    <?php endif; ?>
    <div><span class="label" style="width:150px">Source:</span></div>
    <div id="source">
        <div>
            <?php
                $data = array(
                    'name' => 'modified',
                    'id' => 'modified',
                    'value' => '1',
                    'checked' => FALSE,
                );
                if ($this->input->post()) {
                    if ($this->input->post('modified'))
                        $data['checked'] = 'checked';
                }
                elseif (isset($key['Modified']) && $key['Modified'] == 1)
                    $data['checked'] = 'checked';
                echo form_checkbox($data);
                echo form_label('Modified', 'modified');
                echo '<br/>';
            ?>
            <?php
                $data = array(
                    'name' => 'authors',
                    'id' => 'authors',
                    'value' => (isset($key['Authors'])) ? $key['Authors'] : FALSE,
                    'size' => '80',
                );
                echo form_label('Authors: ', 'authors');
                echo form_input($data);
            ?>
        </div>
        <div style="margin-bottom:10px;"><label>&nbsp;</label><span style="color:gray">Format: &lt;last name&gt;, &lt;initials&gt;[; &lt;last name&gt;, &lt;initials&gt;][...]</span></div>
        <div>
            <?php
                $data = array(
                    'name' => 'year',
                    'id' => 'year',
                    'value' => (isset($key['Year'])) ? $key['Year'] : FALSE,
                    'size' => '6'
                );
                echo form_label('Year: ', 'year');
                echo form_input($data);
            ?>
        </div>
        <div>
            <?php
                $data = array(
                    'name' => 'title',
                    'id' => 'title',
                    'value' => (isset($key['Title'])) ? $key['Title'] : FALSE,
                    'rows' => 2,
                    'cols' => 80,
                    'style' => 'vertical-align: top; font-family: sans-serif',
                );
                echo form_label('Title: ', 'title');
                echo form_textarea($data);
            ?>
        </div>
        <div>
            <?php
                $data = array(
                    'name' => 'inauthors',
                    'id' => 'inauthors',
                    'value' => (isset($key['InAuthors'])) ? $key['InAuthors'] : FALSE,
                    'size' => '80',
                );
                echo form_label('In (authors): ', 'inauthors');
                echo form_input($data);
            ?>
        </div>
        <div>
            <?php
                $data = array(
                    'name' => 'intitle',
                    'id' => 'intitle',
                    'value' => (isset($key['InTitle'])) ? $key['InTitle'] : FALSE,
                    'rows' => 2,
                    'cols' => 80,
                    'style' => 'vertical-align: top;',
                );
                echo form_label('<span style="color: #ffffff;">In</span> (title): ', 'intitle');
                echo form_textarea($data);
            ?>
        </div>
        <div>
            <?php
                $data = array(
                    'name' => 'edition',
                    'id' => 'edition',
                    'value' => (isset($key['Edition'])) ? $key['Edition'] : FALSE,
                    'rows' => 2,
                    'cols' => 80,
                    'style' => 'vertical-align: top;',
                );
                echo form_label('Edition: ', 'edition');
                echo form_textarea($data);
            ?>
        </div>
        <div>
            <?php
                $data = array(
                    'name' => 'journal',
                    'id' => 'journal',
                    'value' => (isset($key['Journal'])) ? $key['Journal'] : FALSE,
                    'size' => '80',
                );
                echo form_label('Journal: ', 'journal');
                echo form_input($data);
            ?>
        </div>
        <div>
            <?php
                $data = array(
                    'name' => 'volume',
                    'id' => 'volume',
                    'value' => (isset($key['Volume'])) ? $key['Volume'] : FALSE,
                    'size' => '6',
                );
                echo form_label('Volume: ', 'volume');
                echo form_input($data);
            ?>
        </div>
        <div>
            <?php
                $data = array(
                    'name' => 'part',
                    'id' => 'part',
                    'value' => (isset($key['Part'])) ? $key['Part'] : FALSE,
                    'size' => '6',
                );
                echo form_label('Part: ', 'part');
                echo form_input($data);
            ?>
        </div>
        <div>
            <?php
                $data = array(
                    'name' => 'pages',
                    'id' => 'pages',
                    'value' => (isset($key['Pages'])) ? $key['Pages'] : FALSE,
                    'size' => '12',
                );
                echo form_label('Pages: ', 'pages');
                echo form_input($data);
            ?>
        </div>
        <div>
            <?php
                $data = array(
                    'name' => 'publisher',
                    'id' => 'publisher',
                    'value' => (isset($key['Publisher'])) ? $key['Publisher'] : FALSE,
                    'size' => '40',
                );
                echo form_label('Publisher: ', 'publisher');
                echo form_input($data);
            ?>
        </div>
        <div>
            <?php
                $data = array(
                    'name' => 'placeofpublication',
                    'id' => 'placeofpublication',
                    'value' => (isset($key['PlaceOfPublication'])) ? $key['PlaceOfPublication'] : FALSE,
                    'size' => '40',
                );
                echo form_label('Place of publication: ', 'placeofpublication');
                echo form_input($data);
            ?>
        </div>
        <div>
            <?php
                $data = array(
                    'name' => 'url',
                    'id' => 'url',
                    'value' => (isset($key['Url'])) ? $key['Url'] : FALSE,
                    'size' => '80',
                );
                echo form_label('URL: ', 'url');
                echo form_input($data);
            ?>
        </div>
    </div>
    
    <p>&nbsp;</p>
    <h3>Upload new key file</h3>
    <hr />
    
    <div><b>Delimited text (txt/csv)</b></div>
    <div style="margin-left: 3em">
    <p>
        <?=form_label('Load file:', 'delimitedtext')?>
        <?=form_upload('delimitedtext', '') ?>
        Delimiter: <?=form_radio(array('name'=>'delimiter', 'id'=>'comma', 'value'=>'comma', 'checked'=>'checked')) ?>
        <?=form_label('comma', 'comma', array('style'=>'width: auto'))?>
        <?=form_radio(array('name'=>'delimiter', 'id'=>'tab', 'value'=>'tab')) ?>
        <?=form_label('tab', 'tab')?>
    </p>
    </div>
    <p>&nbsp;</p>
    <div><b>Lucid Phoenix Key (lpxk)</b></div>
    <div style="margin-left: 3em">
    <p>
    <?=form_label('Load file:', 'loadfile')?>
    <?=form_upload('loadfile', '') ?>
    </p>
    <!--p>
    <?=form_label('Load from URL:', 'loadurl')?>
    <?=form_input(array('name'=>'loadurl', 'value'=>'', 'style'=>'width:400px')) ?>
    <?=form_checkbox(array('name'=>'loadimages', 'id'=>'loadimages',  'value'=>'loadimages', 'checked'=>FALSE, 'disabled'=>'disabled')) ?>
    <?=form_label('load images', 'loadimages') ?>
    </p-->
    </div>
    
    <p style="text-align: right">
        <?=form_submit('submit', 'Submit')?>
        <?=form_submit('cancel', 'Cancel')?>
    </p>
    <?=form_close()?>

<?php 
    if (!$cbox) 
        require_once('footer.php'); 
    else 
        echo '</div><!-- end colorbox -->';
?>
