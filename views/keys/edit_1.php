            <?=form_open_multipart('', 'class="form-horizontal"')?>
            <h3>Key metadata</h3>
            <hr />
            <?php 
                if ($this->input->post()) {
                    $key = json_decode(json_encode($this->input->post()));
                }
            
                $data = array(
                    'name' => 'key_name',
                    'id' => 'key_name',
                    'size' => 80,
                    'tabindex' => 0,
                    'class' => 'form-control'
                );
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
                    $data['value'] = (isset($key->key_name)) ? $key->key_name : FALSE;
                }
                else {
                    echo form_hidden(array('project[project_id]'=>$key->project->project_id, 'created_by_id'=>$this->session->userdata['id']));
                }
            ?>
            <div class="form-group is_required">
                <?=form_label('Name', 'key_name', array('class' => 'form-label col-md-2'));?>
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

            <?php if (isset($key)): ?>
            <div class="form-group is_required">
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
            
            <h3>Source:</h3>
            <hr/>
            <?php 
                if (!isset($key->source)) {
                    $key->source = FALSE;
                }
            ?>
            <div class="form-group">
                <div class="checkbox">
                    <?php
                        $data = array(
                            'name' => 'source[is_modified]',
                            'id' => 'modified',
                            'value' => '1',
                            'checked' => FALSE,
                        );
                        if ($this->input->post()) {
                            if ($this->input->post('source')) {
                                $source = $this->input->post('source');
                                if (isset($source['is_modified']) && $source['is_modified']) {
                                    $data['checked'] = 'checked';
                                }
                            }
                        }
                        elseif (isset($key->source->is_modified) && $key->source->is_modified == 1) {
                            $data['checked'] = 'checked';
                        }
                    ?>
                    <label>
                        <?=form_checkbox($data);?>
                        Modified
                    </label>
                </div>
            </div>
            
            <div class="form-group">
                <?php
                    $data = array(
                        'name' => 'source[author]',
                        'id' => 'author',
                        'value' => ($key->source) ? $key->source->author : FALSE,
                        'class' => 'form-control'
                    );
                ?>
                <?=form_label('Authors: ', 'author', array('class'=>'col-md-2'));?>
                <div class="col-md-10">
                    <?=form_input($data);?>
                </div>
                
            </div>

            <div class="form-group">
                <?php
                    $data = array(
                        'name' => 'source[publication_year]',
                        'id' => 'publication_year',
                        'value' => ($key->source) ? $key->source->publication_year : FALSE,
                        'class' => 'form-control'
                    );
                ?>
                <?=form_label('Year: ', 'publication_year', array('class'=>'col-md-2'));?>
                <div class="col-md-2">
                    <?=form_input($data);?>
                </div>
            </div>
            
            <div class="form-group">
                <?php
                    $data = array(
                        'name' => 'source[title]',
                        'id' => 'title',
                        'value' => ($key->source) ? $key->source->title : FALSE,
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
                        'name' => 'source[in_author]',
                        'id' => 'in_author',
                        'value' => ($key->source) ? $key->source->in_author : FALSE,
                        'class' => 'form-control'
                    );
                ?>
                <?=form_label('In (author): ', 'in_author', array('class'=>'col-md-2'));?>
                <div class="col-md-10">
                    <?=form_input($data);?>
                </div>
            </div>
            
            <div class="form-group">
                <?php
                    $data = array(
                        'name' => 'source[in_title]',
                        'id' => 'in_title',
                        'value' => ($key->source) ? $key->source->in_title : FALSE,
                        'rows' => 2,
                        'class' => 'form-control',
                    );
                ?>
                <?=form_label('<span style="color: #ffffff;">In</span> (title): ', 'in_title', array('class'=>'form-label col-md-2'));?>
                <div class="col-md-10">
                    <?=form_textarea($data);?>
                </div>
            </div>
            
            <div class="form-group">
                <?php
                    $data = array(
                        'name' => 'source[edition]',
                        'id' => 'edition',
                        'value' => ($key->source) ? $key->source->edition : FALSE,
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
                        'name' => 'source[journal]',
                        'id' => 'journal',
                        'value' => ($key->source) ? $key->source->journal : FALSE,
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
                        'name' => 'source[volume]',
                        'id' => 'volume',
                        'value' => ($key->source) ? $key->source->volume : FALSE,
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
                        'name' => 'source[part]',
                        'id' => 'part',
                        'value' => ($key->source) ? $key->source->part : FALSE,
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
                        'name' => 'source[page]',
                        'id' => 'page',
                        'value' => ($key->source) ? $key->source->page : FALSE,
                        'size' => '12',
                        'class' => 'form-control'
                    );
                ?>
                <?=form_label('Page(s): ', 'page', array('class'=>'form-label col-md-2'));?>
                <div class="col-md-2">
                     <?=form_input($data);?>
                </div>
            </div>
            
            <div class="form-group">
                <?php
                    $data = array(
                        'name' => 'source[publisher]',
                        'id' => 'publisher',
                        'value' => ($key->source) ? $key->source->publisher : FALSE,
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
                        'name' => 'source[place_of_publication]',
                        'id' => 'place_of_publication',
                        'value' => ($key->source) ? $key->source->place_of_publication : FALSE,
                        'size' => '40',
                        'class' => 'form-control'
                    );
                ?>
                <?=form_label('Place of publication: ', 'place_of_publication', array('class' => 'form-label col-md-2'));?>
                <div class="col-md-6">
                    <?=form_input($data);?>
                </div>
            </div class="form-group">
            
            <div class="form-group">
                <?php
                    $data = array(
                        'name' => 'source[url]',
                        'id' => 'url',
                        'value' => ($key->source) ? $key->source->url : FALSE,
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
