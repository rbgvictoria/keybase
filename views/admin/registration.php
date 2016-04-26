<?php require_once('views/header.php'); ?>
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h2>Registration</h2>
            <?php if (isset($messages)): ?>
            <ul>
                <?php foreach ($messages as $message): ?>
                <li class="message"><?=$message?></li>
                <?php endforeach; ?>
            </ul>
            <?php endif; ?>
            <?php if(isset($success)): ?>
            <p style="font-weight: bold; color: #009900">User account <?=$this->input->post('username')?> has been created.</p>
            <?php endif; ?>
        </div> <!-- /.col -->
        
        <div class="col-md-6">
            <p>Fields marked <span class="required">*</span> are required.</p>
            <?=form_open()?>
            <div class="form-group is-required">
                <?=form_label('First name: ', 'firstname')?>
                <?=form_input(array('name'=>'firstname', 'id'=>'firstname', 'value'=>$this->input->post('firstname'), 'class' => 'form-control'))?>
                <span class="form-control-required" aria-hidden="true"><i class="fa fa-asterisk"></i></span>
                <span id="inputSuccess2Status" class="sr-only">(required)</span>
            </div>
            <div class="form-group is-required">
                <?=form_label('Last name: ', 'lastname')?>
                <?=form_input(array('name'=>'lastname', 'id'=>'lastname', 'value'=>$this->input->post('lastname'), 'class' => 'form-control'))?>
                <span class="form-control-required" aria-hidden="true"><i class="fa fa-asterisk"></i></span>
                <span id="inputSuccess2Status" class="sr-only">(required)</span>
            </div>
            <div class="form-group is-required">
                <?=form_label('Email: ', 'email')?>
                <?=form_input(array('name'=>'email', 'id'=>'email', 'value'=>$this->input->post('email'), 'class' => 'form-control'))?>
                <span class="form-control-required" aria-hidden="true"><i class="fa fa-asterisk"></i></span>
                <span id="inputSuccess2Status" class="sr-only">(required)</span>
            </div>
            <div class="form-group is-required">
                <?=form_label('Username: ', 'username')?>
                <?=form_input(array('name'=>'username', 'id'=>'username', 'value'=>$this->input->post('username'), 'class' => 'form-control'))?>
                <span class="form-control-required" aria-hidden="true"><i class="fa fa-asterisk"></i></span>
                <span id="inputSuccess2Status" class="sr-only">(required)</span>
            </div>
            <div class="form-group is-required">
                <?=form_label('Password: ', 'passwd')?>
                <?=form_password(array('name'=>'passwd', 'id'=>'passwd', 'class' => 'form-control'))?>
                <span class="form-control-required" aria-hidden="true"><i class="fa fa-asterisk"></i></span>
                <span id="inputSuccess2Status" class="sr-only">(required)</span>
            </div>
            <div class="form-group is-required">
                <?=form_label('Confirm password: ', 'confirm')?>
                <?=form_password(array('name'=>'confirm', 'id'=>'confirm', 'class' => 'form-control'))?>
                <span class="form-control-required" aria-hidden="true"><i class="fa fa-asterisk"></i></span>
                <span id="inputSuccess2Status" class="sr-only">(required)</span>
            </div>

            <p>Please show us you are human</p>
            <div class="form-group">
                <label><?=$captcha['image']?></label>
                <?=form_submit('refresh', 'Refresh', 'class="btn btn-default"');?>
            </div>
                
            <div class="form-group is-required">
                <?=form_label('Captcha: ', 'captcha')?>
                <?=form_input(array('name'=>'captcha', 'id'=>'captcha', 'value'=>'', 'class' => 'form-control'))?>
                <span class="form-control-required" aria-hidden="true"><i class="fa fa-asterisk"></i></span>
                <span id="inputSuccess2Status" class="sr-only">(required)</span>
            </div>
            <div class="form-group">
                <?=form_submit('submit', 'Submit', 'class="btn btn-default"')?>
            </div>
            <?=form_close()?>

        </div> <!-- /.col -->
    </div> <!-- /.row -->
</div> <!-- /.row -->




<?php require_once('views/footer.php'); ?>
