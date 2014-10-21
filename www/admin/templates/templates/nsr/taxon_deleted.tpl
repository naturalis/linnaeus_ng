{include file="../shared/admin-header.tpl"}

<div id="page-main">

<h2>Taxa gemarkeerd als verwijderd <span style="font-size:12px;font-style:normal">({$concepts|@count})</span></h2>

<p>
	<ul>
    {foreach from=$concepts item=v key=k}
    <li><a href="taxon.php?id={$v.id}">{$v.taxon} ({$v.rank})</a></li>
    {/foreach}
    </ul>
</p>
<p>
	<a href="index.php">terug</a>
</p>

</div>

<script type="text/JavaScript">
$(document).ready(function() {
	
	if(jQuery().prettyPhoto) {
		
	 	$("a[rel^='prettyPhoto']").prettyPhoto( { 
	 		opacity: 0.70, 
			animation_speed:50,
			show_title: false,
	 		overlay_gallery: false,
	 		social_tools: false,
			changepicturecallback:function() { prettyPhotoCycle(); }
	 	} );
	}
	
} );
</script>

{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}