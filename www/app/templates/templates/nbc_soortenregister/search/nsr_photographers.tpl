{include file="../shared/header.tpl"}

<div id="dialogRidge">

	<div id="left">

		<div id="toolboxContainer">
            <h2>Toolbox</h2>
		</div>  

		<div id="treebranchContainer">

			<div class="top5">
				<h2>Top 5 fotografen</h2>
				<h4>Fotograaf (foto’s/soorten)</h4>
				<ul>
				{foreach from=$photographers item=v name=foo}
					{if $smarty.foreach.foo.index < 5}
					{assign var=photograhper_name value=", "|explode:$v.meta_data} 
					<li>
						<a href="nsr_search_pictures.php?photographer={$v.meta_data}">{$photograhper_name[1]} {$photograhper_name[0]} ({$v.total} / {$v.taxon_count})</a>
					</li>
					{/if}
				{/foreach}
				</ul>
				<p>
					<a href="nsr_search_pictures.php?show=photographers"><i>Bekijk volledige lijst</i></a>
				</p>
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

		<div>
		
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