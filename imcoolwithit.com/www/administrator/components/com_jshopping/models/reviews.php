<?php
/**
* @version      4.1.0 13.02.2011
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/

defined('_JEXEC') or die('Restricted access');
jimport( 'joomla.application.component.model');

class JshoppingModelReviews extends JModelLegacy{
    
     function getAllReviews($category_id = null, $product_id = null, $limitstart = null, $limit = null, $text_search = null, $result = "list", $vendor_id = 0, $order = null, $orderDir = null) {
            
        $lang = JSFactory::getLang();
        $db = JFactory::getDBO(); 
        $where = "";
        if ($product_id) $where .= " AND pr_rew.product_id='".$db->escape($product_id)."' ";
        if ($vendor_id) $where .= " AND pr.vendor_id='".$db->escape($vendor_id)."' ";
        
        if($limit > 0) {
            $limit = " LIMIT " . $limitstart . " , " . $limit;
        }
        $where .= ($text_search) ? ( " AND CONCAT_WS('|',pr.`".$lang->get('name')."`,pr.`".$lang->get('short_description')."`,pr.`".$lang->get('description')."`,pr_rew.review, pr_rew.user_name, pr_rew.user_email ) LIKE '%".$db->escape($text_search)."%' " ) : ('');
        $ordering = 'pr_rew.review_id desc';
        
        if ($order && $orderDir){
            $ordering = $order." ".$orderDir;
        }
        
        if($category_id) {   
            $query = "select pr.`".$lang->get('name')."` as name,pr_rew.* , DATE_FORMAT(pr_rew.`time`,'%d.%m.%Y') as dateadd 
            from  #__jshopping_products_reviews as pr_rew
            LEFT JOIN #__jshopping_products  as pr USING (product_id)
            LEFT JOIN `#__jshopping_products_to_categories` AS pr_cat USING (product_id)
            WHERE pr_cat.category_id = '" . $db->escape($category_id) . "' ".$where." ORDER BY ". $ordering ." ". $limit;
        }else {
            $query = "select pr.`".$lang->get('name')."` as name,pr_rew.*, DATE_FORMAT(pr_rew.`time`,'%d.%m.%Y') as dateadd 
            from  #__jshopping_products_reviews as pr_rew
            LEFT JOIN #__jshopping_products  as pr USING (product_id)            
            WHERE 1 ".$where." ORDER BY ". $ordering ." ". $limit;
        }
        extract(js_add_trigger(get_defined_vars(), "before"));
        $db->setQuery($query);
        if ($result=="list"){
            return $db->loadObjectList();
        }else{
            $db->query();
            return $db->getNumRows();    
        }
    }
    
    function getReview($id){
        $db = JFactory::getDBO();
        $lang = JSFactory::getLang();   
        $query = "select pr_rew.*, pr.`".$lang->get('name')."` as name from #__jshopping_products_reviews as pr_rew LEFT JOIN #__jshopping_products  as pr USING (product_id)  where pr_rew.review_id = '$id'";
        $db->setQuery($query); 
        return $db->loadObject(); 
    }
    
    function getProdNameById($id){
        $db = JFactory::getDBO();
        $lang = JSFactory::getLang();   
        $query = "select pr.`".$lang->get('name')."` as name from #__jshopping_products  as pr where pr.product_id = '$id' LIMIT 1";
        $db->setQuery($query); 
        return $db->loadResult(); 
    }
    
    function deleteReview($id){
        $db = JFactory::getDBO(); 
        $query = "delete from #__jshopping_products_reviews where review_id = '$id'";
        $db->setQuery($query);
        return $db->query();
    }
}
?>