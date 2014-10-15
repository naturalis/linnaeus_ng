{include file="../shared/header.tpl"}

<div id="dialogRidge">

	<div id="left">
	</div>

	<div id="content" class="taxon-detail">

		<div id="taxonHeader" class="hasImage">
			<div id="titles" class="full">
				{if $subtitle}
					<h1>{$title}</h1>
					<h2>{$subtitle}</h2>
				{else}
					<h1 class="no-subtitle">{$title}</h1>
					<h2></h2>
				{/if}
                {$text}
			</div>
		</div>
	</div>

	{include file="../shared/_right_column.tpl"}

</div>
{include file="../shared/footer.tpl"}