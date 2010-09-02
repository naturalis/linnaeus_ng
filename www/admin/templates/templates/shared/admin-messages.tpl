{if !empty($errors)}
<div id="page-block-errors">
{section name=error loop=$errors}
<span class="message-error">{$errors[error]}</span><br />
{/section}
</div>
{/if}
{if !empty($messages)}
<div id="page-block-messages">
{section name=i loop=$messages}
<span class="admin-message">{$messages[i]}</span><br />
{/section}
</div>
{/if}