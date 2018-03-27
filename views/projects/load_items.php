<?php require_once 'views/header.php'; ?>
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h1>Load project items</h1>
            <p>You can load items and their links from a CSV file where the first column contains the item name and the second column the URL.</p>
            
            <?=form_open_multipart('', 'class="form-horizontal"');?>
                <div class="form-group">
                    <span class="btn btn-default btn-file">
                        Load file <?=form_upload('file_content', '') ?>
                    </span><span id="selected-file">No file chosen</span>
                </div>
            <div><input name="submit" type="submit" class="btn btn-primary" value="Submit" /></div>
            </form><?=form_close();?>
            
            <?php if (isset($success)): ?>
            <div class="alert alert-success">
                <ul>
                    <?php foreach ((array) $success as $key=>$value): ?>
                    <li><?=$key?>: <?=$value?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php endif; ?>
            
        </div> <!-- /.col-md-12 -->
    </div> <!-- /.row -->
</div> <!-- /.container-md-12 -->

<?php require_once 'views/footer.php'; ?>