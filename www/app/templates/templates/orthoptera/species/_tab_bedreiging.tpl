	<div>
	
		{if $wetten}
	
			<h2>Beschermingsstatus</h2>

			{foreach $wetten soort naam}
			<p>
            	
				{* if $naam!=$names.nomen_no_tags}
				<h3><i>{$naam}</i></h3><br />
				{/if *}
				{if $wetten|@count>1}
				<h3><i>{$naam}</i></h3><br />
				{/if}
				
				<ul>
					{foreach $soort.wetten v wet}
					<li>
						<b>{$wet}</b>
						<ul>
							{foreach $v w}
							<li>
								{$w.categorie}<br />
								{$w.publicatie}
							</li>
							{/foreach}
						</ul>
					</li>
					{/foreach}
				</ul>
				<br />
				
				{* Zie ook: <a href="{$soort.url}">EL&I wettelijke bescherming, beleid en signalering</a><br /><br /> *}
                Bron: <a href="{$soort.url}">{if $wetten|@count>1}{$naam}{else}soortgegevens{/if}</a> uit Beschermde natuur van Nederland: soorten in wetgeving en beleid (Ministerie van Economische Zaken)
	
			</p>
			{/foreach}
			
		{/if}
	
		<p>
			{$content}
		</p>
	
		<script type="text/JavaScript">
		$(document).ready(function()
		{
			// remove inherited html-embedded (and ouddated) status
	//					$('#lnv').remove();
		});
		</script>
	
	</div>