{include file="../shared/header.tpl"}
<!-- {include file="../shared/flexslider.tpl"} -->

<style>
table.photographersList tr td:nth-child(2)
{
	text-align:right;
}
table.photographersList tr:nth-child(1) td
{
	font-weight:bold;
}
</style>

<div id="dialogRidge">

	<div id="left">

		<div id="treebranchContainer">

			{include file="_validators.tpl"}

		</div>  

	</div>

	<div id="content">
		<div class="whiteBox">
			<h2>{t}Overzicht fotografen{/t}</h2>
			
			<table class="photographersList">
				<tr>
					<td>{t}Fotograaf{/t}</td>
					<td>{t}foto’s / soorten{/t}</td>
				</tr>
			{foreach $photographers v}
				<tr>
					<td><a href="nsr_search_pictures.php?photographer={$v.photographer}">{$v.photographer}</a></td>
					<td>{$v.total} / {$v.taxon_count}</td>
				</tr>
			{/foreach}
			</table>
		</div>
	</div>

</div>

{include file="../shared/footer.tpl"}

<script type="text/JavaScript">
$(document).ready(function(){
	
	$('title').html('{t}Overzicht fotografen{/t} - '+$('title').html());

});
</script>