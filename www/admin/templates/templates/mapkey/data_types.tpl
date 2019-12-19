{include file="../shared/admin-header.tpl"}

<div id="page-main">
{t}Below, you can define up to ten types of geographically organised data. Once defined, you can specify locations on the map for each species, for every data type.{/t} {t}Text you enter is automatically saved when you leave the input field.{/t}
<br /><br />
<table>
<tr>
{if $languages|@count > 1}
{section name=i loop=$languages}
{if $languages[i].def_language=='1'}
	<td>{$languages[i].language} *</td>
{/if}
{/section}
<td colspan="2" id="project-language-tabs">(languages)</td>
{/if}
</tr>
{if $types|@count==0}
<tr><td colspan="2">{t}(no types have been defined yet){/t}</td></tr>
{/if}
{section name=i loop=$types}
	<tr class="tr-highlight">
			<td>
				<input 
					type="text" 
					id="default-{$types[i].id}" 
					maxlength="64" 
					onblur="mapSaveTypelabel({$types[i].id},this.value,'default')" />
			</td>
			{if $languages|@count > 1}
			<td>
				<input 
					type="text" 
					id="other-{$types[i].id}" 
					maxlength="64" 
					onblur="mapSaveTypelabel({$types[i].id},this.value,'other')" />
			</td>
			{/if}
			<td><input id="color-{$types[i].id}" class="color" style="width:50px;font-size:inherit" onchange="mapSaveTypeColour({$types[i].id},this.value);"></td>

		{if $smarty.section.i.first}
		<td></td>
		{else}
		<td
			style="text-align:center;width:15px" 
			class="a" 
			onclick="mapMoveType({$types[i].id},'up');">
			&uarr;
		</td>
		{/if}
		{if $smarty.section.i.last}
		<td></td>
		{else}
		<td
			style="text-align:center;width:15px" 
			class="a" 
			onclick="mapMoveType({$types[i].id},'down');">
			&darr;
		</td>
		{/if}

			<td style="text-align:center;width:15px" class="a" onclick="mapDeleteType({$types[i].id},$('#default-{$types[i].id}').val());">x</td>
		</tr>
{/section}
</table>
<br />
{if $languages|@count==0}
{t}You have to define at least one language in your project before you can add any categories.{/t} <a href="../projects/data.php">{t}Define languages now.{/t}</a>
{else}
<form method="post" action="" id="theForm">
<input type="hidden" name="id" value="" id="id" />
<input type="hidden" name="action" value="" id="action" />
{if $types|@count<$maxTypes}
{t}Add a new data type:{/t} <input type="text" maxlength="32" id="new_type" name="new_type" value="" />
<input type="hidden" name="rnd" value="{$rnd}" />
<input type="submit" value="{t}save{/t}" />
{/if}
</form>
{/if}

</div>

<script type="text/javascript">
{literal}
$(document).ready(function(){
{/literal}
allActiveView = 'geotypes';
{section name=i loop=$languages}
allAddLanguage([{$languages[i].language_id},'{$languages[i].language}',{if $languages[i].def_language=='1'}1{else}0{/if}]);
{/section}
allActiveLanguage = {if $languages[1].language_id!=''}{$languages[1].language_id}{else}false{/if};
allDrawLanguages();
mapGetTypeLabels(allDefaultLanguage);
mapGetTypeLabels(allActiveLanguage);
mapGetTypeColours();
{literal}
});
{/literal}
</script>

{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}