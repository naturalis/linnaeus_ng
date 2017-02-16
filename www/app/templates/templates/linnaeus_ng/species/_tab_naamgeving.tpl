	<p>

		{assign var=names value=$content}

	    <p>
	    {foreach from=$names.list_non_nsr.valid_names item=v}
            {capture extras}
            {if $v.rank_label}[{$v.rank_label}]{/if}
            {if $v.addition[$currentLanguageId].addition}({$v.addition[$currentLanguageId].addition}){/if}
            {/capture}
            <b>{$v.name}</b>{$smarty.capture.extras}
            {if $v.expert != ''}{t}Expert{/t}: {$v.expert}<br>{/if}
            {if $v.organisation_name != ''}{t}Organisation{/t}: {$v.organisation_name}<br>{/if}
		{/foreach}
		</p>

		{if !empty($names.list_non_nsr.synonyms)}
			<p><b>{t}Synonyms{/t}</b>:<br>
		    {foreach from=$names.list_non_nsr.synonyms item=v}
	            {capture extras}
	            {if $v.rank_label}[{$v.rank_label}]{/if}
	            {if $v.addition[$currentLanguageId].addition}({$v.addition[$currentLanguageId].addition}){/if}
	            {/capture}
				{assign var=another_name value="`$v.uninomial` `$names.hybrid_marker``$v.specific_epithet` `$v.infra_specific_epithet`"}
                {if $another_name!='' && $v.uninomial!=''}
                   <i>{$another_name}</i> {$v.authorship}{$smarty.capture.extras}<br>
                 {elseif $v.uninomial==''}
                     {assign var=another_name value=$v.name|@replace:$v.authorship:''}
                     <i>{$another_name}</i> {$v.authorship}{$smarty.capture.extras}<br>
                 {else}
                    {$v.name}{$smarty.capture.extras}
                {/if}
                {if $v.nametype != $smarty.const.PREDICATE_SYNONYM} [{$v.nametype_label}]{/if}
            {/foreach}
		{/if}

		{if !empty($names.list_non_nsr.common_names)}
			<p><b>{t}Common names{/t}</b>:<br>
		    {foreach from=$names.list_non_nsr.common_names item=v}
                {$v.name} [{t}{$v.language}{/t}; {t}{$v.nametype_label}{/t}]<br>
           {/foreach}
           </p>
		{/if}

<!--
		<table id="names-table">
			{foreach from=$names.list item=v}

                {if $v.expert.name}{assign var=expert value=$v.expert.name}{/if}
                {if $v.organisation.name}{assign var=organisation value=$v.organisation.name}{/if}
                {capture extras}
                {if $v.rank_label}[{$v.rank_label}]{/if}
                {if $v.language_id!=$smarty.const.LANGUAGE_ID_SCIENTIFIC} ({$v.language}){/if}
                {if $v.addition[$currentLanguageId].addition}({$v.addition[$currentLanguageId].addition}){/if}
                {/capture}

	            <tr>
                    {if $v.nametype==$smarty.const.PREDICATE_VALID_NAME && $taxon.base_rank_id<$smarty.const.SPECIES_RANK_ID}
						<td style="white-space:nowrap">
                        	{t}{$v.nametype_label}{/t}
                         </td>
                         <td>
                         	<b>{$v.name}</b>{$smarty.capture.extras}
                         </td>
					{else}
                        {if $v.language_id==$smarty.const.LANGUAGE_ID_SCIENTIFIC && $v.nametype!=$smarty.const.PREDICATE_VALID_NAME}
                            {assign var=another_name value="`$v.uninomial` `$names.hybrid_marker``$v.specific_epithet` `$v.infra_specific_epithet`"}
                            {if $another_name!='' && $v.uninomial!=''}
                                <td style="white-space:nowrap">
                                    {t}{$v.nametype_label}{/t}
                                </td>
                                <td>
                                    <a href="name.php?id={$v.id}"><i>{$another_name}</i> {$v.authorship}</a>{$smarty.capture.extras}
                                </td>
                            {elseif $v.uninomial==''}
                                {assign var=another_name value=$v.name|@replace:$v.authorship:''}
                                <td style="white-space:nowrap">
                                    {t}{$v.nametype_label}{/t}
                                </td>
                                <td>
                                    <a href="name.php?id={$v.id}"><i>{$another_name}</i> {$v.authorship}</a>{$smarty.capture.extras}
                                 </td>
                            {else}
                                <td style="white-space:nowrap">
                                    {t}{$v.nametype_label}{/t}
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
                                {t}{$v.nametype_label}{/t}
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
				<tr><td>{t}Organisation{/t}</td><td colspan="2">{$organisation}</td></tr>
				{/if}
			{/if}
		</table>
-->

	</p>
