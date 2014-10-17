{include file="../shared/admin-header.tpl"}

<style>
table tr td {
	vertical-align:top;
}
.inline-table {
	margin-left:10px;
	border-collapse:collapse;
}
.inline-table tr td, .inline-table tr th  {
	font-size:10px;
	padding-left:5px;
	background-color:#eee;
}
.inline-table td {
	width:350px;
	border:1px solid #ddd;
}

</style>


<div id="page-main">

<h2>Ovezicht Soortenregister edits</h2>
<h3></h3>

Aantal edits: {$results.count}

<p style="color:red">coming: zoeken, pagineren, verbeterde weergave data, verbeterde weergave user</p>


<table>
{foreach from=$results.data item=v key=k}
    <tr>
    	<td style="width:350px;">
		    {$v.note}
		</td>
    	<td style="width:200px;">
		    {$v.user_id}
		    {$v.user|@replace:' (':'<br />('}
		</td>
    	<td>
		    {$v.last_change_hr}
		</td>
    	<td>
		    <a href="#" onclick="$('#alldata{$k}').toggle();return false;">toon data</a>
		</td>
    <tr>
    
    <tr id="alldata{$k}" style="display:none;border-bottomn:1px solid #666">
    	<td colspan="4">
        	<table class="inline-table"><tr><td>
			    {foreach from=$v.data_before item=b key=kb}
                {if $b|is_array}
                	<p>
	                <b>{$kb}</b><br />
                    {foreach from=$b item=bb key=kbb}
	                &nbsp;&nbsp;{$kbb}: {$bb}<br />
	                {/foreach}
                    </p>
                {else}
					{$kb}: {$b}<br />
                {/if}
                {/foreach}
            </td>
            <td>
			    {foreach from=$v.data_after item=a key=ka}
                {if $a|is_array}
                	<p>
	                <b>{$ka}</b><br />
                    {foreach from=$a item=aa key=kaa}
	                {$kaa}: {$aa}<br />
	                {/foreach}
                    </p>
                {else}
					{$ka}: {$a}<br />
                {/if}
                {/foreach}
			</td></tr></table>
		</td>
    </tr>
{/foreach}
</table>
<p>
	<a href="index.php">terug</a>
</p>

</div>

<script>
$(document).ready(function(e) {
	{foreach from=$tabs item=v key=k}
	currentpublish[{$k}]={if $v.publish==1}true{else}false{/if};
	{/foreach}
});
</script>



{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}