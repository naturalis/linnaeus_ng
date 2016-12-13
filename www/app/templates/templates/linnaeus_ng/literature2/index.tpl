{include file="../shared/header.tpl"}
<div id="header-titles-small">
	<span id="header-title">{t}Literature{/t}</span>
</div>
<div id="page-main">
	<div class="search-title-or-author">
		<ul class="tabs tabs-grow literature-tabs-js">
			<li class="tab-active">
				<a href="javascript:void(0)" class="select-tab-js" data-target="search-title">
					Search by title
				</a>
			</li>
			<li>
				<a href="javascript:void(0)" class="select-tab-js" data-target="search-author">
					Search by author
				</a>
			</li>
		</ul>
		<div class="search-tab-content active tab-content-js" data-content="search-title">
			<div class="alphabet">
				{foreach from=$titleAlphabet item=v}
					<a href="#" class="click-letter" onclick="lit2Lookup(this,'lookup_title_letter','{$v.letter}');return false;">
						{$v.letter|@strtoupper}
					</a>
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

		<!-- <table class="alphabet">
			<tr>
				<td>
					{t}Search by title:{/t}
				</td>
				<td>
					<input type="text" name="" id="lookup-input-title" placeholder="{t}Type to find{/t}" onkeyup="lit2Lookup(this,'lookup_title');" />
				</td>
				<td>
					{foreach from=$titleAlphabet item=v}
					<a href="#" class="click-letter" onclick="lit2Lookup(this,'lookup_title_letter','{$v.letter}');return false;">{$v.letter|@strtoupper}</a>
					{/foreach}
				</td>
			</tr>
			<tr>
				<td>
					{t}Search by author:{/t}
				</td>
				<td>
					<input type="text" name="" id="lookup-input-author" placeholder="{t}Type to find{/t}" onkeyup="lit2Lookup(this,'lookup_author');" />
				</td>
				<td>
					{foreach from=$authorAlphabet item=v}
					{if $v.letter}
					<a href="#" class="click-letter" onclick="lit2Lookup(this,'lookup_author_letter','{$v.letter}');return false;">{$v.letter|@strtoupper}</a>
					{/if}
					{/foreach}
				</td>
			</tr>
		</table> -->
		<p>
			<div id="lit2-result-list">
				test<br><br><br><br><br><br> test 2 <br><br><br><br><br><br> test 3
				test 4<br><br><br><br><br><br> test 5 <br><br><br><br><br><br> test 6
				test 7<br><br><br><br><br><br> test 8 <br><br><br><br><br><br> test 9
				test 10<br><br><br><br><br><br> test 11 <br><br><br><br><br><br> test 12
			</div>
		</p>
	</div>
</div>

<script>
$(document).ready(function() {
	$('body').on('click', '.select-tab-js', function() {
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
