{literal}
@font-face {
{/literal}
    font-family: LeagueGothic;
 	src: url('{$systemMedia}webfonts/League_Gothic-webfont.eot?') format('eot'), 
		 url('{$systemMedia}webfonts/League_Gothic-webfont.woff') format('woff'), 
		 url('{$systemMedia}webfonts/League_Gothic-webfont.ttf') format('truetype');
    font-weight: 400;
{literal}
}
{/literal}

{literal}
#header-container {
{/literal}
	background: #146daf url('{$systemMedia}background.jpg') no-repeat;
{literal}
}
{/literal}

{literal}
#main-menu ul li a.home {
{/literal}
    background: url('{$systemMedia}navigator.png') 5px 3px no-repeat;
{literal}
}
{/literal}

{literal}
#main-menu ul li a.home-selected, #main-menu ul li a.home:hover {
{/literal}
    background:
    	url('{$systemMedia}navigator-selected.png') 5px 3px no-repeat;
    background:
    	url('{$systemMedia}navigator-selected.png') 5px 3px no-repeat,
	    -moz-linear-gradient(top,  #555454 0%, #454444 100%);
    background:
    	url('{$systemMedia}navigator-selected.png') 5px 3px no-repeat,
	    -webkit-gradient(linear, left top, left bottom, color-stop(0%,#555454), color-stop(100%,#454444));
    background:
    	url('{$systemMedia}navigator-selected.png') 5px 3px no-repeat,
	    -webkit-linear-gradient(top,  #555454 0%,#454444 100%);
    background:
    	url('{$systemMedia}navigator-selected.png') 5px 3px no-repeat,
	    -o-linear-gradient(top,  #555454 0%,#454444 100%);
    background:
    	url('{$systemMedia}navigator-selected.png') 5px 3px no-repeat,
	    -ms-linear-gradient(top,  #555454 0%,#454444 100%);
    background:
    	url('{$systemMedia}navigator-selected.png') 5px 3px no-repeat,
	    linear-gradient(top,  #555454 0%,#454444 100%);
    background:
    	url('{$systemMedia}navigator-selected.png') 5px 3px no-repeat,
	    filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#555454', endColorstr='#454444',GradientType=0 );
 {literal}
}
{/literal}

{if $logo}
    {literal}
    #header-container {
    {/literal}
        background: url('{$projectMedia}{$logo}') no-repeat;
        border-bottom: 1px solid #034e85;
    {literal}
    }
    {/literal}
{/if}

{literal}
#contents-icon {
{/literal}
    cursor: pointer; 
    background: url('{$systemMedia}contents.png') 5px 0 no-repeat;
{literal}
}
{/literal}

{literal}
#contents-icon:active {
{/literal}
    background-position: 5px 1px;
{literal}
}
{/literal}

{literal}
#previous-icon {
{/literal}
    cursor: pointer; 
    background: url('{$systemMedia}previous.png') 5px 0 no-repeat;
{literal}
}
{/literal}

{literal}
#previous-icon:active {
{/literal}
    background-position: 5px 1px;
{literal}
}
{/literal}

{literal}
#previous-icon-inactive {
{/literal}
    background: url('{$systemMedia}previous.png') 5px 0 no-repeat;
    opacity: 0.4;
    filter: alpha(opacity=40); 
{literal}
}
{/literal}

{literal}
#next-icon {
{/literal}
    width: 40px;
    cursor: pointer; 
    background: url('{$systemMedia}next.png') no-repeat;
{literal}
}
{/literal}

{literal}
#next-icon:active {
{/literal}
    background-position: 0 1px;
{literal}
}
{/literal}

{literal}
#next-icon-inactive {
{/literal}
    width: 40px;
    background: url('{$systemMedia}next.png') no-repeat;
    opacity: 0.4;
    filter: alpha(opacity=40); 
{literal}
}
{/literal}


{literal}
#back-icon {
{/literal}
    width: 40px;
    cursor: pointer; 
    background: url('{$systemMedia}back.png') no-repeat;
{literal}
}
{/literal}

{literal}
#back-icon:active {
{/literal}
    background-position: 0 1px;
{literal}
}
{/literal}

{literal}
#back-icon-inactive {
{/literal}
    width: 40px;
    background: url('{$systemMedia}back.png') no-repeat;
    opacity: 0.4;
    filter: alpha(opacity=40); 
{literal}
}
{/literal}



{literal}
#decision-path-icon {
{/literal}
    width: 80px;
    cursor: pointer; 
    background: url('{$systemMedia}decision-path.png') center top no-repeat;
{literal}
}
{/literal}

{literal}
#decision-path-icon:active {
{/literal}
    background-position: center 1px;
{literal}
}
{/literal}

{literal}
#decision-path-icon-inactive {
{/literal}
    width: 80px;
    background: url('{$systemMedia}decision-path.png') center top no-repeat;
    opacity: 0.4;
    filter: alpha(opacity=40); 
{literal}
}
{/literal}

{literal}
#first-icon {
{/literal}
    width: 40px;
    cursor: pointer; 
    background: url('{$systemMedia}first.png') no-repeat;
{literal}
}
{/literal}

{literal}
#first-icon:active {
{/literal}
    background-position: 0 1px;
{literal}
}
{/literal}

{literal}
#first-icon-inactive {
{/literal}
    width: 40px;
    background: url('{$systemMedia}first.png') no-repeat;
    opacity: 0.4;
    filter: alpha(opacity=40); 
{literal}
}
{/literal}

{literal}
.selectIcon {
{/literal}
    background: url('{$systemMedia}open-select.png') no-repeat right center;
    padding-right: 18px;
    margin-right: 10px;
    cursor: pointer;
{literal}
}
{/literal}

{literal}
.selectRight {
{/literal}
    background: url('{$systemMedia}go-to.png') no-repeat right 4px;
{literal}
}
{/literal}

{literal}
.selectIcon:hover {
{/literal}
    text-decoration: underline;
{literal}
}
{/literal}

{literal}
#dialog-close {
{/literal}
    background: url('{$systemMedia}close.png') no-repeat 50% 50%;
{literal}
}
{/literal}

{literal}
.closedResult {
{/literal}
	background: url('{$systemMedia}select-right.png') no-repeat 2px center;
	padding-left: 18px;
{literal}
}
{/literal}

{literal}
.openResult {
{/literal}
	background: url('{$systemMedia}select-down.png') no-repeat 2px center;
{literal}
}
{/literal}

{literal}
#back-to-search-icon {
{/literal}
    width: 85px;
    cursor: pointer; 
    background: url('{$systemMedia}back_to_search.png') no-repeat center 0;
{literal}
}
{/literal}

{literal}
#back-to-search-icon:active {
{/literal}
    background-position: center 1px;
{literal}
}
{/literal}
