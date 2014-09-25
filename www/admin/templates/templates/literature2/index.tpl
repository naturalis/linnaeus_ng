{include file="../shared/admin-header.tpl"}

<div id="page-main">
	<table class="alphabet">
		<tr>
			<td>
				Zoek op titel:
			</td>
			<td>
				<input type="text" name="" id="lookup-input-title" onkeyup="lit2Lookup(this,'lookup_title');" />
				
			</td>
			<td>
				{foreach from=$titleAlphabet item=v}
				<a href="#" class="click-letter" onclick="lit2Lookup(this,'lookup_title_letter','{$v.letter}');">{$v.letter|@strtoupper}</a>
				{/foreach}
			</td>
		</tr>
		<tr>
			<td>
				Zoek op auteur:
			</td>
			<td>
				<input type="text" name="" id="lookup-input-author" onkeyup="lit2Lookup(this,'lookup_author');" />
			</td>
			<td>
				{foreach from=$authorAlphabet item=v}
				{if $v.letter}
				<a href="#" class="click-letter" onclick="lit2Lookup(this,'lookup_author_letter','{$v.letter}');">{$v.letter|@strtoupper}</a>
				{/if}
				{/foreach}
			</td>
		</tr>
	</table>

	<p>
		<a href="edit.php">nieuwe literatuurreferentie aanmaken</a>
	</p>

	<p>
		<div id="lit2-result-list"></div>
	</p>

</div>

{include file="../shared/admin-footer.tpl"}
