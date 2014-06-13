		<div>
			<p>
Naturalis is een project gestart om van zoveel mogelijk Nederlandse planten, dieren en schimmels de DNA barcode te bepalen. Een DNA barcode is een internationaal vastgesteld stukje DNA waaraan je een soort kunt herkennen. Het doel van het project is de opbouw van een collectie goed geïdentificeerde soorten met hun DNA barcodes. Deze collectie dient als ijkpunt voor de genetische herkenning van planten, dieren en schimmels. Meer info
			</p>
			<p>
Van de soort <i>{$taxon_display_name}</i> zijn onderstaande exemplaren verzameld voor barcodering. Gegevens bijgewerkt tot 26 augustus 2013.
			</p>
			<p>
				NB: de hieronder vermelde wetenschappelijke naam kan afwijken van de naam in het Soortenregister.
			</p>
			
			<table class="taxon-dna-table">
				<tr>
					<th>Registratienummer</th>
					<th>Verzameldatum, plaats</th>
					<th>Verzamelaar</th>
					<th>Soort</th>
				</tr>
				{foreach from=$content item=v}
				<tr><td>{$v.barcode}</td><td>{$v.date_literal}, {$v.location}</td><td>{$v.specialist}</td><td>{$v.taxon_literal}</td></tr>
				{/foreach}
			</table>

		</div>