{include file="../shared/admin-header.tpl"}
{include file="../shared/admin-messages.tpl"}
<div id="page-main">
{if $processed==true}
<p>
<a href="l2_literature_glossary.php">Import literature and glossary</a>
</p>
{else}
Select which elements you wish to import and click "Import".<br />
Please note that importing might take several minutes, especially when you
are importing media files.
<form method="post">
<input type="hidden" name="process" value="1"  />
<input type="hidden" name="rnd" value="{$rnd}" />
<p>
	<b>Species data</b><br/>

	<label><input type="checkbox" name="taxon_overview" checked="checked">&nbsp;Import general species descriptions</label><br />

    {if $session.admin.system.import.imagePath===false}
	    You specified no media import.<br />
    {else}
    	<label><input type="checkbox" name="taxon_media" checked="checked">&nbsp;Import media</label><br />
    {/if}

    {if $hasCommonNames}
	    <label><input type="checkbox" name="taxon_common" checked="checked">&nbsp;Import common names</label><br />
    {else}
	    No common names found.<input type="hidden" name="taxon_common" value="off"><br />
    {/if}

    {if $hasSynonyms}
	    <label><input type="checkbox" name="taxon_synonym" checked="checked">&nbsp;Import synonyms</label><br />
    {else}
	    No synonyms found.<input type="hidden" name="taxon_synonym" value="off"><br />
    {/if}
</p>

{if $hasSynVernDescription}

<hr  />
<p>
This project has a field called "syn_vern_description". It holds collected data on synonyms, common names
and sometimes more. {if $hasSynonyms||$hasCommonNames}<u>This data is additional to, and might very well overlap with, the data referred to above, which is stored in a proper fashion</u>.{/if}<br />
This "syn_vern_description" field is hard to parse, but this program can make an attempt. Choose how
to treat this data:</p>
<p>
<label><input type="radio" name="syn_vern_description" value="off" checked="checked" />do not import</label><br />
<label><input type="radio" name="syn_vern_description" value="common" />only attempt to parse common names</label><br />
<label><input type="radio" name="syn_vern_description" value="synonyms" />only attempt to parse synonyms</label><br />
<label><input type="radio" name="syn_vern_description" value="both" />attempt to parse both</label>
</p>
<p>
Parsing is done as follows:
<ul>
	<li>data is split in single lines based on [br]</li>
    <li>links ([l][/l]) that have a text part ([t][/t]) ar replaced with just the text, other links are removed entirely, [p] and [/p] tags are removed, lines are trimmed</li>
    <li>if what remains starts with [b],[u],[i], the line is ignored (header)</li>
    <li>if what remains is shorter than 10 characters, or has no spaces (single word), the line is ignored</li>
    <li>if the line contains the word "Type species" (case insensitive)), it is ignored (neither a synonym or common name)</li>
    <li>remaining lines will be judged to be:<ul>
    	<li>
        	<i>common names</i>: if they end with a valid English language name in brackets (straight or curved) ("Dansemyg (Danish)"). If the
            word between the brackets cannot be resolved as a language, an error is raised and the name is not stored. If the first part of the line (everything minus the 
            language) contains semi-colons, they will be considered separate names in the same language.
        </li>
    	<li>
        	<i>synonyms</i>: in all other cases. Synonyms are stored in their entirety as a synonym; no attempt is made to split off the author part.
        </li>
     </ul>
	<li><i>some</i> cleaning up of the strings is attempted before they are stored, but the output of Linnaeus 2 does not adhere to any obvious formatting standards, so no guarantees. If you're afraid things will be messed up, skip this option.</li>
    <li>idem if you're in a hurry (and files are big).</li>
    {if $hasSynonyms||$hasCommonNames}<li><i>No attempt is made to match these entries against the properly stored ones. Again, if you're afraid things will be messed up, skip this option.</i></li>{/if}
    <li>in the Species-module, there is an easy-access list for both synonyms and common names to see and optionally delete erroneous entries.</li>
</ul>
<u>Please note</u> that every occurrence of the "syn_vern_description" field is <i>always</i> (also) stored  for the corresponding taxon in an automatically generated content page called "Nomenclature". So the data is never lost, even if you choose "do not import", but it <i>is</i> treated as "just content", and won't show up in the index. Additionally, when true synonyms are present as well as syn_vern_description for the same taxon, an attempt is made to parse the synonym's author from syn_vern_description and save it in the proper place.
</p>
<hr  />
{else}
<input type="hidden" name="syn_vern_description" value="off">
{/if} 

<input type="submit" value="{t}Import{/t}" />
</form>
{/if}
</div>

{include file="../shared/admin-footer.tpl"}