<?php
    defined('_JEXEC') or die;
    JHtml::addIncludePath(JPATH_COMPONENT . '/helpers');
?>

<div class="already-cool">
    <div class="home-header">
        <div class="container">
            <div class="header-container">
                <div class="header-left">
                    <div class="home-logo">
                        <?php
                            jimport( 'joomla.application.module.helper' );
                            $module = JModuleHelper::getModules('home-logo');
                            $attribs['style'] = 'xhtml';
                            echo JModuleHelper::renderModule($module[0], $attribs);
                        ?>
                    </div>
                    <span class="header-info">
                        The 100% free dating site that takes you out.
                    </span>
                </div>
                <div class="home-sign-in">
                    <?php
                        jimport( 'joomla.application.module.helper' );
                        $module = JModuleHelper::getModules('mainmenu');
                        $attribs['style'] = 'xhtml';
                        echo JModuleHelper::renderModule($module[0], $attribs);
                    ?>
                </div>

            </div>
        </div>
        <div class="home-gradient"></div>
    </div>
    <div class="sign-up-container">
        <div class="home-sign-up">
            <div class="container">
                <div class="col-sm-10 col-sm-offset-1 col-xs-12">
                    <div class="sign-up-box">
                        <div class="sign-up-border">
                            <!--                        --><?php //if($_SERVER['REQUEST_URI'] != '/join'){ ?>
                            <div class="sign-up-fb">
                                <?php
                                jimport( 'joomla.application.module.helper' );          // подключаем нужный класс, один раз на странице, перед первым выводом
                                $module = JModuleHelper::getModules('social');          // получаем в массив все модули из заданной позиции
                                $attribs['style'] = 'xhtml';                            // задаём, если нужно, оболочку модулей (module chrome)
                                echo JModuleHelper::renderModule($module[0], $attribs); // выводим первый модуль из заданной позиции
                                ?>
                            </div>

                            <div class="sign-up-separator">
                                <span class="sign-up-or">OR</span>
                            </div>
                            <!--                        --><?php //} ?>
                            <div class="sign-up-site">
                                <?php
                                jimport( 'joomla.application.module.helper' );          // подключаем нужный класс, один раз на странице, перед первым выводом
                                $module = JModuleHelper::getModules('mylogin');          // получаем в массив все модули из заданной позиции
                                $attribs['style'] = 'xhtml';                            // задаём, если нужно, оболочку модулей (module chrome)
                                echo JModuleHelper::renderModule($module[0], $attribs); // выводим первый модуль из заданной позиции
                                ?>
                                <!--                            <jdoc:include type="modules" name="mylogin" style="none" />-->
                                <!--<jdoc:include type="modules" name="login" style="none" />-->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!--<div class="general_content  col-xs-12">
    <div class="picture_column col-sm-5 col-xs-12">
        <img src="<?php /*print '/images/content/' . $this->data[0]->image*/?> ">
    </div>
    <div class="text_column col-sm-7 col-xs-12">
        <h1 class="text_header"><?php /*print $this->data[0]->header */?></h1>
        <div class="text_block">
            <div id="social">
                <?php
/*                jimport( 'joomla.application.module.helper' );          // подключаем нужный класс, один раз на странице, перед первым выводом
                $module = JModuleHelper::getModules('social');          // получаем в массив все модули из заданной позиции
                $attribs['style'] = 'xhtml';                            // задаём, если нужно, оболочку модулей (module chrome)
                echo JModuleHelper::renderModule($module[0], $attribs); // выводим первый модуль из заданной позиции
                */?>
            </div>
            <hr>
        </div>
    </div>
</div>-->
