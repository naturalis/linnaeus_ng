{if !empty($warnings)}
<div id="page-block-warnings">
{section name=warning loop=$warnings}
<span class="message">{$warnings[warning]}</span><br />
{/section}
</div>
{/if}