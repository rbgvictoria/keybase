            <?=form_open()?>
                <?php   
                    echo form_hidden(array(
                        'keyid' => $keyid,
                        'projectid' => $key->project->project_id,
                        'tempfilename' => $tempfilename,
                        'delimiter' => $delimiter,
                        'referer' => $referer,
                        'key_metadata' => $key_metadata
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
