<div class="rights form">
<?php echo $form->create('Right');?>
	<fieldset>
 		<legend><?php __('Add Right');?></legend>
	<?php
		echo $form->input('name');
		echo $form->input('description');
	?>
	</fieldset>
<?php echo $form->end('Submit');?>
</div>
<div class="actions">
	<ul>
		<li><?php echo $html->link(__('List Rights', true), array('action' => 'index'));?></li>
	</ul>
</div>
