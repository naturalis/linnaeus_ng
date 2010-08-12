<div class="users form">
<?php echo $form->create('User');?>
	<fieldset>
 		<legend><?php __('Edit User');?></legend>
	<?php
		echo $form->input('id');
		echo $form->input('username');
		echo $form->input('password');
		echo $form->input('first_name');
		echo $form->input('last_name');
		echo $form->input('gender');
		echo $form->input('organisation');
		echo $form->input('email_address');
		echo $form->input('telephone');
		echo $form->input('active');
		echo $form->input('first_login');
		echo $form->input('last_login');
		echo $form->input('login_number');
		echo $form->input('password_changed');
	?>
	</fieldset>
<?php echo $form->end('Submit');?>
</div>
<div class="actions">
	<ul>
		<li><?php echo $html->link(__('Delete', true), array('action' => 'delete', $form->value('User.id')), null, sprintf(__('Are you sure you want to delete # %s?', true), $form->value('User.id'))); ?></li>
		<li><?php echo $html->link(__('List Users', true), array('action' => 'index'));?></li>
	</ul>
</div>
