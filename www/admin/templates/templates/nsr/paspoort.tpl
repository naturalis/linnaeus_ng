{include file="../shared/admin-header.tpl"}

<style>
.passport-content {
	display:none;
	border:1px solid #999;
	padding: 5px 10px 0 10px;
	margin:5px 0 25px 0;
}
</style>

<div id="page-main">

<h2>{$concept.taxon}</h2>
<h3>paspoorten</h3>

<p>

	<ul>
	{foreach from=$tabs item=v key=k}
	<li>
		<div class="passport-title"><a href="#" onclick="$('#body-{$k}').toggle();return false;">{$v.title}</a>{if $v.content|@strlen>0} *{/if}</div>
		<div class="passport-content" id="body-{$k}"><a href="#" class="edit" onclick="alert('soon!');return false;" style="margin-left:0;">edit</a>{$v.content}</div>
	</li>
	{/foreach}
	</ul>

</p>

<p>
	<a href="taxon.php?id={$concept.id}">terug</a>
</p>

</div>

{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}