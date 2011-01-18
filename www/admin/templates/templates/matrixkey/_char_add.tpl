{include file="../shared/_admin-head.tpl"}
<body>
<div id="page-main">

<form name="theForm" id="theForm" action="" method="post">
<input type="hidden" name="rnd" value="{$rnd}" />
<table>
<tr>
	<th>{t}Characteristic{/t} 
{section name=i loop=$languages}
{if $languages[i].def_language=='1'}
	({$languages[i].language} *)
{/if}
{/section}
	</th>
{if $languages|@count>1}		
	<td colspan="2" id="project-language-tabs">(languages)</td>
{/if}
</tr>

<tr>
	<td>
		<input 
			type="text" 
			maxlength="64" 
			id="default-{$pages[i].sections[j].id}"
			onblur="taxonSaveSectionTitle({$pages[i].sections[j].id},this.value,'default')" />
	</td>
	<td>
		<input 
			type="text" 
			maxlength="64" 
			id="other-{$pages[i].sections[j].id}"
			onblur="taxonSaveSectionTitle({$pages[i].sections[j].id},this.value,'other')" />
	</td>
	<tr>
		<td></td>
		<td></td>
	</tr>
</tr>
</table>
</form>

</div>

<script type="text/javascript">
{literal}
$(document).ready(function(){
{/literal}
taxonActiveView = 'sections';
{section name=i loop=$languages}
allAddLanguage([{$languages[i].language_id},'{$languages[i].language}',{if $languages[i].def_language=='1'}1{else}0{/if}]);
{/section}

allActiveLanguage = {if $languages[1].language_id!=''}{$languages[1].language_id}{else}false{/if};
allDrawRankLanguages();

{literal}
});
{/literal}
</script>



</body>