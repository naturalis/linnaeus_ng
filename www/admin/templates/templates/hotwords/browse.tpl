{include file="../shared/admin-header.tpl"}

<script>

function hotwordsDelete( id )
{
	if( confirm( _('Are you sure?') ) )
	{
		$('#action').val('delete_module');
		
		if ( id=='*' )
		{
			$('#action').val('delete_all');
		} 
		else
		if ( id )
		{
			$('#action').val('delete');
			$('#id').val(id);
		}

		$('#theForm').submit(); 
	}
}

</script>

<div id="page-main">

	<h3>Hotwords</h3>

    <h4>{if $controller}{t _s1=$controller}Hotwords in %s module{/t}{else}{t}All hotwords{/t}{/if} ({$num})</h4>
    
    
    {foreach $hotwords v}
    {$v.hotword}{if !$controller} <span style="color:#999">[{$v.controller}]{/if}</span> <span title="delete" class="a" onclick="hotwordsDelete('{$v.id}');">x</span><br />
    {/foreach}
    
    {if ($prevStart!=null && $prevStart!=-1) || ($nextStart!=null && $nextStart!=-1)}
        <div id="navigation">
            {if $prevStart!=-1}
            <span class="a" onclick="goNavigate({$prevStart});">< {t}previous{/t}</span>
            {/if}
            {if $nextStart!=-1}
            <span class="a" onclick="goNavigate({$nextStart});">{t}next{/t} ></span>
            {/if}
        </div>
    {/if}
    
    {if $hotwords|@count >0}
    <p>
        {if $controller}
        <span title="{t}delete all{/t}" class="a" onclick="hotwordsDelete();">{t}delete all hotwords in this module{/t}</span>
        {else}
        <span title="{t}delete all{/t}" class="a" onclick="hotwordsDelete('*');">{t}delete all hotwords{/t}</span>
        {/if}
    </p>
    {/if}
    
    <p>
        <a href="index.php">{t}back{/t}</a>
    </p>

</div>

<form id="theForm" method="post">
    <input type="hidden" id="id" name="id" value="" />
    <input type="hidden" id="action" name="action" value="" />
    <input type="hidden" id="c" name="c" value="{$controller}" />
</form>

{include file="../shared/admin-footer.tpl"}