// "constants"
var GROUP = 'c_group';
var CHARACTER = 'character';
var STATE = 'state';

var appController = (function() {

	var error=null;
	var query=null;
	var callbacks=Array();
	var variables={};
	var db=null;
	var results={taxa:{},variations:{}};
	var id=null;
	var matrixId=null;
	var languageId=null;
	var isVariation=false;
	var forceStateImages=true;
	var imgRoot=null;
	var utf8decode=false;

	// extensions
	Object.size = function(obj) {
		var size = 0, key;
		for (key in obj)
			if (obj.hasOwnProperty(key)) size++;
		return size;
	};

	String.prototype.ucwords = function() {
		str = this.toLowerCase();
		return str.replace(/(^([a-zA-Z\p{M}]))|([ -][a-zA-Z\p{M}])/g,
			function($1){
				return $1.toUpperCase();
			});
	}


	// private functions
	function setError(err)
	{
		error=err;
	}

	function getError()
	{
		return error;
	}

	function setLanguageId(i)
	{
		languageId=i;
	}

	function getLanguageId()
	{
		return languageId;
	}

	function setMatrixId(i)
	{
		matrixId=i;
	}

	function getMatrixId()
	{
		return matrixId;
	}

	function setId(i)
	{
		id=i;
	}

	function getId()
	{
		return id;
	}

	function setImgRoot(i)
	{
		imgRoot=i;
	}

	function getImgRoot()
	{
		return imgRoot;
	}

	function getUtf8decode()
	{
		return utf8decode;
	}
	
	function utf8decode(s)
	{
		// encode: unescape(encodeURIComponent(s));
		return getUtf8decode() ? decodeURIComponent(escape(s)) : s;
	}

	function setCallback(c)
	{
		callbacks.push(c);
		return callbacks.length-1;
	}

	function getCallback(i)
	{
		return callbacks[i];
	}

	function setIsVariation(s)
	{
		if (typeof s!='boolean') return;
		isVariation=s;
	}

	function getIsVariation()
	{
		return isVariation;
	}


	function doReinitialise()
	{
		for(var property in variables) delete variables[property];
	};
				
	function setSelectedState(property,value) 
	{
		if (value==undefined || value==null)
			delete variables[property];
		else
			variables[property]=value;
	};


	function getSelectedState(property)
	{
		if (property==null)
			return variables;
		else
			return variables[property];
	};
	

	function getImplodedStates()
	{
		var buffer=Array();
		for(var property in variables) {
			if (variables[property]==true)
				buffer.push(property);
		}
		return buffer.join(',');
	};

	function setQuery(q)
	{
		query=q;
	};

	function getData(callback,index)
	{

		$.ajax({
			url : 'app_controller_interface.php',
			type: 'POST',
			data : ({
				'action' : 'query',
				'query' : query ,
				'language' : getLanguageId() ,
				'matrix' : getMatrixId() ,
				'states' : getImplodedStates() ,
				'results' : results ,
				'force' : (forceStateImages?1:0) ,
				'time' : new Date().getTime()
			}),
			success : function (data) {
				console.log(data);
				callback($.parseJSON(data),index);
			}
		});
		
	}


	function getStates(callbackSuccess)
	{
		setQuery('states');
		getData(formatStates,setCallback(callbackSuccess));
	}

	function getResults(callbackSuccess)
	{
		setQuery('results');
		getData(storeResults,setCallback(callbackSuccess));
	}
	
	function formatStates(data,index)
	{
		var c=getCallback(index);
		if (typeof c=="function") c($.extend({},data.all),data.active);
	}

	function storeResults(data,index)
	{
		
		results = {taxa:{},variations:{}};

		if (Object.size(getSelectedState())>0 && data) {

			for (var i=0; i<data.length; i++){
				var row = data[i];		
				if (row.type!="taxon")
					results.variations[Object.size(results.variations)]=row.id;
				else
					results.taxa[Object.size(results.taxa)]=row.id;
			}
			
		}
		
		var c=getCallback(index);
		if (c) c(data);
	}

	// public interface functions
	return {

		setmatrix: function(i)
		{
			setMatrixId(i);
		},

		setlanguage: function(i)
		{
			setLanguageId(i);
		},

		setimgroot: function(i)
		{
			setImgRoot(i);
		},

		setimgroot: function(i)
		{
			setImgRoot(i);
		},

		geterror: function()
		{
			return getError()==null ? null : 'appController::'+getError();
		},

		states: function(callbackSuccess,callbackError)
		{
			try {
				getStates(callbackSuccess);
			}
			catch(err)  {
				setError(err);
				if (callbackError) callbackError();
			}			
		},

		set: function(vars,callbackSuccess,callbackError)
		{
			try {
				for(var property in vars)
					setSelectedState(property,vars[property]);

				if (callbackSuccess)
					callbackSuccess();
				else
					true;
			}
			catch(err)  {
				setError(err);
				if (callbackError) callbackError();
			}
		},
	
		get: function(v)
		{
			return getSelectedState(v);
		},
		
		reinitialise: function(callbackSuccess,callbackError)
		{
			try {
				doReinitialise();
				callbackSuccess();
			}
			catch(err)  {
				setError(err);
				if (callbackError) callbackError();
			}
		},

		result: function(callbackSuccess,callbackError)
		{
			try {
				getResults(callbackSuccess);
			}
			catch(err)  {
				setError(err);
				if (callbackError) callbackError();
			}	
		},
	
		detail: function(id,isvari,callbackSuccess,callbackError)
		{
			try {

				setId(id);
				setIsVariation(isvari);
				getDetail(callbackSuccess,callbackError);
			}
			catch(err)  {
				setError(err);
				if (callbackError) callbackError();
			}
		},
			
	}

})();
/*



// "constants"
var GROUP = 'c_group';
var CHARACTER = 'character';
var STATE = 'state';

var appController = (function() {

    // project specific 
    var credentials = {
        dbName:'drnzkr_dtch',
        dbVersion: '1.0', 
        dbDisplayName: 'Dierenzoeker', 
        dbEstimatedSize: 1143890
    };

    var pId = 327;

    // general
	var connected = false;
	var error = null;
	var query = null;
	var variables = {};
	var db = null;
	var results={};
	var id = null;
	var isVariation = false;
	var forceStateImages = true;
	var doUtf8decode=true; // utf8decodes all text values

	// extensions
	Object.size = function(obj) {
		var size = 0, key;
		for (key in obj)
			if (obj.hasOwnProperty(key)) size++;
		return size;
	};

	String.prototype.ucwords = function() {
		str = this.toLowerCase();
		return str.replace(/(^([a-zA-Z\p{M}]))|([ -][a-zA-Z\p{M}])/g,
			function($1){
				return $1.toUpperCase();
			});
	}

	String.prototype.ucfirst = function() {
		str = this.toLowerCase();
		return str.charAt(0).toUpperCase() + str.slice(1);
	}


	// private functions
	function setError(err)
	{
		error=err;
	}


	function getError()
	{
		return error;
	}


	function setId(i)
	{
		id=i;
	}


	function getId()
	{
		return id;
	}

	function setIsVariation(s)
	{
		if (typeof s!='boolean') return;
		isVariation=s;
	}

	function getIsVariation()
	{
		return isVariation;
	}

	function setUtf8decode(s)
	{
		if (typeof s!='boolean') return;
		doUtf8decode=s;
	}


	function getUtf8decode()
	{
		return doUtf8decode;
	}
	
	function utf8decode(s)
	{
		if (s==null || s==undefined) return s;
		// encode: unescape(encodeURIComponent(s));
		return getUtf8decode() ? decodeURIComponent(escape(s)) : s;
	}

	function setResults(r)
	{
		results={};

		for (var i=0; i<r.rows.length; i++){
			var row = r.rows.item(i);		
			results[Object.size(results)]={id:row.id,type:row.type};
		}

		return r;
	}


	function getResults()
	{
		return results;
	}


	function doReinitialise()
	{
		for(var property in variables) delete variables[property];
	}
				

	function isConnected()
	{
		return connected;
	}


	function doConnect() 
	{
		if (!window.openDatabase) {
			throw "appController: databases are not supported on this device";
			return false;
		} else {
			db = window.openDatabase(credentials.dbName,credentials.dbVersion,credentials.dbDisplayName,credentials.dbEstimatedSize);
			connected = true;
			return true;
		}
	};


	function setSelectedState(property,value) 
	{
		if (value==undefined || value==null)
			delete variables[property];
		else
			variables[property]=value;
	};


	function getSelectedState(property)
	{
		if (property==null)
			return variables;
		else
			return variables[property];
	};
	

	function getImplodedStates()
	{
		var buffer=Array();
		for(var property in variables) {
			if (variables[property]==true)
				buffer.push(property);
		}
		return buffer.join(',');
	};


	function setQuery(q)
	{
		query = q;
	};


	function executeQuery(callbackSuccess,callbackError,preProcessor)
	{
		db.transaction(function(tx)
			{
				tx.executeSql(query,[],
					function(tx,r)
					{
						if (preProcessor) r=preProcessor(r);
						callbackSuccess(r); 
						return;
					},
					function(tx,e)
					{
						error = e.message+' ('+e.code+')';
						callbackError(e);
						return;
					}
				)
			})

	};
	

	// specific private functions
	function setCountQuery()
	{

		var selState=getSelectedState();

		if (Object.size(selState)==0) return setQuery(queries.statecount_all);

		var states=Array();
		var taxa=Array();
		var variations=Array();

		for (var state in selState) 
			states.push(state);

		var r = getResults();

		for (var i in r) {
			if (r[i].type=='variation')
				variations.push(r[i].id);
			else
				taxa.push(r[i].id);
		}

		setQuery(
			queries.statecount.
				replace(/\%TAXACOUNT\%/ig,taxa.length).
				replace(/\%STATES\%/ig,states.join(',')).
				replace(/\%CLAUSE_STATECOUNT_TAXA\%/ig,taxa.length==0 ? '' : queries._clauses.statecount_taxa).
				replace(/\%CLAUSE_STATECOUNT_VARIATIONS\%/ig,variations.length==0 ? '' : queries._clauses.statecount_variations).
				replace(/\%TAXA\%/ig,taxa.join(',')).
				replace(/\%VARIATIONS\%/ig,variations.join(','))
		);

	}


	function getStates(callbackSuccess,callbackError)
	{
		
		function makeCharacterIconName(label)
		{
			return '__menu'+label.ucwords().replace(/\W/ig,'')+'.png';
		}

		db.transaction(function(tx) {
			
			var mem=Array();
			
			var memcount=Array();

			subOne = function(r)
			{
				for (var i=0; i<r.rows.length; i++)
					mem.push(r.rows.item(i));

				setCountQuery();
				tx.executeSql(query,[],function(tx,r){subTwo(r);});
			};

			subTwo = function(r)
			{

				var dummy=Array();

				for (var i=0; i<r.rows.length; i++)
					dummy[r.rows.item(i).state_id]=r.rows.item(i).can_select;

				var selected = getSelectedState();

				for (var i=0; i<mem.length; i++)
					mem[i].state=dummy[mem[i].state_id];

				var result=results=characters=states={};

				var states=Array(),r=0,c=0,s=0;
				
				for (var i=0; i<mem.length; i++) {

					var row = mem[i];
					var l=row.char_label.split('|');

					if (result.id!=(row.group_id!='-'?row.group_id:row.char_id) && i!=0) {

						if (result.type==GROUP) {
							result.characters=characters;
							result.hasStates=false;
							result.hasCharacters=true;
						} else {
							delete(result.characters);
							result.states=states;
							result.hasStates=true;
							result.hasCharacters=false;
						}

						results[r++]=result;
						result=characters=states={};
						c=s=0;
					}

					if (row.group_id!='-') {
						result = {
							type:GROUP,
							id:row.group_id,
							label:utf8decode(row.group_label),
							description:null,
							img:makeCharacterIconName(utf8decode(row.group_label))
						};

						if (Object.size(characters)==0 || characters[c-1].id!=row.char_id) {

							if (Object.size(characters)!=0)
								characters[c-1].states=states;
							states={};
							s=0;							
							characters[c++] = {
								type:CHARACTER,
								id:row.char_id,
								label:utf8decode(l[0]),
								description:utf8decode(l[1]),
								img:makeCharacterIconName(utf8decode(l[0])),
								hasStates:true,
								states:states
							};
						}

					} else {
						result = {
							type:CHARACTER,
							id:row.char_id,
							label:utf8decode(l[0]),
							description:utf8decode(l[1]),
							img:makeCharacterIconName(utf8decode(l[0])),
							hasStates:null,
							states:null
						};
					}

					if ((forceStateImages && row.state_image.length>0) || !forceStateImages) {
						states[s++] = {
							id:row.state_id,
							label:utf8decode(row.state_label),
							img:utf8decode(row.state_image),
							select_state:row.state
						};
					}
			
					
				}

				if (result.type==GROUP) {
					result.characters=characters;
					result.hasStates=false;
					result.hasCharacters=true;
				} else {
					delete(result.characters);
					result.states=states;
					result.hasStates=true;
					result.hasCharacters=false;
					
				}
				results[r++]=result;
				
				var activeList=Array();

				for(var i in results) {
					if (results[i].hasStates) {
						var h=false;
						for(var j in results[i].states) {
							if (results[i].states[j].select_state=='1') {
								h=true;
								var d=results[i].states[j];
								d.character={id:results[i].id,label:results[i].label};
								activeList.push(d);
							}
						}
						results[i].hasSelected=h;
					} else
					if (results[i].hasCharacters) {
						var g=false;
						for(var k in results[i].characters) {
							if (results[i].characters[k].hasStates) {
								var h=false;
								for(var j in results[i].characters[k].states) {
									if (results[i].characters[k].states[j].select_state=='1') {
										h=true;
										var d=results[i].characters[k].states[j];
										d.character={id:results[i].characters[k].id,label:results[i].characters[k].label};
										activeList.push(d);
									}
								}
								results[i].characters[k].hasSelected=h;
							}
							if (h==true) g=true;
						}
						results[i].hasSelected=g;
					}
				}

				callbackSuccess(results,activeList);
			};

			setQuery(queries.states);
			tx.executeSql(query,[],function(tx,r){subOne(r);});

		})	

	}


	function getDetail(callbackSuccess,callbackError,vari)
	{
		db.transaction(function(tx) {
			
			var mem={};
		
			subOne = function(r)
			{
				
				// basic taxon info
				mem={
					id:r.rows.item(0).id,
					name_nl:utf8decode(r.rows.item(0).name_nl).ucfirst(),
					name_sci:utf8decode(r.rows.item(0).name_sci),
					type:r.rows.item(0).type
				};

				mem.group={
					id:r.rows.item(0).group_id,
					name_nl:r.rows.item(0).groupname_nl ? utf8decode(r.rows.item(0).groupname_nl).ucfirst() : null,
					name_sci:r.rows.item(0).groupname_sci ? utf8decode(r.rows.item(0).groupname_sci) : null
				};

				var q = getIsVariation() ? queries.variationcontent : queries.taxoncontent;
				setQuery(q.replace(/%ID%/g,getId()));
				tx.executeSql(query,[],function(tx,r){subTwo(r);});
			};

			subTwo = function(r)
			{
				// taxon content
				var content='';
				var DescriptionTitle='';

				for (var i=0; i<r.rows.length; i++){
					
					var row = r.rows.item(i);
					row.title=row.title;
					row.content=row.content;

					if (row.page=='DescriptionTitle') {
						DescriptionTitle=row.content;
					} else {
						var title = (row.page=='Description' ? '%descriptiontitle%' : row.title.replace(/\%s/g,mem.name_nl));
						content=content+'<span class="content-species-detail-content-para">'+( row.title ? '<span class="content-species-detail-para-header">'+title+'</span>' : '' )+row.content+'</span>';
					}

				} 
				
				content='<p>'+content.replace('%descriptiontitle%',DescriptionTitle)+'</p>';

				mem.text=utf8decode(content);

				var q = getIsVariation() ? queries.variationimages : queries.taxonimage;
				setQuery(q.replace(/%ID%/g,getId()));
				tx.executeSql(query,[],function(tx,r){subThree(r);});
			};

			subThree = function(r)
			{
				// taxon image
				var imgMain={};

				for (var i=0; i<r.rows.length; i++){
					var row = r.rows.item(i);
					if (row.overview_image==1)
						imgMain = {file:utf8decode(row.file_name),copyright:utf8decode(row.copyright),caption:null};
				} 
				
				mem.img_main=imgMain;

				var q = getIsVariation() ? queries.variationimagesextra : queries.taxonimagesextra;
				setQuery(q.replace(/%ID%/g,getId()));
				tx.executeSql(query,[],function(tx,r){subFour(r);});
			};

			subFour = function(r)
			{
				// extra images
				var imgAdd={};

				for (var i=0; i<r.rows.length; i++) {
					var row = r.rows.item(i);
					imgAdd[Object.size(imgAdd)] = {file:utf8decode(row.file_name),copyright:null,caption:null};
				} 
				
				mem.img_add=imgAdd;

				var q = getIsVariation() ? queries.variationrelations : queries.taxonrelations;
				setQuery(q.replace(/%ID%/g,getId()));
				tx.executeSql(query,[],function(tx,r){subFive(r);});
			};

			subFive = function(r)
			{
				var similar={};

				for (var i=0; i<r.rows.length; i++){
					var row = r.rows.item(i);
					similar[Object.size(similar)] = {id:row.id,label:utf8decode(row.label),img:utf8decode(row.img),type:row.type};
				} 

				mem.similar=similar;
			
				callbackSuccess(mem);
			};

			var q = getIsVariation() ? queries.variation : queries.taxon;
			setQuery(q.replace(/%ID%/g,getId()));

			tx.executeSql(query,[],function(tx,r){subOne(r);});

		})	
	}


	var queries = {
		install:
			"select * from app_install_log",
		alltaxa:
			"select 'taxon' as type, _a.taxon_id as id, 0 as total_states, 100 as score,_c.is_hybrid as is_hybrid, trim(replace(_c.taxon,'%VAR%','')) as sci_name, "+
			"trim(_d.commonname) as label,_e.value as url_thumbnail "+
			"from matrices_taxa _a "+
			"left join taxa _c on _a.taxon_id = _c.id left join commonnames _d on _d.taxon_id = _a.taxon_id "+
			"left join nbc_extras _e on _c.id = _e.ref_id and _e.ref_type='taxon' and _e.name='url_thumbnail' "+
			"group by _a.taxon_id "+
			"union "+
			"select 'variation' as type, _a.variation_id as id, 0 as total_states, 100 as score,0 as is_hybrid, trim(_d.taxon) as sci_name, trim(_c.label) as label, _e.value as url_thumbnail "+
			"from  matrices_variations _a "+
			"left join taxa_variations _c on _a.variation_id = _c.id "+
			"left join taxa _d on _c.taxon_id = _d.id "+
			"left join nbc_extras _e on _a.variation_id = _e.ref_id and _e.ref_type='variation' and _e.name='url_thumbnail' "+
			"group by _a.variation_id "+
			"order by label",
		results:
			"select 'taxon' as type, _a.taxon_id as id, count(_b.state_id) as total_states, "+
			"round((case when count(_b.state_id)>%SELECTED_STATE_COUNT% then %SELECTED_STATE_COUNT% else count(_b.state_id) end/%SELECTED_STATE_COUNT%)*100,0) as score, "+
			"_c.is_hybrid as is_hybrid, trim(_c.taxon) as sci_name, trim(_d.commonname) as label,_e.value as url_thumbnail "+
			"from matrices_taxa _a "+
			"left join matrices_taxa_states _b on _a.project_id = _b.project_id and _a.matrix_id = _b.matrix_id and _a.taxon_id = _b.taxon_id and (_b.state_id in (%STATES%)) "+
			"left join taxa _c on _a.taxon_id = _c.id left join commonnames _d on _d.taxon_id = _a.taxon_id "+
			"left join nbc_extras _e on _c.id = _e.ref_id and _e.ref_type='taxon' and _e.name='url_thumbnail' "+
			"group by _a.taxon_id having score=100 "+
			"union "+
			"select 'variation' as type, _a.variation_id as id, count(_b.state_id) as total_states, "+
			"round((case when count(_b.state_id)>%SELECTED_STATE_COUNT% then %SELECTED_STATE_COUNT% else count(_b.state_id) end/%SELECTED_STATE_COUNT%)*100,0) as score, "+
			"0 as is_hybrid, trim(_d.taxon) as sci_name, trim(_c.label) as label, _e.value as url_thumbnail "+
			"from  matrices_variations _a "+
			"left join matrices_taxa_states _b on _a.project_id = _b.project_id and _a.matrix_id = _b.matrix_id and _a.variation_id = _b.variation_id and (_b.state_id in (%STATES%)) "+
			"left join taxa_variations _c on _a.variation_id = _c.id "+
			"left join taxa _d on _c.taxon_id = _d.id "+
			"left join nbc_extras _e on _a.variation_id = _e.ref_id and _e.ref_type='variation' and _e.name='url_thumbnail' "+
			"group by _a.variation_id having score=100 "+
			"order by score,label",
		states:
			"select "+
			"ifnull(gmo_g.ref_id,'-') as group_id,ifnull(gl.label,'-') as group_label,ifnull(gmo_c.show_order,gmo_g.show_order) as show_order, "+
			"c.id as char_id,cl.label as char_label,c.type as char_type,"+
			"cs.id as state_id,cls.label as state_label,cls.text as state_text,cs.file_name as state_image "+
			"from characteristics_states cs "+
			"left join characteristics_labels_states cls on cls.state_id = cs.id "+
			"left join characteristics c on cs.characteristic_id = c.id "+
			"left join characteristics_labels cl on cl.characteristic_id = c.id "+
			"left join characteristics_chargroups cc on cc.characteristic_id = c.id and cc.chargroup_id = g.id "+
			"left join chargroups g on gmo_g.ref_id = g.id and gmo_g.ref_type='group' "+
			"left join chargroups_labels gl on gl.chargroup_id = g.id "+
			"left join gui_menu_order gmo_g on gmo_g.ref_id = g.id and gmo_g.ref_type='group' "+
			"left join gui_menu_order gmo_c on gmo_c.ref_id = c.id and gmo_c.ref_type='char' "+
			"order by show_order,char_label,state_label",
		statecount:
			"select case when count(1)=0 then -1 else 0 end  as can_select, state_id "+
			"from matrices_taxa_states  "+
			"where taxon_id in (%TAXA%) "+
			"and state_id not in (%STATES%) "+
			"group by state_id "+
			"union "+
			"select -1 as can_select, state_id "+
			"from matrices_taxa_states  "+
			"where taxon_id not in (%TAXA%) "+
			"union "+
			"select 1 as can_select, id as state_id "+
			"from characteristics_states "+
			"where id in (%STATES%)",
		statecount_all:
			"select 0 as can_select, id as state_id from characteristics_states",
		taxon:
			"select t.id,trim(replace(t.taxon,'%VAR%','')) as name_sci, "+
			"c.commonname as name_nl, p.id as group_id, p.taxon as groupname_sci, pc.commonname as groupname_nl, 'taxon' as type from taxa t "+
			"left join commonnames c on c.taxon_id = t.id "+
			"left join taxa p on t.parent_id = p.id "+
			"left join commonnames pc on pc.taxon_id = p.id "+
			"where t.id = %ID%",
		taxoncontent:
			"select _b.title,_a.content, _c.page from content_taxa _a "+
			"left join pages_taxa_titles _b on _a.page_id = _b.page_id "+
			"left join pages_taxa _c on _a.page_id = _c.id "+
			"where _a.taxon_id = %ID% ",
		taxonimage:
			"select _a.value as file_name,_b.value as copyright, '1' as overview_image from nbc_extras _a "+
			"left join nbc_extras _b on _b.ref_type = 'taxon' and _b.ref_id=_a.ref_id and _b.name='photographer' "+
			"where _a.ref_id = %ID% and _a.ref_type='taxon' and _a.name='url_image'",
		taxonimagesextra:
			"select file_name from media_taxon where taxon_id = %ID% ",
		taxonrelations:
			"select 'taxon' as type, _b.id as id, _b.taxon as taxon,_c.commonname as label, _n.value as img "+
			"from taxa_relations _a "+
			"left join taxa _b on _b.id = _a.relation_id "+
			"left join commonnames _c on _c.taxon_id = _b.id "+
			"left join nbc_extras _n on _b.id = _n.ref_id and _n.ref_type='taxon' and _n.name='url_thumbnail' "+
			"where _a.ref_type='taxon' and _a.taxon_id = %ID% "+
			"union "+
			"select 'variation' as type, _e.id as id,  _f.taxon as taxon, _e.label as label, _n.value as img "+
			"from taxa_relations _d "+
			"left join taxa_variations _e on _e.id = _d.relation_id "+
			"left join taxa _f on _f.id = _d.taxon_id "+
			"left join nbc_extras _n on _d.taxon_id = _n.ref_id and _n.ref_type='taxon' and _n.name='url_thumbnail' "+
			"where _d.ref_type='variation'  "+
			"and _d.taxon_id = %ID%",
		variation:
			"select t.taxon_id,t.label,c.taxon, n.name, n.value, 'variation' as type  from taxa_variations t "+
			"left join taxa c on t.taxon_id = c.id left join nbc_extras n on n.ref_id = t.id and ref_type = 'variation' where t.id = %ID%",
		variationcontent: "",
		variationimages: "",
		variationimagesextra: "",
		variationrelations: "",
		_clauses: {
			statecount_taxa: "and taxon_id in (%TAXA%) ",
			statecount_variations: "and variation_id in (%VARIATIONS%) "
		}
	};


	return {

		set: function(vars,callbackSuccess,callbackError)
		{
			try {
				for(var property in vars)
					setSelectedState(property,vars[property]);
				if (callbackSuccess)
					callbackSuccess();
				else
					true;
			}
			catch(err)  {
				setError(err);
				if (callbackError) callbackError();
			}
		},
	
		get: function(v)
		{
			return getSelectedState(v);
		},
		
		reinitialise: function(callbackSuccess,callbackError)
		{
			try {
				doReinitialise();
				callbackSuccess();
			}
			catch(err)  {
				setError(err);
				if (callbackError) callbackError();
			}
		},

		result: function(callbackSuccess,callbackError)
		{
			try {
				this.connect();
				var numofstates=Object.size(this.get());

				if (numofstates==0)
					setQuery(queries.alltaxa);
				else
					setQuery(queries.results.replace(/%STATES%/g,getImplodedStates()).replace(/%SELECTED_STATE_COUNT%/g,numofstates));

				executeQuery(callbackSuccess,callbackError,setResults);
			}
			catch(err)  {
				setError(err);
				if (callbackError) callbackError();
			}		
		},
	
		getcachedresult: function()
		{
			return getResults();
		},
	
		detail: function(id,isvari,callbackSuccess,callbackError)
		{
			try {

				setId(id);
				setIsVariation(isvari);
				getDetail(callbackSuccess,callbackError);
			}
			catch(err)  {
				setError(err);
				if (callbackError) callbackError();
			}
		},
			
	}

})();



*/