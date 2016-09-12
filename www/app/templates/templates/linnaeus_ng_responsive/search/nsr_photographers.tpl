{include file="../shared/header.tpl"}
{include file="../shared/flexslider.tpl"}
<div id="dialogRidge">

	<div id="left">

		<div id="treebranchContainer">

			{include file="_validators.tpl"}

		</div>  

	</div>

	<div id="content">
		<div class="whiteBox">
			<h2>{t}Overzicht fotografen{/t}</h2>
			<h4>{t}Fotograaf (foto’s/soorten){/t}</h4>
			<table class="photographersList">
			{foreach from=$photographers item=v}
				<tr>
					<td><a href="nsr_search_pictures.php?photographer={$v.photographer}">{$v.photographer}</a></td>
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
	
	$('title').html('{t}Overzicht fotografen{/t} - '+$('title').html());

});
</script>