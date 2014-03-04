{include file="../shared/header.tpl"}

<div id="dialogRidge">

	<div id="left">

		<div id="toolboxContainer">
            <h2>Toolbox</h2>
		</div>  

		<div id="treebranchContainer">

			<div class="top5">
			</div>
			
			<br />

			<div class="top5">
				<h2>Top 5 validatoren</h2>
				<h4>Validator (foto’s/soorten)</h4>
				<i>nog niet weten te exporteren uit de beeldbankdump</i>
			</div>

		</div>  

	</div>

	<div id="content">

		<h2>Overzicht fotografen</h2>
		<h4>Fotograaf (foto’s/soorten)</h4>
		<br>
			<ol>
			{foreach from=$photographers item=v}
				{assign var=photograhper_name value=", "|explode:$v.meta_data} 
				<li>
					<a href="nsr_search_pictures.php?photographer={$v.meta_data}">{$photograhper_name[1]} {$photograhper_name[0]} ({$v.total} / {$v.taxon_count})</a>
				</li>
			{/foreach}
			</ol>

	</div>

	{include file="../shared/_right_column.tpl"}

</div>

{include file="../shared/footer.tpl"}