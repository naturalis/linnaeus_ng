{if $logo}
    {literal}
    #header-container {
    {/literal}
        background: url('{$projectMedia}{$logo}') no-repeat;
        border-bottom: 1px solid #034e85;
    {literal}
    }
    {/literal}
{/if}

{literal}
#contents-icon {
{/literal}
    cursor: pointer; 
    background: url('{$pathToDefaultMedia}contents.png') 5px 0 no-repeat;
{literal}
}
{/literal}

{literal}
#contents-icon:active {
{/literal}
    background-position: 5px 1px;
{literal}
}
{/literal}

{literal}
#previous-icon {
{/literal}
    cursor: pointer; 
    background: url('{$pathToDefaultMedia}previous.png') 5px 0 no-repeat;
{literal}
}
{/literal}

{literal}
#previous-icon:active {
{/literal}
    background-position: 5px 1px;
{literal}
}
{/literal}

{literal}
#previous-icon-inactive {
{/literal}
    background: url('{$pathToDefaultMedia}previous.png') 5px 0 no-repeat;
    opacity: 0.4;
    filter: alpha(opacity=40); 
{literal}
}
{/literal}

{literal}
#next-icon {
{/literal}
    width: 40px;
    margin-right: 15px;
    cursor: pointer; 
    background: url('{$pathToDefaultMedia}next.png') no-repeat;
{literal}
}
{/literal}

{literal}
#next-icon:active {
{/literal}
    background-position: 0 1px;
{literal}
}
{/literal}

{literal}
#next-icon-inactive {
{/literal}
    background: url('{$pathToDefaultMedia}previous.png') top center no-repeat;
    opacity: 0.4;
    filter: alpha(opacity=40); 
{literal}
}
{/literal}
