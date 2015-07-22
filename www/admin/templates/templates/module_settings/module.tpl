{include file="../shared/admin-header.tpl"}

<div id="page-main">

<h3>module settings for "{$module}"</h3>

<form method="post">
<input type="hidden" name="action" value="save" />
<input type="hidden" name="module" value="{$module}" />
<ul>
{foreach $settings v}
	<li>
    	{$v.setting}
    	<input type="text" value="{$v.value}" name="setting[{$v.id}]" />
    	<!-- input type="text" value="{$v.item_type}" />:
    	<input type="text" value="{$v.lng_id}" / -->
	</li>
{/foreach}
</ul>

new: <input type="text" value="" name="new_setting" />: <input type="text" value="" name="new_value" />

<p>
	<input type="submit" value="save" />
</p>

</form>

<a href="index.php">index</a>

</div>

{include file="../shared/admin-messages.tpl"}

<script>
$(document).ready(function()
{
	$('#page-block-messages').fadeOut(3000);
});
</script>

{include file="../shared/admin-footer.tpl"}
