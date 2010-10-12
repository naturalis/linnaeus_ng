{section name=i loop=$errors}{$errors[i]} {/section}{section name=i loop=$messages}{$messages[i]}{/section}
{if $errors|@count!=0}<error>{else}{$returnText}{/if}