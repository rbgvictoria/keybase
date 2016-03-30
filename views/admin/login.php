<?php require_once('views/header.php'); ?>
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div id="loginform">
                <h2>Log in to KeyBase</h2>
                <?=form_open("admin/authenticate", 'class="form-horizontal"');?>
                    <?=form_hidden('referer', $referer); ?>
                    <div class="form-group">
                        <?=form_label('Username', 'username', array('class' => 'form-label col-sm-4 col-md-2 col-lg-1')); ?>
                        <div class="col-sm-8 col-md-4 col-lg-3">
                            <?=form_input(array('name'=>'username', 'id'=>'username', 'size'=>'25', 'class' => 'form-control')); ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <?=form_label('Password', 'passwd', array('class' => 'form-label col-sm-4 col-md-2 col-lg-1')); ?>
                        <div class="col-sm-8 col-md-4 col-lg-3">
                            <?=form_password(array('name'=>'passwd', 'id'=>'passwd', 'size'=>'25', 'class' => 'form-control')); ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-2">
                            <?=form_submit('submit', 'Log in', 'class="btn btn-default"'); ?>
                        </div>
                    <div>
                <?=form_close();?>
            </div>
            
        </div> <!-- /.col -->
    </div> <!-- /.row -->
</div> <!-- /.container -->

<?php require_once('views/footer.php'); ?>