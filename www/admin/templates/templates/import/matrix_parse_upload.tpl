{include file="../shared/admin-header.tpl"}
{include file="../shared/admin-messages.tpl"}

<div id="page-main">

<form method="post" action="matrix_save_upload.php">

<h2>Parsed data overview</h2>

{if !$parsed_data.project.title}
    <p>
        Could not find a title. Please make sure that you have uploaded the correct file
        and that there is a valid project title in cell A1.
    </p>
    <p>
        <a href="matrix_index.php">Back</a>
    </p>
{else}
    <p>
        Project: {$parsed_data.project.title}<br />
        Matrix name: {if $parsed_data.project.matrix_name}{$parsed_data.project.matrix_name}{else}{$parsed_data.project.title}{/if}<br />
        Species group: {$parsed_data.project.soortgroep}<br />
    </p>

dutch: <input type="text" value="24" name="language_id" />
<p>
species:
	<input type="radio" value="replace" name="taxon_treatment" />replace (first delete all old)<br />
	<input type="radio" value="add" name="taxon_treatment" />add (keep old, add new)<br />
	<input type="radio" value="sync" name="taxon_treatment" checked="checked" />sync (keep / add new ones, delete ones not among new)<br />
</p>

<pre>
project cannot be deleted 

if matrix exists
	will delete matrix (or go back and give another name)

if matrix doesnt exists
	will create matrix
    
species

</pre>

    
	{if $existing_project}
	<p>
	<span class="message-error">A project with that name already exists. Project names need to be unique; please specify how to treat this import:</span><br />
		<input type="radio" id="id4" name="action" value="replace_species_data"/>
			<label for="id4">Keep the project, just delete matrix-data before importing new data (<u>choose this when first uploading a new matrix into an existing project</u>).
		</label><br />
		<input type="radio" id="id2" name="action" value="merge_data" checked="checked" />
			<label for="id2">Keep the project and keep all data (<u>choose this when updating the data of an existing matrix</u> or <u>when uploading the next matrices from a multi-matrix key</u>)<br />
			(you'll get the chance to replace the specific matrix later on in this wizard)
		</label><br />
		<input type="radio" id="id1" name="action" value="replace_data" />
			<label for="id1">Keep the project, but delete <i>all</i> data before importing new data.
		</label><br />
		<input type="radio" id="id3" name="action" value="new_project" />
			<label for="id3">Create a whole new project titled "{$suggestedTitle}".
		</label><br />
	If you wish to create a new project with a different title, alter the title in your CSV-file and <a href="nbc_determinatie_1.php?action=new">reload the file</a>.<br />
</p>
{/if}
<p>
	Below is a sample of the data parsed from the input file. Please verify that it looks okay, and click 'Next'.
</p>
<p>
    <b>First five units (of {$species|@count}) with some states:</b><br />
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