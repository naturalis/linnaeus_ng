{section name=l loop=$languages}
{if $activeLanguage==$languages[l].language_id}
{assign var=spellchecker_languages value=$spellchecker_languages|cat:'+'}
{/if}
{assign var=spellchecker_languages value=$spellchecker_languages|cat:$languages[l].language|cat:'='|cat:$languages[l].iso2|cat:','}
{/section}
<script type="text/javascript" src="{$baseUrl}admin/vendor/tinymce/jquery.tinymce.min.js" ></script>
<script type="text/javascript">
var mediaPath = '{$session.admin.project.urls.project_media|@addslashes}';
{literal}
var inclLiteraryButtons = true;
var inclMediaButtons = true;

function initTinyMce(litRefs,mediaRefs) {

	if (litRefs==false) inclLiteraryButtons = false;
	if (mediaRefs==false) inclMediaButtons = false;

	tinymce.create('tinymce.plugins.LinnaeusPlugin', {
		createControl: function(n, cm) {
			switch (n) {
				case 'litref':
					var mlb = cm.createListBox('litref', {
						 title : 'Literary references',
						 onselect : function(v) {
						 	if (v==undefined) return;
							 tinyMCE.execInstanceCommand(tinymce.EditorManager.activeEditor.id,"mceInsertContent",false,v);
							 if (v=='[new litref]') {
								allAjaxAsynchMode = false;
								if (typeof taxonSaveDataAll == 'function') taxonSaveDataAll();
								window.open('../literature/edit.php?add=hoc&action=new','_self');
							 }
							 //ed.execCommand("mceInsertContent",false,'[i]'+v+'[/i]');
						 }
					});
	
					mlb.add('create a new reference','[new litref]');

					var obj = $.parseJSON(litRefs);
	
					if (obj) {
						mlb.add('----------------------',null);
						for (var i=0;i<obj.length;i++) {
							
							var l = 
								obj[i].author_first+
								(obj[i].author_second!=null ?
									' &amp; ' + obj[i].author_second :
									(obj[i].multiple_authors=='1' ? ' et al.' : '' )
								) +
								' ('+obj[i].year+')';
						
							mlb.add(l,'<span style="text-decoration:underline;color:#CC0000;cursor:pointer;" onclick="goLiterature('+obj[i].id+');">'+l+'</span>');
						}
					}

					return mlb;


			   case 'addlitref':
						var c = cm.createMenuButton('addlitref', {
								title : 'Create a literary reference',
								image : '../../media/system/tinymce/litref_add.png',
								onclick : function() {
									tinyMCE.execInstanceCommand(tinymce.EditorManager.activeEditor.id,"mceInsertContent",false,'[new litref]');
									allAjaxAsynchMode = false;
									if (typeof taxonSaveDataAll == 'function') taxonSaveDataAll();
									window.open('../literature/edit.php?add=hoc&action=new','_self');
								},
								icons : false
						});

						return c;

				case 'linkMedia':
					var mlb = cm.createListBox('linkMedia', {
						 title : 'Media link',
						 onselect : function(v) {
						 	if (v==undefined) return;
							 tinyMCE.execInstanceCommand(tinymce.EditorManager.activeEditor.id, "mceInsertContent",false,v);
							 //ed.execCommand("mceInsertContent",false,'[i]'+v+'[/i]');
							 if (v=='[new media]') {
								allAjaxAsynchMode = false;
								if (typeof taxonSaveDataAll == 'function') taxonSaveDataAll();
								window.open('media_upload.php?add=hoc','_self');
							 }
						 }
					});
		
					mlb.add('upload new media','[new media]');

					var obj = $.parseJSON(mediaRefs);

					if (obj) {
						mlb.add('----------------------',null);
						for (var i=0;i<obj.length;i++) {
							mlb.add(
								obj[i].file_name+' ('+obj[i].mime+')',
								'<span class="inline-'+obj[i].mime+'" onclick="showMedia(\''+mediaPath+obj[i].file_name+'\',\''+obj[i].file_name+'\');">'+obj[i].file_name+'</span>'
							);

						}
					}

					return mlb;

			   case 'addmedia':
						var c = cm.createMenuButton('addmedia', {
								title : 'Upload new media',
								image : '../../media/system/tinymce/film_add.png',
								onclick : function() {
									tinyMCE.execInstanceCommand(tinymce.EditorManager.activeEditor.id,"mceInsertContent",false,'[new media]');
									allAjaxAsynchMode = false;
									if (typeof taxonSaveDataAll == 'function') taxonSaveDataAll();
									window.open('media_upload.php?add=hoc','_self');
								},
								icons : false
						});

						return c;

			   case 'addinternallink':
						var c = cm.createMenuButton('addinternallink', {
								title : 'Add an internal link',
								image : '../../media/system/tinymce/link_add.png',
								onclick : function() {
									intLinkShowSelector();
								},
								icons : false
						});

						return c;

			}
	
			return null;
		}
	});

	// Register plugin with a short name
	tinymce.PluginManager.add('example', tinymce.plugins.LinnaeusPlugin);

	var propertyList = {
		entity_encoding : "raw",
		mode : "textareas",
		theme : "modern",
		init_instance_callback : function(editor) {
		    tMCEOnInit();
        },
        plugins: 'print preview fullpage searchreplace autolink directionality visualblocks visualchars fullscreen image link media template codesample table charmap hr pagebreak nonbreaking anchor toc insertdatetime advlist lists textcolor wordcount imagetools contextmenu colorpicker textpattern help code',
        menubar: false,
        toolbar: 'formatselect | bold italic strikethrough forecolor backcolor | link | alignleft aligncenter alignright alignjustify  | numlist bullist outdent indent  | removeformat | code',
		theme_modern_toolbar_location : "top",
		theme_modern_toolbar_align : "left",
		theme_modern_statusbar_location : "bottom",
	{/literal}{if $session.admin.project.css_url!=''}  content_css : "{$session.admin.project.css_url}?rnd={$rnd}",
	{/if}{literal}
		spellchecker_languages : "{/literal}{$spellchecker_languages}{literal}",
		valid_elements : '*[*]', // allow it all - i've had enough of disappearing spans and images
		extended_valid_elements : "b/strong,i/em", // change strong to b, em to i
        paste_auto_cleanup_on_paste : true // cleanup pasted ms word xml
	};

	if (inclLiteraryButtons && inclMediaButtons) {
	 	//propertyList.theme_advanced_buttons2 = "litref,addlitref,|,media,addmedia,|,addinternallink";
	 	propertyList.theme_modern_buttons2 = "litref,linkMedia,addinternallink";
	 	propertyList.theme_modern_buttons3 = "";
	} else
	if (inclLiteraryButtons && !inclMediaButtons) {
	 	//propertyList.theme_advanced_buttons2 = "litref,addlitref,|,addinternallink";
	 	propertyList.theme_modern_buttons2 = "litref,addinternallink";
	 	propertyList.theme_modern_buttons3 = "";
	} else
	if (!inclLiteraryButtons && inclMediaButtons) {
	 	//propertyList.theme_advanced_buttons2 = "media,addmedia,|,addinternallink";
	 	propertyList.theme_modern_buttons2 = "linkMedia,addinternallink";
	 	propertyList.theme_modern_buttons3 = "";
	} else {
	 	propertyList.theme_modern_buttons2 = "addinternallink";
	 	propertyList.theme_modern_buttons3 = "";
	}


	tinyMCE.init(propertyList);

}


var tMCEEditorFirstLoad=Array();

function tMCEFirstUndoPurge(editorname)
{
	for(var i in tinyMCE.editors) 
	{
		if(tinyMCE.editors[i].editorId==editorname && tMCEEditorFirstLoad[editorname]!==false)
		{
			tinyMCE.editors[i].undoManager.clear()
			tMCEEditorFirstLoad[editorname]=false;

		}
	}
}

// function called after tMCE init
function tMCEOnInit() {

	// checks for the existence of onInitTinyMce() function and executes; for functions that are dependent on tMCE being loaded and ready
	if (typeof onInitTinyMce == 'function') {

		onInitTinyMce();

	}

}
</script>
{/literal}
