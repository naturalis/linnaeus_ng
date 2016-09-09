<div>
    
    {assign var=trendByYear value=$trendData.byYear|@count>0}
    {assign var=trendByTrend value=$trendData.byTrend|@count>0}
    {assign var=trendSources value=$trendData.sources|@count>0}

    {if $trendByYear || $trendByTrend}

        <h2>{t}Trend{/t}</h2>
        {if $trendByYear}
        <div id="graph" style="height:300px;"></div>
        {/if}
        {if $trendByTrend}
        {foreach from=$trendData.byTrend item=v}
        {$v.trend_label}: {$v.trend}<br />
        {/foreach}
        {/if}
        {if $trendSources}
        <br />
        {t}Bron{/t}:
        {foreach from=$trendData.sources item=v key=k}
        {if $k>0}, {/if}{$v}
        {/foreach}
        (via <a href="http://www.netwerkecologischemonitoring.nl" target="_blank">{t}Netwerk Ecologische Monitoring{/t}</a>)
        {/if}
    
    {/if}

    <p>
        {$content}
    </p>

</div>
