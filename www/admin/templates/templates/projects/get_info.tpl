{include file="../shared/admin-header.tpl"}

<div id="page-main">
{t _s1=$taxon[0].total}The current project contains %s taxa, with:{/t}
<ul>
<li>{t _s1=$variations[0].total}%s variations{/t}</li>
<li>{t _s1=$mediataxon[0].total}%s media files{/t}</li>
<li>{t _s1=$commonname[0].total}%s common names{/t}</li>
<li>{t _s1=$synonym[0].total}%s synonyms{/t}</li>
<li>{t _s1=$contenttaxon[0].total}%s pages{/t}</li>
</ul>
Futhermore, there are:
<p>
&#149;&nbsp;{t _s1=$glossary[0].total}%s glossary entries{/t}<br />
&#149;&nbsp;{t _s1=$literature[0].total}%s literature references{/t}<br />
&#149;&nbsp;{t _s1=$freemoduleproject[0].total _s2=$freemodulepage[0].total}%s additional info module(s) with a total of %s pages{/t}<br />
&#149;&nbsp;{t _s1=$contentkeystep[0].total}%s steps in the dichtomous key{/t}<br/>
&#149;&nbsp;{t _s1=$matrix[0].total}%s matrix key(s){/t}<br />
&#149;&nbsp;{t _s1=$occurrencetaxon[0].total}%s map items{/t}
</p>
</div>

{include file="../shared/admin-footer.tpl"}
