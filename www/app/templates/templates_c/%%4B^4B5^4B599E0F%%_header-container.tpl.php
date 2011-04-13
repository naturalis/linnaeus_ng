<?php /* Smarty version 2.6.26, created on 2011-04-13 15:05:39
         compiled from C:/Users/maarten/htdocs/linnaeus+ng/linnaeus_ng/www/app/templates/templates/shared/0064/_header-container.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'escape', 'C:/Users/maarten/htdocs/linnaeus ng/linnaeus_ng/www/app/templates/templates/shared/0064/_header-container.tpl', 4, false),)), $this); ?>
<div id="header-container">
<?php if ($this->_tpl_vars['session']['project']['logo']): ?>
	<div id="image">
	<a href="<?php echo $this->_tpl_vars['session']['project']['urls']['project_start']; ?>
"><img src="<?php echo $this->_tpl_vars['session']['project']['urls']['project_media']; ?>
<?php echo ((is_array($_tmp=$this->_tpl_vars['session']['project']['logo'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'url') : smarty_modifier_escape($_tmp, 'url')); ?>
" id="project-logo" /></a>
	</div>
<?php endif; ?>
</div>

<div id="tanbif-menu">
<a href="/index.php" title="Home page"><span class="mainmenuitem" alt="Home page">Home page</span></a>
<span class="mainmenuseparator">|</span>
<a href="/search.php" title="Search"><span class="mainmenuitem" alt="Search">Search</span></a>
<span class="mainmenuseparator">|</span>
<a href="/app/views/species/" title="Browse species"><span class="mainmenuitem<?php if ($this->_tpl_vars['controllerBaseName'] == 'species'): ?>_selected<?php endif; ?>" alt="Browse species">Browse species</span></a>
<span class="mainmenuseparator">|</span>
<a href="/app/views/matrixkey/" title="Identify"><span class="mainmenuitem<?php if ($this->_tpl_vars['controllerBaseName'] == 'matrixkey'): ?>_selected<?php endif; ?>" alt="Identify">Identify</span></a>
<span class="mainmenuseparator">|</span>
<a href="/news.php" title="Biodiversity news"><span class="mainmenuitem" alt="Biodiversity news">Biodiversity news</span></a>
<span class="mainmenuseparator">|</span>
<a href="/forum/index.php" title="Forum"><span class="mainmenuitem" alt="Forum">Forum</span></a>
<span class="mainmenuseparator">|</span>
<a href="/gallery.php" title="Gallery"><span class="mainmenuitem" alt="Gallery">Gallery</span></a>
<span class="mainmenuseparator">|</span>
<a href="/contentpage.php?cat=bio-facts" title="Bio facts"><span class="mainmenuitem" alt="Bio facts">Bio facts</span></a>
<span class="mainmenuseparator">|</span>
<a href="/contentpage.php?cat=partners" title="Partners"><span class="mainmenuitem" alt="Partners">Partners</span></a>
<span class="mainmenuseparator">|</span>
<a href="/contentpage.php?cat=about-tanbif" title="About TanBIF"><span class="mainmenuitem" alt="About TanBIF">About TanBIF</a>
<span class="mainmenuseparator">|</span>
<a href="/gbifwidget.php" title="GBIF Widget"><span class="mainmenuitem" alt="GBIF Widget">GBIF Widget</span></a>
<span class="mainmenuseparator"></span>
</div>