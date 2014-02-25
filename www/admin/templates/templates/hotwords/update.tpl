{include file="../shared/admin-header.tpl"}

<div id="page-main">
<p>
Hotwords are words that are automatically linked to a corresponding module and topic.
The application will collect the following hotwords:
<ul>
	<li>Introduction: topics</li>
	<li>Glossary: words and synonyms</li>
	<li>Literature: complete references, in two forms:  "Henk & Henk, 2005" and "Henk & Henk (2005)"</li>
	<li>Species & Higher Taxa: accepted names & common names</li>
	<li>Dichotomous Key: step titles</li>
	<li>Free modules: topics</li>
</ul>
Where relevant, language is taken into account. As there can be only one instance of a hotword per language, possible doubles will be ignored.
The application processes hotwords in the order as listed above. For instance, should a step exist in the key with a title indentical to that of a topic in the introduction, occurrences of the title in texts will be linked to the introduction topic, not to the key.<br />
You can suppress the automatic linking through hotwords by placing text betweeb [no][/no] tags.
</p>
<p>
Click the button below to update the hotwords table. Last update: {if $last_created}{$last_created}{else}(never){/if}
<form method="post">
<input type="hidden" name="action" value="update" />
<input type="hidden" name="rnd" value="{$rnd}" />
<input type="submit" value="{t}Update hotwords table{/t}" />
</form>
</p>
<a href="index.php">Back</a>
</div>

{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}