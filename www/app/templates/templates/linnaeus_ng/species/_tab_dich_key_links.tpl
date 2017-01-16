{t}This taxon is keyed out in the following step of the single-entry key:{/t}
<p>
{foreach $content v k}
<a href="../key/index.php?choice=&step={$v.keystep_id}">{t}Step{/t} {$v.number}{if $v.title}. {$v.title}{/if}</a><br />
{/foreach}
</p>
