{include file="../shared/admin-header.tpl"}

{include file="../shared/left_column_tree.tpl"}
{include file="../shared/left_column_admin_menu.tpl"}

{assign var=hasContent value=false}
{foreach from=$tabs item=v key=k}
{if !($v.obsolete && $v.content|@strlen==0) && $v.content|@strlen!=0}
{assign var=hasContent value=true}
{/if}
{/foreach}

<script type="text/javascript" src="../../../admin/javascript/ckeditor/ckeditor.js"></script>
<script type="text/javascript" src="../../../admin/javascript/nsr_passport.js"></script>
<script type="text/javascript" src="../../../admin/javascript/literature2.js"></script>
<style>
li {
	margin-bottom:10px;
}
th {
	border-bottom:1px solid black;
	text-align:left;
}
select {
	width:150px;
}
</style>

<div id="page-container-div">

<div id="page-main">

<h2><span style="font-size:12px;font-style:normal">{t}concept{/t}:</span> {$concept.taxon}</h2>
<h3>{t}passports (metadata){/t}</h3>

<form method="post" id="theForm">
<input type="hidden" id="rnd" name="rnd" value="{$rnd}" />
<input type="hidden" id="action" name="action" value="save" />
<input type="hidden" id="taxon_id" name="taxon_id" value="{$concept.id}" />

<p>

{if !$hasContent}

{t}Metadata can only be assigned to tabs with texts. None of the tabs contains text. Please add texts before assigning metadata.{/t}

{else}
<table>
	<tr style="vertical-align:top">
    	<th style="width:225px;">Author(s)</th>
    	<!--<th style="width:225px;">Organisation(s)</th>-->
    	<th style="width:400px;">Publication(s)</th>
	</tr>
    <tr>
    	<td>
            <a class="edit" style="margin-left:0px;" href="#" onclick="addAuthorField();return false;">{t}add author{/t}</a><br />
            <span id="authors">
            </span>
		</td>
        <!--
    	<td>
            <a class="edit" style="margin-left:0px;" href="#" onclick="addOrganisationField();return false;">{t}add organisation{/t}</a><br />
            <span id="organisations">
            </span>
		</td>
		-->
        <td>
            <a class="edit" style="margin-left:0" href="#" onclick="dropListDialog(this,'Publicatie');return false;" rel="reference_id">{t}add reference{/t}</a><br />
            <input type="hidden" id="reference_id" value="" onchange="collectReferences(this.value);"/>
            <span id="reference" style="display:none;"></span>
			<span id="references"></span>

		</td>
        <td>
		</td>
	</tr>
</table>
</p>

<p>
{t}add to{/t}:<br />
<select name="update-reach">
    <option value="all-text">{t}all tabs containing text{/t}</option>
    <option value="text-no-meta">{t}current tabs with text lacking metadata{/t}</option>
    <option disabled="disabled">----------------------------</option>
	{foreach from=$tabs item=v key=k}
	{if !($v.obsolete && $v.content|@strlen==0) && $v.content|@strlen!=0}
    <option value="{$v.content_id}">{$v.title}</option>
	{/if}
    {/foreach}
</select><br />
({t}note: existing metadata of the selected tab(s) will be overwritten{/t})
</p>
<p>
<input type="button" onclick="doPassportMeta();" value="{t}save{/t}" />
</p>

</form>


<p>
	{t}Overview of current metadata{/t}:<br />
    <a href="#" onclick="doDeleteMeta('*');">{t}delete all metadata{/t}</a>
	<ul>
    {assign var=hasObsolete value=false}
	{foreach from=$tabs item=v key=k}
	{if !($v.obsolete && $v.content|@strlen==0) && $v.content|@strlen!=0}
	<li>
		<span class="passport-title">
			<b>{$v.title}</b>
            {if $v.obsolete}{assign var=hasObsolete value=true}<span class="passport-waarschuwing">Obsolete passport title</span>{/if}
            <span id="indicator{$k}">
	            {if $v.content|@strlen>0 && $v.publish==1}
                <span title="heeft content, is gepubliceerd" class="passport-published">{$v.content|@strlen} {t}characters in field{/t}</span>
                {elseif $v.content|@strlen>0 && $v.publish!=1}
                <span title="heeft content, niet gepubliceerd (onzichtbaar)" class="passport-unpublished">{$v.content|@strlen} {t}characters in field{/t}</span>
                {else}
                <span title="geen content (onzichtbaar)" class="passport-leeg">{t}empty{/t}</span>
                {/if}
            </span>
			<a href="/linnaeus_ng/app/views/species/nsr_taxon.php?id={$concept.id}&cat={$v.id}&epi={$session.admin.project.id}" class="edit"  style="margin:0" target="nsr" title="{t}view passport (in new window){/t}">&rarr;</a><br />
		</span>

        <div class="passport-meta">
            <span class="label">Author(s):</span> {foreach from=$v.rdf.author item=f key=q}{if $q>0}; {/if}{$f.name}{/foreach}<br />
            <span class="label">Publication(s):</span> {foreach from=$v.rdf.reference item=f key=q}{if $q>0}; {/if}{$f.label}{/foreach}<br />
            <!-- <span class="label">Organisation(s):</span> {foreach from=$v.rdf.publisher item=f key=q}{if $q>0}; {/if}{$f.name}{/foreach}<br />-->
			{if $v.rdf.author|@count>0 || $v.rdf.reference|@count>0 || $v.rdf.publisher|@count>0}
			<a href="#" onclick="doDeleteMeta({$v.content_id});">{t}delete metadata{/t}</a>
            {/if}
        </div>
	</li>
    {/if}
	{/foreach}
	</ul>

    {if $hasObsolete}
    <p>
    <span class="passport-waarschuwing">{t}Obsolete passport titles{/t}</span><br/>
    {t}These are passport titles that overlap with new titles.{/t}<br />
    {t}For consistency's sake, please migrate the text from the old to the new passport:{/t}
    <ul>
        <li>Algemeen > Samenvatting</li>
        <li>Bescherming > Bedreiging en bescherming</li>
        <li>Description > Summary</li>
        <li>Habitat > Biotopen</li>
    </ul>
    </p>
    {/if}

    {/if}
</p>

<p>
	<a href="paspoort.php?id={$concept.id}" class="edit"  style="margin:0">{t}tabs{/t}</a><br />
</p>

<p>
	<a href="taxon.php?id={$concept.id}&amp;noautoexpand=1">{t}back{/t}</a>
</p>

</div>

<script>
$(document).ready(function(e)
{
	{foreach from=$actors item=v key=k}
	{if $v.is_company!='1'}
	storeAuthor({ id: {$v.id},name:'{$v.label|@escape}'});
	{else}
	storeOrganisation({ id: {$v.id},name:'{$v.label|@escape}'});
	{/if}
	{/foreach}
});
</script>

{include file="../shared/admin-messages.tpl"}

</div>

{include file="../shared/admin-footer.tpl"}