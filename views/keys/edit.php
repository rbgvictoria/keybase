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
                    if ((isset($projectid) && $projectid)||isset($key['ProjectName'])) {
                        echo '<span class="project">';
                        echo anchor('key/project/' . $projectid, (isset($projectname) && $projectname) ? $projectname : $key['ProjectName']) . ': ';
                        echo '</span>';
                    }
                    if (isset($key['Name'])) {
                        echo anchor(site_url() . 'keys/show/' . $key['KeysID'] . '?tab=3', $key['Name'], array('class'=>'keydetaillink'));
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

            <?=form_open()?>
                <?php   
                    echo form_hidden(array(
                        'keyid'=>$keyid,
                        'projectid'=>$projectid,
                        'tempfilename'=>  $tempfilename,
                        'referer' => $referer
                    ));
                ?>
                <p>
                    Delimiter: <?=form_radio(array('name'=>'delimiter', 'id'=>'comma', 'value'=>'comma', 'checked' =>($delimiter == 'comma') ? 'checked' : FALSE)) ?>
                    <?=form_label('comma', 'comma', array('style'=>'width: auto'))?>
                    <?=form_radio(array('name'=>'delimiter', 'id'=>'tab', 'value'=>'tab', 'checked' => ($delimiter == 'tab') ? 'checked' : FALSE)) ?>
                    <?=form_label('tab', 'tab')?>
                </p>


                <div id="input_key">
                    <table class="table table-bordered table-condensed detect-delimiter">
                    <?php foreach ($input_key as $row): ?>
                        <tr>
                        <?php foreach ($row as $cell): ?>
                            <td><?=$cell?></td>
                        <?php endforeach; ?>
                        </tr>
                    <?php endforeach; ?>
                    </table>
                </div>

                <div class="form-group text-right">
                    <?=form_submit('submit2', 'OK', 'class="btn btn-default"')?>
                    <?=form_submit('cancel', 'Cancel', 'class="btn btn-default"')?>
                    <?=form_hidden('keyid', $keyid)?>
                </p>

            <?=form_close()?>

            <?php elseif(isset($error_key) && $error_key): ?>

            <?=form_open()?>
                <?php   
                    echo form_hidden(array(
                        'keyid' => $keyid,
                        'projectid' => $projectid,
                        'tempfilename' => $tempfilename,
                        'delimiter' => $delimiter,
                        'referer' => $referer
                    ));
                ?>
                <?php if ($errors): ?>
                <div class="errors">
                    <table class="table table-bordered table-condensed"><tr><td>Errors:</td>
                    <?php foreach ($errors as $key => $value): ?>
                        <td class="<?=$key?>"><?=ucfirst(str_replace('-', ' ', $key))?> (<?=count($value)?>)</td>
                    <?php endforeach; ?>
                    </tr></table>
                </div>
                <?php endif; ?>

                <?php if ($warnings): ?>
                <div class="warnings">
                    <table class="table table-bordered table-condensed"><tr><td>Warnings:</td>
                    <?php foreach ($warnings as $key => $value): ?>
                        <td class="<?=$key?>"><?=ucfirst(str_replace('-', ' ', $key))?> (<?=count($value)?>)</td>
                    <?php endforeach; ?>
                    </tr></table>
                </div>
                <?php endif; ?>

                <div class="error_table"><?=$error_key?></div>

                <p style="text-align: right">
                    <?php 
                        $data = array(
                            'name' => 'submit3',
                            'value' => 'OK',
                            'class' => 'btn btn-default'
                        );
                        if ($errors) 
                            $data['disabled'] = 'disabled'; 
                        echo form_submit($data);
                    ?>
                    <?=form_submit('cancel', 'Cancel', 'class="btn btn-default"')?>
                </p>

            <?=form_close()?>

            <?php else: ?>

            <?=form_open_multipart('', 'class="form-horizontal"')?>
            <h3>Key metadata</h3>
            <hr />
            <?php 
                $data = array(
                    'name' => 'name',
                    'id' => 'name',
                    'size' => 80,
                    'tabindex' => 0,
                    'class' => 'form-control'
                );
                if (isset($key)) {
                    echo form_hidden(array(
                        'keyid'=>$key['KeysID'], 
                        //'name'=>$key['Name'], 
                        'projectid'=>$key['ProjectsID'],
                        'createdbyid'=>$key['CreatedByID'],
                        'taxonomicscope_old'=>(isset($key['TaxonomicScope'])) ? $key['TaxonomicScope'] : FALSE,
                        'referer'=>$referer
                    ));
                    $data['value'] = $key['Name'];
                }
                else {
                    echo form_hidden(array('projectid'=>$projectid, 'createdbyid'=>$this->session->userdata['id']));
                }
            ?>
            <div class="form-group is_required">
                <?=form_label('Name', 'name', array('class' => 'form-label col-md-2'));?>
                <div class="col-md-6">
                    <?=form_input($data);?>
                    <span class="form-control-required" aria-hidden="true"><i class="fa fa-asterisk"></i></span>
                    <span class="sr-only">(required)</span>
                </div>
            </div>
            
            <div class="form-group">
                <?php
                    $data = array(
                        'name' => 'description',
                        'id' => 'description',
                        'value' => (isset($key['Description'])) ? $key['Description'] : FALSE,
                        'rows' => 3,
                        'class' => 'form-control'
                    );
                ?>
                <?=form_label('Description: ', 'description', array('class' => 'form-label col-md-2'));?>
                <div class="col-md-10">
                    <?=form_textarea($data);?>
                </div>
            </div>
        
            <?php
                $data = array(
                    'name' => 'taxonomicscope',
                    'id' => 'taxonomicscope',
                    'value' => (isset($key['TaxonomicScope'])) ? $key['TaxonomicScope'] : FALSE,
                    'class' => 'form-control'
                );
            ?>
            <div class="form-group is_required">
                <?=form_label('Taxonomic scope', 'taxonomicscope', array('class' => 'form-label col-md-2'));?>
                <div class="col-md-6">
                    <?=form_input($data);?>
                    <span class="form-control-required" aria-hidden="true"><i class="fa fa-asterisk"></i></span>
                    <span class="sr-only">(required)</span>
                </div>
            </div>

            <div class="form-group is_required">
                <?php
                    $data = array(
                        'name' => 'geographicscope',
                        'id' => 'geographicscope',
                        'value' => (isset($key['GeographicScope'])) ? $key['GeographicScope'] : FALSE,
                        'class' => 'form-control'
                    );
                ?>
                <?=form_label('Geographic scope', 'geographicscope', array('class' => 'form-label col-md-2'));?>
                <div class="col-md-6">
                    <?=form_input($data);?>
                    <span class="form-control-required" aria-hidden="true"><i class="fa fa-asterisk"></i></span>
                    <span class="sr-only">(required)</span>
                </div>
            </div>

            <div class="form-group">
                <?php
                    $data = array(
                        'name' => 'notes',
                        'id' => 'notes',
                        'value' => (isset($key['Notes'])) ? $key['Notes'] : FALSE,
                        'rows' => 3,
                        'class' => 'form-control'
                    );
                ?>
                <?=form_label('Notes: ', 'notes', array('class' => 'form-label col-md-2'));?>
                <div class="col-md-10">
                    <?=form_textarea($data);?>
                </div>
            </div>

            <?php if (isset($key)): ?>
            <div class="form-group is_required">
                <?php
                    $data = array(
                        'name' => 'changecomment',
                        'id' => 'changecomment',
                        'class' => 'form-control'
                    );
                ?>
                <?=form_label('Change comment', 'changecomment', array('class' => 'form-label col-md-2'));?>
                <div class="col-md-10">
                    <?=form_input($data);?>
                </div>
            </div>
            <?php endif; ?>
            
            <h3>Source:</h3>
            <hr/>
            
            <div class="checkbox">
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
                    elseif (isset($key['Modified']) && $key['Modified'] == 1) {
                        $data['checked'] = 'checked';
                    }
                ?>
                <label>
                    <?=form_checkbox($data);?>
                    Modified
                </label>
            </div>
            
            <div class="form-group">
                <?php
                    $data = array(
                        'name' => 'authors',
                        'id' => 'authors',
                        'value' => (isset($key['Authors'])) ? $key['Authors'] : FALSE,
                        'class' => 'form-control'
                    );
                ?>
                <?=form_label('Authors: ', 'authors', array('class'=>'col-md-2'));?>
                <div class="col-md-10">
                    <?=form_input($data);?>
                </div>
                
            </div>

            <div class="form-group">
                <?php
                    $data = array(
                        'name' => 'year',
                        'id' => 'year',
                        'value' => (isset($key['Year'])) ? $key['Year'] : FALSE,
                        'class' => 'form-control'
                    );
                ?>
                <?=form_label('Year: ', 'year', array('class'=>'col-md-2'));?>
                <div class="col-md-2">
                    <?=form_input($data);?>
                </div>
            </div>
            
            <div class="form-group">
                <?php
                    $data = array(
                        'name' => 'title',
                        'id' => 'title',
                        'value' => (isset($key['Title'])) ? $key['Title'] : FALSE,
                        'rows' => 2,
                        'class' => 'form-control'
                    );
                ?>
                <?=form_label('Title: ', 'title', array('class'=>'col-md-2'));?>
                <div class="col-md-10">
                    <?=form_textarea($data);?>
                </div>
            </div>
            
            <div class="form-group">
                <?php
                    $data = array(
                        'name' => 'inauthors',
                        'id' => 'inauthors',
                        'value' => (isset($key['InAuthors'])) ? $key['InAuthors'] : FALSE,
                        'class' => 'form-control'
                    );
                ?>
                <?=form_label('In (authors): ', 'inauthors', array('class'=>'col-md-2'));?>
                <div class="col-md-10">
                    <?=form_input($data);?>
                </div>
            </div>
            
            <div class="form-group">
                <?php
                    $data = array(
                        'name' => 'intitle',
                        'id' => 'intitle',
                        'value' => (isset($key['InTitle'])) ? $key['InTitle'] : FALSE,
                        'rows' => 2,
                        'class' => 'form-control',
                    );
                ?>
                <?=form_label('<span style="color: #ffffff;">In</span> (title): ', 'intitle', array('class'=>'form-label col-md-2'));?>
                <div class="col-md-10">
                    <?=form_textarea($data);?>
                </div>
            </div>
            
            <div class="form-group">
                <?php
                    $data = array(
                        'name' => 'edition',
                        'id' => 'edition',
                        'value' => (isset($key['Edition'])) ? $key['Edition'] : FALSE,
                        'class' => 'form-control',
                    );
                ?>
                <?=form_label('Edition: ', 'edition', array('class'=>'form-label col-md-2'));?>
                <div class="col-md-2">
                    <?=form_input($data);?>
                </div>
            </div>
            
            <div class="form-group">
                <?php
                    $data = array(
                        'name' => 'journal',
                        'id' => 'journal',
                        'value' => (isset($key['Journal'])) ? $key['Journal'] : FALSE,
                        'class' => 'form-control',
                    );
                ?>
                <?=form_label('Journal: ', 'journal', array('class'=>'form-label col-md-2'));?>
                <div class="col-md-10">
                    <?=form_input($data);?>
                </div>
            </div>
            
            <div class="form-group">
                <?php
                    $data = array(
                        'name' => 'volume',
                        'id' => 'volume',
                        'value' => (isset($key['Volume'])) ? $key['Volume'] : FALSE,
                        'size' => '6',
                        'class' => 'form-control'
                    );
                ?>
                <?=form_label('Volume: ', 'volume', array('class'=>'form-label col-md-2'));?>
                <div class="col-md-2">
                    <?=form_input($data);?>
                </div>
            </div>
            
            <div class="form-group">
                <?php
                    $data = array(
                        'name' => 'part',
                        'id' => 'part',
                        'value' => (isset($key['Part'])) ? $key['Part'] : FALSE,
                        'size' => '6',
                        'class' => 'form-control'
                    );
                ?>
                <?=form_label('Part: ', 'part', array('class'=>'form-label col-md-2'));?>
                <div class="col-md-2">
                    <?=form_input($data);?>
                </div>
            </div>
            
            <div class="form-group">
                <?php
                    $data = array(
                        'name' => 'pages',
                        'id' => 'pages',
                        'value' => (isset($key['Pages'])) ? $key['Pages'] : FALSE,
                        'size' => '12',
                        'class' => 'form-control'
                    );
                ?>
                <?=form_label('Pages: ', 'pages', array('class'=>'form-label col-md-2'));?>
                <div class="col-md-2">
                     <?=form_input($data);?>
                </div>
            </div>
            
            <div class="form-group">
                <?php
                    $data = array(
                        'name' => 'publisher',
                        'id' => 'publisher',
                        'value' => (isset($key['Publisher'])) ? $key['Publisher'] : FALSE,
                        'size' => '40',
                        'class' => 'form-control'
                    );
               ?>
                <?=form_label('Publisher: ', 'publisher', array('class' => 'form-label col-md-2'));?>
                <div class="col-md-6">
                    <?=form_input($data);?>
                </div>
            </div>
            
            <div class="form-group">
                <?php
                    $data = array(
                        'name' => 'placeofpublication',
                        'id' => 'placeofpublication',
                        'value' => (isset($key['PlaceOfPublication'])) ? $key['PlaceOfPublication'] : FALSE,
                        'size' => '40',
                        'class' => 'form-control'
                    );
                ?>
                <?=form_label('Place of publication: ', 'placeofpublication', array('class' => 'form-label col-md-2'));?>
                <div class="col-md-6">
                    <?=form_input($data);?>
                </div>
            </div class="form-group">
            
            <div class="form-group">
                <?php
                    $data = array(
                        'name' => 'url',
                        'id' => 'url',
                        'value' => (isset($key['Url'])) ? $key['Url'] : FALSE,
                        'size' => '80',
                        'class' => 'form-control'
                    );
                ?>
                <?=form_label('URL: ', 'url', array('class' => 'form-label col-md-2'));?>
                <div class="col-md-10">
                    <?=form_input($data);?>
                </div>
            </div>

            <h3>Upload new key file</h3>
            <hr />
            <div>
                <span class="btn btn-default btn-file">
                    Load file <?=form_upload('delimitedtext', '') ?>
                </span><span id="selected-file">No file chosen</span>
            </div>
            <?=form_hidden('loadfile', FALSE);?>

            <?php /* <p>&nbsp;</p>
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
            </div> */?>

            <div class="form-group text-right">
                <?=form_submit('submit', 'Submit', 'class="btn btn-default"')?>
                <?=form_submit('cancel', 'Cancel', 'class="btn btn-default"')?>
            </div>
            <?=form_close()?>
        <?php endif; ?>
        </div> <!-- /.col-* -->
    </div> <!-- /.row -->
</div> <!-- /.container -->

<?php require_once 'views/footer.php'; ?>
