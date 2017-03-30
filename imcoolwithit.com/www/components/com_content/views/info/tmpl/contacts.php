<?php
    defined('_JEXEC') or die;
    JHtml::addIncludePath(JPATH_COMPONENT . '/helpers');
    $params  = $this->item->params;
?>

<div class="home-header">
    <div class="container">
        <div class="header-container">
            <div class="header-left">
                <div class="home-logo">
                    <?php
                    jimport( 'joomla.application.module.helper' );          // подключаем нужный класс, один раз на странице, перед первым выводом
                    $module = JModuleHelper::getModules('home-logo');       // получаем в массив все модули из заданной позиции
                    $attribs['style'] = 'xhtml';                            // задаём, если нужно, оболочку модулей (module chrome)
                    echo JModuleHelper::renderModule($module[0], $attribs); // выводим первый модуль из заданной позиции
                    ?>
                </div>
                <span class="header-info">
                        The 100% free dating site that takes you out.
                    </span>
            </div>
            <div class="home-sign-in">
                <?php
                jimport( 'joomla.application.module.helper' );          // подключаем нужный класс, один раз на странице, перед первым выводом
                $module = JModuleHelper::getModules('mainmenu');        // получаем в массив все модули из заданной позиции
                $attribs['style'] = 'xhtml';                            // задаём, если нужно, оболочку модулей (module chrome)
                echo JModuleHelper::renderModule($module[0], $attribs); // выводим первый модуль из заданной позиции
                ?>
            </div>

        </div>
    </div>
    <div class="home-gradient"></div>
</div>
<div class="contact-us contact-logout col-sm-8 col-sm-offset-2 col-xs-12">

    <div class="page-content row">
        <div class="page-content-top padding-null">
            <h1>Contact Us</h1>
        </div>

        <div class="contact-us-content">
            <span><b><?php print JText::_('CONTACT_US_INFO_1'); ?></b></span>
            <span><?php print JText::_('CONTACT_US_INFO_2'); ?></span>
            <span><?php print JText::_('CONTACT_US_INFO_3'); ?></span>
            <span><?php print JText::_('CONTACT_US_INFO_4'); ?></span>
            <span><?php print JText::_('CONTACT_US_INFO_5'); ?></span>
        </div>

    </div>

</div>
