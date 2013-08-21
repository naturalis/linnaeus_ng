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

/*
	function getDetail(callbackSuccess,callbackError,vari)
	{
		db.transaction(function(tx) {
			
			var mem={};
		
			subOne = function(r)
			{
				// basic taxon info
				mem = {
					id: r.rows.item(0).id,
					name_nl: r.rows.item(0).name_nl,
					name_sci: r.rows.item(0).name_sci,
					type: r.rows.item(0).type
				};

				mem.group={id:r.rows.item(0).group_id,name_nl:r.rows.item(0).groupname_nl,name_sci:r.rows.item(0).groupname_sci};

				var q = getIsVariation() ? queries.variationcontent : queries.taxoncontent;
				setQuery(q.replace(/%ID%/g,getId()));
				tx.executeSql(query,[],function(tx,r){subTwo(r);});
			};

			subTwo = function(r)
			{
				// taxon content
				var content='';

				for (var i=0; i<r.rows.length; i++){
					var row = r.rows.item(i);
					content=content+( row.title ? '<span class="label">'+row.title+'</span>' : '' )+row.content+'<br /><br />';
				} 
				
				content='<p>'+content+'</p>';

				mem.text=content;

				var q = getIsVariation() ? queries.variationimages : queries.taxonimages;
				setQuery(q.replace(/%ID%/g,getId()));
				tx.executeSql(query,[],function(tx,r){subThree(r);});
			};

			subThree = function(r)
			{
				// taxon images
				var imgMain={};
				var imgAdd={};

				for (var i=0; i<r.rows.length; i++){
					var row = r.rows.item(i);
					if (row.overview_image==1)
						imgMain = {file:row.file_name,copyright:row.copyright,caption:null};
					else
						imgAdd[Object.size(imgAdd)] = {file:row.file_name,copyright:row.copyright,caption:null};
				} 
				
				mem.img_main=imgMain;
				mem.img_add=imgAdd;

				var q = getIsVariation() ? queries.variationrelations : queries.taxonrelations;
				setQuery(q.replace(/%ID%/g,getId()));
				tx.executeSql(query,[],function(tx,r){subFour(r);});
			};

			subFour = function(r)
			{
				var similar={};

				for (var i=0; i<r.rows.length; i++){
					var row = r.rows.item(i);
					similar[Object.size(similar)] = {id:row.id,label:row.label,img:row.img,type:row.type};
				} 

				mem.similar=similar;
			
				callbackSuccess(mem);
			};

			var q = getIsVariation() ? queries.variation : queries.taxon;
			setQuery(q.replace(/%ID%/g,getId()));

			tx.executeSql(query,[],function(tx,r){subOne(r);});

		})	
	}

*/


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
