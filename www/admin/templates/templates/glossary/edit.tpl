{include file="../shared/admin-header.tpl"}

<div id="page-main">

<form action="" method="post" id="theForm" action="edit.php">
<input type="hidden" name="rnd" value="{$rnd}" />
<input type="hidden" name="id" id="id" value="{$gloss.id}" />
<input type="hidden" name="action" id="action" value="" />
{if $languages|@count==1}
<input type="hidden" name="language_id" id="language_id" value="{$languages[0].language_id}" />
{/if}
    <table>
        <tr>
            <td colspan="2">
                <input type="button" value="{t}save{/t}" onclick="$('#action').val('save');glossCheckForm();" />
                {* <input type="button" value="{t}save and preview{/t}" onclick="$('#action').val('preview');glossCheckForm();" /> *}
                {if $gloss.id}
                <input type="button" value="{t}delete{/t}" onclick="glossDelete()" />
                {/if}
            </td>
        </tr>
        <tr><td colspan="2">&nbsp;</td></tr>		
    
    {if $languages|@count > 1}
        <tr>
            <td>{t}Language:{/t}</td>
            <td>
                <select name="language_id" id="language">
                {foreach $languages v i}
                    {if $v.language!=''}
                    	<option value="{$v.language_id}"{if $v.language_id==$activeLanguage} selected="selected"{/if}>{$v.language}{if $v.language_id==$defaultLanguage} *{/if}</option>
                    {/if}
                {/foreach}
                </select> *
            </td>
        </tr>
    {/if}
        <tr>
            <td>
                {t}Term:{/t}
            </td>
            <td>
                <input
                    type="text"
                    name="term"
                    id="term"
                    value="{$gloss.term}"
                    maxlength="255"/> *
            </td>
        </tr>
  
    
        <tr style="vertical-align:top">
            <td>
                {t}Alternative forms:{/t}
            </td>
            <td>
                <input type="text" name="synonym" id="synonym" value="" />
                <input type="button" onclick="glossDoAddSynonym();" value="{t}add form{/t}">
                {t}(alternative forms are also linked to this lemma by the hotwords-function){/t}
                <p id="synonyms"></p>
            </td>
        </tr>
    </table>    
    
    <table>
        <tr style="vertical-align:top">
            <td>{t}Definition:{/t} *</td>
        </tr>
        <tr style="vertical-align:top">
            <td>
                <textarea
                    name="definition"
                    id="definition">{$gloss.definition}</textarea>
            </td>
        </tr>
    </table>

	{if $use_media}
    <p>
		<span class="a" onclick="$('#action').val('media');glossCheckForm();">{t}Edit multimedia{/t}</span>	
	</p>
	{/if}

</form>
</div>

<script type="text/JavaScript">
$(document).ready(function()
{
	{if $gloss.synonyms}
		{section name=i loop=$gloss.synonyms}
		glossAddSynonymToList('{$gloss.synonyms[i].synonym|@addslashes}');
		{/section}
	{else}
		glossUpdateSynonyms();
	{/if}
	glossThisTerm = '{$gloss.term|@addslashes}';
	initTinyMce(false,false);
});
</script>

{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}
