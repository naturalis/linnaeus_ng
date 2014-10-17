{include file="../shared/admin-header.tpl"}

<script type="text/javascript" src="../../../admin/javascript/ckeditor/ckeditor.js"></script>
<script type="text/javascript" src="../../../admin/javascript/nsr_passport.js"></script>

<script>

var authors=Array();

function storeAuthor(p)
{
	authors.push(p);
}

function addAuthorField()
{
	// find the next available author field
	for(var k=0;k<99;k++)
	{
		if ($("#actor_id-"+k).length==0)
			break;
	}
	
	var buffer=Array()
	buffer.push('<option value="">-</option>');
	for(var i in authors)
	{
		buffer.push('<option value="'+authors[i].id+'">'+authors[i].name+'</option>');
	}


	var currVals=Array;
	$('select[name^=actor_id]').each(function(i){
		currVals[i]=$(this).val();
	});

	$('#authors').html(
		$('#authors').html()+
		'<span id="actor_id-'+k+'"><select name="actor_id[]">'+buffer.join('')+'</select>'+
		'<a class="edit" href="#" onclick="removeAuthorField('+k+');return false;">verwijderen</a><br /></span>');

	$('select[name^=actor_id]').each(function(i){
		$(this).val(currVals[i]);
	});

}

function removeAuthorField(k)
{
	$('#actor_id-'+k).remove();
}
</script>

<style>
li {
	margin-bottom:10px;
}
</style>

<div id="page-main">

<h2><span style="font-size:12px;font-style:normal">concept:</span> {$concept.taxon}</h2>
<h3>paspoorten</h3>

<form>
<input type="hidden" id="taxon_id" value="{$concept.id}" />

auteur(s):<br />
<span id="authors">
    <span id="actor_id-0">
    <select name="actor_id[]">
        <option value="" {if !$reference.actor_id} selected="selected"{/if}>-</option>
    {foreach from=$actors item=v key=k}
    {if $v.is_company=='0'}
        <option value="{$v.id}" {if $v.id==$author.actor_id} selected="selected"{/if}>{$v.label}</option>
    {/if}
    {/foreach}
    </select><a class="edit" href="#" onclick="removeAuthorField(0);return false;">verwijderen</a>
    <br />
    </span>
</span>
<a class="edit" style="margin-left:0px;" href="#" onclick="addAuthorField();return false;">auteur toevoegen</a>



referentie(s):<br />
		<a class="edit" href="#" onclick="toggleedit(this);editreference(this);return false;" rel="name_reference_id">edit</a>
		<span class="editspan" id="reference"></span>
		<input type="hidden" id="name_reference_id" value="{$name.reference_id}" />





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
	{/if}
	{/foreach}
});
</script>



{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}