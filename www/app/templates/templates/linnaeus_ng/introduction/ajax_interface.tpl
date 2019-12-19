{foreach $errors v}{$v}. {/foreach}{foreach $messages v}{$v}{/foreach}
{if $errors|@count!=0}<error>{else}{$returnText}{/if}