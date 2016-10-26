<?php

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
<div class="partners container">
    <div class="partners-header" style=" box-shadow: none">
        <h1 class="partner-header-title">Interested in being part of our community?</h1>
        <p class="partner-header-description">Cool with it&trade; partners with local venues and establishments in order to provide our members with new, fun and interesting places to meet/Linc Up and get to know each other. Our partners benefit from being part of Cool with it by consitent referrals from our site to their venues.</p>
        <p class="partner-header-description">If you feel your venue fits this description please fill in your contact info below and someone will get back to you within 24 hours.</p>
    </div>
    <form class="partners-form" id="partners_form">
        <div class="partners-form-inputs">
            <div>
                <label for="name"><?php print JText::_('SUPPORT_FORM_NAME'); ?></label>
                <input type="text" name="name">
            </div>
            <div>
                <label for="subject"><?php print JText::_('SUPPORT_FORM_SUBJECT'); ?></label>
                <input type="text" name="subject">
            </div>
            <div>
                <label for="email"><?php print JText::_('SUPPORT_FORM_EMAIL'); ?></label>
                <input type="text" name="email">
            </div>
        </div>
        <div class="partners-form-message">
            <div>
                <label for="message"><?php print JText::_('SUPPORT_FORM_MESSAGE'); ?></label>
                <textarea name="message"></textarea>
                <span class="message"></span>
            </div>
        </div>
        <div class="partners-form-submit">
            <input class="submit-button" type="submit" value="Submit">
        </div>
    </form>
</div>

<script type="text/javascript">
    jQuery('#partners_form').submit(function(){
        jQuery.ajax({
            type: "POST",
            url: '/components/com_content/views/info/send_email_message.php',
            data: jQuery("#partners_form").serialize(),
            success: function(data){
                console.log(data);
                if(data === "Success"){
                    jQuery('.partners .partners-form .message').removeClass('error').addClass('success').html('Message Sent.');
                } else {
                    jQuery('.partners .partners-form .message').removeClass('success').addClass('error').html('Error! Please try again.');
                }
            },
            error: function(data){

            }
        });
        return false;
    });
</script>