{include file="../shared/header.tpl"}

{if $alpha}
	<div id="alphabet">
		
		{foreach from=$alpha key=k item=v}
			{if $letter==$v}
				<span class="letter-active">{$v}</span>
			{else}
				{if $useJavascriptLinks}
					<span class="letter" onclick="goAlpha('{$v}')">{$v}</span>
				{else}
					<a class="letter" href="?letter={$v}">{$v}</a>
				{/if}
			{/if}
		{/foreach}

	</div>
{/if}

<div id="page-main" class="template-index">

	{if !$refs}
		{t}No literature has been defined.{/t}
	{else}

		<table id="references">
			<thead> 
				<tr>
					<th id="th-author">{t}author(s){/t}</th>
					<th id="th-year">{t}year of publication{/t}</th>
				</tr>
			</thead>

			<tbody>
				{foreach from=$refs key=k item=v}

					<tr class="highlight">
						{if $useJavascriptLinks}
						<td class="a" onclick="goLiterature({$v.id})">
							{$v.author_full}
						</td>
						{else}
						<td>
							<a href="../literature/reference.php?id={$v.id}">{$v.author_full}</a>
						</td>
						{/if}
						<td>
							{$v.year}
						</td>
					</tr>
				{/foreach}
			</tbody>
		</table>
	{/if}

</div>

{include file="../shared/footer.tpl"}
