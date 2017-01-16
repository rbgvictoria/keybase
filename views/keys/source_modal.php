<!-- Modal -->
<div class="modal fade" id="sourceModal" tabindex="-1" role="dialog" aria-labelledby="sourceModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="sourceModalLabel">Source</h4>
      </div>
      <div class="modal-body">
          <div class="container-fluid">
              <div class="row">
                  <div class="col-lg-12">
                      <div class="form">
            <div class="form-group clearfix">
                <?php
                    $data = array(
                        'data-source-field' => 'author',
                        'id' => 'author',
                        'class' => 'form-control'
                    );
                ?>
                <?=form_label('Authors: ', 'author', array('class'=>'col-md-2'));?>
                <div class="col-md-10">
                    <?=form_input($data);?>
                </div>
                
            </div>

            <div class="form-group clearfix">
                <?php
                    $data = array(
                        'data-source-field' => 'publication_year',
                        'id' => 'publication_year',
                        'class' => 'form-control'
                    );
                ?>
                <?=form_label('Year: ', 'publication_year', array('class'=>'col-md-2'));?>
                <div class="col-md-2">
                    <?=form_input($data);?>
                </div>
            </div>
            
            <div class="form-group clearfix">
                <?php
                    $data = array(
                        'name' => FALSE,
                        'data-source-field' => 'title',
                        'id' => 'title',
                        'rows' => 2,
                        'class' => 'form-control'
                    );
                ?>
                <?=form_label('Title: ', 'title', array('class'=>'col-md-2'));?>
                <div class="col-md-10">
                    <?=form_textarea($data);?>
                </div>
            </div>
            
            <div class="form-group clearfix">
                <?php
                    $data = array(
                        'data-source-field' => 'in_author',
                        'id' => 'in_author',
                        'class' => 'form-control'
                    );
                ?>
                <?=form_label('In (author): ', 'in_author', array('class'=>'col-md-2'));?>
                <div class="col-md-10">
                    <?=form_input($data);?>
                </div>
            </div>
            
            <div class="form-group clearfix">
                <?php
                    $data = array(
                        'name' => FALSE,
                        'data-source-field' => 'in_title',
                        'id' => 'in_title',
                        'rows' => 2,
                        'class' => 'form-control',
                    );
                ?>
                <?=form_label('<span style="color: #ffffff;">In</span> (title): ', 'in_title', array('class'=>'form-label col-md-2'));?>
                <div class="col-md-10">
                    <?=form_textarea($data);?>
                </div>
            </div>
            
            <div class="form-group clearfix">
                <?php
                    $data = array(
                        'data-source-field' => 'edition',
                        'id' => 'edition',
                        'class' => 'form-control',
                    );
                ?>
                <?=form_label('Edition: ', 'edition', array('class'=>'form-label col-md-2'));?>
                <div class="col-md-2">
                    <?=form_input($data);?>
                </div>
            </div>
            
            <div class="form-group clearfix">
                <?php
                    $data = array(
                        'data-source-field' => 'journal',
                        'id' => 'journal',
                        'class' => 'form-control',
                    );
                ?>
                <?=form_label('Journal: ', 'journal', array('class'=>'form-label col-md-2'));?>
                <div class="col-md-10">
                    <?=form_input($data);?>
                </div>
            </div>
            
            <div class="form-group clearfix">
                <?php
                    $data = array(
                        'data-source-field' => 'volume',
                        'id' => 'volume',
                        'class' => 'form-control'
                    );
                ?>
                <?=form_label('Volume: ', 'volume', array('class'=>'form-label col-md-2'));?>
                <div class="col-md-2">
                    <?=form_input($data);?>
                </div>

                <?php
                    $data = array(
                        'data-source-field' => 'part',
                        'id' => 'part',
                        'class' => 'form-control'
                    );
                ?>
                <?=form_label('Part: ', 'part', array('class'=>'form-label col-md-2 text-right'));?>
                <div class="col-md-2">
                    <?=form_input($data);?>
                </div>

                <?php
                    $data = array(
                        'data-source-field' => 'page',
                        'id' => 'page',
                        'class' => 'form-control'
                    );
                ?>
                <?=form_label('Page(s): ', 'page', array('class'=>'form-label col-md-2 text-right'));?>
                <div class="col-md-2">
                     <?=form_input($data);?>
                </div>
            </div>
            
            <div class="form-group clearfix">
                <?php
                    $data = array(
                        'data-source-field' => 'publisher',
                        'id' => 'publisher',
                        'class' => 'form-control'
                    );
               ?>
                <?=form_label('Publisher: ', 'publisher', array('class' => 'form-label col-md-2'));?>
                <div class="col-md-4">
                    <?=form_input($data);?>
                </div>

                <?php
                    $data = array(
                        'data-source-field' => 'place_of_publication',
                        'id' => 'place_of_publication',
                        'class' => 'form-control'
                    );
                ?>
                <?=form_label('Place of publication: ', 'place_of_publication', array('class' => 'form-label col-md-2 text-right'));?>
                <div class="col-md-4">
                    <?=form_input($data);?>
                </div>
            </div>
            
            <div class="form-group clearfix">
                <?php
                    $data = array(
                        'name' => 'source[url]',
                        'id' => 'url',
                        'class' => 'form-control'
                    );
                ?>
                <?=form_label('URL: ', 'url', array('class' => 'form-label col-md-2'));?>
                <div class="col-md-10">
                    <?=form_input($data);?>
                </div>
            </div>
                      </div> <!-- /.form -->
                  </div> <!-- ./col- -->
              </div> <!-- ./row -->
          </div> <!-- ./container -->
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="button" id="save-source-form" class="btn btn-primary" disabled>Save changes</button>
      </div>
    </div>
  </div>
</div>