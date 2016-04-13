{include file="../shared/admin-header.tpl"}

<div id="page-main">

<h2>{t}Indextabel bijwerken{/t}</h2>

<p>
{t}Deze functie werkt de extra indextabel bij waarin ouder-kindrelaties van de taxonconcepten worden bijgehouden.<br />
In principe wordt de tabel automatische bijgewerkt, maar mocht blijken dat bijvoorbeeld de aantallen onderliggende soorten
in de taxonomische boom niet overeenkomen met het werkelijke aantal, werk hem dan handmatig bij. Houd er rekening mee dat
het bijwerken ongeveer een minuut in beslag kan nemen.{/t}
<form method="post">
<input type="hidden" name="rnd" value="{$rnd}" />
<input type="hidden" name="action" value="update" />
<input type="submit" value="bijwerken" />
</form>
</p>
<p>
	<a href="index.php">{t}terug{/t}</a>
</p>

</div>

{include file="../shared/admin-messages.tpl"}
{include file="../shared/admin-footer.tpl"}