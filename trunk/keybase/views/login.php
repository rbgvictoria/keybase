<?php require_once('header.php'); ?>
<div id="loginform">
	<h2>Log in to KeyBase</h2>
	<?=form_open("admin/authenticate");?>
    <?=form_hidden('referer', $referer); ?>
	<div class="row">
	  <?=form_label('Username', 'username'); ?>
	  <?=form_input(array('name'=>'username', 'id'=>'username', 'size'=>'25')); ?>
	</div>
	<div class="row">
	  <?=form_label('Password', 'passwd'); ?>
	  <?=form_password(array('name'=>'passwd', 'id'=>'passwd', 'size'=>'25')); ?>
	</div>
	<div class="submit"><?=form_submit('submit', 'Log in'); ?></div>
	<?=form_close();?>
</div>
<?php require_once('footer.php'); ?>