{include file="../shared/header.tpl" title="Literature"}
<div id="page-main">
	<div class="search-title-or-author">
		<ul class="tabs tabs-grow literature-tabs-js">
			<li class="tab-active">
				<a href="javascript:checkFirst();" class="select-tab-js" data-target="search-title">
					Search by title
				</a>
			</li>
			<li>
				<a href="javascript:checkFirst();" class="select-tab-js" data-target="search-author">
					Search by author
				</a>
			</li>
		</ul>
		<div class="search-tab-content active tab-content-js" data-content="search-title">
			<div class="alphabet">
				{foreach from=$titleAlphabet item=v}
					{if $v.letter}
					<a href="#" class="click-letter" onclick="lit2Lookup(this,'lookup_title_letter','{$v.letter}');return false;">
						{$v.letter|@strtoupper}
					</a>
					{/if}
				{/foreach}
			</div>
			<div class="search-input__container without-button">
				<input type="text" name="" id="lookup-input-title" placeholder="{t}Type to find{/t}" onkeyup="lit2Lookup(this,'lookup_title');" />	
			</div>
		</div>
		<div class="search-tab-content tab-content-js" data-content="search-author">
			<div class="alphabet">
				{foreach from=$authorAlphabet item=v}
					{if $v.letter}
						<a href="#" class="click-letter" onclick="lit2Lookup(this,'lookup_author_letter','{$v.letter}');return false;">
							{$v.letter|@strtoupper}
						</a>
					{/if}
				{/foreach}
			</div>
			<div class="search-input__container without-button">
				<input type="text" name="" id="lookup-input-author" placeholder="{t}Type to find{/t}" onkeyup="lit2Lookup(this,'lookup_author');" />
			</div>
		</div>
		<div id="lit2-result-list">
		</div>
	</div>
</div>
<div class="search-results"></div>

<script>

function  checkFirst()
{
	if ($('.alphabet-active-letter:visible').length==0) $('.click-letter:visible:first').trigger('click');
	return false;
}

$(document).ready(function() {
	$('body').on('click', '.select-tab-js', function()
	{
		var target = $(this).attr('data-target');
		$('.tab-content-js').removeClass('active');
		$('.tab-content-js[data-content="'+target+'"]').addClass('active');
		$('.literature-tabs-js li').removeClass('tab-active');
		$(this).parent().addClass('tab-active');
	});

	{if $prevSearch.search_title!=''}
		$('#lookup-input-title').val('{$prevSearch.search_title|@escape}').trigger('onkeyup');
	{else if $prevSearch.search_author!=''}
		$('#lookup-input-author').val('{$prevSearch.search_author|@escape}').trigger('onkeyup');
	{/if}	

	checkFirst();
});




</script>


<div class="inline-templates" id="reference-table">
<!--

    %TBODY%

-->
</div>

<div class="inline-templates" id="reference-table-row">
	<!-- 
	<div class="highlight-list">
		<ul>
			<li class="author">
				<a href="reference.php?id=%ID%">%AUTHOR%</a> %YEAR%
			</li>
			<li class="content">%REFERENCE%</li>
		</ul>
	</div> 
	-->
</div>

<div class="inline-templates" id="string-highlight">
<!--
    <span style="background-color:yellow">%STRING%</span>
-->
</div>

{include file="../shared/footer.tpl"}
