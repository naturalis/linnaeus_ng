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
	var projectId=null;
	var matrixId=null;
	var languageId=null;
	var isVariation=false;
	var forceStateImages=true;

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
		str = this;//.toLowerCase();
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

	function setProjectId(i)
	{
		projectId=i;
	}

	function getProjectId()
	{
		return projectId;
	}

	function setMatrixId(i)
	{
		matrixId=i;
	}

	function getMatrixId()
	{
		return matrixId;
	}

	function setLanguageId(i)
	{
		languageId=i;
	}

	function getLanguageId()
	{
		return languageId;
	}

	function setId(i)
	{
		id=i;
	}

	function getId()
	{
		return id;
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
				'id' : getId() ,
				'results' : results ,
				'force' : (forceStateImages?1:0) ,
				'time' : new Date().getTime()
			}),
			success : function (data) {
				//console.clear();console.log(data);
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

	function getDetail(callbackSuccess)
	{
		setQuery('detail');
		getData(formatDetail,setCallback(callbackSuccess));
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

	function formatDetail(data,index)
	{

		var res={
			id:data.id,
			name_nl:data.name_nl.ucfirst(),
			name_sci:data.name_sci,
			type:data.type,
			group:{
				id:data.group_id,
				name_nl:data.groupname_nl ? data.groupname_nl.ucfirst() : null,
				name_sci:data.groupname_sci ? data.groupname_sci : null
			}
		};


		if (data.content) {
			var content='';
			var DescriptionTitle='';
			for (var i=0; i<data.content.length; i++) {
				var row = data.content[i];
				if (row.page=='DescriptionTitle') {
					DescriptionTitle=row.content;
				} else {
					var title=(row.page=='Description' ? '%descriptiontitle%' : row.title.replace(/\%s/g,res.name_nl.toLowerCase()));
					content=content+'<span class="content-species-detail-content-para">'+( row.title ? '<span class="content-species-detail-para-header">'+title+'</span>' : '' )+row.content+'</span>';
				}
			} 
			res.text='<p>'+content.replace('%descriptiontitle%',DescriptionTitle)+'</p>';
		}


		if (data.img_main) {
			var imgMain={};
			for (var i=0; i<data.img_main.length; i++) {
				var row = data.img_main[i];
				if (row.overview_image==1)
					imgMain = {file:row.file_name,copyright:row.copyright,caption:null};
			} 
			res.img_main=imgMain;
		} else {
			res.img_main={file:'',copyright:'',caption:''};
		}

		
		if (data.img_other) {
			var imgAdd={};
			for (var i=0; i<data.img_other.length; i++) {
				var row = data.img_other[i];
				imgAdd[Object.size(imgAdd)] = {file:row.file_name,copyright:null,caption:null};
			} 
			res.img_add=imgAdd;
		}


		if (data.similar) {
			var similar={};
			for (var i=0; i<data.similar.length; i++) {
				var row = data.similar[i];
				similar[Object.size(similar)] = {id:row.id,label:row.label,img:row.img,type:row.type};
			}
			res.similar=similar;
		}


		var c=getCallback(index);
		if (c) c(res);
	}


	// public interface functions
	return {

		setproject: function(i)
		{
			setProjectId(i);
		},

		getproject: function()
		{
			return getProjectId();
		},

		setmatrix: function(i)
		{
			setMatrixId(i);
		},

		setlanguage: function(i)
		{
			setLanguageId(i);
		},

		getmatrix: function()
		{
			return getMatrixId();
		},

		getlanguage: function()
		{
			return getLanguageId();
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
