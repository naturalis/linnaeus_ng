var drnzkr_startDier=null;

function hook_prePrintResults()
{
	for(var i=0;i<data.dataset.length;i++)
	{
		if (data.dataset[i].images)
		{
			for(var j=0;j<data.dataset[i].images.length;j++)
			{
				if (data.dataset[i].images[j].overview_image && data.dataset[i].images[j].overview_image==1)
				{
					data.dataset[i].info.url_image=data.dataset[i].images[j].file_name;
					var url= data.dataset[i].info.url_thumbnail=data.dataset[i].images[j].file_name;
					var filename = url.substring(url.lastIndexOf('/')+1);
					data.dataset[i].info.url_thumbnail=filename.replace(".jpg","_thumb.jpg");
				}
			}
		}
	}

	drnzk_verberg_dier();
}

function hook_postPrintResults()
{
	$('#result-count-container').html( data.resultset.length );
	drnzkr_open_dier_link();
}

function hook_postPrintMenu()
{
	drnzkr_update_choices_made();
}

function hook_postApplyScores()
{
	drnzkr_update_navigatie();
}

function hook_preSetStateValue(state)
{
	var d=state.split(':');
	if (!data.statecount[d[2]] || data.statecount[d[2]]<1)
	{
		return false;
	}
}


function drnzkr_navigeren( target )
{
	if (target=='eerste')
	{
		matrixsettings.start=0;
	}
	else
	if (target=='laatste')
	{
		matrixsettings.start=Math.floor(data.resultset.length/matrixsettings.perPage)*matrixsettings.perPage;
	}
	else
	if (target=='vorige')
	{
		matrixsettings.start=matrixsettings.start-matrixsettings.perPage;
	}
	else
	{
		matrixsettings.start=matrixsettings.start+matrixsettings.perPage;
	}

	if (matrixsettings.start>data.resultset.length)
	{
		matrixsettings.start=matrixsettings.start-matrixsettings.perPage;
	}
	if (matrixsettings.start<0)
	{
		matrixsettings.start=0;
	}

	printResults();
	drnzkr_update_navigatie();
	drnzkr_result_style_update();
}

function drnzkr_update_navigatie()
{
	if (matrixsettings.start==0)
	{
		$('#prev-button-container-top,#prev-button-container-bottom').css('visibility','hidden');
	}
	else
	{
		$('#prev-button-container-top,#prev-button-container-bottom').css('visibility','visible');
	}

	if ((matrixsettings.start+matrixsettings.perPage)>=data.resultset.length)
	{
		$('#next-button-container-top,#next-button-container-bottom').css('visibility','hidden');
	}
	else
	{
		$('#next-button-container-top,#next-button-container-bottom').css('visibility','visible');
	}
}

function drnzkr_toon_dier( p )
{

	$.ajax(
	{
		url : '../species/taxon_overview.php',
		type: 'POST',
		data : ({
			id : p.id,
			back : p.back,
			hotwords: false,
			navigation: false,
			time : getTimestamp()
		}),
		success : function (data)
		{
			if (data)
			{
				if ( p.name )
				{
					try {
						// altering the URL without reloading for bookmarking purposes
						var stateObj = { foo: "bar" };
						window.history.pushState(stateObj, p.name , 'index.php?dier='+p.name );
					}
					catch(err) {}
				}
				
				//console.log( data );
				$('#dier-content').html( data );
				$('#dier-content-wrapper').css('visibility','visible');
				drnzkr_prettyPhotoInit();
			}
		}
	});
}

function drnzk_verberg_dier()
{
	$('#dier-content').html('');
	$('#dier-content-wrapper').css('visibility','hidden');

}

function drnzkr_prettyPhotoInit()
{
	if(!$.prettyPhoto) return;

 	$("a[rel^='prettyPhoto']").prettyPhoto({
		allow_resize:true,
		animation_speed:50,
 		opacity: 0.70, 
		show_title: false,
 		overlay_gallery: false,
 		social_tools: false
 	});
}

function drnzkr_open_dier_link()
{
	if (!drnzkr_startDier) return;

	drnzkr_startDier=$('<textarea />').html( drnzkr_startDier ).text(); // convert entities to characters

	var n=null;

	for(var i=0;i<data.resultset.length;i++)
	{
		var d=data.resultset[i];

		if (d.commonname.toLowerCase()==drnzkr_startDier.toLowerCase())
		{
			n=d
			break;
		}
	}

	if (n)
	{
		drnzkr_startDier=null;

		for (var j=0;j<Math.floor(i/matrixsettings.perPage);j++)
		{
			drnzkr_navigeren('volgende');
		}
	
		drnzkr_toon_dier( { id:n.id, type:n.type } );
	}	
}


function drnzkr_update_choices_made()
{
	$('#gemaakte-keuzes').html( "" );

	var d=Array();

	for(var i in data.states)
	{
		var state = data.states[i];
	
		for(var j=0;j<data.menu.length;j++)
		{
			if (data.menu[j].id==state.characteristic_id)
			{
				var characterinfo=data.menu[j].label;
			}
			
			if (data.menu[j].chars) 
			{
				for(var k=0;k<data.menu[j].chars.length;k++)
				{
					if (data.menu[j].chars[k].id==state.characteristic_id)
					{
						var characterinfo=data.menu[j].chars[k].label;
					}
				}
			}
		}
		
		if (characterinfo)
		{
			characterinfo=characterinfo.split('|');
			characterinfo=characterinfo[0];
		}
		
		for(var k=0;k<data.characterStates.length;k++)
		{
			if (data.characterStates[k].id==state.id)
			{
				state.file_name=data.characterStates[k].file_name;
				break;
			}
		}
		
		d.push(
			fetchTemplate( 'drzkr_selectedFacetHtmlTemplate' )
				.replace('%CHARACTER-LABEL%',characterinfo)
				.replace('%STATE-VAL%',state.val)
				.replace('%STATE-LABEL%',state.label)
				.replace('%ICON%',state.file_name)
				//.replace('%ICON%',matrixsettings.imageRootProject+state.file_name)
				.replace('%IMG-ROOT-SKIN%',matrixsettings.imageRootSkin)
		);

		//if (d.length<data.resultset.length) d.push('<li class="lijn no-text">|</li>');

	}

	$('#gemaakte-keuzes').html( d.join("") );

	if (d.length==0)
	{
		$('.sub-header-wrapper').css('display','none');
	}
	else
	{
		$('.sub-header-wrapper').css('display','block');
	}
}


function charClick( item )
{
	setStateValue( $(item).attr('data-value') );
	$('.facetgrouppage-close-btn').trigger('click');
	return false;
}


function drnzkr_update_states()
{
	$('a[id^="state-"]').addClass('ui-disabled').prop('onclick',null).off('click');

	var d=getStateCount();

	for(var i in d)
	{
		if (d[i]>0) $('#state-'+i).removeClass('ui-disabled').on('click',function(){ charClick(this);});
	}
}

function drnzkr_result_style_update()
{
    $('.result0').hover(
        function()
        {
            $(this).children('a').children('table').children('tbody').children('tr').children('td').css('color','#25AAD5');
        },
        function()
        {
            $(this).children('a').children('table').children('tbody').children('tr').children('td').css('color','#000000');
        }
    );
}