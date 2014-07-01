	<p>
		<h2 id="name-header">Naamgeving</h2>

		<table id="names-table">
			{foreach from=$names.list item=v}
				{if $v.expert.name}{assign var=expert value=$v.expert.name}{/if}
				{if $v.organisation.name}{assign var=organisation value=$v.organisation.name}{/if}
				{if $v.nametype=='isValidNameOf' && $taxon.base_rank_id<$smarty.const.SPECIES_RANK_ID}
					<tr><td style="white-space:nowrap">{$v.nametype_label|@ucfirst}</td><td><b>{$v.name}</b></td></tr>
				{else}
					{if $v.language_id==$smarty.const.LANGUAGE_ID_SCIENTIFIC && $v.nametype!='isValidNameOf'}
						{assign var=another_name value="`$v.uninomial` `$v.specific_epithet` `$v.infra_specific_epithet`"}
						{if $another_name!='' && $v.uninomial!=''}
							<tr><td style="white-space:nowrap">{$v.nametype_label|@ucfirst}</td><td><a href="name.php?id={$v.id}"><i>{$another_name}</i> {$v.authorship}</a></td></tr>
						{elseif $v.uninomial==''}
							{assign var=another_name value=$v.name|@replace:$v.authorship:''}
							<tr><td style="white-space:nowrap">{$v.nametype_label|@ucfirst}</td><td><a href="name.php?id={$v.id}"><i>{$another_name}</i> {$v.authorship}</a></td></tr>
						{else}
							<tr><td style="white-space:nowrap">{$v.nametype_label|@ucfirst}</td><td><a href="name.php?id={$v.id}">{$v.name}</a></td></tr>
						{/if}
					{else}
						<tr><td style="white-space:nowrap">{$v.nametype_label|@ucfirst}</td><td><a href="name.php?id={$v.id}">{$v.name}</a></td></tr>
					{/if}
				{/if}
			{/foreach}
			{if $expert || $organisation}
				{if $expert}
				<tr><td>Expert</td><td colspan="2">{$expert}{if $organisation} ({$organisation}){/if}</td></tr>
				{else}
				<tr><td>Organisatie</td><td colspan="2">{$organisation}</td></tr>
				{/if}
			{/if}
		</table>
	</p>

	<p>
		<h2>Indeling</h2>
		<ul class="taxonoverzicht">
			<li class="root">
			{foreach from=$classification item=v key=x}
			{if $v.parent_id!=null}{* skipping top most level "life" *}
				<span class="classification-preffered-name"><a href="nsr_taxon.php?id={$v.id}">{$v.taxon}</a></span>
				<span class="classification-rank">[{$v.rank_label}]</span>
				{if $v.common_name}<br />
				<span class="classification-accepted-name">{$v.common_name}</span>{/if}
				<ul class="taxonoverzicht">
					<li>
			{/if}
			{/foreach}
			{foreach from=$classification item=v key=x}
			{if $v.parent_id!=null}{* skipping top most level "life" *}
			</li></ul>
			{/if}
			{/foreach}
			</li>				
		</ul>
	</p>

	<p>
		{$content}
	</p>