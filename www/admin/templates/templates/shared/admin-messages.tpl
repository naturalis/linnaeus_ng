{if !empty($errors)}
<div id="admin-errors">
{section name=error loop=$errors}
<span class="admin-message-error">{$errors[error]}</span><br />
{/section}
</div>
{/if}
{if !empty($messages)}
<div id="admin-messages">
{section name=i loop=$messages}
<span class="admin-message">{$messages[i]}</span><br />
{/section}
</div>
{/if}