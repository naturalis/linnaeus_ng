	<p>
		<h2 id="name-header">{t}Naamgeving{/t}</h2>
       
   		{assign var=names value=$content}

		<table id="names-table">
			{foreach from=$names.list item=v}

                {if $v.expert.name}{assign var=expert value=$v.expert.name}{/if}
                {if $v.organisation.name}{assign var=organisation value=$v.organisation.name}{/if}
                {capture extras}
                {if $v.rank_label}[{$v.rank_label}]{/if}
                {if $v.addition[$currentLanguageId].addition}({$v.addition[$currentLanguageId].addition}){/if}
                {/capture}
    
	            <tr>
                    {if $v.nametype==$smarty.const.PREDICATE_VALID_NAME && $taxon.base_rank_id<$smarty.const.SPECIES_RANK_ID}
						<td style="white-space:nowrap">
                        	{$v.nametype_label|@ucfirst}
                         </td>
                         <td>
                         	<b>{$v.name}</b>{$smarty.capture.extras}
                         </td>
					{else}
                        {if $v.language_id==$smarty.const.LANGUAGE_ID_SCIENTIFIC && $v.nametype!=$smarty.const.PREDICATE_VALID_NAME}
                            {assign var=another_name value="`$v.uninomial` `$names.hybrid_marker``$v.specific_epithet` `$v.infra_specific_epithet`"}
                            {if $another_name!='' && $v.uninomial!=''}
                                <td style="white-space:nowrap">
                                    {$v.nametype_label|@ucfirst}
                                </td>
                                <td>
                                    <a href="name.php?id={$v.id}"><i>{$another_name}</i> {$v.authorship}</a>{$smarty.capture.extras}
                                </td>
                            {elseif $v.uninomial==''}
                                {assign var=another_name value=$v.name|@replace:$v.authorship:''}
                                <td style="white-space:nowrap">
                                    {$v.nametype_label|@ucfirst}
                                </td>
                                <td>
                                    <a href="name.php?id={$v.id}"><i>{$another_name}</i> {$v.authorship}</a>{$smarty.capture.extras}
                                 </td>
                            {else}
                                <td style="white-space:nowrap">
                                    {$v.nametype_label|@ucfirst}
                                </td>
                                <td>
                                    <a href="name.php?id={$v.id}">{$v.name}</a>{$smarty.capture.extras}
                                </td>
                            {/if}
                        {else}
                            <td style="white-space:nowrap">
                                {if $v.nametype==$smarty.const.PREDICATE_ALTERNATIVE_NAME && $names.language_has_preferredname[$v.language_id]!=true && $v.alt_alt_nametype_label}
                                {$v.alt_alt_nametype_label|@ucfirst}
                                {else}
                                {$v.nametype_label|@ucfirst}
                                {/if}
                            </td>
                            <td>
                                <a href="name.php?id={$v.id}">{$v.name}</a>{$smarty.capture.extras}
                            </td>
                        {/if}
                    {/if}
                </tr>
			{/foreach}
			{if $expert || $organisation}
				{if $expert}
				<tr><td>{t}Expert{/t}</td><td colspan="2">{$expert}{if $organisation} ({$organisation}){/if}</td></tr>
				{else}
				<tr><td>{t}Organisatie{/t}</td><td colspan="2">{$organisation}</td></tr>
				{/if}
			{/if}
		</table>
	</p>

	<p>
		<h2>{t}Indeling{/t}</h2>

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

	<!-- p>
		{$content}
	</p -->
