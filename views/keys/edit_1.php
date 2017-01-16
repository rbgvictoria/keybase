            <?=form_open_multipart('', 'class="form-horizontal"')?>
            <h3>Key metadata</h3>
            <?php 
                if ($this->input->post()) {
                    $key = json_decode(json_encode($this->input->post()));
                }
            
                if (isset($key)) {
                    $project = FALSE;
                    if (isset($key->project_id)) {
                        $project = $key->project_id;
                    }
                    elseif (isset($key->project->project_id)) {
                        $project = $key->project->project_id;
                    }
                    echo form_hidden(array(
                        'key_id'=>(isset($key->key_id)) ? $key->key_id : FALSE, 
                        'project_id'=>$project,
                        'created_by_id'=>(isset($key->created_by->user_id)) ? $key->created_by->user_id : $this->session->userdata('id'),
                        'taxonomic_scope_old'=>(isset($key->taxonomic_scope)) ? $key->taxonomic_scope : FALSE,
                        'referer'=>$referer
                    ));
                    $data['value'] = (isset($key->key_title)) ? $key->key_title : FALSE;
                }
                else {
                    echo form_hidden(array('project[project_id]'=>$key->project->project_id, 'created_by_id'=>$this->session->userdata['id']));
                }
            ?>
            
            <?php
                $data = array(
                    'name' => 'taxonomic_scope',
                    'id' => 'taxonomic_scope',
                    'value' => (isset($key->taxonomic_scope)) ? $key->taxonomic_scope : FALSE,
                    'class' => 'form-control'
                );
            ?>
            <div class="form-group is_required">
                <?=form_label('Taxonomic scope', 'taxonomic_scope', array('class' => 'form-label col-md-2'));?>
                <div class="col-md-6">
                    <?=form_input($data);?>
                    <span class="form-control-required" aria-hidden="true"><i class="fa fa-asterisk"></i></span>
                    <span class="sr-only">(required)</span>
                </div>
            </div>

            <div class="form-group is_required">
                <?php
                    $data = array(
                        'name' => 'geographic_scope',
                        'id' => 'geographic_scope',
                        'value' => (isset($key->geographic_scope)) ? $key->geographic_scope : FALSE,
                        'class' => 'form-control'
                    );
                ?>
                <?=form_label('Geographic scope', 'geographic_scope', array('class' => 'form-label col-md-2'));?>
                <div class="col-md-6">
                    <?=form_input($data);?>
                    <span class="form-control-required" aria-hidden="true"><i class="fa fa-asterisk"></i></span>
                    <span class="sr-only">(required)</span>
                </div>
            </div>

            <?php 
                $data = array(
                    'name' => 'key_title',
                    'id' => 'key_title',
                    'value' => (isset($key->key_title)) ? $key->key_title : FALSE,
                    'size' => 80,
                    'class' => 'form-control'
                );
            ?>
            <div class="form-group is_required">
                <?=form_label('Title', 'key_title', array('class' => 'form-label col-md-2'));?>
                <div class="col-md-6">
                    <?=form_input($data);?>
                    <span class="form-control-required" aria-hidden="true"><i class="fa fa-asterisk"></i></span>
                    <span class="sr-only">(required)</span>
                </div>
            </div>
            
            <?php 
                $data = array(
                    'name' => 'key_author',
                    'id' => 'key_author',
                    'value' => (isset($key->key_author)) ? $key->key_author : FALSE,
                    'size' => 80,
                    'class' => 'form-control'
                );
            ?>
            <div class="form-group is_required">
                <?=form_label('Author(s)', 'key_author', array('class' => 'form-label col-md-2'));?>
                <div class="col-md-6">
                    <?=form_input($data);?>
                </div>
            </div>
            
            <div class="form-group">
                <?php
                    $data = array(
                        'name' => 'description',
                        'id' => 'description',
                        'value' => (isset($key->description)) ? $key->description : FALSE,
                        'rows' => 3,
                        'class' => 'form-control'
                    );
                ?>
                <?=form_label('Description: ', 'description', array('class' => 'form-label col-md-2'));?>
                <div class="col-md-10">
                    <?=form_textarea($data);?>
                </div>
            </div>
        
            <div class="form-group">
                <?php
                    $data = array(
                        'name' => 'notes',
                        'id' => 'notes',
                        'value' => (isset($key->notes)) ? $key->notes : FALSE,
                        'rows' => 3,
                        'class' => 'form-control'
                    );
                ?>
                <?=form_label('Notes: ', 'notes', array('class' => 'form-label col-md-2'));?>
                <div class="col-md-10">
                    <?=form_textarea($data);?>
                </div>
            </div>

            <h3>Source:</h3>
            <?php 
                if (!isset($key->source)) {
                    $key->source = FALSE;
                }
            ?>
            
            <div class="col-md-12">
                <div class="form-horizontal">
                    <div class="form-group clearfix">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="input-group">
                                    <?=form_input(array('name' => FALSE, 'id' => 'source-search', 'class' => 'form-control')); ?>
                                    <?=form_input(array('type' => 'hidden', 'name' => 'source_id', 'id' => 'source-id', 'value' => (isset($key->source_id)) ? $key->source_id : FALSE)); ?>
                                    <div class="input-group-addon"><i class="fa fa-search fa-lg"></i></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <button type="button" id="edit-source" class="btn btn-primary" 
                                        data-toggle="modal" data-target="#sourceModal" data-modal-type="edit">
                                    <i class="fa fa-pencil-square-o fa-lg"></i>
                                </button>
                                <button type="button" id="create-source" class="btn btn-primary" 
                                        data-toggle="modal" data-target="#sourceModal" data-modal-type="create">
                                    <i class="fa fa-plus fa-lg"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="form-group clearfix">
                        <div id="source-citation"></div>

                        <div class="checkbox">
                            <?php
                                $data = array(
                                    'name' => 'modified_from_source',
                                    'id' => 'modified',
                                    'value' => '1',
                                    'checked' => FALSE,
                                );
                                if ($this->input->post()) {
                                    if ($this->input->post('source')) {
                                        if (isset($key->modified_from_source) && $key->modified_from_source) {
                                            $data['checked'] = 'checked';
                                        }
                                    }
                                }
                                elseif (isset($key->modified_from_source) && $key->modified_from_source == 1) {
                                    $data['checked'] = 'checked';
                                }
                            ?>
                            <label>
                                <?=form_checkbox($data);?>
                                Modified
                            </label>
                        </div>
                    </div>
                </div> <!-- /.form-horizontal -->
            </div> <!-- /.col -->
            <br />
            
            <h3>Upload new key file</h3>
            <div id="upload-key-file-pills">
                <ul class="nav nav-pills">
                    <li role="presentation" class="active"><a href="#delimited" aria-controls="delimited-text" role="tab" data-toggle="tab">Delimited text</a></li>
                    <li role="presentation"><a href="#lpxk" aria-controls="lpxk" role="tab" data-toggle="tab">Lucid Phoenix Key (LPXK) format</a></li>
                </ul>
                
                <div class="tab-content">
                    <div role="tabpanel" class="tab-pane active" id="delimited">
                        <div class="form-group">
                            <span class="btn btn-default btn-file">
                                Load file <?=form_upload('delimitedtext', '') ?>
                            </span><span id="selected-file">No file chosen</span>
                        </div>
                    </div>
                    <div role="tabpanel" class="tab-pane" id="lpxk">
                        <div class="form-group"> 
                            <span class="btn btn-default btn-file">
                                Load file <?=form_upload('loadfile', '') ?>
                            </span><span id="selected-file">No file chosen</span>
                        </div>
                        <div class="form-group">
                            <?=form_label('Load from URL:', 'loadurl', array('class' => 'col-lg-2'))?>
                            <div class="col-lg-10">
                                <?=form_input(array('name'=>'loadurl', 'value'=>'', 'class'=>'form-control input-sm')) ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php if (isset($key)): ?>
            <div class="form-group">
                <?php
                    $data = array(
                        'name' => 'change_comment',
                        'id' => 'change_comment',
                        'class' => 'form-control'
                    );
                ?>
                <?=form_label('Change comment', 'change_comment', array('class' => 'form-label col-md-2'));?>
                <div class="col-md-10">
                    <?=form_input($data);?>
                </div>
            </div>
            <?php endif; ?>
            
            

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