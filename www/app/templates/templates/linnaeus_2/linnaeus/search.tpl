{include file="../shared/header.tpl"}
{include file="_search-main-no-tabs.tpl"}

<div id="page-main">
{if $search|@strlen>2}
<div id="results">
<div id="header">
{if $results.numOfResults==0}
	{t _s1=$search|replace:'"':''}Your search for "%s" produced no results.{/t}
{elseif $results.numOfResults==1}
	{t _s1=$search|replace:'"':'' _s2=$results.numOfResults}Your search for "%s" produced %s result:{/t}
{else}
	{t _s1=$search|replace:'"':'' _s2=$results.numOfResults _s3=$resultWord}Your search for "%s" produced %s results:{/t}
{/if}
</div>

{assign var=hidden value=1}

{if $results.species.numOfResults > 0}
<div class="set">
	{foreach from=$results.species.results key=cat item=res}
	{if $res.data|@count>0}
	<div class="subset">
		<div class="set-header">{$res.data|@count} {t}in{/t} {$res.label|@strtolower}</div>
		
		{foreach from=$res.data key=k item=v name=r}
		{if $smarty.foreach.r.first || $background=='c2'}
			{assign var="background" value="c1"}
		{else if $background=='c1'}
			{assign var="background" value="c2"}
		{/if}

		{if $k==$visibleSearchResultsPerCategory}
		<div class="showHidden" id="switch-{$hidden}" onclick="toggleHidden({$hidden});"></div>
		</div>
		<div class="subset invisible" id="hidden-{$hidden++}" visible="0">
		{/if}
		
		<p class='{$background}'>

		{if $res.label|@strtolower=='species media'}
			<img alt="{$v.label}" src="{$session.app.project.urls.uploadedMedia}{$v.label}" style="width:50px" />
		{/if}

		{if $results.species.taxonList[$v.taxon_id] && $results.species.taxonList[$v.taxon_id].taxon!==$v.label}{$results.species.taxonList[$v.taxon_id].taxon}{if $results.species.categoryList[$v.cat]} ({$results.species.categoryList[$v.cat].title|@strtolower}){/if}:
		{/if}

		{if $useJavascriptLinks}
		<span class="result" onclick="goTaxon({$v.taxon_id}{if $v.cat},'{$v.cat}'{/if})">
		{if $v.is_hybrid==1}<span class="hybrid-marker" title="{t}hybrid{/t}">{$session.app.project.hybrid_marker}</span>{/if}
		{if $v.label}{h search=$search}{$v.label}{/h}
		{elseif $v.content}"{foundContent search=$search}{$v.content}{/foundContent}"
		{/if}
		</span>
		{else}
		<a class="result" href="../species/taxon.php?id={$v.taxon_id}{if $v.cat}&cat={$v.cat}{/if}">
			{t}Taxon:{/t} {$v.target}
		{if $v.is_hybrid==1}<span class="hybrid-marker" title="{t}hybrid{/t}">{$session.app.project.hybrid_marker}</span>{/if}
		{if $v.label}{h search=$search}{$v.label}{/h}
		{elseif $v.content}"{foundContent search=$search}{$v.content}{/foundContent}"
		{/if}
		</a>
		{/if}
		</p>
		{/foreach}
	</div>
	{/if}
	{/foreach}
</div>
{/if}

{if $results.glossary.numOfResults > 0}
<div class="set">
	{foreach from=$results.glossary.results key=cat item=res}
	{if $res.data|@count>0}
	<div class="subset">
		<div class="set-header">{$res.data|@count} {t}in{/t} {$res.label|@strtolower}</div>
		{foreach from=$res.data key=k item=v name=r}
		{if $smarty.foreach.r.first || $background=='c2'}
			{assign var="background" value="c1"}
		{else if $background=='c1'}
			{assign var="background" value="c2"}
		{/if}	
		<p class='{$background}'>
		{if $useJavascriptLinks}
		<span  onclick="goGlossaryTerm({$v.id})">
			{if $v.term && $v.term!=$v.label}{$v.term}: {/if}
			{h search=$search}{$v.label}{/h}{if $v.content}: "{foundContent search=$search}{$v.content}{/foundContent}"{/if}
			{if $v.synonym && $v.synonym!=$v.label} ({t}synonym of{/t} {$v.synonym}){/if}
		</span>
		{else}
		<a href="../glossary/term.php?id={$v.id}">
			{if $v.term && $v.term!=$v.label}{$v.term}: {/if}
			{h search=$search}{$v.label}{/h}{if $v.content}: "{foundContent search=$search}{$v.content}{/foundContent}"{/if}
			{if $v.synonym && $v.synonym!=$v.label} ({t}synonym of{/t} {$v.synonym}){/if}
		</a>
		{/if}	
		</p>
		{/foreach}
	</div>
	{/if}
	{/foreach}
</div>
{/if}

{if $results.literature.numOfResults > 0}
<div class="set">
	{foreach from=$results.literature.results key=cat item=res}
	{if $res.data|@count>0}
	<div class="subset">
		<div class="set-header">{$res.data|@count} {t}in{/t} {$res.label|@strtolower}</div>
		{foreach from=$res.data key=k item=v name=r}
		{if $smarty.foreach.r.first || $background=='c2'}
			{assign var="background" value="c1"}
		{else if $background=='c1'}
			{assign var="background" value="c2"}
		{/if}	
		<p class='{$background}'>
		{if $useJavascriptLinks}
		<span class="result" onclick="goLiterature({$v.id})">
			{h search=$search}{$v.author_full} ({$v.year}){/h}{if $v.content}: "{foundContent search=$search}{$v.content}{/foundContent}"{/if}
		</span>
		{else}
		<a class="result" href="../literature/reference.php?id={$v.id}">
			{h search=$search}{$v.author_full} ({$v.year}){/h}{if $v.content}: "{foundContent search=$search}{$v.content}{/foundContent}"{/if}
		</a>
		{/if}
		
		</p>
		{/foreach}
	</div>
	{/if}
	{/foreach}
</div>
{/if}

{if $results.dichkey.numOfResults > 0}
<div class="set">
	{foreach from=$results.dichkey.results key=cat item=res}
	{if $res.data|@count>0}
	<div class="subset">
		<div class="set-header">{$res.data|@count} {t}in{/t} {$res.label|@strtolower}</div>
		{foreach from=$res.data key=k item=v name=r}
		{if $smarty.foreach.r.first || $background=='c2'}
			{assign var="background" value="c1"}
		{else if $background=='c1'}
			{assign var="background" value="c2"}
		{/if}	
		<p class='{$background}'>
		
			{if $v.label}{t}Step{/t} {$v.number}:{h search=$search} {$v.label}{/h}
			{elseif $v.content}{t}Step{/t} {$v.number} ("{$v.title}"){if $v.marker}, {t}choice{/t} {$v.marker}{/if}: "{foundContent search=$search}{$v.content}{/foundContent}"
			{/if}
		</p>
		{/foreach}
	</div>
	{/if}
	{/foreach}
	{t _s1='<a href="../key/">' _s2=</a>}It is not possible to jump directly to a specific step or choice of the dichotomous key. Click %shere%s to start the key from the start.{/t}
</div>
{/if}

{if $results.matrixkey.numOfResults > 0}
<div class="set">
	{foreach from=$results.matrixkey.results key=cat item=res}
	{if $res.data|@count>0}
	<div class="subset">
		<div class="set-header">{$res.data|@count} {t}in{/t} {$res.label|@strtolower}</div>
		{foreach from=$res.data key=k item=v name=r}
		{if $smarty.foreach.r.first || $background=='c2'}
			{assign var="background" value="c1"}
		{else if $background=='c1'}
			{assign var="background" value="c2"}
		{/if}	
		<p class='{$background}'>
		{if $useJavascriptLinks}
		<span {if !$v.matrices && $v.matrix_id} onclick="goMatrix({$v.matrix_id}){/if}">
			{if $v.label}{h search=$search}{$v.label}{/h}{/if}
			{if $v.content}: "{foundContent search=$search}{$v.content}{/foundContent}"{/if}
			{if $v.characteristic}(of characteristic "{$v.characteristic}"{if !$v.matrices}){/if}{/if}
			{if $v.matrices}{if !$v.characteristic}({/if}{if $v.matrices|@count==1}in matrix{else}in matrices{/if}
			{foreach from=$v.matrices key=k item=m name=matrices}{if $smarty.foreach.matrices.index!==0}, {/if}"<span class="result" onclick="goMatrix({$m.matrix_id})">{$results.matrixkey.matrices[$m.matrix_id].name}</span>"{/foreach}){/if}
		</span>
		{else}
		{if !$v.matrices && $v.matrix_id}<a href="../matrixkey/use_matrix.php?id={$v.matrix_id}">{/if}
			{if $v.label}{h search=$search}{$v.label}{/h}{/if}
			{if $v.content}: "{foundContent search=$search}{$v.content}{/foundContent}"{/if}
			{if $v.characteristic}(of characteristic "{$v.characteristic}"{if !$v.matrices}){/if}{/if}
			{if $v.matrices}{if !$v.characteristic}({/if}{if $v.matrices|@count==1}in matrix{else}in matrices{/if}
			{foreach from=$v.matrices key=k item=m name=matrices}{if $smarty.foreach.matrices.index!==0}, {/if}"<a class="result" href="../matrixkey/use_matrix.php?id={$m.matrix_id}">{$results.matrixkey.matrices[$m.matrix_id].name}</a>"{/foreach}){/if}
		{if !$v.matrices && $v.matrix_id}</a>{/if}
		{/if}
		</p>
		{/foreach}
	</div>
	{/if}
	{/foreach}
</div>
{/if}

{if $results.map.numOfResults > 0}
<div class="set">
	{foreach from=$results.map.results key=cat item=res}
	{if $res.data|@count>0}
	<div class="subset">
		<div class="set-header">{$res.data|@count} {t}in{/t} {$res.label|@strtolower}</div>
		{foreach from=$res.data key=k item=v name=r}
		{if $smarty.foreach.r.first || $background=='c2'}
			{assign var="background" value="c1"}
		{else if $background=='c1'}
			{assign var="background" value="c2"}
		{/if}	
		<p class='{$background}'>
		{if $useJavascriptLinks}
		<span class="result" onclick="goMap({$v.id})">{h search=$search}{$v.content}{/h}</span> ({$v.number} occurrences)
		{else}
		<a href="../mapkey/examine_species.php?id={$v.id}">{h search=$search}{$v.content}{/h}</a> ({$v.number} occurrences)
		{/if}
		</p>

		{/foreach}
	</div>
	{/if}
	{/foreach}
</div>
{/if}

{if $results.modules.numOfResults > 0}
<div class="set">
	{foreach from=$results.modules.results key=cat item=res}
	{if $res.data|@count>0}
	<div class="subset">
		<div class="set-header">{$res.data|@count} {t}in{/t} {$res.label|@strtolower}</div>
		{foreach from=$res.data key=k item=v name=r}
		{if $smarty.foreach.r.first || $background=='c2'}
			{assign var="background" value="c1"}
		{else if $background=='c1'}
			{assign var="background" value="c2"}
		{/if}	
		<p class='{$background}'>
		{if $useJavascriptLinks}
		<span class="result" onclick="goModuleTopic({$v.page_id},{$v.module_id})">
			{if $v.label}{h search=$search}{$v.label}{/h}{/if}
			{if $v.label && $v.content}: {/if}
			{if $v.content}"{foundContent search=$search}{$v.content}{/foundContent}"{/if}
		</span>
		{else}
		<a class="result" href="../module/topic.php?modId={$v.module_id}&id={$v.page_id}">
			{if $v.label}{h search=$search}{$v.label}{/h}{/if}
			{if $v.label && $v.content}: {/if}
			{if $v.content}"{foundContent search=$search}{$v.content}{/foundContent}"{/if}
		</a>
		{/if}
		</p>
		{/foreach}
	</div>
	{/if}
	{/foreach}
</div>
{/if}

{if $results.content.numOfResults > 0}
<div class="set">
	{foreach from=$results.content.results key=cat item=res}	
	{if $res.data|@count>0}
	<div class="subset">
		<div class="set-header">{$res.data|@count} {t}in{/t} {$res.label|@strtolower}</div>
		{foreach from=$res.data key=k item=v name=r}
		{if $smarty.foreach.r.first || $background=='c2'}
			{assign var="background" value="c1"}
		{else if $background=='c1'}
			{assign var="background" value="c2"}
		{/if}	
		<p class='{$background}'>
		{if $useJavascriptLinks}
		<span class="result" onclick="goContent({$v.id})">
			{h search=$search}{$v.label}{/h}{if $v.content}: "{foundContent search=$search}{$v.content}{/foundContent}"{/if}
		</span>
		{else}
		<a class="result" href="../linnaeus/?id={$v.id}">
			{h search=$search}{$v.label}{/h}{if $v.content}: "{foundContent search=$search}{$v.content}{/foundContent}"{/if}
		</a>
		{/if}
		</p>
		{/foreach}
	</div>
	{/if}
	{/foreach}
</div>
{/if}

</div>
{/if}
</div>

{include file="../shared/messages.tpl"}
{include file="../shared/footer.tpl"}
