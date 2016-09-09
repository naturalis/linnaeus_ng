	<div class="protection">
	
		{if $wetten}

			<h2>{t}Beschermingsstatus{/t}</h2>

			{foreach from=$wetten item=soort key=naam}
            	
				{* if $naam!=$names.nomen_no_tags}
				<h3><i>{$naam}</i></h3>
				{/if *}
				{if $wetten|@count>1}
				<h3><i>{$naam}</i></h3>
				{/if}
				
				<ul>
					{foreach from=$soort.wetten item=v key=wet}
					<li>
						<b>{$wet}</b>
						<ul>
							{foreach from=$v item=w}
							<li>
								{$w.categorie}<br />
								{$w.publicatie}
							</li>
							{/foreach}
						</ul>
					</li>
					{/foreach}
				</ul>
				
        {t}Bron:{/t} <a href="{$soort.url}">{if $wetten|@count>1}{$naam}{else}{t}soortgegevens{/t}{/if}</a> uit Beschermde natuur van Nederland: soorten in wetgeving en beleid (Ministerie van Economische Zaken)
	
			{/foreach}
			
		{/if}
    	
		{if $content}
      <h2>{t}Bedreiging en bescherming{/t}</h2>
			{$content}
		{/if}	
	</div>