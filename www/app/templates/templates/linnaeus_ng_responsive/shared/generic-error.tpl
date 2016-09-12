{include file="../shared/header.tpl"}
{include file="../shared/messages.tpl"}
<div id="page-main">

	<div id="left">
	</div>

	<div id="content" class="taxon-detail">
		<div id="taxonHeader" class="hasImage">
			<div id="titles" class="full">
					<h1>{t}Fout opgetreden{/t}</h1>
			</div>

		</div>
		<p>
			{$message}
		</p>	
		<p>
			<a href="/linnaeus_ng/app/views/search/nsr_search_extended.php">{t}Opnieuw zoeken{/t}</a>
		</p>	
	</div>
</div>
{include file="../shared/footer.tpl"}
