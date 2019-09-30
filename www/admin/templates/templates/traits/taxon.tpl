{include file="../shared/admin-header.tpl"}

<!--

  ["max_length"]=>
  ["can_include_comment"]=>
  ["can_have_range"]=>
  ["date_format_format_hr"]=>

  ["type_sysname"]=>
  ["type_allow_values"]=>
  ["type_allow_select_multiple"]=>
  ["type_allow_max_length"]=>
  ["type_allow_unit"]=>
  ["value_count"]=>

-->
<style>

li.values:hover {
	background-color:#eee;
}

</style>

<div id="page-main">

{if !$concept || !$group}
    <p>
        {if !$concept}No concept selected.{/if}
    </p>
    <p>
	    {if !$group}No trait group selected.{/if}
    </p>
{else}

    <h2>
        <span style="font-size:12px;font-style:normal">{$group.sysname}:</span>
        {$concept.taxon}
    </h2>
    <p>{$group.description}</p>
    <p>
	    {if $group.parent}(parent: <a href="taxon.php?id={$concept.id}&group={$group.parent.id}">{$group.parent.sysname}</a>)<br />{/if}
    </p>
    {if $group.groups|@count>0}
    <p>
        subgroups:
        <ul>
        {foreach from=$group.groups item=v}
            <li><a href="taxon.php?id={$concept.id}&group={$v.id}">{$v.sysname}</a></li>
        {/foreach}
        </ul>
    </p>
    {/if}

    <p>
        <table>
            {foreach from=$traits item=v}
            <tr class="tr-highlight values">
                <th style="width:200px">{if $v.name}{$v.name}{else}{$v.sysname}{/if}:</th>
                <td>
                {foreach from=$values item=t}
                {if $t.trait.id==$v.id}
                    {foreach from=$t.values item=i}
                    {$i.value_start}{if $i.value_end} - {$i.value_end}{/if}<br />
                    {/foreach}
                {/if}
                {/foreach}
                </td>
                <td>
					<a class="edit values" data='{ "trait":{$v.id},"taxon":{$concept.id},"group":{$group.id} }'>edit</a>
                </td>
            </tr>
            {/foreach}
        </table>

        <p>
        	referenties:
        	<ul id="references">
			</ul>
        </p>

        <a
        	class="edit"
            style="margin-left:0"
            href="#"
            onclick="dropListDialog(this,'Publicatie');return false;"
            rel="presence_reference_id">
        	referentie toevoegen
		</a>
		<span id="presence_reference" style="display:none"></span>
        <input
        	type="hidden"
            id="presence_reference_id"
            value=""
            onchange="taxonTraits.setReference( { literature_id: $(this).val(), label: $('#presence_reference').html() } );taxonTraits.printReferences();"
		/>

    </p>

    <p>
    	<input type="button" value="save" onclick="taxonTraits.saveReferences();" />
    </p>

    <p>
	    <a href="#" style="margin-left:0" class="edit" onclick="taxonTraits.deleteTraitsReferencesByGroup();return false;">delete traits and references for this taxon and trait group</a><br />
	    <a href="../nsr/taxon.php?id={$concept.id}"  style="margin-left:0" class="edit">to taxon concept</a>
    </p>

{/if}
</div>

{include file="../shared/admin-messages.tpl"}

<script id="stringlist_template_one" language="text">
<table style="width:100%">
	<tr>
		<td style="width:50%;padding-left:5px;border-right:1px dotted #666;">available values</td>
		<td style="width:50%;padding-left:5px;">selected values</td>
	</tr>
	<tr>
		<td colspan=2 style="border-bottom:1px solid #666"></td>
	</tr>
	<tr>
		<td style="width:50%;border-right:1px dotted #666"><ul style="list-style-type:none;padding:0;margin:0 0 0 5px;" id="value-list">%VALUES%</ul></td>
		<td style="width:50%;"><ul style="list-style-type:none;padding:0;margin:0 0 0 5px;" id="selection-list">%SELECTED%</ul></td>
	</tr>
</table>
</script>

<script id="stringlist_template_two" language="text">
<li>%VALUE% <a href="#" class="edit selected-values" style="padding:5px" data="%ID%" onclick="taxonTraits.listTraitRemove( this );return false;">X</a></li>
</script>

<script id="stringlist_template_three" language="text">
<li>%VALUE% <a href="#" class="edit" style="padding:5px" data="%ID%" onclick="taxonTraits.listTraitAdd( this );return false;">&rarr;</a></li>
</script>


<script id="stringfree_template_one" language="text">
<table style="width:100%"><tr><td>%SELECTED%</td></tr></table>
</script>

<script id="stringfree_template_two" language="text">
<textarea class="__stringfree" style="width:100%;height:50px" name="values[]">%VALUE%</textarea>
</script>

<script id="datefree_template_one" language="text">
<input class="__datefree" type="text" maxlength="%MAX_LENGTH%" name="%NAME%[]" value="%VALUE%" placeholder="%PLACEHOLDER%" style="width:50px;text-align:right">
</script>

<script id="datefree_template_two" language="text">
<tr><td>%VALUE_START%</td><td>%SEPARATOR%</td><td>%VALUE_END%</td></tr>
</script>

<script id="datefree_template_three" language="text">
<table style="" class="datefree_table">%SELECTED%</table>
</script>


<script>
    var initialNrRefs = {$references|@count};

$(document).ready(function()
{
	taxonTraits.setConcept( {$concept.id} );
	taxonTraits.setGroup( {$group.id} );
	taxonTraits.setGroupLabel( '{$group.sysname|@escape}' );

	$('a.values').each(function()
	{
		$(this).attr('href','#').on('click',function(e)
		{
			try
			{
				var d=JSON.parse($(this).attr('data'));
				taxonTraits.editTaxonTrait( d );
			}
			catch (err) {
				console.log( err );
			}
			e.preventDefault();
		});
	});

	{foreach from=$references item=v}
	taxonTraits.setReference( { id:{$v.id}, literature_id:{$v.literature_id}, label: '{$v.label|@escape}'} );
	{/foreach}
	taxonTraits.printReferences();

});
</script>

{include file="../shared/admin-footer.tpl"}


