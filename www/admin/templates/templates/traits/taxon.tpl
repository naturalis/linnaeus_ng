{include file="../shared/admin-header.tpl"}

<div id="page-main">

<p>
concept: {$concept.taxon}<br />
trait group: {$group.sysname} {if $group.parent}(parent: <a href="taxon.php?id={$concept.id}&group={$group.parent.id}">{$group.parent.sysname}</a>)<br />{/if}
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
<tr>
	<th>{$v.sysname}:</th>
    <td>
<!--

  ["max_length"]=>
  ["can_select_multiple"]=>
  ["can_include_comment"]=>
  ["can_be_null"]=>
  ["can_have_range"]=>
  ["date_format_format_hr"]=>

  ["type_sysname"]=>
  ["type_allow_values"]=>
  ["type_allow_select_multiple"]=>
  ["type_allow_max_length"]=>
  ["type_allow_unit"]=>
  ["value_count"]=>
  
-->
    {foreach from=$values item=t}
    {if $t.trait.id==$v.id}
        {foreach from=$t.values item=i}
        {$i.value_start}{if $i.value_end} - {$i.value_end}{/if}<br />
        {/foreach}
    {/if}
    {/foreach}
    </td>
</tr>
{/foreach}
</table>
</p>

<script>
$(document).ready(function()
{
	$('#page-block-messages').fadeOut(3000);

});
</script>

{include file="../shared/admin-footer.tpl"}
