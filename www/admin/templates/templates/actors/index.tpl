{include file="../shared/admin-header.tpl"}

<div id="page-main">
	<table class="alphabet">
		<tr>
			<td>
				Zoek een persoon:
			</td>
			<td>
				<input type="text" name="" id="lookup-input-individual" onkeyup="actorsLookup(this,'lookup_individual');" />
			</td>
			<td>
				{foreach from=$individualAlphabet item=v}
				{if $v.letter}
				<a href="#" class="click-letter" onclick="actorsLookup(this,'lookup_individual_letter','{$v.letter}');">{$v.letter|@strtoupper}</a>
				{/if}
				{/foreach}
				<a href="#" class="click-letter" onclick="actorsLookup(this,'lookup_individual_letter','*');">*</a>
			</td>
		</tr>
		<tr>
			<td>
				Zoek een instelling:
			</td>
			<td>
				<input type="text" name="" id="lookup-input-company" onkeyup="actorsLookup(this,'lookup_company');" />
			</td>
			<td>
				{foreach from=$companyAlphabet item=v}
				{if $v.letter}
				<a href="#" class="click-letter" onclick="actorsLookup(this,'lookup_company_letter','{$v.letter}');">{$v.letter|@strtoupper}</a>
				{/if}
				{/foreach}
				<a href="#" class="click-letter" onclick="actorsLookup(this,'lookup_company_letter','*');">*</a>
			</td>
		</tr>
	</table>
	<p>
		<div id="actor-result-list"></div>
	</p>
	<p>
		<a href="edit.php">nieuwe actor aanmaken</a>
	</p>

</div>

{include file="../shared/admin-footer.tpl"}
