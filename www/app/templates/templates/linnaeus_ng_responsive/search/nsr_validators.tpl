{include file="../shared/header.tpl"}

<style>
table.validatorList tr td:nth-child(2)
{
	text-align:right;
}
table.validatorList tr:nth-child(1) td
{
	font-weight:bold;
}
</style>


<div id="dialogRidge">

	<div id="left">

		<div class="treebranchContainer">

			{include file="_photographers.tpl"}

		</div>  

	</div>

	<div id="content">
		<div class="whiteBox">
			<h2>{t}Overzicht validatoren{/t}</h2>
			
			<table class="validatorList">
				<tr>
					<td>{t}Validator{/t}</td>
					<td>{t}foto’s / soorten{/t}</td>
				</tr>
			{foreach $validators v}
				<tr>
					<td><a href="nsr_search_pictures.php?validator={$v.name}">{$v.name}</a></td>
					<td>{$v.picture_count} / {$v.taxon_count}</td>
				</tr>
			{/foreach}
			</table>
		</div>
	</div>

</div>

{include file="../shared/footer.tpl"}

<script type="text/JavaScript">
$(document).ready(function(){
	
	$('title').html('Overzicht validatoren - '+$('title').html());

});
</script>