{if !empty($errors)}
<div id="errors">
{foreach $errors v}
<span class="error">{$v}</span><br />
{/foreach}
</div>
{/if}
{if !empty($messages)}
<div id="messages">
{foreach $messages v}
<span class="message">{$v}</span><br />
{/foreach}
</div>
{/if}