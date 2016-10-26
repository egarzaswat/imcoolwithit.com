<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_content
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers');

$params  = $this->item->params;

?>

    <div class="home-header">
        <div class="container">
            <div class="col-sm-10 col-sm-offset-1 col-xs-12">
                <div class="home-sign-in">
                    <?php
                    jimport( 'joomla.application.module.helper' );          // подключаем нужный класс, один раз на странице, перед первым выводом
                    $module = JModuleHelper::getModules('mainmenu');        // получаем в массив все модули из заданной позиции
                    $attribs['style'] = 'xhtml';                            // задаём, если нужно, оболочку модулей (module chrome)
                    echo JModuleHelper::renderModule($module[0], $attribs); // выводим первый модуль из заданной позиции
                    ?>
                </div>
                <div class="home-logo">
                    <?php
                    jimport( 'joomla.application.module.helper' );          // подключаем нужный класс, один раз на странице, перед первым выводом
                    $module = JModuleHelper::getModules('home-logo');       // получаем в массив все модули из заданной позиции
                    $attribs['style'] = 'xhtml';                            // задаём, если нужно, оболочку модулей (module chrome)
                    echo JModuleHelper::renderModule($module[0], $attribs); // выводим первый модуль из заданной позиции
                    ?>
                </div>
            </div>
        </div>
    </div>
    <div class="content-block container">
        <h1 class="content-header col-sm-10 col-sm-offset-1 col-xs-12"><?php print $this->data[0]->header ?></h1>
        <div class="content-text col-sm-10 col-sm-offset-1 col-xs-12">
            <?php print $this->data[0]->content ?>
        </div>
    </div>

<!--<div class="general_content container">
    <div class="full_column col-xs-12">
        <h1 class="text_header"><?php /*print $this->data[0]->header */?></h1>
        <div class="text_block">
            <?php /*print $this->data[0]->content */?>
        </div>
    </div>
</div>-->
