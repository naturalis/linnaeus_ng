<input type="button" value="{t}Content{/t}"  onclick="window.open('../../admin-index.php','_self');" />
<input type="button" value="{t}Welcome{/t}" {if $subject=='Welcome'}disabled="disabled" style="color:red"{else}onclick="window.open('welcome.php','_self')"{/if} />
<input type="button" value="{t}Contributors{/t}" {if $subject=='Contributors'}disabled="disabled" style="color:red"{else}onclick="window.open('contributors.php','_self')"{/if} />
<input type="button" value="{t}About ETI{/t}" {if $subject=='About ETI'}disabled="disabled" style="color:red"{else}onclick="window.open('about_eti.php','_self')"{/if} />
