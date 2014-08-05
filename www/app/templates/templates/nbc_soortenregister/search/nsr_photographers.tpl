{include file="../shared/header.tpl"}

<div id="dialogRidge">

	<div id="left">

		<div id="treebranchContainer">

			{include file="_validators.tpl"}

		</div>  

	</div>

	<div id="content">

		<h2>Overzicht fotografen</h2>
		<h4>Fotograaf (foto’s/soorten)</h4>
		<br>
			<ol>
			{foreach from=$photographers item=v}
				<li>
					<a href="nsr_search_pictures.php?photographer={$v.photographer}">{$v.photographer} ({$v.total} / {$v.taxon_count})</a>
				</li>
			{/foreach}
			</ol>

	</div>

	{include file="../shared/_right_column.tpl"}

</div>

{include file="../shared/footer.tpl"}

<script type="text/JavaScript">
$(document).ready(function(){
	
	$('title').html('Overzicht fotografen - '+$('title').html());

});
</script>