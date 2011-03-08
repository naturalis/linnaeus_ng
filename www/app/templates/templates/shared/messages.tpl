{if !empty($errors)}
<div id="errors">
{section name=error loop=$errors}
<span class="error">{$errors[error]}</span><br />
{/section}
</div>
{/if}
{if !empty($messages)}
<div id="messages">
{section name=i loop=$messages}
<span class="message">{$messages[i]}</span><br />
{/section}
</div>
{/if}