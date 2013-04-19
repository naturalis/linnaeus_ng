{include file="../shared/header.tpl"}
{include file="_topic.tpl"}
{include file="../shared/footer.tpl"}

{* literal}
<script type="text/JavaScript">
$(document).ready(function(){
{/literal}
	$('body').css('background-position-y','115px');
	$('body').css('background','url(\'{$session.app.project.urls.systemMedia}background_blurry.jpg\')');
{literal}
});
</script>
{/literal *}