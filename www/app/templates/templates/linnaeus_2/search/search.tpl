{include file="../shared/header.tpl"}
{include file="_search-main-no-tabs.tpl"}

{assign var=showZeroHeaders value=false}

<div id="page-main">
	<div id="results">
		<div id="header">
		{if $results.numOfResults==0}
			{t _s1=$search|replace:'"':''}Your search for "%s" produced no results.{/t}
		{elseif $results.numOfResults==1}
			{t _s1=$search|replace:'"':'' _s2=$results.numOfResults}Your search for "%s" produced %s result.{/t}
		{else}
			{t _s1=$search|replace:'"':'' _s2=$results.numOfResults _s3=$resultWord}Your search for "%s" produced %s results.{/t}
		{/if}
		{if $results.species.numOfResults > 0}
			<a id="toggle-all" class="collapsed" href="javascript:toggleAll()">{t}Expand all{/t}</a>.
		{/if}
		</div>

{if $results.species.numOfResults > 0}
<div class="set">
	<div class="set-header{if $results.species.subsetsWithResults==1}-clickable{/if}">{t}Species{/t} {t}and{/t} {t}Higher taxa{/t} ({$results.species.numOfResults})</div>
	{foreach from=$results.species.results key=cat item=res}
	{if $res.data|@count>0  || $showZeroHeaders}
	{assign var=resultCount value=$res.data|@count}
	{if $results.species.subsetsWithResults>1}<div class="subset-header{if $resultCount==0}-zero{/if}">{$res.label} ({$resultCount})</div>{/if}
	<div class="subset">
		{foreach from=$res.data key=k item=v name=r}
		{if $smarty.foreach.r.first || $background=='c2'}
			{assign var="background" value="c1"}
		{else if $background=='c1'}
			{assign var="background" value="c2"}
		{/if}
		<p class="{$background}">
		{if $res.label|@strtolower=='species media'}
		<a href="../species/taxon.php?id={$v.taxon_id}{if $v.cat}&cat={$v.cat}{/if}"><img alt="{$v.label}" src={if $v.mime=='image'}"{$session.app.project.urls.uploadedMedia}{$v.label}"{else}"{$session.app.project.urls.systemMedia}video.png" class="no-border"{/if} /></a>
		{/if}{if $v.taxon}{$v.taxon}{if $results.species.categoryList[$v.cat]} ({$results.species.categoryList[$v.cat].title|@strtolower}){/if}: {/if}
		{if $useJavascriptLinks}
		<span class="result" onclick="goTaxon({$v.taxon_id}{if $v.cat},'{$v.cat}'{/if})">
		{if $v.label}{h search=$search}{$v.label}{/h}
		{elseif $v.content}"{foundContent search=$search}{$v.content}{/foundContent}"
		{/if}
		</span>
		{else}
		<a class="result" onclick="storeClickedSearchIndexNr({$v.sIndex});" href="../species/taxon.php?id={$v.taxon_id}{if $v.cat}&cat={$v.cat}{/if}&sidx={$v.sIndex}">{t}Taxon:{/t} {$v.target}{if $v.label}{h search=$search}{$v.label}{/h}{elseif $v.content}"{foundContent search=$search}{$v.content}{/foundContent}"{/if}</a>
		{/if}
		{if $v.post_script}{$v.post_script}{/if}</p>
        
        
        
		{/foreach}
	</div>
	{/if}
	{/foreach}
</div>
{/if}

{if $results.glossary.numOfResults > 0}
<div class="set">
	<div class="set-header{if $results.glossary.subsetsWithResults==1}-clickable{/if}">{t}Glossary{/t} ({$results.glossary.numOfResults})</div>
	{foreach from=$results.glossary.results key=cat item=res}
	{if $res.data|@count>0  || $showZeroHeaders}
	{assign var=resultCount value=$res.data|@count}
	{if $results.glossary.subsetsWithResults>1}<div class="subset-header{if $resultCount==0}-zero{/if}">{$res.label} ({$resultCount})</div>{/if}
	<div class="subset">
		{foreach from=$res.data key=k item=v name=r}
		{if $smarty.foreach.r.first || $background=='c2'}
			{assign var="background" value="c1"}
		{else if $background=='c1'}
			{assign var="background" value="c2"}
		{/if}
		<p class="{$background}">
		{if $useJavascriptLinks}
		<span class="result" onclick="goGlossaryTerm({$v.id})">
			{if $v.term && $v.term!=$v.label}{$v.term}: {/if}
			{h search=$search}{$v.label}{/h}{if $v.content}: "{foundContent search=$search}{$v.content}{/foundContent}"{/if}
			{if $v.synonym && $v.synonym!=$v.label} ({t}synonym of{/t} {$v.synonym}){/if}
		</span>
		{else}
		<a class="result" onclick="storeClickedSearchIndexNr({$v.sIndex});" href="../glossary/term.php?id={$v.id}&sidx={$v.sIndex}">
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
	<div class="set-header{if $results.literature.subsetsWithResults==1}-clickable{/if}">{t}Literature{/t} ({$results.literature.numOfResults})</div>
	{foreach from=$results.literature.results key=cat item=res}
	{if $res.data|@count>0  || $showZeroHeaders}
	{assign var=resultCount value=$res.data|@count}
	{if $results.literature.subsetsWithResults>1}<div class="subset-header{if $resultCount==0}-zero{/if}">{$res.label} ({$resultCount})</div>{/if}
	<div class="subset">
		{foreach from=$res.data key=k item=v name=r}
		{if $smarty.foreach.r.first || $background=='c2'}
			{assign var="background" value="c1"}
		{else if $background=='c1'}
			{assign var="background" value="c2"}
		{/if}
		<p class="{$background}">
		{if $useJavascriptLinks}
		<span class="result" onclick="goLiterature({$v.id})">
			{h search=$search}{$v.author_full} ({$v.year_full}){/h}{if $v.content}: "{foundContent search=$search}{$v.content}{/foundContent}"{/if}
		</span>
		{else}
		<a class="result" onclick="storeClickedSearchIndexNr({$v.sIndex});" href="../literature/reference.php?id={$v.id}&sidx={$v.sIndex}">
			{h search=$search}{$v.author_full} ({$v.year_full}){/h}{if $v.content}: "{foundContent search=$search}{$v.content}{/foundContent}"{/if}
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
	<div class="set-header{if $results.dichkey.subsetsWithResults==1}-clickable{/if}">{t}Dichotomous key{/t} ({$results.dichkey.numOfResults})</div>
	{foreach from=$results.dichkey.results key=cat item=res}
	{if $res.data|@count>0  || $showZeroHeaders}
	{assign var=resultCount value=$res.data|@count}
	{if $results.dichkey.subsetsWithResults>1}<div class="subset-header{if $resultCount==0}-zero{/if}">{$resultCount} {t}in{/t} {$res.label|@strtolower}</div>{/if}
	<div class="subset">
		{foreach from=$res.data key=k item=v name=r}
		{if $smarty.foreach.r.first || $background=='c2'}
			{assign var="background" value="c1"}
		{else if $background=='c1'}
			{assign var="background" value="c2"}
		{/if}
		<p class="{$background}"><span>
        {if $v.choice_id}
        <a class="result" href="../key/index.php?forcetree=1&choice={$v.choice_id}">
        {elseif $v.keystep_id}
		<a class="result" onclick="storeClickedSearchIndexNr({$v.sIndex});" href="../key/index.php?forcetree=1&step={$v.keystep_id}&sidx={$v.sIndex}">
        {/if}
			{if $v.label}{t}Step{/t} {$v.number}:{h search=$search} {$v.label}{/h}
			{elseif $v.content}{t}Step{/t} {$v.number} ("{$v.title}"){if $v.marker}, {t}choice{/t} {$v.marker}{/if}: "{foundContent search=$search}{$v.content}{/foundContent}"
			{/if}
        {if $v.choice_id || $v.keystep_id}
        </a>
        {/if}
		</span></p>
		{/foreach}
	</div>
	{/if}
	{/foreach}
</div>
{/if}

{* if $results.matrixkey.numOfResults > 0}
<div class="set">
	<div class="set-header{if $results.matrixkey.subsetsWithResults==1}-clickable{/if}">{t}Matrix key{/t} ({$results.matrixkey.numOfResults})</div>
	{foreach from=$results.matrixkey.results key=cat item=res}
	{if $res.data|@count>0  || $showZeroHeaders}
	{assign var=resultCount value=$res.data|@count}
	{if $results.matrixkey.subsetsWithResults>1}<div class="subset-header{if $resultCount==0}-zero{/if}">{$res.label} ({$resultCount})</div>{/if}
	<div class="subset">
		{foreach from=$res.data key=k item=v name=r}
		{if $smarty.foreach.r.first || $background=='c2'}
			{assign var="background" value="c1"}
		{else if $background=='c1'}
			{assign var="background" value="c2"}
		{/if}
		<p class="{$background}">
		{if $useJavascriptLinks}
		<span {if !$v.matrices && $v.matrix_id}class="result" onclick="goMatrix({$v.matrix_id}){/if}">
			{if $v.label}{h search=$search}{$v.label}{/h}{/if}
			{if $v.content}: "{foundContent search=$search}{$v.content}{/foundContent}"{/if}
			{if $v.characteristic}(of character "{$v.characteristic}"{if !$v.matrices}){/if}{/if}
			{if $v.matrices}{if !$v.characteristic}({/if}{if $v.matrices|@count==1}in matrix{else}in matrices{/if}
			{foreach from=$v.matrices key=k item=m name=matrices}{if $smarty.foreach.matrices.index!==0}, {/if}"<span class="result" onclick="goMatrix({$m.matrix_id})">{$results.matrixkey.matrices[$m.matrix_id].name}</span>"{/foreach}){/if}
		</span>
		{else}
		{if !$v.matrices && $v.matrix_id}<a class="result" href="../matrixkey/use_matrix.php?id={$v.matrix_id}">{/if}
			{if $v.label}{h search=$search}{$v.label}{/h}{/if}
			{if $v.content}: "{foundContent search=$search}{$v.content}{/foundContent}"{/if}
			{if $v.characteristic}(of characteris "{$v.characteristic}"{if !$v.matrices}){/if}{/if}
			{if $v.matrices}{if !$v.characteristic}({/if}{if $v.matrices|@count==1}in matrix{else}in matrices{/if}
			{foreach from=$v.matrices key=k item=m name=matrices}{if $smarty.foreach.matrices.index!==0}, {/if}"<a class="result" onclick="storeClickedSearchIndexNr({$v.sIndex});" href="../matrixkey/use_matrix.php?id={$m.matrix_id}&sidx={$v.sIndex}">{$results.matrixkey.matrices[$m.matrix_id].name}</a>"{/foreach}){/if}
		{if !$v.matrices && $v.matrix_id}</a>{/if}
		{/if}
		</p>
		{/foreach}
	</div>
	{/if}
	{/foreach}
</div>
{/if *}

{if $results.map.numOfResults > 0}
<div class="set">
	<div class="set-header{if $results.map.subsetsWithResults==1}-clickable{/if}">{t}Distribution{/t} ({$results.map.numOfResults})</div>
	{foreach from=$results.map.results key=cat item=res}
	{if $res.data|@count>0  || $showZeroHeaders}
	{assign var=resultCount value=$res.data|@count}
	{if $results.map.subsetsWithResults>1}<div class="subset-header{if $resultCount==0}-zero{/if}">{$resultCount} {t}in{/t} {$res.label|@strtolower}</div>{/if}
	<div class="subset">
		{foreach from=$res.data key=k item=v name=r}
		{if $smarty.foreach.r.first || $background=='c2'}
			{assign var="background" value="c1"}
		{else if $background=='c1'}
			{assign var="background" value="c2"}
		{/if}
		<p class="{$background}">
		{if $useJavascriptLinks}
		<span class="result" onclick="goMap({$v.id})">{h search=$search}{$v.content}{/h}</span> ({$v.number} occurrences)
		{else}
		<a class="result" onclick="storeClickedSearchIndexNr({$v.sIndex});" href="../mapkey/examine_species.php?id={$v.id}&sidx={$v.sIndex}">{h search=$search}{$v.content}{/h}</a> ({$v.number} occurrences)
		{/if}
		</p>
		{/foreach}
	</div>
	{/if}
	{/foreach}
</div>
{/if}

{if $results.introduction.numOfResults > 0}
<div class="set">
	<div class="set-header{if $results.introduction.subsetsWithResults==1}-clickable{/if}">{t}Introduction{/t} ({$results.introduction.numOfResults})</div>
	{foreach from=$results.introduction.results key=cat item=res}
	{if $res.data|@count>0  || $showZeroHeaders}
	{assign var=resultCount value=$res.data|@count}
	<div class="subset">
		{foreach from=$res.data key=k item=v name=r}
		{if $smarty.foreach.r.first || $background=='c2'}
			{assign var="background" value="c1"}
		{else if $background=='c1'}
			{assign var="background" value="c2"}
		{/if}
		<p class="{$background}">
		{if $useJavascriptLinks}
		<span class="result" onclick="goContent({$v.id})">
			{h search=$search}{$v.label}{/h}{if $v.content}: "{foundContent search=$search}{$v.content}{/foundContent}"{/if}
		</span>
		{else}
		<a class="result" onclick="storeClickedSearchIndexNr({$v.sIndex});" href="../linnaeus/?id={$v.id}&sidx={$v.sIndex}">
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

{if $results.content.numOfResults > 0}
<div class="set">
	<div class="set-header{if $results.content.subsetsWithResults==1}-clickable{/if}">{t}Other content{/t} ({$results.content.numOfResults})</div>
	{foreach from=$results.content.results key=cat item=res}
	{if $res.data|@count>0  || $showZeroHeaders}
	{assign var=resultCount value=$res.data|@count}
	{if $results.content.subsetsWithResults>1}<div class="subset-header{if $resultCount==0}-zero{/if}">{$resultCount} {t}in{/t} {$res.label|@strtolower}</div>{/if}
	<div class="subset">
		{foreach from=$res.data key=k item=v name=r}
		{if $smarty.foreach.r.first || $background=='c2'}
			{assign var="background" value="c1"}
		{else if $background=='c1'}
			{assign var="background" value="c2"}
		{/if}
		<p class="{$background}">
		{if $useJavascriptLinks}
		<span class="result" onclick="goContent({$v.id})">
			{h search=$search}{$v.label}{/h}{if $v.content}: "{foundContent search=$search}{$v.content}{/foundContent}"{/if}
		</span>
		{else}
		<a class="result" onclick="storeClickedSearchIndexNr({$v.sIndex});" href="../linnaeus/?id={$v.id}&sidx={$v.sIndex}">
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

{if $results.modules.numOfResults > 0}
<div class="set">
	<div class="set-header{if $results.modules.subsetsWithResults==1}-clickable{/if}">{$results.modules.numOfResults} in additional modules</div>
	{foreach from=$results.modules.results key=cat item=res}
	{if $res.data|@count>0  || $showZeroHeaders}
	{assign var=resultCount value=$res.data|@count}
	{if $results.modules.subsetsWithResults>1}<div class="subset-header{if $resultCount==0}-zero{/if}">{$resultCount} {t}in{/t} {$res.label|@strtolower}</div>{/if}
	<div class="subset">
		{foreach from=$res.data key=k item=v name=r}
		{if $smarty.foreach.r.first || $background=='c2'}
			{assign var="background" value="c1"}
		{else if $background=='c1'}
			{assign var="background" value="c2"}
		{/if}
		<p class="{$background}">
		{if $useJavascriptLinks}
		<span class="result" onclick="goModuleTopic({$v.page_id},{$v.module_id})">
			{if $v.label}{h search=$search}{$v.label}{/h}{/if}
			{if $v.label && $v.content}: {/if}
			{if $v.content}"{foundContent search=$search}{$v.content}{/foundContent}"{/if}
		</span>
		{else}
		<a class="result" onclick="storeClickedSearchIndexNr({$v.sIndex});" href="../module/topic.php?modId={$v.module_id}&id={$v.page_id}&sidx={$v.sIndex}">
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


</div>
</div>
{literal}
<script type="text/JavaScript">
$(document).ready(function(){

$('.subset').hide();
$('.set-header-clickable').addClass('closedResult');
$('.subset-header').addClass('closedResult');
$('.subset-header').click(function(){
	$(this).next('.subset').slideToggle(300, toggleClass(this));
});
$('.set-header-clickable').click(function(){
	$(this).next('.subset').slideToggle(300, toggleClass(this));
});
	
});

function toggleClass(theDiv) {
	if ($(theDiv).hasClass('closedResult')) {
		$(theDiv).removeClass('closedResult').addClass('openResult');
	} else {
		$(theDiv).removeClass('openResult').addClass('closedResult');
	}
}

function toggleAll() {
	if ($('#toggle-all').hasClass('collapsed')) {
		$('.subset').slideDown(300);
		$('#toggle-all').html(_('Collapse all'));
		$('.set-header-clickable').removeClass('closedResult').addClass('openResult');
		$('.subset-header').removeClass('closedResult').addClass('openResult');
		$('#toggle-all').removeClass('collapsed').addClass('expanded');
	} else {
		$('.subset').slideUp(300);
		$('#toggle-all').html(_('Expand all'));
		$('.set-header-clickable').removeClass('openResult').addClass('closedResult');
		$('.subset-header').removeClass('openResult').addClass('closedResult');
		$('#toggle-all').removeClass('expanded').addClass('collapsed');
	}
}
</script>
{/literal}

{include file="../shared/messages.tpl"}
{include file="../shared/footer.tpl"}
