{include file="../shared/header.tpl"}
{include file="../shared/flexslider.tpl"}
<div id="dialogRidge">

	<div id="left">

		<div id="treebranchContainer">

			{include file="_photographers.tpl"}

		</div>  

	</div>

	<div id="content">
		<div class="whiteBox">
			<h2>{t}Overzicht validatoren{/t}</h2>
			<h4>{t}Validator (foto’s/soorten){/t}</h4>
			<table class="validatorList">
			{foreach from=$validators item=v}
				<tr>
					<td><a href="nsr_search_pictures.php?validator={$v.validator}">{$v.validator}</a></td>
					<td>({$v.total} / {$v.taxon_count})</td>
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