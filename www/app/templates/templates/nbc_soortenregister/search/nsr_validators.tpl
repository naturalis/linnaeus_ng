{include file="../shared/header.tpl"}

<div id="dialogRidge">

	<div id="left">

		<div id="treebranchContainer">

			{include file="_photographers.tpl"}

		</div>  

	</div>

	<div id="content">

		<h2>Overzicht validatoren</h2>
		<h4>Validator (foto’s/soorten)</h4>
		<br>
			<ol>
			{foreach from=$validators item=v}
				<li>
					<a href="nsr_search_pictures.php?validator={$v.validator}">{$v.validator} ({$v.total} / {$v.taxon_count})</a>
				</li>
			{/foreach}
			</ol>

	</div>

	{include file="../shared/_right_column.tpl"}

</div>

{include file="../shared/footer.tpl"}