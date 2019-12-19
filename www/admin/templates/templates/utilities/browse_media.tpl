{include file="../shared/admin-header.tpl"}

{literal}
<style>
td {
	border:1px dotted #ddd;
	vertical-align:central;
	text-align:center;
}
td:hover {
	background-color:#eee;
}
.image-frame {
	height:100px;
}
.image-subscript {
	font-size:9px;
}
.image-delete {
	text-align:left;
	padding-left:8px;
}
.delete {
	color:red;
	font-weight:bold;
	font-size:10px;	
	padding:0 2px 0 2px;
	cursor:pointer;
}
.image {
	width:auto;
	max-height:100px;
}
.edit-name {
	font-size:9px;
}

</style>
<script>
function deleteAll()
{
	if (confirm('Are you sure?'))
	{
		$('#action').val('purge');
		$('#theForm').attr('onsubmit','');
		$('#theForm').submit();
	}
}

function confirmDeleteImage(id) {
	if (confirm('Are you sure?')){
		window.open('?action=delete&id='+id,'_self');
	}
}
function confirmDeleteImages() {
	if ($('input[type=checkbox]:checked').length==0)
		return false;
	return confirm('Are you sure?');
}
function toggleCheckboxes() {
	$('input[type=checkbox]').each(function(i){
		$(this).attr('checked',$(this).attr('checked') ? false : true);
	});
}
var imageBeingEdited = null;
function enableTitleEditing(ele,id) {
	if (imageBeingEdited!=null)
		return;
	imageBeingEdited  = id;
	var oldStr = $(ele).html().trim();
	$(ele).html('<input type="text" id="edit-name" value="'+oldStr+'"">');
	$("#edit-name").focus();
	$("#edit-name").keyup(function(e){ 
		var code = e.which; // recommended to use e.which, it's normalized across browsers
		if (code==13) {
			var newStr = $("#edit-name").val();
			
			allAjaxHandle = $.ajax({
				url : "ajax_interface.php",
				type: "POST",
				data : ({
					action : 'change_media_name' ,
					id : id , 
					name : newStr , 
					time : allGetTimestamp()
				}),
				success : function (data) {
					if (data==1) {
						$(ele).html(newStr);
					} else {
						alert('Rename failed.');
						$(ele).html(oldStr);
					}
				}
			})	
		
			imageBeingEdited = null;
		} else
		if (code==27) {
			$(ele).html(oldStr);
			imageBeingEdited = null;
		}
	});

}
</script>
{/literal}

<div id="page-header-localmenu">
<div id="page-header-localmenu-content">
    <a href="mass_upload.php" class="allLookupLink">{t}Mass upload images{/t}</a>
    &nbsp;&nbsp;
    <a href="browse_media.php" class="allLookupLink">{t}Browse images{/t}</a>
</div>
</div>

<div id="page-main">
<p>
Double-click a filename to change it. Click 'delete' to delete a file. To delete multiple files, check the appropriate checkboxes
and click 'delete selected'. To delete <i>all</i> files, <a href="#" onclick="deleteAll();return false;">click here</a>.
</p>
<form method="post" id="theForm" onsubmit="return confirmDeleteImages();">
<input type="hidden" name="action" id="action" value="delete" />
<input type="hidden" name="rnd" value="{$rnd}" />
{if $files.files|@count==0}
(there are no files in your media folder)
{/if}
<table><tr>
{foreach from=$files.files item=v key=k}
<td>
    <div class="image-frame">
       	<a rel="prettyPhoto[gallery]" href="{$session.admin.project.urls.project_media}{$v}" title="{$v}">
	        <img src="{$session.admin.project.urls.project_media}{$v}" class="image" /><br />
		</a>
    </div>
    <div class="image-subscript" ondblclick="enableTitleEditing(this,{$k});">
		{$v}
    </div>
    <div class="image-delete">
        <input type="checkbox" name="delete[]" value="{$k}"  />
        <span class="delete" onclick="confirmDeleteImage({$k})">delete</span>
    </div>
</td>
{if ($k+1)%4==0}</tr><tr>{/if}
{/foreach}
</tr></table>
{if $files.files|@count>0}
<p>
<span class="a" onclick="toggleCheckboxes();return false;">(Un)check all</span><br />
</p>
<p>
<input type="submit" value="delete selected" />
</p>
</form>
{/if}

<p>
Please be aware that these images are very likely referred to from various modules in your project: species, matrix, introduction, etc. 
This function is just a file browswer: name changes or not deletions do <u>not</u> automatically propagate to the various referring modules.
</p>


</div>


{literal}
<script type="text/JavaScript">
$(document).ready(function(){

	if(jQuery().prettyPhoto) {
		prettyPhotoInit();
	}

});
</script>
{/literal}

{include file="../shared/admin-footer.tpl"}