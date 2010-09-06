<?php /* Smarty version 2.6.26, created on 2010-09-06 18:52:56
         compiled from ../shared/admin-messages.tpl */ ?>
<?php if (! empty ( $this->_tpl_vars['errors'] )): ?>
<div id="page-block-errors">
<?php unset($this->_sections['error']);
$this->_sections['error']['name'] = 'error';
$this->_sections['error']['loop'] = is_array($_loop=$this->_tpl_vars['errors']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['error']['show'] = true;
$this->_sections['error']['max'] = $this->_sections['error']['loop'];
$this->_sections['error']['step'] = 1;
$this->_sections['error']['start'] = $this->_sections['error']['step'] > 0 ? 0 : $this->_sections['error']['loop']-1;
if ($this->_sections['error']['show']) {
    $this->_sections['error']['total'] = $this->_sections['error']['loop'];
    if ($this->_sections['error']['total'] == 0)
        $this->_sections['error']['show'] = false;
} else
    $this->_sections['error']['total'] = 0;
if ($this->_sections['error']['show']):

            for ($this->_sections['error']['index'] = $this->_sections['error']['start'], $this->_sections['error']['iteration'] = 1;
                 $this->_sections['error']['iteration'] <= $this->_sections['error']['total'];
                 $this->_sections['error']['index'] += $this->_sections['error']['step'], $this->_sections['error']['iteration']++):
$this->_sections['error']['rownum'] = $this->_sections['error']['iteration'];
$this->_sections['error']['index_prev'] = $this->_sections['error']['index'] - $this->_sections['error']['step'];
$this->_sections['error']['index_next'] = $this->_sections['error']['index'] + $this->_sections['error']['step'];
$this->_sections['error']['first']      = ($this->_sections['error']['iteration'] == 1);
$this->_sections['error']['last']       = ($this->_sections['error']['iteration'] == $this->_sections['error']['total']);
?>
<span class="message-error"><?php echo $this->_tpl_vars['errors'][$this->_sections['error']['index']]; ?>
</span><br />
<?php endfor; endif; ?>
</div>
<?php endif; ?>
<?php if (! empty ( $this->_tpl_vars['messages'] )): ?>
<div id="page-block-messages">
<?php unset($this->_sections['i']);
$this->_sections['i']['name'] = 'i';
$this->_sections['i']['loop'] = is_array($_loop=$this->_tpl_vars['messages']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['i']['show'] = true;
$this->_sections['i']['max'] = $this->_sections['i']['loop'];
$this->_sections['i']['step'] = 1;
$this->_sections['i']['start'] = $this->_sections['i']['step'] > 0 ? 0 : $this->_sections['i']['loop']-1;
if ($this->_sections['i']['show']) {
    $this->_sections['i']['total'] = $this->_sections['i']['loop'];
    if ($this->_sections['i']['total'] == 0)
        $this->_sections['i']['show'] = false;
} else
    $this->_sections['i']['total'] = 0;
if ($this->_sections['i']['show']):

            for ($this->_sections['i']['index'] = $this->_sections['i']['start'], $this->_sections['i']['iteration'] = 1;
                 $this->_sections['i']['iteration'] <= $this->_sections['i']['total'];
                 $this->_sections['i']['index'] += $this->_sections['i']['step'], $this->_sections['i']['iteration']++):
$this->_sections['i']['rownum'] = $this->_sections['i']['iteration'];
$this->_sections['i']['index_prev'] = $this->_sections['i']['index'] - $this->_sections['i']['step'];
$this->_sections['i']['index_next'] = $this->_sections['i']['index'] + $this->_sections['i']['step'];
$this->_sections['i']['first']      = ($this->_sections['i']['iteration'] == 1);
$this->_sections['i']['last']       = ($this->_sections['i']['iteration'] == $this->_sections['i']['total']);
?>
<span class="admin-message"><?php echo $this->_tpl_vars['messages'][$this->_sections['i']['index']]; ?>
</span><br />
<?php endfor; endif; ?>
</div>
<?php endif; ?>