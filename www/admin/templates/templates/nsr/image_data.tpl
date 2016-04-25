{include file="../shared/admin-header.tpl"}

{assign $concept.id=$image.taxon.id}

{include file="../shared/left_column_tree.tpl"}

{assign image $image.data[0]}

<div id="page-main">

<h2><span style="font-size:12px">afbeelding:</span> {$image.image}</h2>
<h3><span style="font-size:12px;font-style:normal">concept:</span> {$image.taxon}</h3>


<img class="speciesimage" style="margin:10px 0 5px 0" src="http://images.naturalis.nl/160x100/{$image.thumb}" />



<p>
	<form method="post">
    <input type="hidden" name="action" value="save" />
    <input type="hidden" name="rnd" value="{$rnd}" />
    <input type="hidden" name="media_id" value="{$image.id}" />
	<table>

		<tr><th>taxon:</th>
			<td>
                <span id="taxon">{$image.taxon}</span>
                <a class="edit" style="margin-left:0" href="#" onclick="dropListDialog(this,'Taxon');return false;" rel="taxon_id">edit</a> *
				<input type="hidden" name="taxon_id" id="taxon_id" value="{$image.taxon_id}" droplistminlength="3" />
			</td>
		</tr>

		<tr>
        	<th title="deze afbeelding wordt altijd voor deze soort groot getoond op de taxon-pagina. als een taxon geen bannerafbeelding heeft, wordt standaard de meest recente afbeelding getoond.">
        		banner-afbeelding:
        	</th>
			<td>
                <input type="checkbox" name="overview_image"{if $image.overview_image==1} checked="checked"{/if}>
			</td>
		</tr>


        {foreach $image.meta v k}
	    <tr>
        	<td style="text-align:right">
		        {$v.sys_label}:
			</td>
        	<td>
            	{if $v.meta_date}
		        <input name="values[{$v.id},meta_date]" maxlength="19" class=medium type=text value="{$v.meta_date}" />
                <span class="small">(datum; YYYY-MM-DD HH:MM:SS)</span>
                {elseif $v.meta_number}
		        <input name="values[{$v.id},meta_number]" maxlength="24" class=small style="text-align:right" type=text value="{$v.meta_number}" />
                <span class="small">(getal)</span>
                {else}
		        <input name="values[{$v.id},meta_data]" maxlength="1000" class=large type=text value="{$v.meta_data}" />
                <span class="small">(tekst)</span>
                {/if}
			</td>
        </tr>
        {/foreach}
        {foreach $meta_rest v k}
	    <tr>
        	<td style="text-align:right">
		        {$v}:
			</td>
        	<td>
		        <input name="new[{$v}]" maxlength="1000" class=large type=text value="" />
                <span class="small">
                <label><input type=radio name=type[{$v}] value="meta_date">(datum; YYYY-MM-DD HH:MM:SS)</label>
                <label><input type=radio name=type[{$v}] value="meta_number">(getal)</label>
                <label><input type=radio name=type[{$v}] value="meta_data" checked="checked">(tekst)</label>
                </span>
			</td>
        </tr>
        {/foreach}        
	</table>
    <input type="submit" value="opslaan" />
    </form>

</p>

<p>
	<a href="images.php?id={$image.taxon_id}">terug</a>
</p>

</div>

<script type="text/JavaScript">
$(document).ready(function()
{
	$('#page-block-messages').fadeOut(3000);
} );
</script>

{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}