{include file="../shared/admin-header.tpl"}

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

<div id="page-main">

<h2><span style="font-size:12px;font-style:normal">concept:</span> {$concept.taxon}</h2>
<h3>paspoorten (meta-gegevens)</h3>

<form method="post" id="theForm">
<input type="hidden" id="taxon_id" value="{$concept.id}" />

<p>
<table>
	<tr style="vertical-align:top">
    	<th style="width:225px;">Auteur(s)</th>
    	<th style="width:225px;">Organisatie(s)</th>
    	<th style="width:400px;">Publicatie(s)</th>
	</tr>        
    <tr>
    	<td>
            <a class="edit" style="margin-left:0px;" href="#" onclick="addAuthorField();return false;">auteur toevoegen</a><br />
            <span id="authors">
            </span>
		</td>
    	<td>
            <a class="edit" style="margin-left:0px;" href="#" onclick="addOrganisationField();return false;">organisatie toevoegen</a><br />
            <span id="organisations">
            </span>
		</td>
        <td>
            <a class="edit" style="margin-left:0" href="#" onclick="dropListDialog(this,'Publicatie');return false;" rel="reference_id">referentie toevoegen</a><br />
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
toevoegen aan:<br />
<select name="update-reach">
    <option value="all">alle tabbladen</option>
    <option value="all-text">alle tabbladen met tekst</option>
    <option value="no-meta">tabbladen zonder meta-gegevens</option>
    <option value="text-no-meta">tabbladen met tekst zonder meta-gegevens</option>
    <option disabled="disabled">----------------------------</option>
	{foreach from=$tabs item=v key=k}
	{if !($v.obsolete && $v.content|@strlen==0)}
    <option value="{$v.content_id}">{$v.title}</option>
	{/if}
    {/foreach}
</select><br />
(let op: bestaande meta-gegevens van de geselecteerde tab(s) worden overschreven)
</p>
<p>
<input type="button" onclick="doPassportMeta();" value="opslaan" />
</p>


</form>



<p>
	<ul>
    {assign var=hasObsolete value=false}
	{foreach from=$tabs item=v key=k}
	{if !($v.obsolete && $v.content|@strlen==0)}
	<li>
		<span class="passport-title">
        	<a href="#" onclick="$('#body{$k}').toggle();return false;">{$v.title}</a>
            {if $v.obsolete}{assign var=hasObsolete value=true}<span class="passport-waarschuwing">Verouderde paspoorttitel</span>{/if}
            <span id="indicator{$k}">
	            {if $v.content|@strlen>0 && $v.publish==1}
                <span title="heeft content, is gepubliceerd" class="passport-published">{$v.content|@strlen} tekens</span>
                {elseif $v.content|@strlen>0 && $v.publish!=1}
                <span title="heeft content, niet gepubliceerd (onzichtbaar)" class="passport-unpublished">{$v.content|@strlen} tekens</span>
                {else}
                <span title="geen content (onzichtbaar)" class="passport-leeg">leeg</span>
                {/if}
            </span>
			<a href="/linnaeus_ng/app/views/species/nsr_taxon.php?id={$concept.id}&cat={$v.id}&epi={$session.admin.project.id}" class="edit"  style="margin:0" target="nsr" title="paspoort bekijken in het Soortenregister (nieuw venster)">&rarr;</a><br />
		</span>

        <div id="meta{$k}" class="passport-meta">
            <span class="label">Auteur(s):</span> {$v.rdf.author.name}<br />
            <span class="label">Publicatie:</span> {$v.rdf.reference.citation}<br />
            <span class="label">Organisatie:</span> {$v.rdf.publisher.name}<br />
        </div>
	</li>
    {/if}
	{/foreach}
	</ul>

{if $hasObsolete}
<p>
<span class="passport-waarschuwing">Verouderde paspoorttitels</span><br/>
Dit zijn oude paspoorttitels die overlappen met nieuwe titels.<br />
Verplaats voor de consistentie de tekst s.v.p. van het oude naar het nieuwe paspoort:
<ul>
    <li>Algemeen > Samenvatting</li>
    <li>Bescherming > Bedreiging en bescherming</li>
    <li>Description > Summary</li>
    <li>Habitat > Biotopen</li>
</ul>
</p>
{/if}

</p>

<p>
	<a href="paspoort.php?id={$concept.id}" class="edit"  style="margin:0">tabbladen</a><br />
</p>

<p>
	<a href="taxon.php?id={$concept.id}">terug</a>
</p>

</div>

<script>
$(document).ready(function(e) {
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
{include file="../shared/admin-footer.tpl"}