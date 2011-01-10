{include file="../shared/admin-header.tpl"}

<div id="page-main">
<form action="" method="post" id="theForm">
<input type="hidden" name="rnd" value="{$rnd}" />
<input type="hidden" name="id" value="{$gloss.id}" />
<input type="hidden" name="action" id="action" value="" />

<table>
	<tr>
		<td>{t}Language:{/t}</td>
		<td colspan="2">
		<select name="language_id" id="language">
		{section name=i loop=$languages}
			{if $languages[i].language!=''}<option value="{$languages[i].language_id}"{if $languages[i].language_id==$activeLanguage} selected="selected"{/if}>{$languages[i].language}{if $languages[i].language_id==$defaultLanguage} *{/if}</option>
			{/if}
		{/section}
		</select> *
		</td>
	</tr>
	<tr>
		<td>
			{t}Term:{/t}
		</td>
		<td colspan="2">
			<input
				type="text"
				name="term"
				id="term"
				value="{$gloss.term}"
				style="width:200px;"
				maxlength="255"/> *
		</td>
	</tr>
	<tr style="vertical-align:top">
		<td>{t}Definition:{/t}</td>
		<td>
			<textarea
				name="definition"
				id="definition"
				style="width:500px;height:250px;font-size:13px">{$gloss.definition}</textarea>
		</td>
		<td>*</td>
	</tr>
	<tr style="vertical-align:top">
		<td>{t}Synonyms:{/t}</td>
		<td colspan="2">
			<input type="text" name="synonym" id="synonym" value=""/>
			<span class="pseudo-a" id="add" style="padding: 0px 10px 0px 10px;cursor:pointer" onclick="glossAddSynonymToList()">{t}add{/t}</span>
			<div id="synonyms"></div>
		</td>
	</tr>

	<tr style="vertical-align:top">
		<td>{t}Media:{/t}</td>
		<td colspan="2">
		{if $gloss.media|@count==0}
		{t}(no media have been uploaded){/t}
		{else}
		<table>
		{section name=i loop=$gloss.media}
		<tr class="tr-highlight" id="media-row-{$gloss.media[i].id}">
			<td style="width:400px">
				<span
					class="pseudo-a" onclick="allShowMedia('{$session.project.urls.project_media|@escape}{$gloss.media[i].file_name|@escape}','{$gloss.media[i].original_name}');" >{$gloss.media[i].original_name}</span>
			</td>
			<td style="cursor:pointer" onclick="glossMediaDelete({$gloss.media[i].id})">
				<img src="{$baseUrl}admin/media/system/icons/cross.png" />
			</td>
		</tr>
		{/section}
		</table>
		{/if}
		<br />
		<span class="pseudo-a" onclick="$('#action').val('media');glossCheckForm(this);">{t}upload media{/t}</span>
		</td>
	</tr>

	<tr><td colspan="3">&nbsp;</td></tr>		
	<tr>
		<td colspan="3">
			<input type="button" value="{t}save{/t}" onclick="glossCheckForm(this)" />
			<input type="button" value="{t}back{/t}" onclick="window.open('index.php','_top')" />&nbsp;&nbsp;
			{if $gloss.id}
			<input type="button" value="{t}delete{/t}" onclick="glossDelete()" />
			{/if}
		</td>
	</tr>

</table>
</form>
</div>

{literal}
<script type="text/JavaScript">
$(document).ready(function(){

var f = $('#synonyms');

var off = $('#add').offset();
f.offset({left : off.left + $('#add').width() + 50, top: off.top});

});

{/literal}

{section name=i loop=$gloss.synonyms}
glossAddSynonymToList('{$gloss.synonyms[i].synonym|@addslashes}');
{/section}

glossThisTerm = '{$gloss.term|@addslashes}';

</script>

{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}
