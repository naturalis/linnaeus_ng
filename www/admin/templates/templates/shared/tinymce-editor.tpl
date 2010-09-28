{section name=l loop=$languages}
{if $activeLanguage==$languages[l].language_id}
{assign var=spellchecker_languages value=$spellchecker_languages|cat:'+'}
{/if}
{assign var=spellchecker_languages value=$spellchecker_languages|cat:$languages[l].language|cat:'='|cat:$languages[l].iso2|cat:','}
{/section}

<script type="text/javascript" src="{$baseUrl}admin/javascript/tinymce/jscripts/tiny_mce/tiny_mce.js" ></script >
{literal}
<script type="text/javascript">
tinyMCE.init({
		mode : "textareas",
		theme : "advanced",
		plugins : "media,fullscreen,spellchecker,advhr,preview,print,advimage,searchreplace,table,directionality",	
		
		// Theme options - button# indicated the row# only
	theme_advanced_buttons1 : "cut,copy,paste,|,undo,redo,|,search,replace,|,spellchecker,removeformat,charmap,|,code,preview,visualaid,fullscreen,|,print,|,link,unlink,anchor,image,media,|,forecolor,backcolor,|,advhr",
	theme_advanced_buttons2 : "bold,italic,underline,strikethrough,|,fontselect,fontsizeselect,formatselect,|,ltr,rtl,|,sub,sup,|,blockquote,|,justifyleft,justifycenter,justifyright,justifyfull,|,outdent,indent,|,bullist,numlist,|,table",
	theme_advanced_buttons3 : "",
	theme_advanced_toolbar_location : "top",
	theme_advanced_toolbar_align : "left",
	theme_advanced_statusbar_location : "bottom",
	spellchecker_languages : "{/literal}{$spellchecker_languages}{literal}" //(n.b. no trailing comma in last line of code)
	//theme_advanced_resizing : true //leave this out as there is an intermittent bug.
});
</script>
{/literal}