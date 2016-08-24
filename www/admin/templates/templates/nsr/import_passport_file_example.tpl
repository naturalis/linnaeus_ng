{foreach $categories  v k}{if $v.type!='auto'}{foreach $languages l};{$v.page}{/foreach}{/if}{/foreach}

{foreach $categories  v k}{if $v.type!='auto'}{foreach $languages l};{$l.language}{/foreach}{/if}{/foreach}

{foreach $taxa t}
{$t.taxon}{foreach $categories  v k}{if $v.type!='auto'}{foreach $languages l};"About '{$v.page}' of {$t.taxon} (in {$l.language})"{/foreach}{/if}{/foreach}

{/foreach}
