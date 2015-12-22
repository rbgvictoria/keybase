<?php date_default_timezone_set('Australia/Melbourne'); ?>
<div id="about-pills">
        <ul class="nav nav-pills">
            <li role="presentation" class="active"><a href="#metadata" aria-controls="metadata" role="tab" data-toggle="tab">Metadata</a></li>
            <li role="presentation"><a href="#items" aria-controls="items" role="tab" data-toggle="tab">Items</a></li>
            <?php if(isset($changes) && $changes):?>
            <li role="presentation"><a href="#items" aria-controls="changes" role="tab" data-toggle="tab">Changes</a></li>
            <?php endif; ?>
            <li role="presentation"><a href="#export" aria-controls="items" role="tab" data-toggle="tab">Export</a></li>
            <?php if (isset($this->session->userdata['id'])  && (in_array($userid, $prmanagers))):?>
            <li role="presentation"><a href="<?=site_url()?>keys/edit/<?=$keyid?>">Edit</a></li>
            <?php endif;?>
        </ul>
        
        <div class="tab-content">
            <div role="tabpanel" class="tab-pane active" id="metadata">
                <h3>Key metadata</h3>
                <div class="form-horizontal">
                    <div class="form-group">
                        <label class="control-label col-md-2 col-sm-4 text-left">Taxonomic scope</label>
                        <div class="col-sm-8 col-md-10">
                            <p class="form-control-static"><?=$key['TaxonomicScope']?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-2 col-sm-4 text-left">Geographic scope</label>
                        <div class="col-sm-8 col-md-10">
                            <p class="form-control-static"><?=$key['GeographicScope']?></p>
                        </div>
                    </div>
                   <?php if ($key['Description']): ?>
                    <div class="form-group">
                        <label class="control-label col-md-2 col-sm-4 text-left">Description</label>
                        <div class="col-sm-8 col-md-10">
                            <p class="form-control-static"><?=$key['Description']?></p>
                        </div>
                    </div>
                    <?php endif; ?>
                    <?php if ($key['Notes']): ?>
                    <div class="form-group">
                        <label class="control-label col-md-2 col-sm-4 text-left">Notes</label>
                        <div class="col-sm-8 col-md-10">
                            <p class="form-control-static"><?=$key['Notes']?></p>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>


                <?php if ($citation): ?>
                <h3>Source/attribution</h3>
                <p><?=substr($citation, strpos($citation, ':')+2)?>
                <?php if ($key['Url']): ?>
                    [<?=anchor($key['Url'], $key['Url']); ?>]
                <?php endif; ?>    
                </p>
                <?php endif; ?>

                <h3>Cite this key</h3>
                <p><b>KeyBase</b> (<?=date('Y')?>). <?=$project['Name']?>: <?=$keyname?>. <?=anchor(site_url() . 'key/uid/' . $key['UID'], site_url() . 'key/uid/' . $key['UID'])?> [Seen: <?=date('d-m-Y')?>].</p>
            </div>
            
            <div role="tabpanel" class="tab-pane" id="items"></div>
            
            <?php if(isset($changes) && $changes):?>
            <div role="tabpanel" class="tab-pane" id="changes">
                <h3>Changes (<?=count($changes)?>)</h3>
                <table>
                    <tr>
                        <th>Time modified</th>
                        <th>Modified by</th>
                        <th>Comment</th>
                    </tr>
                <?php foreach ($changes as $change): ?>
                    <tr>
                        <td><?=$change['TimestampModified']?></td>
                        <td><?=$change['FullName']?></td>
                        <td><?=$change['Comment']?></td>
                    </tr>
                <?php endforeach; ?>
                </table>
            </div>
            <?php endif;?>
            
            <div class="tab-pane" role="tabpanel" id="export">
                <div class="btn-group" role="group">
                    <a href="<?=site_url()?>key/export/lpxk/<?=$key['KeysID']?>/" class="btn btn-default">LPXK</a>
                    <a href="<?=site_url()?>key/export/csv/<?=$key['KeysID']?>/" class="btn btn-default">CSV</a>
                    <a href="<?=site_url()?>key/export/txt/<?=$key['KeysID']?>/" class="btn btn-default">TXT</a>
                    <a href="<?=site_url()?>key/export/sdd/<?=$key['KeysID']?>/" class="btn btn-default">SDD</a>
                </div>
            </div>
            
        </div> <!-- /.tab-content -->
</div>    
    
