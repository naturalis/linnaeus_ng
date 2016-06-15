{include file="../shared/admin-header.tpl"}


<div id="page-main">
<p>
<h2>{$reference.label}</h2>
<h3>{$reference.author_or_verbatim}</h3>
</p>
<p>

<table>
	<tr>
		<th>taal:</th>
		<td>
            {if !$reference.language_id}{t}onbekend{/t}{/if}
            {foreach from=$languages item=v key=k}
            {if $v.id==$reference.language_id}{$v.label}{/if}
            {/foreach}
		</td>
	</tr>
	<tr><th>titel:</th><td>{$reference.label}</td></tr>

	{if $reference.alt_label}
	<tr>
    	<th>alt. label:</th>
        <td>
			{$reference.alt_label}
		</td>
	</tr>
	{/if}

	<tr><th>datum:</th><td>{$reference.date}</td></tr>
	<tr><th>auteur (verbatim):</th><td>{$reference.author}</td></tr>
	<tr>
		<th>auteur(s):</th>
		<td>
			{foreach from=$reference.authors item=author key=kk}
                {foreach from=$actors item=v key=k}
				{if $v.id==$author.actor_id}{$v.label}{/if}<br />
                {/foreach}
			{/foreach}
		</td>
	</tr>
	<tr>
		<th>type publicatie:</th>
		<td>
			{foreach from=$publicationTypes item=v}
			{if $v.id==$reference.publication_type_id}{$v.label}{/if}
			{/foreach}
		</td>
	</tr>
	<tr><th>{t}citatie:{/t}</th><td>{$reference.citation}</td></tr>
	<tr><th>{t}bron:{/t}</th><td>{$reference.source}</td></tr>

	<tr>
		<th>gepubliceerd in:</th>
		<td>
			{if $reference.publishedin_label}{$reference.publishedin_label}{/if}
		</td>
	</tr>

	<tr><th>uitgever:</th><td>{$reference.publisher}</td></tr>

	{if $reference.publishedin}
	<tr>
    	<th>{t}gepubliceerd in (verbatim):{/t}</th>
        <td>{$reference.publishedin}</td>
	</tr>
	{/if}

	<tr>
		<th title="gebruik dit veld voor het tijdschrift of seriewerk waarin betreffende referentie gepubliceerd is.">periodiek:</th>
		<td>{$reference.periodical_label}</td>
	</tr>
	{if $reference.periodical}
	<tr>
    	<th>{t}periodiek (verbatim):{/t}</th>
        <td>{$reference.periodical}</td>
	</tr>
	{/if}

    
	<tr>
    	<th>{t}volume:{/t}</th>
        <td>{$reference.volume}</td>
	</tr>
	<tr>
    	<th>{t}pagina(s):{/t}</th>
        <td>{$reference.pages}</td>
	</tr>
	<tr>
    	<th>{t}link:{/t}</th>
        <td>{$reference.external_link}</td>
	</tr>

	<tr>
    	<th>
        	{t}gekoppelde taxa:{/t}
        </th>
    	<td>
            <ul id="new_taxa">
            </ul>        
		</td>
	</tr>

</table>

</form>
</p>
<p>
<div>
	<b>Koppelingen</b><br />

	{if $links.presences|@count==0 && $links.names|@count==0 && $links.traits|@count==0 && $links.passports|@count==0 && $links.taxa|@count==0}
	(geen koppelingen)
	{/if}
    
	{if $links.names|@count > 0}
    <div>
	<a href="#" onclick="$('#links-names').toggle();return false;">Gekoppelde namen ({$links.names|@count})</a>
	<div id="links-names" style="display:none">
		<ul class="small">
			{foreach from=$links.names item=v}
			<li><a href="../nsr/taxon.php?id={$v.taxon_id}">{$v.name}{if $v.nametype=='isValidNameOf'} ({$v.nametype_label}){else} ({$v.nametype_label} van {$v.taxon}){/if}</a></li>
			{/foreach}
		</ul>
	</div>
    </div>
	{/if}

	{if $links.presences|@count > 0}
    <div>
	<a href="#" onclick="$('#links-presences').toggle();return false;">Gekoppelde voorkomensstatussen ({$links.presences|@count})</a>
	<div id="links-presences" style="display:none">
		<ul class="small">
			{foreach from=$links.presences item=v}
			<li><a href="../nsr/taxon.php?id={$v.taxon_id}">{$v.taxon}</a>, {$v.presence_label}</li>
			{/foreach}
		</ul>
	</div>
    </div>
	{/if}

	{if $links.passports|@count > 0}
    <div>
	<a href="#" onclick="$('#links-passports').toggle();return false;">Gekoppelde paspoorten ({$links.passports|@count})</a>
	<div id="links-passports" style="display:none">
		<ul class="small">
			{foreach from=$links.passports key=k item=v}{if $v.taxon_id!=$links.passports[$k-1].taxon_id}
            {if $k>0}</li>{/if}
			<li><a href="../nsr/taxon.php?id={$v.taxon_id}">{$v.taxon}</a>: {/if}{if $v.taxon_id==$links.passports[$k-1].taxon_id}, {/if}{$v.title}{/foreach}</li>
		</ul>
	</div>
   	</div>
	{/if}

	{if $links.taxa|@count > 0}
    <div>
	<a href="#" onclick="$('#links-taxa').toggle();return false;">Gekoppelde taxa ({$links.taxa|@count})</a>
	<div id="links-taxa" style="display:none">
		<ul class="small">
			{foreach from=$links.taxa item=v}
			<li><a href="../nsr/literature.php?id={$v.id}">{$v.taxon} [{$v.rank}]</a></li>
			{/foreach}
		</ul>
	</div>
    </div>
	{/if}

	{if $links.traits|@count > 0}
    <div>
	<a href="#" onclick="$('#links-traits').toggle();return false;">Gekoppelde kenmerken (<span id="trait-total">0</span>)</a>
	<div id="links-traits" style="display:none">
    	<ul>
            {foreach from=$links.traits item=vv key=trait}
        	<li>
            	<a href="#" onclick="$('#links-traits-{$vv.id}').toggle();return false;">{$trait} ({$vv|@count})</a>
                <ul class="small" id="links-traits-{$vv.id}" style="display:none">
                    {foreach from=$vv item=v}
                    <li><a href="../nsr/taxon.php?id={$v.taxon_id}">{$v.taxon}</a></li>
                    {/foreach}
                </ul>
			</li>
            <script>
			$( '#trait-total' ).html( parseInt($( '#trait-total' ).html())+{$vv|@count} );
            </script>
            {/foreach}
		</ul>
	</div>
    </div>
	{/if}
    
</div>
</p>
<p>
	<a href="index.php">terug</a>
</p>


</div>

<script>
$(document).ready(function()
{
	{foreach from=$actors item=v key=k}
	{if $v.is_company!='1'}
//	storeAuthor({ id: {$v.id},name:'{$v.label|@escape}'});
	{/if}
	{/foreach}

});
</script>

{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}
