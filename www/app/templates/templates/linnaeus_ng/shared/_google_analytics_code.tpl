<script>

  (function(i,s,o,g,r,a,m) { i['GoogleAnalyticsObject']=r;i[r]=i[r]||function() {
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  } )(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

  {if $googleAnalyticsCode|is_array}
  {foreach $googleAnalyticsCode v k}
	ga('create', '{$v->code}', 'auto'{if $k>0}, 'clientTracker{$k}'{/if});
	ga('{if $k>0}clientTracker{$k}.{/if}send', 'pageview');
  {/foreach}
  {else}
	ga('create', '{$googleAnalyticsCode}', 'auto');
	ga('send', 'pageview');
  {/if}

</script>