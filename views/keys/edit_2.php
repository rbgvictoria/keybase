            <?=form_open()?>
                <?php   
                    echo form_hidden(array(
                        'keyid'=>$key->key_id,
                        'projectid'=>$key->project->project_id,
                        'tempfilename'=>  $tempfilename,
                        'referer' => $referer,
                        'key_metadata' => $key_metadata
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

                <p class="form-group text-right">
                    <?=form_submit('submit2', 'OK', 'class="btn btn-default"')?>
                    <?=form_submit('cancel', 'Cancel', 'class="btn btn-default"')?>
                    <?=form_hidden('keyid', $keyid)?>
                </p>

            <?=form_close()?>
