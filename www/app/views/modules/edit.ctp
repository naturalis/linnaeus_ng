<div class="modules form">
<?php echo $form->create('Module');?>
	<fieldset>
 		<legend><?php __('Edit Module');?></legend>
	<?php
		echo $form->input('id');
		echo $form->input('name');
		echo $form->input('description');
	?>
	</fieldset>
<?php echo $form->end('Submit');?>
</div>
<div class="actions">
	<ul>
		<li><?php echo $html->link(__('Delete', true), array('action' => 'delete', $form->value('Module.id')), null, sprintf(__('Are you sure you want to delete # %s?', true), $form->value('Module.id'))); ?></li>
		<li><?php echo $html->link(__('List Modules', true), array('action' => 'index'));?></li>
	</ul>
</div>
