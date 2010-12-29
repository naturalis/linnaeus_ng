{section name=l loop=$languages}
{if $activeLanguage==$languages[l].language_id}
{assign var=spellchecker_languages value=$spellchecker_languages|cat:'+'}
{/if}
{assign var=spellchecker_languages value=$spellchecker_languages|cat:$languages[l].language|cat:'='|cat:$languages[l].iso2|cat:','}
{/section}
<script type="text/javascript" src="{$baseUrl}admin/javascript/tinymce/jscripts/tiny_mce/tiny_mce.js" ></script >
{literal}
<script type="text/javascript">

function initTinyMce(litRefs,mediaRefs) {

	tinymce.create('tinymce.plugins.LinnaeusPlugin', {
		createControl: function(n, cm) {
			switch (n) {
				case 'litref':
					var mlb = cm.createListBox('litref', {
						 title : 'Literary references',
						 onselect : function(v) {
							 tinyMCE.execInstanceCommand(tinymce.EditorManager.activeEditor.id, "mceInsertContent",false,v);
							 //ed.execCommand("mceInsertContent",false,'[i]'+v+'[/i]');
						 }
					});
	
					var obj = $.parseJSON(litRefs);
	
					if (obj) {
						// Add some values to the list box
						for (var i=0;i<obj.length;i++) {
							
							var l = 
								obj[i].author_first+
								(obj[i].author_second!=null ?
									' &amp; ' + obj[i].author_second :
									(obj[i].multiple_authors=='1' ? ' et al.' : '' )
								) +
								' ('+obj[i].year+')';
						
							mlb.add(l,'[litref id="'+obj[i].id+'"]'+l+'[/litref]');
						}
					}
	
					// Return the new listbox instance
					return mlb;


			   case 'addlitref':
						var c = cm.createMenuButton('addlitref', {
								title : 'Create a literary reference',
								image : '../../media/system/tinymce/litref_add.png',
								onclick : function() {
									window.open('../literature/edit.php','_self');
								},
								icons : false
						});

						return c;

			   case 'addmedia':
						var c = cm.createMenuButton('addmedia', {
								title : 'Upload new media',
								image : '../../media/system/tinymce/film_add.png',
								onclick : function() {
									window.open('http://www.xs4all.nl/','_self');
								},
								icons : false
						});

						return c;

				case 'media':
					var mlb = cm.createListBox('media', {
						 title : 'Media link',
						 onselect : function(v) {
							 tinyMCE.execInstanceCommand(tinymce.EditorManager.activeEditor.id, "mceInsertContent",false,v);
							 //ed.execCommand("mceInsertContent",false,'[i]'+v+'[/i]');
						 }
					});

					var obj = $.parseJSON(mediaRefs);

					if (obj) {
						// Add some values to the list box
						for (var i=0;i<obj.length;i++) {
							mlb.add(obj[i].file_name+' ('+obj[i].mime_type+')', '[media id="'+obj[i].id+'"]'+obj[i].file_name+'[/media]');
						}
					}
	
					// Return the new listbox instance
					return mlb;
			}
	
			return null;
		}
	});
	
	// Register plugin with a short name
	tinymce.PluginManager.add('example', tinymce.plugins.LinnaeusPlugin);
	
	
	tinyMCE.init({
			mode : "textareas",
			theme : "advanced",
			plugins : "media,fullscreen,spellchecker,advhr,preview,print,advimage,searchreplace,table,directionality,-example",	
			
			// Theme options - button# indicated the row# only
		theme_advanced_buttons1 : "cut,copy,paste,|,undo,redo,|,search,replace,|,bold,italic,underline,formatselect,|,ltr,rtl,|,link,unlink,|,bullist,numlist,|,table,|,spellchecker,removeformat,charmap,|,code,preview,visualaid,fullscreen,print",
		theme_advanced_buttons2 : "litref,addlitref,|,media,addmedia",
		theme_advanced_buttons3 : "",
		theme_advanced_toolbar_location : "top",
		theme_advanced_toolbar_align : "left",
		theme_advanced_statusbar_location : "bottom",
	{/literal}{if $session.project.css_url!=''}  content_css : "{$session.project.css_url}",
	{/if}{literal}
		spellchecker_languages : "{/literal}{$spellchecker_languages}{literal}" //(n.b. no trailing comma in last line of code)
		//theme_advanced_resizing : true //leave this out as there is an intermittent bug.
	});

}
</script>
{/literal}


