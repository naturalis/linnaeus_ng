<script type="text/javascript">
    var decisionPath = '<ul>';
    {if $keypath|@count > 1}
        {foreach from=$keypath key=k item=v name=pathPopup}
            {if !$smarty.foreach.pathPopup.last}
            decisionPath = decisionPath + 
                '<li class="row">'+
                '<a href="javascript:void(0);keyDoStep({$v.id})"><b>{t}Step{/t} {$v.step_number|@escape:javascript}{if $v.choice_marker}{$v.choice_marker|@escape:javascript}{/if}</b>{if $v.step_number!=$v.step_title} - {$v.step_title|@escape:javascript}{/if}{if $v.choice_txt}:<br>{$v.choice_txt|@escape:javascript}{/if}</a>'+
                '</li>';
            {/if}
        {/foreach}
    {else}
        decisionPath = decisionPath + '<p>{t}No choices made yet{/t}</p>';
    {/if}
    decisionPath = decisionPath + '</ul>';
</script>

<div id="panel">
	<div id="taxa-categories">
		<ul>
			<li>
				<a id="rLi" href="javascript:showRemaining();" class="category-first{if $taxaState=='remaining' || $excluded|@count==0} category-active{/if}">
					{t}Remaining{/t}
				</a>
			</li>
			<li class="{if $excluded|@count==0} category-no-content{/if}">
				<a id="eLi" href="javascript:showExcluded();" class="category-last {if $taxaState=='excluded'}category-active{/if}">
					Excluded
				</a>
			</li>
			<li>
				<a class="navigation-icon icon-hierarchy" id="decision-path-icon"
			    	href='javascript:renderDecisionPath("Decision path", decisionPath);' 
			    	title="{t}Decision path{/t}"></a>
			</li>
		</ul>
	</div>
	<div id="taxa">
		<div id="remaining" style="display: {if $taxaState=='remaining' || $excluded|@count==0}block{else}none{/if};">
			{if $remaining|@count==1}
				{assign var=w value=taxon}
			{else}
				{assign var=w value=taxa}
			{/if}
			<p id="header">
				<span class="remaining-count">
					{t _s1=$remaining|@count _s2=$w}%s possible %s remaining{/t}
				</span>
				<a href="#" class="name_switch" data-type="name_sci" style="display:none" onclick="keyNameswitch(this);return false;";>
					{t}show scientific names{/t}
				</a>
				<a href="#" class="name_switch" data-type="name_common" style="display:block" onclick="keyNameswitch(this);return false;">
					{t}show common names{/t}
				</a>
			</p>
			<ul id="ul-remaining">
				{foreach $remaining v k}
					<li>
						<a class="taxon-links" href="../species/taxon.php?id={$v.id}" name_common="{$v.commonname|@escape}" name_sci="{$v.taxon|@escape}">
							{$v.taxon}
						</a>
					</li>
				{/foreach}
			</ul>
		</div>
		<div id="excluded" style="display: {if $taxaState=='remaining' || $excluded|@count==0}none{else}block{/if};">
			{if $excluded|@count==1}
				{assign var=w value=taxon}
			{else}
				{assign var=w value=taxa}
			{/if}
			<p id="header">
				<span class="remaining-count">
					{t _s1=$excluded|@count _s2=$w}%s %s excluded{/t}<br />
				</span>
				<a href="#" class="name_switch" data-type="name_sci" style="display:none" onclick="keyNameswitch(this);return false;";>
					{t}show scientific names{/t}
				</a>
				<a href="#" class="name_switch" data-type="name_common" style="display:block" onclick="keyNameswitch(this);return false;">
					{t}show common names{/t}
				</a>
			</p>
			<ul id="ul-excluded">
			{foreach $excluded v k}
				<li>
					<a class="taxon-links" href="../species/taxon.php?id={$v.id}" name_common="{$v.commonname|@escape}" name_sci="{$v.taxon|@escape}">
						{$v.taxon}
					</a>
				</li>
			{/foreach}
			</ul>
		</div>
		<div id="decisionPathContainer">
			<div class="decisionPathHeader">Decision path</div>
			<div class="decisionpathContent"></div>
		</div>
	</div>
</div>