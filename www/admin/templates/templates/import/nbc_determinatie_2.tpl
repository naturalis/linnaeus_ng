{include file="../shared/admin-header.tpl"}
{include file="../shared/admin-messages.tpl"}

<div id="page-main">

<form method="post" action="nbc_determinatie_3.php">
{if !$title}
<p>
	Could not find a title. Please make sure that you have uploaded the correct file
    and that there is a valid project title in cell A1.
</p>
<p>
	<a href="nbc_determinatie_1.php?action=new">Back</a>
</p>
{else}
<p>
	Data to be imported:
</p>
<p>
	Project title: "<b>{$title}</b>"<br />
    Soortgroep: {$soortgroep}<br />
</p>
{if $exists}
<p>
	<span class="message-error">A project with that name already exists. Project names need to be unique; please specify how to treat this import:</span><br />
	<input type="radio" id="id1" name="action" value="replace_data" checked="checked" /></td><td><label for="id1">import into existing project, replacing existing data</label><br />
	<!-- input type="radio" id="id2" name="action" value="merge_data" /></td><td><label for="id2">import into existing project, merging with existing data</label><br / -->
	<input type="radio" id="id3" name="action" value="new_project" /></td><td><label for="id3">create a new project with the title "{$suggestedTitle}"</label><br />
	If you wish to create a new project with a different title, alter the title in your CSV-file and <a href="nbc_determinatie_1.php?action=new">reload the file</a>.<br />
</p>
{/if}
<p>
	Below is a sample of the data parsed from the input file. Please verify that it looks okay, and click 'Next'.
</p>
<p>
    <b>First five species (of {$species|@count}) with some states:</b><br />
    {assign var=i value=0}
    {foreach from=$species item=v}
    {if $i<5}
    {$v.label}
    
    (<i>{assign var=t value=0}
    {foreach from=$v.states item=s}
    {if $t<5}
    {$s[0]}; 
    {/if}
    {assign var=t value=$t+1}
    {/foreach}
    ...</i>)
    <br />
    {/if}
    {assign var=i value=$i+1}
    {/foreach}
</p>
<p>
    <b>First five characters (of {$characters|@count}):</b><br />
    {assign var=i value=0}
    {foreach from=$characters item=v}
    {if $i>1 && $i<7}
    {$v.code}<br />
    {/if}
    {assign var=i value=$i+1}
    {/foreach}
</p>
<p>
	<input type="hidden" name="rnd" value="{$rnd}" />
	<input type="submit" value="Next">
</p>
	</form>
<p>
	<a href="nbc_determinatie_1.php">Back</a>
</p>
{/if}

</div>

{include file="../shared/admin-footer.tpl"}