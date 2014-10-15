{include file="../shared/admin-header.tpl"}

<script type="text/javascript" src="../../../admin/javascript/ckeditor/ckeditor.js"></script>
<script type="text/javascript" src="../../../admin/javascript/nsr_passport.js"></script>

<div id="page-main">

<h2><span style="font-size:12px;font-style:normal">concept:</span> {$concept.taxon}</h2>
<h3>paspoorten</h3>

<p>
<form>
<input type="hidden" id="taxon_id" value="{$concept.id}" />
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
                <span title="geen content (onzichtbaar)" class="passport-leeg">(leeg)</span>
                {/if}
            </span>
			<a href="/linnaeus_ng/app/views/species/nsr_taxon.php?id={$concept.id}&cat={$v.id}&epi={$session.admin.project.id}" class="edit"  style="margin:0" target="nsr" title="paspoort bekijken in het Soortenregister (nieuw venster)">&rarr;</a><br />
		</span>
		<div class="passport-body" id="body{$k}">
            <span class="passport-content" id="content{$k}">{$v.content}</span>

{* if $v.rdf.author.name || $v.rdf.reference.citation || $v.rdf.publisher.name}
<div id="meta{$k}" class="passport-meta">
    <span class="label">Auteur(s):</span> {$v.rdf.author.name}<br />
    <span class="label">Publicatie:</span> {$v.rdf.reference.citation}<br />
    <span class="label">Organisatie:</span> {$v.rdf.publisher.name}<br />
</div>
{/if *}
            
			<a href="#" class="edit" id="edit{$k}" onclick="openeditor(this);return false;" style="margin-left:0;">edit</a>
            <div id="button-container{$k}" class="button-container" style="display:none">
{*<p>
    Auteur:<select id="author{$k}">
        <option value="" {if !$v.rdf.author.id}selected="selected"{/if}>-</option>
    {foreach from=$actors item=a key=k}
    {if $a.is_company=='0'}
        <option value="{$a.id}"{if $a.id==$v.rdf.author.id} selected="selected"{/if}>{$a.label}</option>
    {/if}
    {/foreach}
    </select> 
<i>
{$v.rdf.publisher.id}{$v.rdf.publisher.name}
{$v.rdf.reference.id}{$v.rdf.reference.label}
{$v.rdf.author.id}{$v.rdf.author.name}
 </i> 
 *}          
            <input id="publish{$k}" type="checkbox" value="publiceren" {if $v.publish==1}checked="checked"{/if} />publiceren?
            </p>
            <p>
            <input id="save{$k}" value="opslaan" type="button" onclick="saveeditordata(this);">
            <input id="close{$k}" value="sluiten" type="button" onclick="closeeditor(this);">
            <input id="revert{$k}" value="oorspronkelijke tekst" type="button" onclick="reverttext(this);">
            <input id="page{$k}" value="{$v.id}" type="hidden" />
            <span id="message{$k}"></span>
            </p>
            </div>
        </div>
	</li>
    {/if}
	{/foreach}
	</ul>
</form>

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
	<a href="taxon.php?id={$concept.id}">terug</a>
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