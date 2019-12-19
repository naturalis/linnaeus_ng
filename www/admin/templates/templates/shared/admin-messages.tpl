{include file="../shared/_admin-errors.tpl"}
{include file="../shared/_admin-messages.tpl"}
{include file="../shared/_admin-warnings.tpl"}

<script type="text/JavaScript">
$(document).ready(function()
{
	if (typeof noMessageFade == 'undefined' || noMessageFade!=true)
	{
		$('#page-block-messages').fadeOut({$adminMessageFadeOutDelay});	
	}
})
</script>