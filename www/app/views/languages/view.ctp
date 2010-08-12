<div class="languages view">
<h2><?php  __('Language');?></h2>
	<dl><?php $i = 0; $class = ' class="altrow"';?>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Id'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $language['Language']['id']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Name'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $language['Language']['name']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Created'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $language['Language']['created']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Updated'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $language['Language']['updated']; ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="actions">
	<ul>
		<li><?php echo $html->link(__('Edit Language', true), array('action' => 'edit', $language['Language']['id'])); ?> </li>
		<li><?php echo $html->link(__('Delete Language', true), array('action' => 'delete', $language['Language']['id']), null, sprintf(__('Are you sure you want to delete # %s?', true), $language['Language']['id'])); ?> </li>
		<li><?php echo $html->link(__('List Languages', true), array('action' => 'index')); ?> </li>
		<li><?php echo $html->link(__('New Language', true), array('action' => 'add')); ?> </li>
	</ul>
</div>
