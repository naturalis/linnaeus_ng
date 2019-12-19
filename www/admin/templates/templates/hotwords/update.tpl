{include file="../shared/admin-header.tpl"}

<div id="page-main">


	<h3>Hotwords</h3>

    <p>
    {t}The application will collect the following hotwords:{/t}
    </p>
    
    <ul>
        <li>{t}Introduction: topics{/t}</li>
        <li>{t}Glossary: words and synonyms{/t}</li>
        <li>{t}Literature: complete references, in two forms:  "Schermer & Altenburg, 2005" and "Schermer & Altenburg (2005)"{/t}</li>
        <li>{t}Taxon concepts: accepted names, synonyms & common names{/t}</li>
        <li>{t}Dichotomous Key: step titles{/t}</li>
        <li>{t}Custom modules: topics{/t}</li>
    </ul>
    {t}Where relevant, language is taken into account. As there can be only one instance of a hotword per language, possible doubles will be ignored.{/t}
    {t}The application processes hotwords in the order as listed above. For instance, should a step exist in the key with a title identical to that of a topic in the introduction, occurrences of the title in texts will be linked to the introduction topic, not to the key.{/t}<br />
    {t}You can suppress the automatic linking through hotwords by placing text between [no][/no] tags.{/t}
    </p>
    <p>
    {t}Last update:{/t} {if $last_created}{$last_created}{else}(never){/if}<br />
    </p>
    <p>
    {t}Click the button below to update the hotwords table.{/t}
    <p>
    <form method="post">
    <input type="hidden" name="action" value="update" />
    <input type="hidden" name="rnd" value="{$rnd}" />
    <input type="submit" value="{t}Update hotwords table{/t}" />
    </form>
    <p>
    <a href="index.php">{t}back{/t}</a>
    </p>
</div>

<script>
$(document).ready(function(e)
{
	noMessageFade=true;
});
</script>



{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}