{function name=printCurrentItem}
	<li class="current">
		{$taxon.label} <span class="editors"><a href="edit.php?id={$taxon.id}">{t}edit{/t}</a></span>
		<ul id="sortable">
		{foreach from=$progeny item=progen}
			{if $progen.child_count==0}
			<li id="progen-{$progen.id}" class="progen childless">
				<span class="grip">&#9617;</span>
				<span class="{if $highlight && $highlight==$progen.id} taxon-highlight{/if}">
					{if $progen.rank_id<$smarty.const.SPECIES_RANK_ID}{$progen.rank}{/if} {$progen.taxon}
					{if $progen.commonname}<span class="common">{$progen.commonname}</span>{/if}
				</span>
			{else}
			<li id="progen-{$progen.id}" class="progen">
				<span class="grip">&#9617;</span>
				<span class="{if $highlight && $highlight==$progen.id} taxon-highlight{/if}">
					<a href="?p={$progen.id}">{$progen.rank} {$progen.taxon}</a>
					{if $progen.commonname}<span class="common">{$progen.commonname}</span>{/if}
				</span>
				<span class="childcount">
					{$progen.child_count}
				</span>
			{/if}
				<span class="editors">
					<a href="edit.php?id={$progen.id}">{t}edit{/t}</a>;
					<a href="media.php?id={$progen.id}">{t}media{/t} ({$progen.number_of_images})</a>;
					<a href="common.php?id={$progen.id}">{t}names{/t} ({$progen.number_of_commonnames})</a>;
					<a href="synonyms.php?id={$progen.id}">{t}synonyms{/t} ({$progen.number_of_synonyms})</a>;
					<a href="taxon.php?id={$progen.id}">{t}pages{/t} ({$progen.number_of_pages})</a>
				</span>

			</li>
		{/foreach}
		</ul>
	</li>
{/function}
{include file="../shared/admin-header.tpl"}
{include file="../shared/admin-messages.tpl"}
<style>
ul {
	list-style-type:none;
	margin-left:-20px;
}
.current {
	list-style-type:disc;
}
.grip {
	cursor:move;
	font-size:14px;
	color:#888;
}
.childcount {
	margin-left:3px;
	color:#555;
}
.childcount:before {
	content: "["; 
} 
.childcount:after {
	content: "]"; 
}
.common {
	color:#777;
}
.editors {
	margin-left:3px;
	font-size:0.8em;
}
.editors a {
	color:#888;
}
.taxon-highlight {
	background-color:#9CF;
}
</style>

<div id="page-main">

	<p>
		<form method="post" id="theForm">
		<input type="hidden" name="p" id="p" value="{$taxon.id}" />
		<input type="hidden" name="toggle" id="toggle" value="{$toggle}" />
		<input type="hidden" name="rnd" value="{$rnd}" />
		<input type="button" value="{t}save new order{/t}" onclick="reOrder()" />
		<a href="#" onclick="$('.peer').toggle();$('#toggle').val($('.peer:first').css('display'));">toggle peers</a>
		</form>
	</p>
	<ul>
		{if $parent.taxon}
		<li class="parent">
			{if $parent.parent_id}
			<a href="?p={$parent.id}">{$parent.label}</a>
			{else}
			{$parent.label}
			{/if}
			{if $parent.commonname}<span class="common">{$parent.commonname}</span>{/if}
			
			<span class="childcount">
				{$peers|@count}
			</span>
		{/if}
			<ul>
			{if $peers}
				{foreach from=$peers item=peer key=peer_key}
					{if $peer.id==$taxon.id}
						{$data=['taxon'=>$taxon,'progeny'=>$progeny]}
						{printCurrentItem data=$data}				
					{else}
						<li class="peer"><a href="?p={$peer.id}">{$peer.rank} {$peer.taxon}</a>
						{if $peer.commonname}<span class="common">{$peer.commonname}</span>{/if}
						<span class="childcount">
							{$peer.child_count}
						</span>
						</li>
					{/if}
				{/foreach}
			{else}
				{$data=['taxon'=>$taxon,'progeny'=>$progeny]}
				{printCurrentItem data=$data}
			{/if}
			</ul>
		{if $parent.taxon}
		</li>
		{/if}
	</ul>
</div>

<script>

function reOrder() {

	$('li[id^=progen-]').each(function () {
		var val=$(this).attr('id').replace('progen-','');
		$('#theForm').append('<input type="hidden" name="newOrder[]" value="'+val+'">').val(val);
	})

	$('#theForm').submit();

}

$(document).ready(function()
{
	allLookupNavigateOverrideUrl('branches.php?p=%s');

	$( "#sortable" ).sortable({
		opacity: 0.6, 
		cursor: 'move',
	}).disableSelection();
	
	$('.peer').toggle( {if $toggle=='none'}false{else}true{/if} );
	
});
</script>

{include file="../shared/admin-footer.tpl"}