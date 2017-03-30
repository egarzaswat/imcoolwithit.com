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

?>

<div class="about-us">
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

    <div class="content-block about-us-block container">
        <h1><?php print $this->data[0]->header ?></h1>
        <?php print $this->data[0]->content ?>
    </div>
</div>