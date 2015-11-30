{include file="../shared/header.tpl"}

<div id="dialogRidge">

	<div id="left">

		<div id="treebranchContainer">

			{include file="_photographers.tpl"}

		</div>  

	</div>

	<div id="content">

		<h2>{t}Overzicht validatoren{/t}</h2>
		<h4>{t}Validator (foto’s/soorten){/t}</h4>
		<br>
			<ol>
			{foreach $validators v}
				<li>
					<a href="nsr_search_pictures.php?validator={$v.validator}">{$v.validator} ({$v.total} / {$v.taxon_count})</a>
				</li>
			{/foreach}
			</ol>

	</div>

	{include file="../shared/_right_column.tpl"}

</div>

{include file="../shared/footer.tpl"}

<script type="text/JavaScript">
$(document).ready(function()
{
	$('title').html('Overzicht validatoren - '+$('title').html());
});
</script>