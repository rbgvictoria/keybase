<?php require_once('header.php'); ?>

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

<?=form_open()?>
<p>
    <?=form_label('First name: ', 'firstname')?><?=form_input(array('name'=>'firstname', 'id'=>'firstname', 'value'=>$this->input->post('firstname')))?><span class="required">*</span>
</p>
<p>
    <?=form_label('Last name: ', 'lastname')?><?=form_input(array('name'=>'lastname', 'id'=>'lastname', 'value'=>$this->input->post('lastname')))?><span class="required">*</span>
</p>
<p>
    <?=form_label('Email: ', 'email')?><?=form_input(array('name'=>'email', 'id'=>'email', 'value'=>$this->input->post('email')))?><span class="required">*</span>
</p>
<p>
    <?=form_label('Username: ', 'username')?><?=form_input(array('name'=>'username', 'id'=>'username', 'value'=>$this->input->post('username')))?><span class="required">*</span>
</p>
<p>
    <?=form_label('Password: ', 'passwd')?><?=form_password(array('name'=>'passwd', 'id'=>'passwd'))?><span class="required">*</span>
</p>
<p>
    <?=form_label('Confirm password: ', 'confirm')?><?=form_password(array('name'=>'confirm', 'id'=>'confirm'))?><span class="required">*</span>
</p>
<p>Please show us you are human</p>
<p><span class="label"></span><?=$captcha['image']?><br/><span class="label"></span><?=form_submit('refresh', 'Refresh');?></p>
<p>
    <?=form_label('Captcha: ', 'captcha')?><?=form_input(array('name'=>'captcha', 'id'=>'captcha', 'value'=>''))?><span class="required">*</span>
</p>
<p>
    <?=form_submit('submit', 'Submit')?>
</p>
<p>Fields marked <span class="required">*</span> are required.</p>
<?=form_close()?>



<?php require_once('footer.php'); ?>
