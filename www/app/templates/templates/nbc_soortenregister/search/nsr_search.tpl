{include file="../shared/header.tpl"}

<div id="dialogRidge">

	{include file="_left_column.tpl"}
    
	<div id="content">
	
		<div id="results">
			<p>
				<h2>
				Gezocht op "{$search.search}": <span id="resultcount-header">{$results|@count}</span>
				</h2>

{if $type=='extended'}
<a href="?search={$search.search}&type=concise" style="color:#F3C">versie 1</a>
versie 2
{else}
versie 1
<a href="?search={$search.search}&type=extended" style="color:#F3C">versie 2</a>
{/if}

			</p>
			<p>
				{if $results}

				{if $type=='extended'}
					
					{foreach from=$results item=res}
					<div style="vertical-align:top;width:500px;border-bottom:1px solid #999;padding-bottom:10px;margin-bottom:10px">
						<img src="{$res.overview_image}" style="height:100px;max-width:140px;float:right"/>
						
						<strong><a href="../species/taxon.php?id={$v.taxon_id}">{$res.taxon}</a></strong><br />
						{$res.dutch_name}<br /><br />
						Status voorkomen: {$res.presence_information_index_label} {$res.presence_information_title}
					</div>
					{/foreach}
					
				{else}

					<ol>
						{foreach from=$results item=res}
						<li style="margin-bottom:5px">
						<a class="result" style="font-weight:bold" href="../species/taxon.php?id={$res.taxon_id}">{$res.taxon}</a> {* $res.match_percentage *}
						{if $res.dutch_name}<br />{$res.dutch_name}{/if}
						</li>
						{/foreach}
					</ol>
					
				{/if}

				
				{else}
				Niets gevonden.
				{/if}




			</p>
		</div>
		
	</div>

	{include file="../shared/_right_column.tpl"}

</div>

{literal}
<script type="text/JavaScript">
$(document).ready(function(){
{/literal}
	var n=parseInt($('#resultcount-header').html());
	$('#resultcount-header').html($('#resultcount-header').html()+' '+(n==1 ? 'resultaat' : 'resultaten'));
{literal}
});
</script>
{/literal}

{include file="../shared/footer.tpl"}
