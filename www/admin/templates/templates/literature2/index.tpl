{include file="../shared/admin-header.tpl"}

<div id="page-main">
	<table class="alphabet">
		<tr>
			<td>
				Search by title:
			</td>
			<td>
				<input type="text" name="" id="lookup-input-title" onkeyup="lit2Lookup(this,'lookup_title');" />

			</td>
			<td>
				{foreach from=$titleAlphabet item=v}
				<a href="#" class="click-letter" onclick="lit2Lookup(this,'lookup_title_letter','{$v.letter}');return false;">{$v.letter|@strtoupper}</a>
				{/foreach}
			</td>
		</tr>
		<tr>
			<td>
				Search by author:
			</td>
			<td>
				<input type="text" name="" id="lookup-input-author" onkeyup="lit2Lookup(this,'lookup_author');" />
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
		<a href="edit.php">new reference</a><br />
		<a href="bulk_upload.php">bulk upload & matching</a><br />
		<a href="publication_types.php">publication types</a>
		{if $incomplete > 0}
			<br /><a href="check.php">{t}incompletely parsed references{/t}</a>
		{/if}
	</p>

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

	{if !$CRUDstates.can_update}
	setItemUrl('view.php');
	{/if}


});
</script>

{include file="../shared/admin-footer.tpl"}
