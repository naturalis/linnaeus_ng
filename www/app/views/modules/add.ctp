<div class="modules form">
<?php echo $form->create('Module');?>
	<fieldset>
 		<legend><?php __('Add Module');?></legend>
	<?php
		echo $form->input('name');
		echo $form->input('description');
	?>
	</fieldset>
<?php echo $form->end('Submit');?>
</div>
<div class="actions">
	<ul>
		<li><?php echo $html->link(__('List Modules', true), array('action' => 'index'));?></li>
	</ul>
</div>
