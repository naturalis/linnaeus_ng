<div class="languages form">
<?php echo $form->create('Language');?>
	<fieldset>
 		<legend><?php __('Edit Language');?></legend>
	<?php
		echo $form->input('id');
		echo $form->input('name');
	?>
	</fieldset>
<?php echo $form->end('Submit');?>
</div>
<div class="actions">
	<ul>
		<li><?php echo $html->link(__('Delete', true), array('action' => 'delete', $form->value('Language.id')), null, sprintf(__('Are you sure you want to delete # %s?', true), $form->value('Language.id'))); ?></li>
		<li><?php echo $html->link(__('List Languages', true), array('action' => 'index'));?></li>
	</ul>
</div>
