<?php
/**
* @version      3.13.2 16.02.2013
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/

defined( '_JEXEC' ) or die( 'Restricted access' );
jimport('joomla.application.component.controller');

class JshoppingControllerProduct_images extends JControllerLegacy{
    
    function __construct($config = array()){
        parent::__construct( $config );
    }
	
	function _getLinkForImage($text, $filename) {
		$position = JRequest::getInt('position');
		return '<a href="#" onclick="setImageFromFolder('.$position.', \''.$filename.'\'); return false;">'.$text.'</a>';
	}
    
    function display($cachable = false, $urlparams = false){
		$jshopConfig = JSFactory::getConfig();
        $position = JRequest::getInt('position');
		$filter = JRequest::getVar('filter');
		$path_length = strlen($jshopConfig->image_product_path) + 1;
        $html = "<div class='images_list_search'><input type='text' id='filter_product_image_name' value='".$filter."'> <input type='button' value='"._JSHOP_SEARCH."' onclick='product_images_request(".$position.", \"index.php?option=com_jshopping&controller=product_images&task=display\", jQuery(\"#filter_product_image_name\").val())'></div>";
		$html .= '<div class="images_list">';
		foreach( new RecursiveIteratorIterator ( new RecursiveDirectoryIterator ( $jshopConfig->image_product_path ), RecursiveIteratorIterator :: SELF_FIRST ) as $v ) {
			$filename = substr($v, $path_length);            
            if ($filter!='' && !substr_count($filename, $filter)) continue;
			if (file_exists($jshopConfig->image_product_path .'/'.'thumb_'.$filename)){
				$html .= '<div class="one_image">';
				$html .= '<table>';
				$html .= '<tr><td align="center" valign="middle"><div>';
				$html .= $this->_getLinkForImage('<img alt="" title="'.$filename.'" src="'.$jshopConfig->image_product_live_path.'/thumb_'.$filename.'"/>', $filename);
				$html .= '</div></td></tr>';
				$html .= '<tr><td valign="bottom" align="center"><div>';
				$html .= $this->_getLinkForImage($filename, $filename);
				$html .= '</div></td></tr>';
				$html .= '</table>';
				$html .= '</div>';
			}
		}
		$html .= '<div style="clear: both"></div>';
		$html .= '</div>';
        
        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger('onBeforeDisplayProductsImagesHTML', array(&$html));
		echo $html;
		die();
	}
}
?>