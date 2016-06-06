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



<!-- div id="page-main" class="template-index">

	<p>
    {t}Browse by title:{/t}<br />
    {foreach from=$titleAlphabet item=v}
    <a href="#" class="click-letter">{$v.letter|@strtoupper}</a>
    {/foreach}
    </p>

	<p>
    {t}Browse by author:{/t}<br />
    {foreach from=$authorAlphabet item=v}
    {if $v.letter}
    <a href="#" class="click-letter">{$v.letter|@strtoupper}</a>
    {/if}
    {/foreach}
	</p>

</div -->

{include file="../shared/footer.tpl"}
