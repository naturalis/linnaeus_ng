{include file="../shared/header.tpl"}

{if $alpha}
	<div class="alphabet">
		{foreach $alpha v k}
			{if $letter==$v}
				<span class="letter-active">{$v}</span>
			{else}
                <a class="letter" href="?letter={$v}">{$v}</a>
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
				{foreach $refs v k}

					<tr class="highlight">
						<td>
							<a href="../literature/reference.php?id={$v.id}">{$v.author_full}</a>
						</td>
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
