<?php
defined('_JEXEC') or die('Restricted access');
?>

<div class="verify-email col-sm-6 col-sm-offset-3 col-xs-12" <?php if(!$this->authorized){ print 'style="margin-top: 50px"'; } ?> >

    <div class="page-content row">

        <?php if($this->verify_status) { ?>
            <h1 class="title"><?php print JText::_('VERIFY_EMAIL_TITLE_COMPLETED'); ?></h1>
        <?php } else {?>
            <h1 class="title"><?php print JText::_('VERIFY_EMAIL_TITLE_NOT_COMPLETED'); ?></h1>
        <?php } ?>

        <div class="verify-email-info">
            <img src="<?php print $this->image; ?>">
            <span><?php print JText::_('VERIFY_EMAIL_INFO'); ?></span>
            <div class="verify-email-form">
                <div class="input-block">
                    <?php if($this->verify_status) { ?>
                        <span><?php print JText::_('VERIFY_EMAIL_INFO_COMPLETED'); ?></span>
                    <?php } else { ?>
                        <span><?php print JText::_('VERIFY_EMAIL_TITLE_NOT_COMPLETED'); ?></span>
                    <?php }

                    if (!$this->authorized) { ?>
                        <div id="social">
                            <?php
                            jimport('joomla.application.module.helper'); // подключаем нужный класс, один раз на странице, перед первым выводом
                            $module = JModuleHelper::getModules('social'); // получаем в массив все модули из заданной позиции
                            $attribs['style'] = 'xhtml'; // задаём, если нужно, оболочку модулей (module chrome)
                            echo JModuleHelper::renderModule($module[0], $attribs); // выводим первый модуль из заданной позиции
                            ?>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>

        <div class="page-footer"></div>

    </div>

</div>