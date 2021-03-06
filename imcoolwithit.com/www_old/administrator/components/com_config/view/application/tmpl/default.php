<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_config
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

// Load tooltips behavior
JHtml::_('behavior.formvalidation');
JHtml::_('bootstrap.tooltip');
JHtml::_('formbehavior.chosen', 'select');
?>
<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		if (task == 'application.cancel' || document.formvalidator.isValid(document.id('application-form')))
		{
			Joomla.submitform(task, document.getElementById('application-form'));
		}
	}
</script>

<form action="<?php echo JRoute::_('index.php?option=com_config'); ?>" id="application-form" method="post" name="adminForm" class="form-validate">
	<div class="row-fluid">
		<!-- Begin Sidebar -->
		<!--<div id="sidebar" class="span2">
			<div class="sidebar-nav">
				<?php /*echo $this->loadTemplate('navigation'); */?>
				<?php
/*				// Display the submenu position modules
				$this->submenumodules = JModuleHelper::getModules('submenu');
				foreach ($this->submenumodules as $submenumodule)
				{
					$output = JModuleHelper::renderModule($submenumodule);
					$params = new JRegistry;
					$params->loadString($submenumodule->params);
					echo $output;
				}
				*/?>
			</div>
		</div>-->
		<!-- End Sidebar -->
		<!-- Begin Content -->
		<div class="span10" style="margin-left: 25px;">
			<ul class="nav nav-tabs">
				<li class="active"><a href="#page-site" data-toggle="tab"><?php echo JText::_('JSITE'); ?></a></li>
<!--				<li><a href="#page-system" data-toggle="tab">--><?php //echo JText::_('COM_CONFIG_SYSTEM'); ?><!--</a></li>-->
				<li><a href="#page-server" data-toggle="tab"><?php echo JText::_('COM_CONFIG_SERVER'); ?></a></li>
				<li><a href="#page-config" data-toggle="tab"><?php echo 'Config'; ?></a></li>
<!--				<li><a href="#page-permissions" data-toggle="tab">--><?php //echo JText::_('COM_CONFIG_PERMISSIONS'); ?><!--</a></li>-->
<!--				<li><a href="#page-filters" data-toggle="tab">--><?php //echo JText::_('COM_CONFIG_TEXT_FILTERS'); ?><!--</a></li>-->
<!--				<?php /*if ($this->ftp) : */?>
					<li><a href="#page-ftp" data-toggle="tab"><?php /*echo JText::_('COM_CONFIG_FTP_SETTINGS'); */?></a></li>
				--><?php /*endif; */?>
			</ul>
			<div id="config-document" class="tab-content">
				<div id="page-site" class="tab-pane active">
					<div class="row-fluid">
						<div class="span6">
							<?php echo $this->loadTemplate('site'); ?>
							<?php echo $this->loadTemplate('metadata'); ?>
						</div>
						<div class="span6">
							<?php echo $this->loadTemplate('seo'); ?>
							<?php echo $this->loadTemplate('cookie'); ?>
						</div>
					</div>
				</div>
				<div id="page-system" class="tab-pane">
					<div class="row-fluid">
						<div class="span12">
							<?php echo $this->loadTemplate('system'); ?>
							<?php echo $this->loadTemplate('debug'); ?>
							<?php echo $this->loadTemplate('cache'); ?>
							<?php echo $this->loadTemplate('session'); ?>
						</div>
					</div>
				</div>
				<div id="page-server" class="tab-pane">
					<div class="row-fluid">
						<div class="span6">
							<?php echo $this->loadTemplate('server'); ?>
							<?php echo $this->loadTemplate('locale'); ?>
							<?php echo $this->loadTemplate('ftp'); ?>
							<?php echo $this->loadTemplate('proxy'); ?>
						</div>
						<div class="span6">
							<?php echo $this->loadTemplate('database'); ?>
							<?php echo $this->loadTemplate('mail'); ?>
						</div>
					</div>
				</div>
                <div id="page-config">
                    <div class="row-fluid">
                        <?php echo $this->loadTemplate('config_site'); ?>
                    </div>
                </div>
				<div id="page-permissions" class="tab-pane">
					<div class="row-fluid">
						<?php echo $this->loadTemplate('permissions'); ?>
					</div>
				</div>
				<div id="page-filters" class="tab-pane">
					<div class="row-fluid">
						<?php echo $this->loadTemplate('filters'); ?>
					</div>
				</div>
				<?php if ($this->ftp) : ?>
					<div id="page-ftp" class="tab-pane">
						<?php echo $this->loadTemplate('ftplogin'); ?>
					</div>
				<?php endif; ?>
				<input type="hidden" name="task" value="" />
				<?php echo JHtml::_('form.token'); ?>
			</div>
		</div>
		<!-- End Content -->
	</div>
</form>
