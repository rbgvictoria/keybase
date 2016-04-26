<?php date_default_timezone_set('Australia/Melbourne'); ?>
<div id="about-pills">
        <ul class="nav nav-pills">
            <li role="presentation" class="active"><a href="#metadata" aria-controls="metadata" role="tab" data-toggle="tab">Metadata</a></li>
            <li role="presentation"><a href="#items" aria-controls="items" role="tab" data-toggle="tab">Items</a></li>
            <?php if($key->changes):?>
            <li role="presentation"><a href="#changes" aria-controls="changes" role="tab" data-toggle="tab">Changes</a></li>
            <?php endif; ?>
            <li role="presentation"><a href="#export" aria-controls="items" role="tab" data-toggle="tab">Export</a></li>
            <?php if (isset($this->session->userdata['id'])  && (in_array($userid, $prmanagers))):?>
            <li role="presentation"><a href="<?=site_url()?>keys/edit/<?=$key->key_id?>">Edit</a></li>
            <?php endif;?>
        </ul>
        
        <div class="tab-content">
            <div role="tabpanel" class="tab-pane active" id="metadata">
                <h3>Key metadata</h3>
                <div class="form-horizontal">
                    <div class="form-group">
                        <label class="control-label col-md-2 col-sm-4 text-left">Taxonomic scope</label>
                        <div class="col-sm-8 col-md-10">
                            <p class="form-control-static"><?=$key->taxonomic_scope?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-2 col-sm-4 text-left">Geographic scope</label>
                        <div class="col-sm-8 col-md-10">
                            <p class="form-control-static"><?=$key->geographic_scope?></p>
                        </div>
                    </div>
                   <?php if ($key->description): ?>
                    <div class="form-group">
                        <label class="control-label col-md-2 col-sm-4 text-left">Description</label>
                        <div class="col-sm-8 col-md-10">
                            <p class="form-control-static"><?=$key->description?></p>
                        </div>
                    </div>
                    <?php endif; ?>
                    <?php if ($key->notes): ?>
                    <div class="form-group">
                        <label class="control-label col-md-2 col-sm-4 text-left">Notes</label>
                        <div class="col-sm-8 col-md-10">
                            <p class="form-control-static"><?=$key->notes?></p>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>


                <?php if ($key->source->citation): ?>
                <h3>Source/attribution</h3>
                <p><?=substr($key->source->citation, strpos($key->source->citation, ':')+2)?>
                <?php if ($key->source->url): ?>
                    [<?=anchor($key->source->url, $key->source->url); ?>]
                <?php endif; ?>    
                </p>
                <?php endif; ?>

                <h3>Cite this key</h3>
                <p><b>KeyBase</b> (<?=date('Y')?>). <?=$key->project->project_name?>: <?=$key->key_name?>. <?=anchor(site_url() . 'keys/show/' . $key->key_id, site_url() . 'keys/show/' . $key->key_id)?> [Seen: <?=date('d-m-Y')?>].</p>
                
                <div class="kb-meta">
                    <div class="row">
                        <div class="col-md-2 col-sm-4"><b>Created by</b></div>
                        <div class="col-md-4 col-sm-8"><?=($key->created_by) ? $key->created_by->full_name : '&nbsp;'; ?></div>
                        <div class="col-md-2 col-sm-4"><b>Date created</b></div>
                        <div class="col-md-4 col-sm-8"><?=($key->timestamp_created) ? date('Y-m-d', strtotime($key->timestamp_created)) : '&nbsp;'; ?></div>
                        <div class="col-md-2 col-sm-4"><b>Modified by</b></div>
                        <div class="col-md-4 col-sm-8"><?=($key->modified_by) ? $key->modified_by->full_name : '&nbsp;'; ?></div>
                        <div class="col-md-2 col-sm-4"><b>Date modified</b></div>
                        <div class="col-md-4 col-sm-8"><?=($key->timestamp_modified) ? date('Y-m-d', strtotime($key->timestamp_modified)) : '&nbsp;' ?></div>
                    </div>
                </div>
            </div>
            
            <div role="tabpanel" class="tab-pane" id="items"></div>
            
            <?php if($key->changes):?>
            <div  id="changes" role="tabpanel" class="tab-pane clearfix">
                <h3>Changes (<?=count($key->changes)?>)</h3>
                <table class="table table-bordered table-condensed">
                    <tr>
                        <th>Time modified</th>
                        <th>Modified by</th>
                        <th>Comment</th>
                    </tr>
                <?php foreach ($key->changes as $change): ?>
                    <tr>
                        <td><?=$change->timestamp_modified?></td>
                        <td><?=$change->full_name?></td>
                        <td><?=$change->comment?></td>
                    </tr>
                <?php endforeach; ?>
                </table>
            </div>
            <?php endif;?>
            
            <div class="tab-pane" role="tabpanel" id="export">
                <?php $wsUrl = $this->config->item('ws_url'); ?>
                <div class="btn-group" role="group">
                    <a href="<?=$wsUrl?>ws/export/csv/<?=$key->key_id?>/" class="btn btn-default">CSV</a>
                    <a href="<?=$wsUrl?>ws/export/txt/<?=$key->key_id?>/" class="btn btn-default">TXT</a>
                    <a href="<?=$wsUrl?>ws/export/lpxk/<?=$key->key_id?>/" class="btn btn-default">LPXK</a>
                    <a href="<?=$wsUrl?>ws/export/sdd/<?=$key->key_id?>/" class="btn btn-default">SDD</a>
                    <a href="<?=$wsUrl?>ws/key_get/<?=$key->key_id?>/" class="btn btn-default">KeyBase format (JSON)</a>
                </div>
            </div>
            
        </div> <!-- /.tab-content -->
</div>    
    
