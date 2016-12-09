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
			<input type="text" name="" id="lookup-input-title" placeholder="{t}Type to find{/t}" onkeyup="lit2Lookup(this,'lookup_title');" />
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
			<input type="text" name="" id="lookup-input-author" placeholder="{t}Type to find{/t}" onkeyup="lit2Lookup(this,'lookup_author');" />
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
			<div id="lit2-result-list"></div>
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
<table>
    <tr>
        <td style="width:200px">{t}authors{/t}</td>
        <td style="width:75px;text-align:right;padding-right:10px;">{t}year{/t}</td>
        <td style="width:500px">{t}reference{/t}</td>
    </tr>
    %TBODY%
</table>
-->
</div>

<div class="inline-templates" id="reference-table-row">
<!--
<tr class="tr-highlight" style="vertical-align:top;">
    <td><a href="reference.php?id=%ID%">%AUTHOR%</a>
    </td>
    <td style="text-align:right;padding-right:10px;">%YEAR%</td>
    <td>%REFERENCE%</td>
</tr>
-->
</div>

<div class="inline-templates" id="string-highlight">
<!--
    <span style="background-color:yellow">%STRING%</span>
-->
</div>

{include file="../shared/footer.tpl"}
