{include file="../shared/admin-header.tpl"}

<div id="page-main">
<table>
<tr>
	<td colspan="6">{t _s1=$taxon}Common names for taxon "%s":{/t}</td>
</tr>
<tr><td colspan="6">&nbsp;</td></tr>
<tr>
	<th style="width:225px;">{t}common name{/t}</td>
	<th style="width:225px;">{t}transliteration{/t}</td>
	<th style="width:350px;" colspan="2">{t}language{/t}</td>
	<th style="width:55px;">{t}move up{/t}</td>
	<th style="width:65px;">{t}move down{/t}</td>
	<th>delete</td>
</tr>
{section name=i loop=$commonnames}
<tr class="tr-highlight">
	<td>{$commonnames[i].commonname}</td>
	<td>{$commonnames[i].transliteration}</td>
	<td>{$commonnames[i].language_name}</td>
	<td><input type="text" value="{$commonnames[i].language_name}" /></td>
	{if $smarty.section.i.first}
	<td></td>
	{else}
	<td
		style="text-align:center" 
		class="pseudo-a" 
		onclick="taxonCommonNameAction({$commonnames[i].id},'up');">
		&uarr;
	</td>
	{/if}
	{if $smarty.section.i.last}
	<td></td>
	{else}
	<td
		style="text-align:center" 
		class="pseudo-a" 
		onclick="taxonCommonNameAction({$commonnames[i].id},'down');">
		&darr;
	</td>
	{/if}
	<td
		style="text-align:center" 
		class="pseudo-a" 
		onclick="taxonCommonNameAction({$commonnames[i].id},'delete');">
		x
	</td>
</tr>
{/section}
{if $smarty.section.i.total==0}
<tr><td colspan="6">{t}No synonyms have been defined for this taxon.{/t}</td></tr>
{/if}
</table>

<br />
<form method="post" action="" id="theForm">
<input type="hidden" name="action" id="action" value="" />
<input type="hidden" name="commonname_id" id="commonname_id" value="" />
<input type="hidden" name="id" value="{$id}" />
<input type="hidden" name="rnd" value="{$rnd}" />
<table>
	<tr><td colspan="2">{t}Add a new common name:{/t}</td></tr>
	<tr><td style="width:125px">{t}common name:{/t}</td><td><input type="text" name="commonname" maxlength="64" /></td></tr>
	<tr><td>{t}transliteration:{/t}</td><td><input type="text" name="transliteration" maxlength="64" /></td></tr>
	<tr><td>{t}language:{/t}</td><td>
		<select name="language_id" id="language">
		{section name=i loop=$languages}
			<option value="{$languages[i].id}">{$languages[i].language}</option>
		{/section}
		</select>
	</td></tr>
	<tr><td colspan="2">&nbsp;</td></tr>
	<tr><td colspan="2"><input type="submit" value="{t}save{/t}" />&nbsp;<input type="button" onclick="window.open('list.php','_self');" value="{t}back{/t}" /></td></tr>
</table>
</form>
<br />
{t}After you have added a new common name, you will be allowed to provide the name of its language in the various interface languages that your project uses.{/t}</td></tr>
</div>

{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}


