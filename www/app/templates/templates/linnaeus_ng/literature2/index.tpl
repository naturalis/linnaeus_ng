{include file="../shared/header.tpl"}

<div id="header-titles">
	<span id="header-title" style="white-space: nowrap;">{t}Literature{/t}</span>
</div>


<div id="page-main">
	<table class="alphabet">
		<tr>
			<td>
				{t}Search by title:{/t}
			</td>
			<td>
				<input type="text" name="" id="lookup-input-title" placeholder="{t}Type to find{/t}" onkeyup="lit2Lookup(this,'lookup_title');" />
			</td>
			<td>
				{foreach from=$titleAlphabet item=v}
				<a href="#" class="click-letter" onclick="lit2Lookup(this,'lookup_title_letter','{$v.letter}');return false;">{$v.letter|@strtoupper}</a>
				{/foreach}
			</td>
		</tr>
		<tr>
			<td>
				{t}Search by author:{/t}
			</td>
			<td>
				<input type="text" name="" id="lookup-input-author" placeholder="{t}Type to find{/t}" onkeyup="lit2Lookup(this,'lookup_author');" />
			</td>
			<td>
				{foreach from=$authorAlphabet item=v}
				{if $v.letter}
				<a href="#" class="click-letter" onclick="lit2Lookup(this,'lookup_author_letter','{$v.letter}');return false;">{$v.letter|@strtoupper}</a>
				{/if}
				{/foreach}
			</td>
		</tr>
	</table>

	<p>
		<div id="lit2-result-list"></div>
	</p>

</div>

<script>
$(document).ready(function()
{
{if $prevSearch.search_title!=''}
$('#lookup-input-title').val( '{$prevSearch.search_title|@escape}' ).trigger('onkeyup');
{else if $prevSearch.search_author!=''}
$('#lookup-input-author').val( '{$prevSearch.search_author|@escape}' ).trigger('onkeyup');
{/if}
});
</script>


<div class="inline-templates" id="reference-table">
<!--
<table>
    <tr>
        <td style="width:200px">{t}authors{/t}</td>
        <td style="width:75px;text-align:right;padding-right:10px;">{t}year{/t}</td>
        <td style="width:500px">{t}reference{/t}</td>
    </tr>
    %TBODY%
</table>
-->
</div>

<div class="inline-templates" id="reference-table-row">
<!--
<tr class="tr-highlight" style="vertical-align:top;">
    <td><a href="reference.php?id=%ID%">%AUTHOR%</a>
    </td>
    <td style="text-align:right;padding-right:10px;">%YEAR%</td>
    <td>%REFERENCE%</td>
</tr>
-->
</div>

<div class="inline-templates" id="string-highlight">
<!--
    <span style="background-color:yellow">%STRING%</span>
-->
</div>

{include file="../shared/footer.tpl"}
