<?php
/**
 * @package     Joomla.Site
 * @subpackage  Templates.protostar
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

$app             = JFactory::getApplication();
$doc             = JFactory::getDocument();
$user            = JFactory::getUser();
$this->language  = $doc->language;
$this->direction = $doc->direction;

// Getting params from template
$params = $app->getTemplate(true)->params;

// Detecting Active Variables
$option   = $app->input->getCmd('option', '');
$view     = $app->input->getCmd('view', '');
$layout   = $app->input->getCmd('layout', '');
$task     = $app->input->getCmd('task', '');
$itemid   = $app->input->getCmd('Itemid', '');
$sitename = $app->get('sitename');

if($task == "edit" || $layout == "form" )
{
	$fullWidth = 1;
}
else
{
	$fullWidth = 0;
}

// Add JavaScript Frameworks
JHtml::_('bootstrap.framework');
$doc->addScript('templates/' . $this->template . '/js/js.cookie.js');
$doc->addScript('templates/' . $this->template . '/js/template.js');
//$doc->addScript('templates/' . $this->template . '/js/addtohomescreen.js');

// Add Stylesheets
$doc->addStyleSheet('templates/' . $this->template . '/css/template.css');
$doc->addStyleSheet('templates/' . $this->template . '/css/flaticon.css');
$doc->addStyleSheet('templates/' . $this->template . '/css/bootstrap.css');
//$doc->addStyleSheet('templates/' . $this->template . '/css/addtohomescreen.css');

// Load optional RTL Bootstrap CSS
JHtml::_('bootstrap.loadCss', false, $this->direction);

// Logo file or site title param
if ($this->params->get('logoFile'))
{
	$logo = '<img src="' . JUri::root() . $this->params->get('logoFile') . '" alt="' . $sitename . '" />';
}
elseif ($this->params->get('sitetitle'))
{
	$logo = '<span class="site-title" title="' . $sitename . '">' . htmlspecialchars($this->params->get('sitetitle')) . '</span>';
}
else
{
	$logo = '<span class="site-title" title="' . $sitename . '">' . $sitename . '</span>';
}

if(isset($_GET['referrer'])){
    $session = JFactory::getSession();
    $session->set('referrer', $_GET['referrer']);
}
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $this->language; ?>" lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>">
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="apple-touch-icon" sizes="57x57" href="images/icons/apple-icon-57x57.png">
    <link rel="apple-touch-icon" sizes="60x60" href="images/icons/apple-icon-60x60.png">
    <link rel="apple-touch-icon" sizes="72x72" href="images/icons/apple-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="76x76" href="images/icons/apple-icon-76x76.png">
    <link rel="apple-touch-icon" sizes="114x114" href="images/icons/apple-icon-114x114.png">
    <link rel="apple-touch-icon" sizes="120x120" href="images/icons/apple-icon-120x120.png">
    <link rel="apple-touch-icon" sizes="144x144" href="images/icons/apple-icon-144x144.png">
    <link rel="apple-touch-icon" sizes="152x152" href="images/icons/apple-icon-152x152.png">
    <link rel="apple-touch-icon" sizes="180x180" href="images/icons/apple-icon-180x180.png">
    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
    <link rel="icon" href="favicon.ico" type="image/x-icon">
    <link rel="icon" type="image/png" sizes="32x32" href="images/icons/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="96x96" href="images/icons/favicon-96x96.png">
    <link rel="icon" type="image/png" sizes="16x16" href="images/icons/favicon-16x16.png">
    <link rel="icon" type="image/png" sizes="192x192"  href="images/icons/android-icon-192x192.png">
    <link rel="manifest" href="manifest.json">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="images/icons/ms-icon-144x144.png">
    <meta name="theme-color" content="#ffffff">
	<jdoc:include type="head" />
<!--	<script>addToHomescreen();</script>-->

	<!--[if lt IE 9]>
		<script src="<?php echo $this->baseurl; ?>/media/jui/js/html5.js"></script>
	<![endif]-->
</head>

<?php
    $isoffline = false;
    $ishomepage = false;

    if ($_REQUEST['option'] == 'com_content') {
        $isoffline = true;
    }

    $app = JFactory::getApplication();
    $menu = $app->getMenu();
    if ($menu->getActive() == $menu->getDefault()) {
        $ishomepage = true;
    }
?>

<body <?php if($isoffline){print 'class="homepage"';} ?> >

<?php if(!$isoffline){?>
<div class="header-full">
    <div class="container">
        <div class="header-logo col-lg-4 col-sm-5 col-xs-12">
            <jdoc:include type="modules" name="logo" style="none" />
        </div>

        <div class="header-links col-lg-8 col-sm-7 col-xs-12 hidden-xs">
            <jdoc:include type="modules" name="mainmenu" style="none" />
        </div>
    </div>
</div>
<?php } ?>

<?php if ($isoffline) { ?>
    <div class="container-full">
        <?php if ($ishomepage) { ?>

            <div class="home-top-content">
                <div class="home-header">
                    <div class="container">
                        <div class="col-sm-10 col-sm-offset-1 col-xs-12">
                            <div class="home-sign-in">
                                <jdoc:include type="modules" name="mainmenu" style="none" />
                            </div>
                            <div class="home-logo">
                                <jdoc:include type="modules" name="home-logo" style="none" />
                            </div>
                        </div>
                    </div>
                    <span class="home-header-text">
                        <h1><b>Cool With It™</b> is the 100% free<br>dating site that takes you out.</h1>
                    </span>
                </div>
                <div class="home-sign-up">
                    <div class="container">
                        <div class="col-sm-10 col-sm-offset-1 col-xs-12 padding-null-xs">
                            <div class="sign-up-box">
                                <div class="sign-up-text">
                                    <span>Be yourself, earn credits and use them on great experiences and local deals.</span>
                                    <span class="sign-up-below"><strong>Sign up below</strong> to meet people, go out and have fun!</span>
                                </div>
                                <div class="sign-up-border">
                                    <div class="sign-up-fb">
                                        <jdoc:include type="modules" name="social" style="none" />
                                    </div>
                                    <div class="sign-up-separator">
                                        <span class="sign-up-or">OR</span>
                                    </div>
                                    <div class="sign-up-site">
                                        <jdoc:include type="modules" name="mylogin" style="none" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="home-gradient"></div>
        <?php } ?>

        <?php if ($ishomepage) { ?>
            <div class="home-center-content">
                <div class="container">
                    <jdoc:include type="modules" name="center-content" style="none" />
                </div>
            </div>
            <div class="home-gradient"></div>
            <div class="home-bottom-content">
                <div class="container">
                    <jdoc:include type="modules" name="bottom-content" style="none" />
                </div>
            </div>
        <?php } else { ?>
            <jdoc:include type="component" />
        <?php } ?>
    </div>
<?php } else { ?>
    <jdoc:include type="component" />
<?php } ?>

<div class="footer-full">
    <div class="footer-line">
        <?php if($isoffline){ ?>
            <div class="container">
                <a href="https://www.facebook.com/imcoolwithit" target="_blank">
                    <i class="icon-fb"></i>
                </a>
                <a href="https://twitter.com/rucoolwithit" target="_blank">
                    <i class="icon-twitter"></i>
                </a>
                <a href="https://www.youtube.com/channel/UCdDj1mn8zURzGIjieXnkgZg" target="_blank">
                    <i class="icon-youtube"></i>
                </a>
            </div>
        <?php } ?>
    </div>
    <div class="container">
        <div class="footer-links col-sm-8 col-xs-12">
            <jdoc:include type="modules" name="footer" style="none" />
        </div>
        <div class="footer-text padding-null col-sm-4 col-xs-12">
            <jdoc:include type="modules" name="copyright" style="none" />
        </div>
    </div>
</div>
</body>
</html>