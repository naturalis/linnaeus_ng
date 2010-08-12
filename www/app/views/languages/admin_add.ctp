<div class="languages form">
<?php echo $form->create('Language');?>
	<fieldset>
 		<legend><?php __('Add Language');?></legend>
	<?php
		echo $form->input('name');
	?>
	</fieldset>
<?php echo $form->end('Submit');?>
</div>
<div class="actions">
	<ul>
		<li><?php echo $html->link(__('List Languages', true), array('action' => 'index'));?></li>
	</ul>
</div>
