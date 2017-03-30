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
$doc->addScript('templates/' . $this->template . '/minify/cool.js');
//$doc->addScript('templates/' . $this->template . '/js/js.cookie.js');
//$doc->addScript('templates/' . $this->template . '/js/template.js');
//$doc->addScript('templates/' . $this->template . '/js/addtohomescreen.js');

// Add Stylesheets
$doc->addStyleSheet('templates/' . $this->template . '/minify/cool.css');
//$doc->addStyleSheet('templates/' . $this->template . '/css/template.css');
//$doc->addStyleSheet('templates/' . $this->template . '/css/flaticon.css');
//$doc->addStyleSheet('templates/' . $this->template . '/css/bootstrap.css');
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
<!--    <link rel="manifest" href="manifest.json">-->
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

<script>
    (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
            (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
        m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
    })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

    ga('create', 'UA-89376801-1', 'auto');
    ga('send', 'pageview');

</script>


<?php if(!$isoffline){?>
<div class="header-full">
    <div class="container">
        <div class="header-container">
            <div class="header-logo col-lg-4 col-sm-5 col-xs-12">
                <jdoc:include type="modules" name="logo" style="none" />
            </div>

            <div class="header-links-cool col-lg-8 col-sm-7 col-xs-12 hidden-xs">
                <jdoc:include type="modules" name="mainmenu" style="none" />
            </div>
        </div>
    </div>
</div>
<?php } ?>

<?php if ($isoffline) { ?>
    <div class="container-full">
        <?php if ($ishomepage) { ?>
            <div class="home-header">
                <div class="container">
                    <div class="header-container">
                        <div class="header-left">
                            <div class="home-logo">
                                <jdoc:include type="modules" name="home-logo" style="none" />
                            </div>
                            <span class="header-info">
                                The 100% free dating site that takes you out.
                            </span>
                        </div>
                        <div class="home-sign-in">
                            <jdoc:include type="modules" name="mainmenu" style="none" />
                        </div>

                    </div>
                </div>
                <div class="home-gradient"></div>
            </div>
            <div class="home-top-content">
                <h1>Do something new.</h1>
                <div class="top-info">Meet people. Find cool things to do.</div>
                <button class="join-button">Join</button>
            </div>
            <div class="search-to-go">
                <div class="title">Search local singles, places, and events to go to.</div>
                <div class="container">
                    <div class="search-block">
                        <div class="search-title">Get ready</div>
                        <div class="search-image">
                            <img src="/images/home/search-to-go-1.jpg">
                        </div>
                    </div>
                    <div class="search-block">
                        <div class="search-title">Grab a bite</div>
                        <div class="search-image">
                            <img src="/images/home/search-to-go-2.jpg">
                        </div>
                    </div>
                    <div class="search-block">
                        <div class="search-title">Tour a brewary</div>
                        <div class="search-image">
                            <img src="/images/home/search-to-go-3.jpg">
                        </div>
                    </div>
                    <div class="search-block">
                        <div class="search-title">Go to an event</div>
                        <div class="search-image">
                            <img src="/images/home/search-to-go-4.jpg">
                        </div>
                    </div>
                </div>
            </div>
            <div class="how-we-work home-block">
                <div class="title">How we work</div>
                <div class="container">
                    <div class="how-block">
                        <div class="image">
                            <img src="/images/home/how-we-work-1.png">
                        </div>
                        <div class="how-info">
                            <span class="how-title">Search</span>
                            <span class="how-text">Reach out to local singles or attend an event to meet new people.</span>
                            <a href="#">Sign up</a>
                        </div>
                    </div>
                    <div class="how-block">
                        <div class="image">
                            <img src="/images/home/how-we-work-2.png">
                        </div>
                        <div class="how-info">
                            <span class="how-title">Find things to do</span>
                            <span class="how-text">Search for local places and events. Ask someone to go with you or bring a friend.</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="makes-different">
                <div class="title">What makes us different</div>
                <div class="container">
                    <div class="makes-block">
                        <div class="image">
                            <img src="/images/home/makes-1.png">
                        </div>
                        <div class="makes-info">
                            <div class="makes-title">We cut down on the cost of dating</div>
                            <div class="makes-text">We work with local venues to offer members exclusive Linc Up date ideas. We'll notify you when we have offers in your area and you can use your Credits to redeem them.</div>
                        </div>
                    </div>
                    <div class="makes-block">
                        <div class="image">
                            <img src="/images/home/makes-2.png">
                        </div>
                        <div class="makes-info">
                            <div class="makes-title">Less wasted time</div>
                            <div class="makes-text">Using a Credit system helps eliminate fake likes and harassing messages.</div>
                        </div>
                    </div>
                    <div class="makes-block">
                        <div class="image">
                            <img src="/images/home/makes-3.png">
                        </div>
                        <div class="makes-info">
                            <div class="makes-title">Honesty feedback</div>
                            <div class="makes-text">We developed the first honesty feedback rating system for dating, where members earn credits for reviewing a member they felt was honest.<br>Personal information remains private and we think it's a good way to reward people for being themselves, while promoting a safer online dating environment.</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="home-gradient"></div>

        <?php } else { ?>
            <jdoc:include type="component" />
        <?php } ?>
    </div>
<?php } else { ?>
    <jdoc:include type="component" />
<?php } ?>

<div class="footer-full">
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