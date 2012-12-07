{include file="../shared/admin-header.tpl"}

<div id="page-main">
<select name="lan">
<option value="24">Dutch</option>
</select>
<table>
<tr><th>{t}identifier (and English translation){/t}</th><th>{t _s1='Dutch'}translation in %s{/t}</th></tr>
{foreach from=$texts item=v}
<tr><td>{$v.text}::{$v.id}</td><td>{$v.translation}</td></tr>
{/foreach}
</table>


		
		{if $prevStart!=-1 || $nextStart!=-1}
			<div id="navigation">
				{if $prevStart!=-1}
				<span class="a" onclick="goNavigate({$prevStart});">< previous</span>
				{/if}
				{if $nextStart!=-1}
				<span class="a" onclick="goNavigate({$nextStart});">next ></span>
				{/if}
			</div>
		{/if}
		
</div>
<form action="" method="post" id="theForm" action="">
</form>

{include file="../shared/admin-footer.tpl"}
