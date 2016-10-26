CREATE TABLE IF NOT EXISTS `#__jshopping_categories` (
`category_id` int(11) NOT NULL auto_increment,
`category_image` varchar(255),
`category_parent_id` int(11) NOT NULL,
`category_publish` tinyint(1) unsigned NOT NULL default '1',
`category_ordertype` tinyint(1) unsigned NOT NULL default '1',
`category_template` varchar(64),
`ordering` int(3) NOT NULL,
`category_add_date` datetime,
`products_page` int(8) NOT NULL default '12',
`products_row` int(3) NOT NULL default '3',
`access` int(3) NOT NULL default '1',
PRIMARY KEY  (`category_id`),
KEY `sort_add_date` (`category_add_date`)
) /*!40100 DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci*/;

CREATE TABLE IF NOT EXISTS `#__jshopping_countries` (
`country_id` int(11) NOT NULL auto_increment,
`country_publish` tinyint(4) NOT NULL,
`ordering` smallint(6) NOT NULL,
`country_code` varchar(5) NOT NULL,
`country_code_2` varchar(5) NOT NULL,
`name_en-GB` varchar(255) NOT NULL,
`name_de-DE` varchar(255) NOT NULL,
PRIMARY KEY  (`country_id`)
) /*!40100 DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci*/;

CREATE TABLE IF NOT EXISTS `#__jshopping_currencies` (
`currency_id` int(11) NOT NULL auto_increment,
`currency_name` varchar(64) NOT NULL,
`currency_code` varchar(20) NOT NULL,
`currency_code_iso` VARCHAR(3) NOT NULL,
`currency_code_num` VARCHAR(3) NOT NULL,
`currency_ordering` int(11) NOT NULL,
`currency_value` DECIMAL(14,6) NOT NULL,
`currency_publish` tinyint(1) NOT NULL,
PRIMARY KEY  (`currency_id`)
) /*!40100 DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci*/;

CREATE TABLE IF NOT EXISTS `#__jshopping_delivery_times` (
`id` int(11) NOT NULL auto_increment,
`days` DECIMAL(8,2) NOT NULL,
PRIMARY KEY  (`id`)
) /*!40100 DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci*/;

CREATE TABLE IF NOT EXISTS `#__jshopping_products`(
`product_id` int(11) NOT NULL auto_increment,
`parent_id` int(11) NOT NULL,
`product_ean` varchar(32) NOT NULL,
`product_quantity` DECIMAL(12,2) NOT NULL,
`unlimited` TINYINT(1) NOT NULL,
`product_availability` varchar(1) NOT NULL,
`product_date_added` datetime NOT NULL default '0000-00-00 00:00:00',
`date_modify` datetime NOT NULL default '0000-00-00 00:00:00',
`product_publish` tinyint(1) NOT NULL,
`product_tax_id` tinyint(3) NOT NULL,
`currency_id` int(4) NOT NULL,
`product_template` varchar(64) NOT NULL default "default",
`product_url` varchar(255) NOT NULL,
`product_old_price` DECIMAL(14,4) NOT NULL,
`product_buy_price` DECIMAL(14,4) NOT NULL,
`product_price` DECIMAL(18,6) NOT NULL,
`min_price` DECIMAL(12,2) NOT NULL,
`different_prices` TINYINT(1) NOT NULL,
`product_weight` DECIMAL(14,4) NOT NULL,
`image` varchar(255) NOT NULL,
`product_manufacturer_id` int(11) NOT NULL,
`product_is_add_price` TINYINT(1) NOT NULL,
`add_price_unit_id` int(3) NOT NULL,
`average_rating` float(4,2) NOT NULL,
`reviews_count` int(11) NOT NULL,
`delivery_times_id` int(4) NOT NULL,
`hits` int(11) NOT NULL,
`weight_volume_units` DECIMAL(14,4) NOT NULL,
`basic_price_unit_id` int(3) NOT NULL,
`label_id` int(11) NOT NULL,
`vendor_id` int(11) NOT NULL,
`access` int(3) NOT NULL default '1',
PRIMARY KEY  (`product_id`),
KEY `product_manufacturer_id` (`product_manufacturer_id`)
) /*!40100 DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci*/;

CREATE TABLE IF NOT EXISTS `#__jshopping_products_images` (
`image_id` int(11) NOT NULL auto_increment,
`product_id` int(11) NOT NULL,
`image_name` varchar(255) NOT NULL,
`name` varchar(255) NOT NULL,
`ordering` TINYINT(4) NOT NULL,
PRIMARY KEY  (`image_id`)
) /*!40100 DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci*/;

CREATE TABLE IF NOT EXISTS `#__jshopping_products_prices` (
`price_id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
`product_id` INT NOT NULL,
`discount` DECIMAL(16,6) NOT NULL,
`product_quantity_start` INT NOT NULL,
`product_quantity_finish` INT NOT NULL
) /*!40100 DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci*/;

CREATE TABLE IF NOT EXISTS `#__jshopping_products_relations` (
`id` int(11) NOT NULL auto_increment PRIMARY KEY,
`product_id` int(11) NOT NULL,
`product_related_id` int(11) NOT NULL
) /*!40100 DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci*/;

CREATE TABLE IF NOT EXISTS `#__jshopping_products_to_categories` (
`product_id` int(11) NOT NULL,
`category_id` int(11) NOT NULL,
`product_ordering` int(11) NOT NULL,
PRIMARY KEY  (`product_id`,`category_id`),
KEY `category_id` (`category_id`),
KEY `product_id` (`product_id`)
) /*!40100 DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci*/;

CREATE TABLE IF NOT EXISTS `#__jshopping_products_videos` (
`video_id` int(11) NOT NULL auto_increment,
`product_id` int(11) NOT NULL,
`video_name` varchar(255) NOT NULL,
`video_code` text NOT NULL,
`video_preview` VARCHAR( 255 ) NOT NULL,
PRIMARY KEY  (`video_id`)
) /*!40100 DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci*/;

CREATE TABLE IF NOT EXISTS `#__jshopping_products_reviews` (
`review_id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
`product_id` INT NOT NULL,
`user_id` INT NOT NULL,
`user_name` VARCHAR( 255 ) NOT NULL,
`user_email` VARCHAR( 255 ) NOT NULL,
`time` DATETIME NOT NULL,
`review` TEXT NOT NULL,
`mark` INT NOT NULL,
`publish` TINYINT(1) NOT NULL,
`ip` VARCHAR(20) NOT NULL
) /*!40100 DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci*/;

CREATE TABLE IF NOT EXISTS `#__jshopping_products_files` (
`id` int(11) NOT NULL auto_increment,
`product_id` int(11) NOT NULL,
`demo` varchar(255) NOT NULL,
`demo_descr` varchar(255) NOT NULL,
`file` varchar(255) NOT NULL,
`file_descr` varchar(255) NOT NULL,
`ordering` int(11) NOT NULL,
PRIMARY KEY  (`id`),
KEY `product_id` (`product_id`)
) /*!40100 DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci*/;

CREATE TABLE IF NOT EXISTS `#__jshopping_taxes` (
`tax_id` int(11) NOT NULL auto_increment,
`tax_name` varchar(50) NOT NULL,
`tax_value` DECIMAL(12,2) NOT NULL,
PRIMARY KEY  (`tax_id`)
) /*!40100 DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci*/;

CREATE TABLE IF NOT EXISTS `#__jshopping_config` (
`id` int(11) NOT NULL auto_increment,
`count_products_to_page` int(4) NOT NULL,
`count_products_to_row` int(2) NOT NULL,
`count_category_to_row` int(2) NOT NULL,
`image_category_width` int(4) NOT NULL,
`image_category_height` int(4) NOT NULL,
`image_product_width` int(4) NOT NULL,
`image_product_height` int(4) NOT NULL,
`image_product_full_width` int(4) NOT NULL,
`image_product_full_height` int(4) NOT NULL,
`image_product_original_width` int(4) NOT NULL,
`image_product_original_height` int(4) NOT NULL,
`video_product_width` int(4) NOT NULL,
`video_product_height` int(4) NOT NULL,
`adminLanguage` varchar(8) NOT NULL,
`defaultLanguage` varchar(8) NOT NULL,
`mainCurrency` int(4) NOT NULL,
`decimal_count` TINYINT( 1 ) NOT NULL ,
`decimal_symbol` VARCHAR( 5 ) NOT NULL ,
`thousand_separator` VARCHAR( 5 ) NOT NULL ,
`currency_format` TINYINT( 1 ) NOT NULL,
`use_rabatt_code` TINYINT( 1 ) NOT NULL,
`enable_wishlist` TINYINT( 1 ) NOT NULL,
`default_status_order` TINYINT( 1 ) NOT NULL,
`store_address_format` VARCHAR(32) NOT NULL,
`store_date_format` VARCHAR(32) NOT NULL,
`contact_email` VARCHAR(128) NOT NULL,
`allow_reviews_prod` TINYINT( 1 ) NOT NULL ,
`allow_reviews_only_registered` TINYINT( 1 ) NOT NULL,
`allow_reviews_manuf` TINYINT( 1 ) NOT NULL,
`max_mark` INT NOT NULL,
`summ_null_shipping` DECIMAL(12,2) NOT NULL,
`without_shipping` TINYINT( 1 ) NOT NULL,
`without_payment` TINYINT( 1 ) NOT NULL,
`pdf_parameters` VARCHAR(32) NOT NULL,
`next_order_number` int(11) NOT NULL default '1',
`shop_user_guest` TINYINT( 1 ) NOT NULL,
`hide_product_not_avaible_stock` TINYINT( 1 ) NOT NULL,
`show_buy_in_category` TINYINT( 1 ) NOT NULL,
`user_as_catalog` TINYINT( 1 ) NOT NULL,  
`show_tax_in_product` TINYINT( 1 ) NOT NULL,
`show_tax_product_in_cart` TINYINT( 1 ) NOT NULL,
`show_plus_shipping_in_product` TINYINT( 1 ) NOT NULL,
`hide_buy_not_avaible_stock` TINYINT( 1 ) NOT NULL,  
`show_sort_product` TINYINT( 1 ) NOT NULL,  
`show_count_select_products` TINYINT( 1 ) NOT NULL,
`order_send_pdf_client` TINYINT( 1 ) NOT NULL,
`order_send_pdf_admin` TINYINT( 1 ) NOT NULL,  
`show_delivery_time` TINYINT( 1 ) NOT NULL,  
`securitykey` VARCHAR( 128 ) NOT NULL,    
`demo_type` TINYINT( 1 ) NOT NULL,
`product_show_manufacturer_logo` TINYINT( 1 ) NOT NULL,
`product_show_manufacturer` TINYINT( 1 ) NOT NULL,
`product_show_weight` TINYINT( 1 ) NOT NULL,
`max_count_order_one_product` int(11) NOT NULL,
`min_count_order_one_product` int(11) NOT NULL,
`min_price_order` int(11) NOT NULL,
`max_price_order` int(11) NOT NULL,
`hide_tax` TINYINT(1) NOT NULL,
`licensekod` text NOT NULL,
`product_attribut_first_value_empty` TINYINT(1) NOT NULL,
`show_hits` TINYINT(1) NOT NULL,
`show_registerform_in_logintemplate` TINYINT(1) NOT NULL,
`admin_show_product_basic_price` TINYINT(1) NOT NULL,
`admin_show_attributes` TINYINT(1) NOT NULL,
`admin_show_delivery_time` TINYINT(1) NOT NULL,
`admin_show_languages` TINYINT(1) NOT NULL,
`use_different_templates_cat_prod` TINYINT(1) NOT NULL,
`admin_show_product_video` TINYINT(1) NOT NULL,
`admin_show_product_related` TINYINT(1) NOT NULL,
`admin_show_product_files` TINYINT(1) NOT NULL,
`admin_show_product_bay_price` TINYINT(1) NOT NULL,
`admin_show_product_labels` TINYINT(1) NOT NULL,
`sorting_country_in_alphabet` TINYINT(1) NOT NULL,  
`hide_text_product_not_available` TINYINT(1) NOT NULL,  
`show_weight_order` TINYINT(1) NOT NULL,  
`discount_use_full_sum` TINYINT(1) NOT NULL,  
`show_cart_all_step_checkout` TINYINT(1) NOT NULL,  
`use_plugin_content` TINYINT(1) NOT NULL,  
`display_price_admin` TINYINT(1) NOT NULL,  
`display_price_front` TINYINT(1) NOT NULL,  
`product_list_show_weight` TINYINT(1) NOT NULL,  
`product_list_show_manufacturer` TINYINT(1) NOT NULL,  
`use_extend_tax_rule` TINYINT(4) NOT NULL,
`use_extend_display_price_rule` TINYINT(4) NOT NULL,
`fields_register` text NOT NULL,
`template` VARCHAR(128) NOT NULL,
`show_product_code` TINYINT(1) NOT NULL,
`show_product_code_in_cart` TINYINT(1) NOT NULL,
`savelog` TINYINT(1) NOT NULL,
`savelogpaymentdata` TINYINT(1) NOT NULL,
`product_list_show_min_price` TINYINT(1) NOT NULL,
`product_count_related_in_row` TINYINT(4) NOT NULL,
`category_sorting` TINYINT(1) NOT NULL default '1',
`product_sorting` TINYINT(1) NOT NULL default '1',
`product_sorting_direction` TINYINT(1) NOT NULL,
`show_product_list_filters` TINYINT(1) NOT NULL,
`admin_show_product_extra_field` TINYINT(1) NOT NULL,
`product_list_display_extra_fields` TEXT NOT NULL,
`filter_display_extra_fields` TEXT NOT NULL,
`product_hide_extra_fields` TEXT NOT NULL,
`cart_display_extra_fields` TEXT NOT NULL,
`default_country` int(11) NOT NULL,
`show_return_policy_in_email_order` TINYINT(1) NOT NULL,
`client_allow_cancel_order` TINYINT(1) NOT NULL,
`admin_show_vendors` TINYINT(1) NOT NULL,
`vendor_order_message_type` TINYINT(1) NOT NULL,
`admin_not_send_email_order_vendor_order` TINYINT(1) NOT NULL,
`not_redirect_in_cart_after_buy` TINYINT(1) NOT NULL,
`product_show_vendor` TINYINT(1) NOT NULL,
`product_show_vendor_detail` TINYINT(1) NOT NULL,
`product_list_show_vendor` TINYINT(1) NOT NULL,
`admin_show_freeattributes` TINYINT(1) NOT NULL,
`product_show_button_back` TINYINT(1) NOT NULL,
`calcule_tax_after_discount` TINYINT(1) NOT NULL,
`product_list_show_product_code` TINYINT(1) NOT NULL,
`radio_attr_value_vertical` TINYINT(1) NOT NULL,
`attr_display_addprice` TINYINT(1) NOT NULL,
`use_ssl` TINYINT(1) NOT NULL,
`product_list_show_price_description` TINYINT(1) NOT NULL,
`display_button_print` TINYINT(1) NOT NULL,
`hide_shipping_step` TINYINT(1) NOT NULL,
`hide_payment_step` TINYINT(1) NOT NULL,
`image_resize_type` TINYINT(1) NOT NULL,
`use_extend_attribute_data` TINYINT(1) NOT NULL,
`product_list_show_price_default` TINYINT(1) NOT NULL,
`product_list_show_qty_stock` TINYINT(1) NOT NULL,
`product_show_qty_stock` TINYINT(1) NOT NULL,
`displayprice` TINYINT(1) NOT NULL,
`use_decimal_qty` TINYINT(1) NOT NULL,
`ext_tax_rule_for` TINYINT(1) NOT NULL,
`display_reviews_without_confirm` TINYINT(1) NOT NULL,
`manufacturer_sorting` TINYINT(1) NOT NULL,
`admin_show_units` TINYINT(1) NOT NULL,
`main_unit_weight` TINYINT(3) NOT NULL,
`create_alias_product_category_auto` TINYINT(1) NOT NULL,
`delivery_order_depends_delivery_product` TINYINT(1) NOT NULL, 
`show_delivery_time_step5` TINYINT(1) NOT NULL,
`other_config` TEXT NOT NULL,
`shop_mode` TINYINT(1) NOT NULL,
PRIMARY KEY  (`id`)
) /*!40100 DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci*/;

CREATE TABLE IF NOT EXISTS `#__jshopping_manufacturers` (
`manufacturer_id` INT NOT NULL auto_increment,
`manufacturer_url` VARCHAR( 255 ) NOT NULL ,
`manufacturer_logo` VARCHAR( 255 ) NOT NULL ,
`manufacturer_publish` TINYINT(1) NOT NULL,
`products_page` int(11) NOT NULL, 
`products_row` int(11) NOT NULL,
`ordering` int(6) NOT NULL,
 PRIMARY KEY (`manufacturer_id`)
) /*!40100 DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci*/;

CREATE TABLE IF NOT EXISTS `#__jshopping_coupons` (
`coupon_id` int(11) NOT NULL auto_increment,
`coupon_type` tinyint(4) NOT NULL COMMENT 'value_or_percent',
`coupon_code` varchar(100) NOT NULL,
`coupon_value` DECIMAL(12,2) NOT NULL default '0.00',
`tax_id` int(11) NOT NULL,
`used` int(11) NOT NULL,
`for_user_id` int(11) NOT NULL,
`coupon_start_date` date NOT NULL default '0000-00-00',
`coupon_expire_date` date NOT NULL default '0000-00-00',
`finished_after_used` int(11) NOT NULL,
`coupon_publish` tinyint(4) NOT NULL,
PRIMARY KEY  (`coupon_id`)
) /*!40100 DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci*/;

CREATE TABLE IF NOT EXISTS `#__jshopping_users` (
`user_id` INT NOT NULL ,
`usergroup_id` INT NOT NULL,
`payment_id` INT NOT NULL,
`shipping_id` INT NOT NULL,
`u_name` VARCHAR(150) NOT NULL,
`number` VARCHAR(32) NOT NULL,
`title` tinyint(1) NOT NULL,
`f_name` VARCHAR( 255 ) NOT NULL,
`l_name` VARCHAR( 255 ) NOT NULL,
`m_name` VARCHAR( 255 ) NOT NULL,
`firma_name` VARCHAR( 100 ) NOT NULL,
`client_type` tinyint(1) NOT NULL,
`firma_code` VARCHAR( 100 ) NOT NULL,
`tax_number` VARCHAR( 100 ) NOT NULL,
`email` VARCHAR( 255 ) NOT NULL,
`birthday` DATE NOT NULL,
`street` VARCHAR( 255 ) NOT NULL,
`street_nr` VARCHAR( 16 ) NOT NULL,
`home` VARCHAR( 20 ) NOT NULL,
`apartment` VARCHAR( 20 ) NOT NULL,
`zip` VARCHAR( 20 ) NOT NULL,
`city` VARCHAR( 100 ) NOT NULL,
`state` VARCHAR( 100 ) NOT NULL,
`country` int(11) default NULL,
`phone` VARCHAR( 20 ) NOT NULL,
`mobil_phone` VARCHAR( 20 ) NOT NULL,
`fax` VARCHAR( 20 ) NOT NULL,
`ext_field_1` VARCHAR(255) NOT NULL,
`ext_field_2` VARCHAR(255) NOT NULL,
`ext_field_3` VARCHAR(255) NOT NULL,
`delivery_adress` TINYINT ( 1 ) NOT NULL,
`d_title` tinyint(1) NOT NULL,
`d_f_name` VARCHAR(255) NOT NULL,
`d_l_name` VARCHAR(255) NOT NULL,
`d_m_name` VARCHAR(255) NOT NULL,
`d_firma_name` VARCHAR(100) NOT NULL,
`d_email` VARCHAR(255) NOT NULL,
`d_birthday` DATE NOT NULL,
`d_street` VARCHAR(255) NOT NULL,
`d_street_nr` VARCHAR(16) NOT NULL,
`d_home` VARCHAR(20) NOT NULL,
`d_apartment` VARCHAR(20) NOT NULL,
`d_zip` VARCHAR(20) NOT NULL,
`d_city` VARCHAR(100) NOT NULL,
`d_state` VARCHAR(100) NOT NULL,
`d_country` int(11) NOT NULL,
`d_phone` VARCHAR(20) NOT NULL,
`d_mobil_phone` VARCHAR(20) NOT NULL,
`d_fax` VARCHAR(20) NOT NULL,
`d_ext_field_1` VARCHAR(255) NOT NULL,
`d_ext_field_2` VARCHAR(255) NOT NULL,
`d_ext_field_3` VARCHAR(255) NOT NULL,
`lang` VARCHAR(5) NOT NULL,
UNIQUE KEY `user_id` (`user_id`),
KEY `u_name` (`u_name`),
KEY `usergroup_id` (`usergroup_id`)
) /*!40100 DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci*/;

CREATE TABLE IF NOT EXISTS `#__jshopping_payment_method` (
`payment_id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
`payment_code` VARCHAR(32) NOT NULL ,
`payment_class` VARCHAR(100) NOT NULL,
`scriptname` VARCHAR(100) NOT NULL,
`payment_publish` TINYINT(1) NOT NULL,
`payment_ordering` INT NOT NULL,
`payment_params` TEXT NOT NULL,
`payment_type` TINYINT(4) NOT NULL,
`price` DECIMAL(12,2) NOT NULL,
`price_type` TINYINT(1) NOT NULL,
`tax_id` int(11) NOT NULL,
`image` VARCHAR(255) NOT NULL,
`show_descr_in_email` TINYINT(1) NOT NULL,
`show_bank_in_order` TINYINT(1) NOT NULL DEFAULT '1',
`order_description` TEXT NOT NULL,
`name_en-GB` VARCHAR(100) NOT NULL,
`description_en-GB` TEXT NOT NULL,
`name_de-DE` VARCHAR(100) NOT NULL,
`description_de-DE` TEXT NOT NULL
) /*!40100 DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci*/;

CREATE TABLE IF NOT EXISTS `#__jshopping_usergroups` (
`usergroup_id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`usergroup_name` VARCHAR( 64 ) NOT NULL ,
`usergroup_discount` DECIMAL(12,2) NOT NULL,
`usergroup_description` TEXT NOT NULL ,
`usergroup_is_default` TINYINT(1) NOT NULL,
`name_en-GB` varchar(255) NOT NULL,
`name_de-DE` varchar(255) NOT NULL
) /*!40100 DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci*/;

CREATE TABLE IF NOT EXISTS `#__jshopping_shipping_method` (
`shipping_id` int(11) NOT NULL auto_increment,
`alias` varchar(100) NOT NULL,
`name_en-GB` varchar(100) NOT NULL,
`description_en-GB` text NOT NULL,
`name_de-DE` varchar(100) NOT NULL,
`description_de-DE` text NOT NULL,
`published` tinyint(1) NOT NULL,
`payments` varchar(255) NOT NULL,
`image` VARCHAR(255) NOT NULL,
`ordering` int(6) NOT NULL,
`params` LONGTEXT NOT NULL,
PRIMARY KEY (`shipping_id`)
) /*!40100 DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci*/;

CREATE TABLE IF NOT EXISTS `#__jshopping_shipping_ext_calc`(
`id` int(11) NOT NULL auto_increment,
`name` varchar(100) NOT NULL,
`alias` varchar(100) NOT NULL,
`description` text NOT NULL,
`params` LONGTEXT NOT NULL,
`shipping_method` text NOT NULL,
`published` tinyint(1) NOT NULL,
`ordering` int(6) NOT NULL,
PRIMARY KEY (`id`)
) /*!40100 DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci*/;

CREATE TABLE IF NOT EXISTS `#__jshopping_shipping_method_price` (
`sh_pr_method_id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
`shipping_method_id` INT NOT NULL,
`shipping_tax_id` int(11) NOT NULL,
`shipping_stand_price` DECIMAL(14,4) NOT NULL,
`package_tax_id` int(11) NOT NULL,
`package_stand_price` DECIMAL(14,4) NOT NULL,
`delivery_times_id` int(11) NOT NULL,
`params` LONGTEXT NOT NULL
) /*!40100 DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci*/;

CREATE TABLE IF NOT EXISTS `#__jshopping_shipping_method_price_weight` (
`sh_pr_weight_id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`sh_pr_method_id` INT NOT NULL ,
`shipping_price` DECIMAL(12,2) NOT NULL ,
`shipping_weight_from` DECIMAL(14,4) NOT NULL ,
`shipping_weight_to` DECIMAL(14,4) NOT NULL ,
`shipping_package_price` DECIMAL(14,4) NOT NULL ,
INDEX ( `sh_pr_method_id` )
) /*!40100 DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci*/;

CREATE TABLE IF NOT EXISTS `#__jshopping_shipping_method_price_countries` (
`sh_method_country_id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`country_id` INT NOT NULL ,
`sh_pr_method_id` INT NOT NULL ,
INDEX ( `country_id`) , INDEX( `sh_pr_method_id` )
) /*!40100 DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci*/;

CREATE TABLE IF NOT EXISTS `#__jshopping_order_history` (
`order_history_id` int(11) NOT NULL auto_increment,
`order_id` int(11) NOT NULL,
`order_status_id` tinyint(1) NOT NULL,
`status_date_added` datetime NOT NULL,
`customer_notify` int(1),
`comments` text,
PRIMARY KEY  (`order_history_id`)
) /*!40100 DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci*/;

CREATE TABLE IF NOT EXISTS `#__jshopping_order_item`(
`order_item_id` int(11) NOT NULL auto_increment,
`order_id` int(11) NOT NULL,
`product_id` int(11) NOT NULL,
`product_ean` varchar(50) NOT NULL,
`product_name` varchar(100) NOT NULL,
`product_quantity` DECIMAL(12,2) NOT NULL,
`product_item_price` DECIMAL(14,4) NOT NULL,
`product_tax` DECIMAL(14,4) NOT NULL,
`product_attributes` text NOT NULL,
`product_freeattributes` text NOT NULL,
`attributes` text NOT NULL,
`freeattributes` text NOT NULL,
`extra_fields` TEXT NOT NULL,
`files` text NOT NULL,
`weight` DECIMAL(14,4) NOT NULL,
`thumb_image` varchar(255) NOT NULL,
`manufacturer` varchar(255) NOT NULL,
`delivery_times_id` int(4) NOT NULL,
`vendor_id` int(11) NOT NULL,
`basicprice` DECIMAL(12,2) NOT NULL,
`basicpriceunit` varchar(255) NOT NULL,
`params` text NOT NULL,
PRIMARY KEY (`order_item_id`)
) /*!40100 DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci*/;

CREATE TABLE IF NOT EXISTS `#__jshopping_order_status` (
`status_id` int(11) NOT NULL auto_increment,
`status_code` char(1) NOT NULL,
`name_en-GB` varchar(100) NOT NULL,
`name_de-DE` varchar(100) NOT NULL,
PRIMARY KEY  (`status_id`)
) /*!40100 DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci*/;

CREATE TABLE IF NOT EXISTS `#__jshopping_orders`(
`order_id` int(11) NOT NULL auto_increment,
`order_number` VARCHAR(50) NOT NULL,
`user_id` int(11) NOT NULL,
`order_total` DECIMAL(14,4) NOT NULL,
`order_subtotal` DECIMAL(14,4) NOT NULL,
`order_tax` DECIMAL(14,4) NOT NULL,
`order_tax_ext` text NOT NULL,
`order_shipping` DECIMAL(14,4) NOT NULL,
`order_payment` DECIMAL(14,4) NOT NULL,
`order_discount` DECIMAL(14,4) NOT NULL,
`shipping_tax` DECIMAL(12,4) NOT NULL,
`shipping_tax_ext` text NOT NULL,
`payment_tax` DECIMAL(12,4) NOT NULL,
`payment_tax_ext` text NOT NULL,
`order_package` DECIMAL(12,2) NOT NULL,
`package_tax_ext` text NOT NULL,
`currency_code` VARCHAR( 20 ) NOT NULL,
`currency_code_iso` VARCHAR( 3 ) NOT NULL,
`currency_exchange` DECIMAL(14,6) NOT NULL,
`order_status` tinyint(4) NOT NULL,
`order_created` tinyint(1) NOT NULL,
`order_date` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`invoice_date` DATETIME NOT NULL,
`order_m_date` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`shipping_method_id` int(11) NOT NULL,
`delivery_times_id` int(11) NOT NULL,
`payment_method_id` int(11) NOT NULL,
`payment_params` text NOT NULL,
`payment_params_data` text NOT NULL,
`shipping_params` text NOT NULL,
`shipping_params_data` text NOT NULL,
`delivery_time` VARCHAR(100) NOT NULL,
`delivery_date` DATETIME NOT NULL,
`coupon_id` int(11) NOT NULL,
`ip_address` varchar(15) NOT NULL,
`order_add_info` text NOT NULL,
`title` tinyint(1) NOT NULL ,
`f_name` varchar(255) NOT NULL,
`l_name` varchar(255) NOT NULL,
`m_name` varchar(255) NOT NULL,
`firma_name` varchar(255) NOT NULL,
`client_type` tinyint(1) NOT NULL,
`client_type_name` VARCHAR(100) NOT NULL,
`firma_code` VARCHAR( 100 ) NOT NULL,
`tax_number` VARCHAR( 100 ) NOT NULL,
`email` varchar(255) NOT NULL ,
`birthday` DATE NOT NULL,
`street` varchar(100) NOT NULL,
`street_nr` varchar(16) NOT NULL,
`home` VARCHAR( 20 ) NOT NULL,
`apartment` VARCHAR( 20 ) NOT NULL,
`zip` varchar(20) NOT NULL,
`city` varchar(100) NOT NULL,
`state` varchar(100) NOT NULL,
`country` int(11) NOT NULL,
`phone` varchar(20) NOT NULL,
`mobil_phone` VARCHAR( 20 ) NOT NULL,
`fax` varchar(20) NOT NULL,
`ext_field_1` VARCHAR(255) NOT NULL,
`ext_field_2` VARCHAR(255) NOT NULL,
`ext_field_3` VARCHAR(255) NOT NULL,
`d_title` tinyint(1) NOT NULL,
`d_f_name` varchar(255) NOT NULL,
`d_l_name` varchar(255) NOT NULL,
`d_m_name` varchar(255) NOT NULL,
`d_firma_name` varchar(255) NOT NULL,
`d_email` varchar(255) NOT NULL,
`d_birthday` DATE NOT NULL,
`d_street` varchar(100) NOT NULL,
`d_street_nr` varchar(16) NOT NULL,
`d_home` VARCHAR( 20 ) NOT NULL,
`d_apartment` VARCHAR( 20 ) NOT NULL,
`d_zip` varchar(20) NOT NULL,
`d_city` varchar(100) NOT NULL,
`d_state` varchar(100) NOT NULL,
`d_country` int(11) NOT NULL,
`d_phone` varchar(30) NOT NULL,
`d_mobil_phone` VARCHAR( 20 ) NOT NULL,
`d_fax` varchar(20) NOT NULL,
`d_ext_field_1` VARCHAR(255) NOT NULL,
`d_ext_field_2` VARCHAR(255) NOT NULL,
`d_ext_field_3` VARCHAR(255) NOT NULL,  
`pdf_file` VARCHAR( 50 ) NOT NULL,
`order_hash` varchar(32) NOT NULL,
`file_hash` varchar(64) NOT NULL,
`file_stat_downloads` text  NOT NULL,
`order_custom_info` text NOT NULL,
`display_price` tinyint(1) NOT NULL,
`vendor_type` tinyint(1) NOT NULL,
`vendor_id` int(11) NOT NULL,
`lang` varchar(16) NOT NULL,
`transaction` TEXT NOT NULL,
`product_stock_removed` tinyint(1) NOT NULL,
PRIMARY KEY  (`order_id`)
) /*!40100 DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci*/;

CREATE TABLE IF NOT EXISTS `#__jshopping_attr` (
`attr_id` int(11) NOT NULL auto_increment,
`attr_ordering` int(11) NOT NULL,
`attr_type` TINYINT( 1 ) NOT NULL,
`independent` TINYINT( 1 ) NOT NULL,
`allcats` tinyint(1) NOT NULL default '1',
`cats` text NOT NULL,
`group` tinyint(4) NOT NULL,
PRIMARY KEY (`attr_id`),
KEY `group` (`group`)
) /*!40100 DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci*/;

CREATE TABLE IF NOT EXISTS `#__jshopping_attr_values` (
`value_id` int(11) NOT NULL auto_increment,
`attr_id` int(11) NOT NULL,
`value_ordering` int(11) NOT NULL,
`image` VARCHAR( 255 ) NOT NULL,
PRIMARY KEY (`value_id`)
) /*!40100 DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci*/;

CREATE TABLE IF NOT EXISTS `#__jshopping_attr_groups`(
`id` int(11) NOT NULL AUTO_INCREMENT,
`ordering` int(6) NOT NULL,
PRIMARY KEY (`id`)
) /*!40100 DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci*/;

CREATE TABLE IF NOT EXISTS `#__jshopping_products_attr` (
`product_attr_id` int(11) NOT NULL auto_increment,
`product_id` int(11) NOT NULL,  
`buy_price` DECIMAL(12,2) NOT NULL,
`price` DECIMAL(14,4) NOT NULL,
`old_price` DECIMAL(14,4) NOT NULL,
`count` DECIMAL(14,4) NOT NULL,
`ean` varchar(100) NOT NULL,
`weight` DECIMAL(12,4) NOT NULL,
`weight_volume_units` DECIMAL(14,4) NOT NULL,
`ext_attribute_product_id` int(11) NOT NULL,
PRIMARY KEY  (`product_attr_id`)
) /*!40100 DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci*/;

CREATE TABLE IF NOT EXISTS `#__jshopping_products_attr2`(
`id` int(11) NOT NULL auto_increment,
`product_id` int(11) NOT NULL,
`attr_id` int(11) NOT NULL,
`attr_value_id` int(11) NOT NULL,
`price_mod` char(1) NOT NULL,
`addprice` decimal(14,4) NOT NULL,
PRIMARY KEY  (`id`)
) /*!40100 DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci*/;

CREATE TABLE IF NOT EXISTS `#__jshopping_cart_temp` (
`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`id_cookie` VARCHAR( 255 ) NOT NULL ,
`cart` TEXT NOT NULL,
`type_cart` varchar(32) NOT NULL
) /*!40100 DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci*/;

CREATE TABLE IF NOT EXISTS `#__jshopping_languages` (
`id` int(11) NOT NULL auto_increment,
`language` varchar(32) default NULL,
`name` varchar(255) NOT NULL,
`publish` int(11) NOT NULL,
`ordering` int(11) NOT NULL,
PRIMARY KEY  (`id`)
) /*!40100 DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci*/;

CREATE TABLE IF NOT EXISTS `#__jshopping_import_export` (
`id` int(11) NOT NULL auto_increment,
`name` varchar(255) NOT NULL,
`alias` varchar(255) NOT NULL,
`description` text NOT NULL,
`params` text NOT NULL,
`endstart` int(11) NOT NULL,
`steptime` int(11) NOT NULL,
PRIMARY KEY  (`id`)
) /*!40100 DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci*/;

CREATE TABLE IF NOT EXISTS `#__jshopping_unit` (
`id` int(11) NOT NULL auto_increment,
`qty` int(11) NOT NULL default 1,
`name_de-DE` varchar(255) NOT NULL,
`name_en-GB` varchar(255) NOT NULL,
PRIMARY KEY  (`id`)
) /*!40100 DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci*/;

CREATE TABLE IF NOT EXISTS `#__jshopping_product_labels` (
`id` int(11) NOT NULL auto_increment,  
`name` varchar(255) NOT NULL,
`name_en-GB` VARCHAR(255) NOT NULL,
`name_de-DE` VARCHAR(255) NOT NULL,
`image` varchar(255) NOT NULL,
PRIMARY KEY  (`id`)
) /*!40100 DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci*/;

CREATE TABLE IF NOT EXISTS `#__jshopping_taxes_ext` (
`id` int(11) NOT NULL auto_increment,
`tax_id` int(11) NOT NULL,
`zones` text NOT NULL,
`tax` DECIMAL(12,2) NOT NULL,
`firma_tax` DECIMAL(12,2) NOT NULL,
PRIMARY KEY  (`id`)
) /*!40100 DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci*/;

CREATE TABLE IF NOT EXISTS `#__jshopping_config_display_prices` (
`id` int(11) NOT NULL auto_increment,
`zones` text NOT NULL,
`display_price` tinyint(1) NOT NULL,
`display_price_firma` tinyint(1) NOT NULL,
PRIMARY KEY  (`id`)
) /*!40100 DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci*/;

CREATE TABLE IF NOT EXISTS `#__jshopping_products_extra_fields` (
`id` int(11) NOT NULL auto_increment,
`allcats` tinyint(1) NOT NULL,
`cats` text NOT NULL,
`type` TINYINT(1) NOT NULL,
`multilist` tinyint(1) NOT NULL,
`group` TINYINT(4) NOT NULL,
`ordering` int(6) NOT NULL,
PRIMARY KEY  (`id`),
KEY `group` (`group`)
) /*!40100 DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci*/;

CREATE TABLE IF NOT EXISTS `#__jshopping_products_extra_field_values` (
`id` int(11) NOT NULL auto_increment,
`field_id` int(11) NOT NULL,
`ordering` int(6) NOT NULL,
PRIMARY KEY  (`id`)
) /*!40100 DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci*/;

CREATE TABLE IF NOT EXISTS `#__jshopping_products_extra_field_groups` (
`id` int(11) NOT NULL auto_increment,  
`ordering` int(6) NOT NULL,
PRIMARY KEY  (`id`)
) /*!40100 DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci*/;

CREATE TABLE IF NOT EXISTS `#__jshopping_vendors`(
`id` int(11) NOT NULL auto_increment,
`shop_name` varchar(255) NOT NULL,
`company_name` varchar(255) NOT NULL,
`url` varchar(255) NOT NULL,
`logo` varchar(255) NOT NULL,
`adress` varchar(255) NOT NULL,
`city` varchar(100) NOT NULL,
`zip` varchar(20) NOT NULL,
`state` varchar(100) NOT NULL,
`country` int(11) NOT NULL,
`f_name` varchar(255) NOT NULL,
`l_name` varchar(255) NOT NULL,
`middlename` varchar(255) NOT NULL,
`phone` varchar(20) NOT NULL,
`fax` varchar(20) NOT NULL,
`email` varchar(255) NOT NULL,  
`benef_bank_info` varchar(64) NOT NULL,
`benef_bic` varchar(64) NOT NULL,
`benef_conto` varchar(64) NOT NULL,
`benef_payee` varchar(64) NOT NULL,
`benef_iban` varchar(64) NOT NULL,
`benef_bic_bic` varchar(64) NOT NULL,
`benef_swift` varchar(64) NOT NULL,
`interm_name` varchar(64) NOT NULL,
`interm_swift` varchar(64) NOT NULL,
`identification_number` varchar(64) NOT NULL,
`tax_number` varchar(64) NOT NULL,
`additional_information` text NOT NULL,
`user_id` int(11) NOT NULL,
`main` TINYINT(1) NOT NULL,
`publish` TINYINT(1) NOT NULL,
  PRIMARY KEY  (`id`)
) /*!40100 DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci*/;

CREATE TABLE IF NOT EXISTS `#__jshopping_addons` (
`id` int(11) NOT NULL auto_increment,
`alias` varchar(255) NOT NULL,
`name` varchar(255) NOT NULL,
`key` text NOT NULL,
`usekey` tinyint(1) NOT NULL,
`version` varchar(255) NOT NULL,
`uninstall` varchar(255) NOT NULL,
`params` longtext NOT NULL,
PRIMARY KEY  (`id`)
) /*!40100 DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci*/;

CREATE TABLE IF NOT EXISTS `#__jshopping_free_attr` (
`id` INT NOT NULL AUTO_INCREMENT,
`ordering` INT NOT NULL,
`required` TINYINT(1) NOT NULL,
`type` TINYINT(3) NOT NULL,
PRIMARY KEY ( `id` )
) /*!40100 DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci*/;

CREATE TABLE IF NOT EXISTS `#__jshopping_products_free_attr` (
`id` INT NOT NULL AUTO_INCREMENT,
`product_id` INT NOT NULL,
`attr_id` INT NOT NULL,
PRIMARY KEY (`id`),
KEY `product_id` (`product_id`),
KEY `attr_id` (`attr_id`)
) /*!40100 DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci*/;

CREATE TABLE IF NOT EXISTS `#__jshopping_config_seo` (
`id` INT NOT NULL AUTO_INCREMENT ,
`alias` VARCHAR( 64 ) NOT NULL ,
`ordering` INT NOT NULL,
PRIMARY KEY ( `id` )
) /*!40100 DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci*/;

CREATE TABLE IF NOT EXISTS `#__jshopping_config_statictext` (
`id` INT NOT NULL AUTO_INCREMENT,
`alias` VARCHAR( 64 ) NOT NULL,
`use_for_return_policy` int NOT NULL,
PRIMARY KEY ( `id` )
) /*!40100 DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci*/;

CREATE TABLE IF NOT EXISTS `#__jshopping_products_option` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`product_id` int(11) NOT NULL,
`key` varchar(64) NOT NULL,
`value` text NOT NULL,
PRIMARY KEY (`id`),
UNIQUE KEY `prodkey` (`product_id`,`key`)
) /*!40100 DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci*/;

CREATE TABLE IF NOT EXISTS `#__jshopping_payment_trx` (
`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`order_id` INT NOT NULL,
`transaction` VARCHAR( 255 ) NOT NULL,
`rescode` INT NOT NULL,
`status_id` INT NOT NULL,
`date` datetime NOT NULL
) /*!40100 DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci*/;

CREATE TABLE IF NOT EXISTS `#__jshopping_payment_trx_data` (
`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`trx_id` INT NOT NULL ,
`order_id` INT NOT NULL ,
`key` VARCHAR( 255 ) NOT NULL ,
`value` TEXT NOT NULL
) /*!40100 DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci*/;

ALTER TABLE `#__jshopping_addons` ADD INDEX(`alias`);
ALTER TABLE `#__jshopping_addons` ADD INDEX(`name`);

ALTER TABLE `#__jshopping_attr` ADD INDEX(`attr_ordering`);
ALTER TABLE `#__jshopping_attr` ADD INDEX(`attr_type`);
ALTER TABLE `#__jshopping_attr` ADD INDEX(`independent`);
ALTER TABLE `#__jshopping_attr` ADD INDEX(`allcats`);

ALTER TABLE `#__jshopping_attr_values` ADD INDEX(`attr_id`);
ALTER TABLE `#__jshopping_attr_values` ADD INDEX(`value_ordering`);

ALTER TABLE `#__jshopping_cart_temp` ADD INDEX(`id_cookie`);
ALTER TABLE `#__jshopping_cart_temp` ADD INDEX(`type_cart`);

ALTER TABLE `#__jshopping_categories` ADD INDEX(`category_parent_id`);
ALTER TABLE `#__jshopping_categories` ADD INDEX(`category_publish`);
ALTER TABLE `#__jshopping_categories` ADD INDEX(`category_ordertype`);
ALTER TABLE `#__jshopping_categories` ADD INDEX(`category_template`);
ALTER TABLE `#__jshopping_categories` ADD INDEX(`ordering`);
ALTER TABLE `#__jshopping_categories` ADD INDEX(`category_add_date`);
ALTER TABLE `#__jshopping_categories` ADD INDEX(`products_page`);
ALTER TABLE `#__jshopping_categories` ADD INDEX(`products_row`);
ALTER TABLE `#__jshopping_categories` ADD INDEX(`access`);
ALTER TABLE `#__jshopping_categories` ADD INDEX(`category_publish`, `access`);

ALTER TABLE `#__jshopping_config_display_prices` ADD INDEX(`display_price`);
ALTER TABLE `#__jshopping_config_display_prices` ADD INDEX(`display_price_firma`);

ALTER TABLE `#__jshopping_config_seo` ADD INDEX(`alias`);
ALTER TABLE `#__jshopping_config_seo` ADD INDEX(`ordering`);

ALTER TABLE `#__jshopping_config_statictext` ADD INDEX(`alias`);
ALTER TABLE `#__jshopping_config_statictext` ADD INDEX(`use_for_return_policy`);

ALTER TABLE `#__jshopping_countries` ADD INDEX(`country_publish`);
ALTER TABLE `#__jshopping_countries` ADD INDEX(`ordering`);
ALTER TABLE `#__jshopping_countries` ADD INDEX(`country_code`);
ALTER TABLE `#__jshopping_countries` ADD INDEX(`country_code_2`);

ALTER TABLE `#__jshopping_coupons` ADD INDEX(`coupon_type`);
ALTER TABLE `#__jshopping_coupons` ADD INDEX(`coupon_code`);
ALTER TABLE `#__jshopping_coupons` ADD INDEX(`tax_id`);
ALTER TABLE `#__jshopping_coupons` ADD INDEX(`used`);
ALTER TABLE `#__jshopping_coupons` ADD INDEX(`for_user_id`);
ALTER TABLE `#__jshopping_coupons` ADD INDEX(`coupon_publish`);
ALTER TABLE `#__jshopping_coupons` ADD INDEX(`coupon_start_date`);
ALTER TABLE `#__jshopping_coupons` ADD INDEX(`coupon_expire_date`);
ALTER TABLE `#__jshopping_coupons` ADD INDEX(`finished_after_used`);

ALTER TABLE `#__jshopping_currencies` ADD INDEX(`currency_code_iso`);
ALTER TABLE `#__jshopping_currencies` ADD INDEX(`currency_code_num`);
ALTER TABLE `#__jshopping_currencies` ADD INDEX(`currency_ordering`);
ALTER TABLE `#__jshopping_currencies` ADD INDEX(`currency_publish`);

ALTER TABLE `#__jshopping_languages` ADD INDEX(`publish`);
ALTER TABLE `#__jshopping_languages` ADD INDEX(`ordering`);

ALTER TABLE `#__jshopping_manufacturers` ADD INDEX(`manufacturer_publish`);
ALTER TABLE `#__jshopping_manufacturers` ADD INDEX(`products_page`);
ALTER TABLE `#__jshopping_manufacturers` ADD INDEX(`products_row`);
ALTER TABLE `#__jshopping_manufacturers` ADD INDEX(`ordering`);

ALTER TABLE `#__jshopping_order_history` ADD INDEX(`order_id`);
ALTER TABLE `#__jshopping_order_history` ADD INDEX(`order_status_id`);
ALTER TABLE `#__jshopping_order_history` ADD INDEX(`status_date_added`);
ALTER TABLE `#__jshopping_order_history` ADD INDEX(`customer_notify`);

ALTER TABLE `#__jshopping_order_item` ADD INDEX(`order_id`);
ALTER TABLE `#__jshopping_order_item` ADD INDEX(`product_id`);
ALTER TABLE `#__jshopping_order_item` ADD INDEX(`delivery_times_id`);
ALTER TABLE `#__jshopping_order_item` ADD INDEX(`vendor_id`);

ALTER TABLE `#__jshopping_order_status` ADD INDEX(`status_code`);

ALTER TABLE `#__jshopping_orders` ADD INDEX(`order_number`);
ALTER TABLE `#__jshopping_orders` ADD INDEX(`user_id`);
ALTER TABLE `#__jshopping_orders` ADD INDEX(`currency_code_iso`);
ALTER TABLE `#__jshopping_orders` ADD INDEX(`order_status`);
ALTER TABLE `#__jshopping_orders` ADD INDEX(`order_created`);
ALTER TABLE `#__jshopping_orders` ADD INDEX(`shipping_method_id`);
ALTER TABLE `#__jshopping_orders` ADD INDEX(`delivery_times_id`);
ALTER TABLE `#__jshopping_orders` ADD INDEX(`payment_method_id`);
ALTER TABLE `#__jshopping_orders` ADD INDEX(`coupon_id`);
ALTER TABLE `#__jshopping_orders` ADD INDEX(`client_type`);
ALTER TABLE `#__jshopping_orders` ADD INDEX(`country`);
ALTER TABLE `#__jshopping_orders` ADD INDEX(`phone`);
ALTER TABLE `#__jshopping_orders` ADD INDEX(`d_title`);
ALTER TABLE `#__jshopping_orders` ADD INDEX(`d_country`);
ALTER TABLE `#__jshopping_orders` ADD INDEX(`display_price`);
ALTER TABLE `#__jshopping_orders` ADD INDEX(`vendor_type`);
ALTER TABLE `#__jshopping_orders` ADD INDEX(`vendor_id`);
ALTER TABLE `#__jshopping_orders` ADD INDEX(`lang`);

ALTER TABLE `#__jshopping_payment_method` ADD INDEX(`payment_code`);
ALTER TABLE `#__jshopping_payment_method` ADD INDEX(`payment_publish`);
ALTER TABLE `#__jshopping_payment_method` ADD INDEX(`payment_ordering`);
ALTER TABLE `#__jshopping_payment_method` ADD INDEX(`payment_type`);
ALTER TABLE `#__jshopping_payment_method` ADD INDEX(`price_type`);
ALTER TABLE `#__jshopping_payment_method` ADD INDEX(`tax_id`);

ALTER TABLE `#__jshopping_payment_trx` ADD INDEX(`order_id`);
ALTER TABLE `#__jshopping_payment_trx` ADD INDEX(`transaction`);
ALTER TABLE `#__jshopping_payment_trx` ADD INDEX(`rescode`);
ALTER TABLE `#__jshopping_payment_trx` ADD INDEX(`status_id`);

ALTER TABLE `#__jshopping_payment_trx_data` ADD INDEX(`trx_id`);
ALTER TABLE `#__jshopping_payment_trx_data` ADD INDEX(`order_id`);
ALTER TABLE `#__jshopping_payment_trx_data` ADD INDEX(`key`);

ALTER TABLE `#__jshopping_products` ADD INDEX(`parent_id`);
ALTER TABLE `#__jshopping_products` ADD INDEX(`product_ean`);
ALTER TABLE `#__jshopping_products` ADD INDEX(`unlimited`);
ALTER TABLE `#__jshopping_products` ADD INDEX(`product_publish`);
ALTER TABLE `#__jshopping_products` ADD INDEX(`product_tax_id`);
ALTER TABLE `#__jshopping_products` ADD INDEX(`currency_id`);
ALTER TABLE `#__jshopping_products` ADD INDEX(`product_price`);
ALTER TABLE `#__jshopping_products` ADD INDEX(`min_price`);
ALTER TABLE `#__jshopping_products` ADD INDEX(`add_price_unit_id`);
ALTER TABLE `#__jshopping_products` ADD INDEX(`average_rating`);
ALTER TABLE `#__jshopping_products` ADD INDEX(`reviews_count`);
ALTER TABLE `#__jshopping_products` ADD INDEX(`delivery_times_id`);
ALTER TABLE `#__jshopping_products` ADD INDEX(`hits`);
ALTER TABLE `#__jshopping_products` ADD INDEX(`basic_price_unit_id`);
ALTER TABLE `#__jshopping_products` ADD INDEX(`label_id`);
ALTER TABLE `#__jshopping_products` ADD INDEX(`vendor_id`);
ALTER TABLE `#__jshopping_products` ADD INDEX(`access`);

ALTER TABLE `#__jshopping_products_attr` ADD INDEX(`product_id`);
ALTER TABLE `#__jshopping_products_attr` ADD INDEX(`ext_attribute_product_id`);

ALTER TABLE `#__jshopping_products_attr2` ADD INDEX(`product_id`);
ALTER TABLE `#__jshopping_products_attr2` ADD INDEX(`attr_id`);
ALTER TABLE `#__jshopping_products_attr2` ADD INDEX(`attr_value_id`);
ALTER TABLE `#__jshopping_products_attr2` ADD INDEX(`price_mod`);

ALTER TABLE `#__jshopping_products_extra_field_groups` ADD INDEX(`ordering`);

ALTER TABLE `#__jshopping_products_extra_field_values` ADD INDEX(`field_id`);
ALTER TABLE `#__jshopping_products_extra_field_values` ADD INDEX(`ordering`);

ALTER TABLE `#__jshopping_products_extra_fields` ADD INDEX(`allcats`);
ALTER TABLE `#__jshopping_products_extra_fields` ADD INDEX(`type`);
ALTER TABLE `#__jshopping_products_extra_fields` ADD INDEX(`multilist`);
ALTER TABLE `#__jshopping_products_extra_fields` ADD INDEX(`group`);
ALTER TABLE `#__jshopping_products_extra_fields` ADD INDEX(`ordering`);

ALTER TABLE `#__jshopping_products_files` ADD INDEX(`ordering`);

ALTER TABLE `#__jshopping_products_images` ADD INDEX(`product_id`);
ALTER TABLE `#__jshopping_products_images` ADD INDEX(`ordering`);

ALTER TABLE `#__jshopping_products_option` ADD INDEX(`product_id`);

ALTER TABLE `#__jshopping_products_prices` ADD INDEX(`product_id`);
ALTER TABLE `#__jshopping_products_prices` ADD INDEX(`product_quantity_start`);
ALTER TABLE `#__jshopping_products_prices` ADD INDEX(`product_quantity_finish`);

ALTER TABLE `#__jshopping_products_relations` ADD INDEX(`product_id`, `product_related_id`);
ALTER TABLE `#__jshopping_products_relations` ADD INDEX(`product_id`);
ALTER TABLE `#__jshopping_products_relations` ADD INDEX(`product_related_id`);

ALTER TABLE `#__jshopping_products_reviews` ADD INDEX(`product_id`);
ALTER TABLE `#__jshopping_products_reviews` ADD INDEX(`user_id`);
ALTER TABLE `#__jshopping_products_reviews` ADD INDEX(`user_email`);
ALTER TABLE `#__jshopping_products_reviews` ADD INDEX(`mark`);
ALTER TABLE `#__jshopping_products_reviews` ADD INDEX(`publish`);
ALTER TABLE `#__jshopping_products_reviews` ADD INDEX(`ip`);

ALTER TABLE `#__jshopping_products_to_categories` ADD INDEX(`product_id`, `category_id`, `product_ordering`);
ALTER TABLE `#__jshopping_products_to_categories` ADD INDEX(`product_ordering`);

ALTER TABLE `#__jshopping_products_videos` ADD INDEX(`video_id`, `product_id`);
ALTER TABLE `#__jshopping_products_videos` ADD INDEX(`product_id`);

ALTER TABLE `#__jshopping_shipping_ext_calc` ADD INDEX(`alias`);
ALTER TABLE `#__jshopping_shipping_ext_calc` ADD INDEX(`published`);
ALTER TABLE `#__jshopping_shipping_ext_calc` ADD INDEX(`ordering`);

ALTER TABLE `#__jshopping_shipping_method` ADD INDEX(`alias`);
ALTER TABLE `#__jshopping_shipping_method` ADD INDEX(`published`);
ALTER TABLE `#__jshopping_shipping_method` ADD INDEX(`ordering`);

ALTER TABLE `#__jshopping_shipping_method_price` ADD INDEX(`shipping_method_id`);
ALTER TABLE `#__jshopping_shipping_method_price` ADD INDEX(`shipping_tax_id`);
ALTER TABLE `#__jshopping_shipping_method_price` ADD INDEX(`package_tax_id`);
ALTER TABLE `#__jshopping_shipping_method_price` ADD INDEX(`delivery_times_id`);

ALTER TABLE `#__jshopping_shipping_method_price_countries` ADD INDEX(`sh_method_country_id`, `country_id`, `sh_pr_method_id`);
ALTER TABLE `#__jshopping_shipping_method_price_countries` ADD INDEX(`country_id`, `sh_pr_method_id`);
ALTER TABLE `#__jshopping_shipping_method_price_countries` ADD INDEX(`sh_method_country_id`, `country_id`);

ALTER TABLE `#__jshopping_shipping_method_price_weight` ADD INDEX( `sh_pr_weight_id`, `sh_pr_method_id`);

ALTER TABLE `#__jshopping_taxes_ext` ADD INDEX(`tax_id`);

ALTER TABLE `#__jshopping_unit` ADD INDEX(`qty`);

ALTER TABLE `#__jshopping_usergroups` ADD INDEX(`usergroup_name`);
ALTER TABLE `#__jshopping_usergroups` ADD INDEX(`usergroup_is_default`);

ALTER TABLE `#__jshopping_users` ADD INDEX(`usergroup_id`);
ALTER TABLE `#__jshopping_users` ADD INDEX(`payment_id`);
ALTER TABLE `#__jshopping_users` ADD INDEX(`shipping_id`);
ALTER TABLE `#__jshopping_users` ADD INDEX(`client_type`);
ALTER TABLE `#__jshopping_users` ADD INDEX(`email`);

ALTER TABLE `#__jshopping_vendors` ADD INDEX(`country`);
ALTER TABLE `#__jshopping_vendors` ADD INDEX(`user_id`);
ALTER TABLE `#__jshopping_vendors` ADD INDEX(`email`);
ALTER TABLE `#__jshopping_vendors` ADD INDEX(`main`);
ALTER TABLE `#__jshopping_vendors` ADD INDEX(`publish`);

INSERT INTO `#__jshopping_countries` (`country_id`, `country_publish`, `ordering`, `country_code`, `country_code_2`, `name_en-GB`, `name_de-DE`) VALUES
(1, 1, 1, 'AFG', 'AF', 'Afghanistan', 'Afghanistan'),
(2, 1, 2, 'ALB', 'AL', 'Albania', 'Albanien'),
(3, 1, 3, 'DZA', 'DZ', 'Algeria', 'Algerien'),
(4, 1, 4, 'ASM', 'AS', 'American Samoa', 'Amerikanisch-Samoa'),
(5, 1, 5, 'AND', 'AD', 'Andorra', 'Andorra'),
(6, 1, 6, 'AGO', 'AO', 'Angola', 'Angola'),
(7, 1, 7, 'AIA', 'AI', 'Anguilla', 'Anguilla'),
(8, 1, 8, 'ATA', 'AQ', 'Antarctica', 'Antarktis'),
(9, 1, 9, 'ATG', 'AG', 'Antigua and Barbuda', 'Antigua und Barbuda'),
(10, 1, 10, 'ARG', 'AR', 'Argentina', 'Argentinien'),
(11, 1, 11, 'ARM', 'AM', 'Armenia', 'Armenien'),
(12, 1, 12, 'ABW', 'AW', 'Aruba', 'Aruba'),
(13, 1, 13, 'AUS', 'AU', 'Australia', 'Australien'),
(14, 1, 14, 'AUT', 'AT', 'Austria', 'Österreich'),
(15, 1, 15, 'AZE', 'AZ', 'Azerbaijan', 'Aserbaidschan'),
(16, 1, 16, 'BHS', 'BS', 'Bahamas', 'Bahamas'),
(17, 1, 17, 'BHR', 'BH', 'Bahrain', 'Bahrain'),
(18, 1, 18, 'BGD', 'BD', 'Bangladesh', 'Bangladesch'),
(19, 1, 19, 'BRB', 'BB', 'Barbados', 'Barbados'),
(20, 1, 20, 'BLR', 'BY', 'Belarus', 'Weissrussland'),
(21, 1, 21, 'BEL', 'BE', 'Belgium', 'Belgien'),
(22, 1, 22, 'BLZ', 'BZ', 'Belize', 'Belize'),
(23, 1, 23, 'BEN', 'BJ', 'Benin', 'Benin'),
(24, 1, 24, 'BMU', 'BM', 'Bermuda', 'Bermuda'),
(25, 1, 25, 'BTN', 'BT', 'Bhutan', 'Bhutan'),
(26, 1, 26, 'BOL', 'BO', 'Bolivia', 'Bolivien'),
(27, 1, 27, 'BIH', 'BA', 'Bosnia and Herzegowina', 'Bosnien und Herzegowina'),
(28, 1, 28, 'BWA', 'BW', 'Botswana', 'Botsuana'),
(29, 1, 29, 'BVT', 'BV', 'Bouvet Island', 'Bouvetinsel'),
(30, 1, 30, 'BRA', 'BR', 'Brazil', 'Brasilien'),
(31, 1, 31, 'IOT', 'IO', 'British Indian Ocean Territory', 'Britisches Territorium im Indischen Ozean'),
(32, 1, 32, 'BRN', 'BN', 'Brunei Darussalam', 'Brunei'),
(33, 1, 33, 'BGR', 'BG', 'Bulgaria', 'Bulgarien'),
(34, 1, 34, 'BFA', 'BF', 'Burkina Faso', 'Burkina Faso'),
(35, 1, 35, 'BDI', 'BI', 'Burundi', 'Burundi'),
(36, 1, 36, 'KHM', 'KH', 'Cambodia', 'Kambodscha'),
(37, 1, 37, 'CMR', 'CM', 'Cameroon', 'Kamerun'),
(38, 1, 38, 'CAN', 'CA', 'Canada', 'Kanada'),
(39, 1, 39, 'CPV', 'CV', 'Cape Verde', 'Kap Verde'),
(40, 1, 40, 'CYM', 'KY', 'Cayman Islands', 'Cayman-Inseln'),
(41, 1, 41, 'CAF', 'CF', 'Central African Republic', 'Zentralafrikanische Republik'),
(42, 1, 42, 'TCD', 'TD', 'Chad', 'Tschad'),
(43, 1, 43, 'CHL', 'CL', 'Chile', 'Chile'),
(44, 1, 44, 'CHN', 'CN', 'China', 'China'),
(45, 1, 45, 'CXR', 'CX', 'Christmas Island', 'Christmas Island'),
(46, 1, 46, 'CCK', 'CC', 'Cocos (Keeling) Islands', 'Kokosinseln (Keeling)'),
(47, 1, 47, 'COL', 'CO', 'Colombia', 'Kolumbien'),
(48, 1, 48, 'COM', 'KM', 'Comoros', 'Komoren'),
(49, 1, 49, 'COG', 'CG', 'Congo', 'Kongo, Republik'),
(50, 1, 50, 'COK', 'CK', 'Cook Islands', 'Cookinseln'),
(51, 1, 51, 'CRI', 'CR', 'Costa Rica', 'Costa Rica'),
(52, 1, 52, 'CIV', 'CI', 'Cote D''Ivoire', 'Elfenbeinküste'),
(53, 1, 53, 'HRV', 'HR', 'Croatia', 'Kroatien'),
(54, 1, 54, 'CUB', 'CU', 'Cuba', 'Kuba'),
(55, 1, 55, 'CYP', 'CY', 'Cyprus', 'Zypern'),
(56, 1, 56, 'CZE', 'CZ', 'Czech Republic', 'Tschechien'),
(57, 1, 57, 'DNK', 'DK', 'Denmark', 'Dänemark'),
(58, 1, 58, 'DJI', 'DJ', 'Djibouti', 'Dschibuti'),
(59, 1, 59, 'DMA', 'DM', 'Dominica', 'Dominica'),
(60, 1, 60, 'DOM', 'DO', 'Dominican Republic', 'Dominikanische Republik'),
(61, 1, 61, 'TMP', 'TL', 'East Timor', 'Osttimor'),
(62, 1, 62, 'ECU', 'EC', 'Ecuador', 'Ecuador'),
(63, 1, 63, 'EGY', 'EG', 'Egypt', 'Ägypten'),
(64, 1, 64, 'SLV', 'SV', 'El Salvador', 'El Salvador'),
(65, 1, 65, 'GNQ', 'GQ', 'Equatorial Guinea', 'Äquatorial-Guinea'),
(66, 1, 66, 'ERI', 'ER', 'Eritrea', 'Eritrea'),
(67, 1, 67, 'EST', 'EE', 'Estonia', 'Estland'),
(68, 1, 68, 'ETH', 'ET', 'Ethiopia', 'Äthiopien'),
(69, 1, 69, 'FLK', 'FK', 'Falkland Islands (Malvinas)', 'Falklandinseln'),
(70, 1, 70, 'FRO', 'FO', 'Faroe Islands', 'Färöer'),
(71, 1, 71, 'FJI', 'FJ', 'Fiji', 'Fidschi'),
(72, 1, 72, 'FIN', 'FI', 'Finland', 'Finnland'),
(73, 1, 73, 'FRA', 'FR', 'France', 'Frankreich'),
(74, 1, 74, 'FXX', 'FX', 'France Metropolitan', 'Frankreich, Metropolitan'),
(75, 1, 75, 'GUF', 'GF', 'French Guiana', 'Französisch-Guyana'),
(76, 1, 76, 'PYF', 'PF', 'French Polynesia', 'Franz. Polynesien'),
(77, 1, 77, 'ATF', 'TF', 'French Southern Territories', 'Französiche Süd- und Antarktisgebiete'),
(78, 1, 78, 'GAB', 'GA', 'Gabon', 'Gabun'),
(79, 1, 79, 'GMB', 'GM', 'Gambia', 'Gambia'),
(80, 1, 80, 'GEO', 'GE', 'Georgia', 'Georgien'),
(81, 1, 81, 'DEU', 'DE', 'Germany', 'Deutschland'),
(82, 1, 82, 'GHA', 'GH', 'Ghana', 'Ghana'),
(83, 1, 83, 'GIB', 'GI', 'Gibraltar', 'Gibraltar'),
(84, 1, 84, 'GRC', 'GR', 'Greece', 'Griechenland'),
(85, 1, 85, 'GRL', 'GL', 'Greenland', 'Grönland'),
(86, 1, 86, 'GRD', 'GD', 'Grenada', 'Grenada'),
(87, 1, 87, 'GLP', 'GP', 'Guadeloupe', 'Guadeloupe'),
(88, 1, 88, 'GUM', 'GU', 'Guam', 'Guam'),
(89, 1, 89, 'GTM', 'GT', 'Guatemala', 'Guatemala'),
(90, 1, 90, 'GIN', 'GN', 'Guinea', 'Guinea'),
(91, 1, 91, 'GNB', 'GW', 'Guinea-bissau', 'Guinea-Bissau'),
(92, 1, 92, 'GUY', 'GY', 'Guyana', 'Guyana'),
(93, 1, 93, 'HTI', 'HT', 'Haiti', 'Haiti'),
(94, 1, 94, 'HMD', 'HM', 'Heard and Mc Donald Islands', 'Heard und McDonaldinseln'),
(95, 1, 95, 'HND', 'HN', 'Honduras', 'Honduras'),
(96, 1, 96, 'HKG', 'HK', 'Hong Kong', 'Hong Kong'),
(97, 1, 97, 'HUN', 'HU', 'Hungary', 'Ungarn'),
(98, 1, 98, 'ISL', 'IS', 'Iceland', 'Island'),
(99, 1, 99, 'IND', 'IN', 'India', 'Indien'),
(100, 1, 100, 'IDN', 'ID', 'Indonesia', 'Indonesien'),
(101, 1, 101, 'IRN', 'IR', 'Iran (Islamic Republic of)', 'Iran'),
(102, 1, 102, 'IRQ', 'IQ', 'Iraq', 'Irak'),
(103, 1, 103, 'IRL', 'IE', 'Ireland', 'Irland'),
(104, 1, 104, 'ISR', 'IL', 'Israel', 'Israel'),
(105, 1, 105, 'ITA', 'IT', 'Italy', 'Italien'),
(106, 1, 106, 'JAM', 'JM', 'Jamaica', 'Jamaika'),
(107, 1, 107, 'JPN', 'JP', 'Japan', 'Japan'),
(108, 1, 108, 'JOR', 'JO', 'Jordan', 'Jordanien'),
(109, 1, 109, 'KAZ', 'KZ', 'Kazakhstan', 'Kasachstan'),
(110, 1, 110, 'KEN', 'KE', 'Kenya', 'Kenia'),
(111, 1, 111, 'KIR', 'KI', 'Kiribati', 'Kiribati'),
(112, 1, 112, 'PRK', 'KP', 'Korea Democratic People''s Republic of', 'Korea Demokratische Volksrepublik'),
(113, 1, 113, 'KOR', 'KR', 'Korea Republic of', 'Korea'),
(114, 1, 114, 'KWT', 'KW', 'Kuwait', 'Kuwait'),
(115, 1, 115, 'KGZ', 'KG', 'Kyrgyzstan', 'Kirgistan'),
(116, 1, 116, 'LAO', 'LA', 'Lao People''s Democratic Republic', 'Laos'),
(117, 1, 117, 'LVA', 'LV', 'Latvia', 'Lettland'),
(118, 1, 118, 'LBN', 'LB', 'Lebanon', 'Libanon'),
(119, 1, 119, 'LSO', 'LS', 'Lesotho', 'Lesotho'),
(120, 1, 120, 'LBR', 'LR', 'Liberia', 'Liberia'),
(121, 1, 121, 'LBY', 'LY', 'Libyan Arab Jamahiriya', 'Libyen'),
(122, 1, 122, 'LIE', 'LI', 'Liechtenstein', 'Liechtenstein'),
(123, 1, 123, 'LTU', 'LT', 'Lithuania', 'Litauen'),
(124, 1, 124, 'LUX', 'LU', 'Luxembourg', 'Luxemburg'),
(125, 1, 125, 'MAC', 'MO', 'Macau', 'Makao'),
(126, 1, 126, 'MKD', 'MK', 'Macedonia The Former Yugoslav Republic of', 'Mazedonien'),
(127, 1, 127, 'MDG', 'MG', 'Madagascar', 'Madagaskar'),
(128, 1, 128, 'MWI', 'MW', 'Malawi', 'Malawi'),
(129, 1, 129, 'MYS', 'MY', 'Malaysia', 'Malaysia'),
(130, 1, 130, 'MDV', 'MV', 'Maldives', 'Malediven'),
(131, 1, 131, 'MLI', 'ML', 'Mali', 'Mali'),
(132, 1, 132, 'MLT', 'MT', 'Malta', 'Malta'),
(133, 1, 133, 'MHL', 'MH', 'Marshall Islands', 'Marshallinseln'),
(134, 1, 134, 'MTQ', 'MQ', 'Martinique', 'Martinique'),
(135, 1, 135, 'MRT', 'MR', 'Mauritania', 'Mauretanien'),
(136, 1, 136, 'MUS', 'MU', 'Mauritius', 'Mauritius'),
(137, 1, 137, 'MYT', 'YT', 'Mayotte', 'Mayott'),
(138, 1, 138, 'MEX', 'MX', 'Mexico', 'Mexiko'),
(139, 1, 139, 'FSM', 'FM', 'Micronesia Federated States of', 'Mikronesien'),
(140, 1, 140, 'MDA', 'MD', 'Moldova Republic of', 'Moldawien'),
(141, 1, 141, 'MCO', 'MC', 'Monaco', 'Monaco'),
(142, 1, 142, 'MNG', 'MN', 'Mongolia', 'Mongolei'),
(143, 1, 143, 'MSR', 'MS', 'Montserrat', 'Montserrat'),
(144, 1, 144, 'MAR', 'MA', 'Morocco', 'Marokko'),
(145, 1, 145, 'MOZ', 'MZ', 'Mozambique', 'Mosambik'),
(146, 1, 146, 'MMR', 'MM', 'Myanmar', 'Myanmar'),
(147, 1, 147, 'NAM', 'NA', 'Namibia', 'Namibia'),
(148, 1, 148, 'NRU', 'NR', 'Nauru', 'Nauru'),
(149, 1, 149, 'NPL', 'NP', 'Nepal', 'Nepal'),
(150, 1, 150, 'NLD', 'NL', 'Netherlands', 'Niederlande'),
(151, 1, 151, 'ANT', 'AN', 'Netherlands Antilles', 'Niederländisch-Antillen'),
(152, 1, 152, 'NCL', 'NC', 'New Caledonia', 'Neukaledonien'),
(153, 1, 153, 'NZL', 'NZ', 'New Zealand', 'Neuseeland'),
(154, 1, 154, 'NIC', 'NI', 'Nicaragua', 'Nicaragua'),
(155, 1, 155, 'NER', 'NE', 'Niger', 'Niger'),
(156, 1, 156, 'NGA', 'NG', 'Nigeria', 'Nigeria'),
(157, 1, 157, 'NIU', 'NU', 'Niue', 'Niue'),
(158, 1, 158, 'NFK', 'NF', 'Norfolk Island', 'Norfolkinsel'),
(159, 1, 159, 'MNP', 'MP', 'Northern Mariana Islands', 'Nördliche Marianen'),
(160, 1, 160, 'NOR', 'NO', 'Norway', 'Norwegen'),
(161, 1, 161, 'OMN', 'OM', 'Oman', 'Oman'),
(162, 1, 162, 'PAK', 'PK', 'Pakistan', 'Pakistan'),
(163, 1, 163, 'PLW', 'PW', 'Palau', 'Palau'),
(164, 1, 164, 'PAN', 'PA', 'Panama', 'Panama'),
(165, 1, 165, 'PNG', 'PG', 'Papua New Guinea', 'Papua-Neuguinea'),
(166, 1, 166, 'PRY', 'PY', 'Paraguay', 'Paraguay'),
(167, 1, 167, 'PER', 'PE', 'Peru', 'Peru'),
(168, 1, 168, 'PHL', 'PH', 'Philippines', 'Philippinen'),
(169, 1, 169, 'PCN', 'PN', 'Pitcairn', 'Pitcairn'),
(170, 1, 170, 'POL', 'PL', 'Poland', 'Polen'),
(171, 1, 171, 'PRT', 'PT', 'Portugal', 'Portugal'),
(172, 1, 172, 'PRI', 'PR', 'Puerto Rico', 'Puerto Rico'),
(173, 1, 173, 'QAT', 'QA', 'Qatar', 'Katar'),
(174, 1, 174, 'REU', 'RE', 'Reunion', 'Reunion'),
(175, 1, 175, 'ROM', 'RO', 'Romania', 'Rumänien'),
(176, 1, 176, 'RUS', 'RU', 'Russian Federation', 'Russische Föderation'),
(177, 1, 177, 'RWA', 'RW', 'Rwanda', 'Ruanda'),
(178, 1, 178, 'KNA', 'KN', 'Saint Kitts and Nevis', 'St. Kitts und Nevis'),
(179, 1, 179, 'LCA', 'LC', 'Saint Lucia', 'St. Lucia'),
(180, 1, 180, 'VCT', 'VC', 'Saint Vincent and the Grenadines', 'St. Vincent und die Grenadinen'),
(181, 1, 181, 'WSM', 'WS', 'Samoa', 'Samoa'),
(182, 1, 182, 'SMR', 'SM', 'San Marino', 'San Marino'),
(183, 1, 183, 'STP', 'ST', 'Sao Tome and Principe', 'Sao Tomé und Príncipe'),
(184, 1, 184, 'SAU', 'SA', 'Saudi Arabia', 'Saudi-Arabien'),
(185, 1, 185, 'SEN', 'SN', 'Senegal', 'Senegal'),
(186, 1, 186, 'SYC', 'SC', 'Seychelles', 'Seychellen'),
(187, 1, 187, 'SLE', 'SL', 'Sierra Leone', 'Sierra Leone'),
(188, 1, 188, 'SGP', 'SG', 'Singapore', 'Singapur'),
(189, 1, 189, 'SVK', 'SK', 'Slovakia (Slovak Republic)', 'Slowakei'),
(190, 1, 190, 'SVN', 'SI', 'Slovenia', 'Slowenien'),
(191, 1, 191, 'SLB', 'SB', 'Solomon Islands', 'Salomonen'),
(192, 1, 192, 'SOM', 'SO', 'Somalia', 'Somalia'),
(193, 1, 193, 'ZAF', 'ZA', 'South Africa', 'Republik Südafrika'),
(194, 1, 194, 'SGS', 'GS', 'South Georgia and the South Sandwich Islands', 'Südgeorgien und die Südlichen Sandwichinseln'),
(195, 1, 195, 'ESP', 'ES', 'Spain', 'Spanien'),
(196, 1, 196, 'LKA', 'LK', 'Sri Lanka', 'Sri Lanka'),
(197, 1, 197, 'SHN', 'SH', 'St. Helena', 'St. Helena'),
(198, 1, 198, 'SPM', 'PM', 'St. Pierre and Miquelon', 'St. Pierre und Miquelon'),
(199, 1, 199, 'SDN', 'SD', 'Sudan', 'Sudan'),
(200, 1, 200, 'SUR', 'SR', 'Suriname', 'Suriname'),
(201, 1, 201, 'SJM', 'SJ', 'Svalbard and Jan Mayen Islands', 'Svalbard und Jan Mayen'),
(202, 1, 202, 'SWZ', 'SZ', 'Swaziland', 'Swasiland'),
(203, 1, 203, 'SWE', 'SE', 'Sweden', 'Schweden'),
(204, 1, 204, 'CHE', 'CH', 'Switzerland', 'Schweiz'),
(205, 1, 205, 'SYR', 'SY', 'Syrian Arab Republic', 'Syrien'),
(206, 1, 206, 'TWN', 'TW', 'Taiwan', 'Taiwan'),
(207, 1, 207, 'TJK', 'TJ', 'Tajikistan', 'Tadschikistan'),
(208, 1, 208, 'TZA', 'TZ', 'Tanzania United Republic of', 'Tansania'),
(209, 1, 209, 'THA', 'TH', 'Thailand', 'Thailand'),
(210, 1, 210, 'TGO', 'TG', 'Togo', 'Togo'),
(211, 1, 211, 'TKL', 'TK', 'Tokelau', 'Tokelau'),
(212, 1, 212, 'TON', 'TO', 'Tonga', 'Tonga'),
(213, 1, 213, 'TTO', 'TT', 'Trinidad and Tobago', 'Trinidad und Tobago'),
(214, 1, 214, 'TUN', 'TN', 'Tunisia', 'Tunesien'),
(215, 1, 215, 'TUR', 'TR', 'Turkey', 'Türkei'),
(216, 1, 216, 'TKM', 'TM', 'Turkmenistan', 'Turkmenistan'),
(217, 1, 217, 'TCA', 'TC', 'Turks and Caicos Islands', 'Turks- und Caicosinseln'),
(218, 1, 218, 'TUV', 'TV', 'Tuvalu', 'Tuvalu'),
(219, 1, 219, 'UGA', 'UG', 'Uganda', 'Uganda'),
(220, 1, 220, 'UKR', 'UA', 'Ukraine', 'Ukraine'),
(221, 1, 221, 'ARE', 'AE', 'United Arab Emirates', 'Vereinigte Arabische Emirate'),
(222, 1, 222, 'GBR', 'GB', 'United Kingdom', 'Vereinigtes Königreich'),
(223, 1, 223, 'USA', 'US', 'United States', 'USA'),
(224, 1, 224, 'UMI', 'UM', 'United States Minor Outlying Islands', 'United States Minor Outlying Islands'),
(225, 1, 225, 'URY', 'UY', 'Uruguay', 'Uruguay'),
(226, 1, 226, 'UZB', 'UZ', 'Uzbekistan', 'Usbekistan'),
(227, 1, 227, 'VUT', 'VU', 'Vanuatu', 'Vanuatu'),
(228, 1, 228, 'VAT', 'VA', 'Vatican City State (Holy See)', 'Vatikanstadt'),
(229, 1, 229, 'VEN', 'VE', 'Venezuela', 'Venezuela'),
(230, 1, 230, 'VNM', 'VN', 'Viet Nam', 'Vietnam'),
(231, 1, 231, 'VGB', 'VG', 'Virgin Islands (British)', 'Britische Jungferninseln'),
(232, 1, 232, 'VIR', 'VI', 'Virgin Islands (U.S.)', 'Vereinigte Staaten von Amerika'),
(233, 1, 233, 'WLF', 'WF', 'Wallis and Futuna Islands', 'Wallis und Futuna'),
(234, 1, 234, 'ESH', 'EH', 'Western Sahara', 'Westsahara'),
(235, 1, 235, 'YEM', 'YE', 'Yemen', 'Jemen'),
(236, 1, 236, 'YUG', 'YU', 'Yugoslavia', 'Yugoslavia'),
(237, 1, 237, 'ZAR', 'ZR', 'Zaire', 'Zaire'),
(238, 1, 238, 'ZMB', 'ZM', 'Zambia', 'Sambia'),
(239, 1, 239, 'ZWE', 'ZW', 'Zimbabwe', 'Simbabwe');

INSERT INTO `#__jshopping_unit` (`id`, `qty`, `name_de-DE`, `name_en-GB`) values(1, 1, 'Kg', 'Kg'),(2, 1, 'Liter', 'Liter'),(3, 1, 'St.', 'pcs.');

INSERT INTO `#__jshopping_product_labels` (`id`, `name`, `name_de-DE`, `name_en-GB`, `image`) VALUES(1, 'New', 'New', 'New', 'new.png'), (2, 'Sale', 'Sale', 'Sale', 'sale.png');

INSERT INTO `#__jshopping_import_export` (`id`, `name`, `alias`, `description`, `params`, `endstart`, `steptime`) VALUES
(1, 'Simple Export', 'simpleexport', 'Simple Export in CSV iso-8859-1', 'filename=export', 0, 1), (2, 'Simple Import', 'simpleimport', 'Simple Import in CSV iso-8859-1', '', 0, 0);

INSERT INTO `#__jshopping_config` (`id`, `count_products_to_page`, `count_products_to_row`, `count_category_to_row`, `image_category_width`, `image_category_height`, `image_product_width`, `image_product_height`, `image_product_full_width`, `image_product_full_height`, `video_product_width`, `video_product_height`, `mainCurrency`, `decimal_count`, `decimal_symbol`, `thousand_separator`, `currency_format`, `use_rabatt_code`, `enable_wishlist`, `default_status_order`,
`store_address_format`, `store_date_format`, `contact_email`, 
`allow_reviews_prod`, `allow_reviews_only_registered`, `allow_reviews_manuf`,`max_mark` , `summ_null_shipping`, `without_shipping`, `pdf_parameters`, `show_buy_in_category`, `hide_buy_not_avaible_stock`, `show_sort_product`, `show_count_select_products`, `order_send_pdf_client`,
`admin_show_attributes`, `admin_show_delivery_time`, `admin_show_languages`, `use_different_templates_cat_prod`, `admin_show_product_video`, `admin_show_product_related`, `admin_show_product_files`, `admin_show_product_bay_price`, `admin_show_product_labels`,`show_product_code`,`product_count_related_in_row`, `next_order_number`, `sorting_country_in_alphabet`, `main_unit_weight`,
`savelog`,`savelogpaymentdata`,
`fields_register`)
VALUES (1, 12, 3, 1,  160, 0,  100, 0,  200, 0,  320, 240, '1', 2, '.', '', 2, 1, 1, 1,
'%storename %address %city %zip', '%d.%m.%Y', 'test@test.com',
'1', '1', '0', '10', '-1', 0, '208:65:208:30', 1, 1, 1, 1, 1,
1, 1, 1, 0, 1, 1, 1, 0, 1, 1, 3, 1, 1, 1,
1, 1,
'a:3:{s:8:"register";a:15:{s:5:"title";a:2:{s:7:"display";s:1:"1";s:7:"require";s:1:"1";}s:6:"l_name";a:2:{s:7:"display";s:1:"1";s:7:"require";s:1:"1";}s:10:"firma_name";a:1:{s:7:"display";s:1:"1";}s:6:"street";a:2:{s:7:"display";s:1:"1";s:7:"require";s:1:"1";}s:3:"zip";a:2:{s:7:"display";s:1:"1";s:7:"require";s:1:"1";}s:4:"city";a:2:{s:7:"display";s:1:"1";s:7:"require";s:1:"1";}s:5:"state";a:1:{s:7:"display";s:1:"1";}s:7:"country";a:2:{s:7:"display";s:1:"1";s:7:"require";s:1:"1";}s:5:"phone";a:2:{s:7:"display";s:1:"1";s:7:"require";s:1:"1";}s:3:"fax";a:1:{s:7:"display";s:1:"1";}s:6:"f_name";a:2:{s:7:"require";i:1;s:7:"display";i:1;}s:5:"email";a:2:{s:7:"require";i:1;s:7:"display";i:1;}s:6:"u_name";a:2:{s:7:"require";i:1;s:7:"display";i:1;}s:8:"password";a:2:{s:7:"require";i:1;s:7:"display";i:1;}s:10:"password_2";a:2:{s:7:"require";i:1;s:7:"display";i:1;}}s:7:"address";a:22:{s:5:"title";a:2:{s:7:"display";s:1:"1";s:7:"require";s:1:"1";}s:6:"l_name";a:2:{s:7:"display";s:1:"1";s:7:"require";s:1:"1";}s:10:"firma_name";a:1:{s:7:"display";s:1:"1";}s:6:"street";a:2:{s:7:"display";s:1:"1";s:7:"require";s:1:"1";}s:3:"zip";a:2:{s:7:"display";s:1:"1";s:7:"require";s:1:"1";}s:4:"city";a:2:{s:7:"display";s:1:"1";s:7:"require";s:1:"1";}s:5:"state";a:1:{s:7:"display";s:1:"1";}s:7:"country";a:2:{s:7:"display";s:1:"1";s:7:"require";s:1:"1";}s:5:"phone";a:2:{s:7:"display";s:1:"1";s:7:"require";s:1:"1";}s:3:"fax";a:1:{s:7:"display";s:1:"1";}s:7:"d_title";a:2:{s:7:"display";s:1:"1";s:7:"require";s:1:"1";}s:8:"d_f_name";a:2:{s:7:"display";s:1:"1";s:7:"require";s:1:"1";}s:8:"d_l_name";a:2:{s:7:"display";s:1:"1";s:7:"require";s:1:"1";}s:12:"d_firma_name";a:1:{s:7:"display";s:1:"1";}s:8:"d_street";a:2:{s:7:"display";s:1:"1";s:7:"require";s:1:"1";}s:5:"d_zip";a:2:{s:7:"display";s:1:"1";s:7:"require";s:1:"1";}s:6:"d_city";a:2:{s:7:"display";s:1:"1";s:7:"require";s:1:"1";}s:7:"d_state";a:1:{s:7:"display";s:1:"1";}s:9:"d_country";a:2:{s:7:"display";s:1:"1";s:7:"require";s:1:"1";}s:7:"d_phone";a:2:{s:7:"display";s:1:"1";s:7:"require";s:1:"1";}s:6:"f_name";a:2:{s:7:"require";i:1;s:7:"display";i:1;}s:5:"email";a:2:{s:7:"require";i:1;s:7:"display";i:1;}}s:11:"editaccount";a:22:{s:5:"title";a:2:{s:7:"display";s:1:"1";s:7:"require";s:1:"1";}s:6:"l_name";a:2:{s:7:"display";s:1:"1";s:7:"require";s:1:"1";}s:10:"firma_name";a:1:{s:7:"display";s:1:"1";}s:6:"street";a:2:{s:7:"display";s:1:"1";s:7:"require";s:1:"1";}s:3:"zip";a:2:{s:7:"display";s:1:"1";s:7:"require";s:1:"1";}s:4:"city";a:2:{s:7:"display";s:1:"1";s:7:"require";s:1:"1";}s:5:"state";a:1:{s:7:"display";s:1:"1";}s:7:"country";a:2:{s:7:"display";s:1:"1";s:7:"require";s:1:"1";}s:5:"phone";a:2:{s:7:"display";s:1:"1";s:7:"require";s:1:"1";}s:3:"fax";a:1:{s:7:"display";s:1:"1";}s:7:"d_title";a:2:{s:7:"display";s:1:"1";s:7:"require";s:1:"1";}s:8:"d_f_name";a:2:{s:7:"display";s:1:"1";s:7:"require";s:1:"1";}s:8:"d_l_name";a:2:{s:7:"display";s:1:"1";s:7:"require";s:1:"1";}s:12:"d_firma_name";a:1:{s:7:"display";s:1:"1";}s:8:"d_street";a:2:{s:7:"display";s:1:"1";s:7:"require";s:1:"1";}s:5:"d_zip";a:2:{s:7:"display";s:1:"1";s:7:"require";s:1:"1";}s:6:"d_city";a:2:{s:7:"display";s:1:"1";s:7:"require";s:1:"1";}s:7:"d_state";a:1:{s:7:"display";s:1:"1";}s:9:"d_country";a:2:{s:7:"display";s:1:"1";s:7:"require";s:1:"1";}s:7:"d_phone";a:2:{s:7:"display";s:1:"1";s:7:"require";s:1:"1";}s:6:"f_name";a:2:{s:7:"require";i:1;s:7:"display";i:1;}s:5:"email";a:2:{s:7:"require";i:1;s:7:"display";i:1;}}}');

INSERT INTO `#__jshopping_currencies` ( `currency_id` , `currency_name` , `currency_code`, `currency_code_iso`, `currency_code_num` , `currency_ordering` , `currency_publish` , `currency_value` ) VALUES ( NULL , 'Euro', 'EUR', 'EUR', '978', '1', '1', '1.00');

INSERT INTO `#__jshopping_order_status` (`status_id`, `status_code`, `name_en-GB`, `name_de-DE`) VALUES (1, 'P', 'Pending', 'Offen'),(2, 'C', 'Confirmed', 'Bestätigt'),(3, 'X', 'Cancelled', 'Abgebrochen'),(4, 'R', 'Refunded', 'Gutschrift'),(5, 'S', 'Shipped', 'Gesendet'),(6, 'O', 'Paid', 'Bezahlt'),(7, 'F', 'Complete', 'Abgeschlossen');

 INSERT INTO `#__jshopping_payment_method` (`payment_id`, `name_en-GB`, `name_de-DE`, `description_en-GB`, `description_de-DE`, `payment_code`, `payment_class`, `scriptname`, `payment_publish`, `payment_ordering`, `payment_params`, `payment_type`, `tax_id`, `price`, `show_descr_in_email`) VALUES
(1, 'Cash on delivery', 'Nachnahme', '', '', 'bank', 'pm_bank', '', 1, 1, '', 1, 1, 4.00, 0),
(2, 'Advance payment', 'Vorauskasse', '', '', 'PO', 'pm_purchase_order', '', 1, 2, '', 1, 1, 0, 1),
(3, 'Paypal', 'Paypal', '', '', 'paypal', 'pm_paypal', 'pm_paypal', 1, 3, 'testmode=1\n email_received=test@testing.com\n transaction_end_status=6\n transaction_pending_status=1\n transaction_failed_status=3\n checkdatareturn=0', 2, 1, 0, 0),
(4, 'Debit', 'Lastschrift', 'Please insert your bankdata.', 'Bitte tragen Sie hier Ihre Bankdaten fГјr den Abbuchungsauftrag ein.', 'debit', 'pm_debit', 'pm_debit', 1, 4, '', 1, 1, 0, 0),
(5, 'Sofortueberweisung','Sofortueberweisung', '', '', 'ST','pm_sofortueberweisung', 'pm_sofortueberweisung', 0, 5, 'user_id=00000\nproject_id=00000\nproject_password=00000\ntransaction_end_status=6\ntransaction_pending_status=1\ntransaction_failed_status=3\n', 2, 1, 0, 0);

INSERT INTO `#__jshopping_shipping_method` ( `shipping_id` , `name_en-GB` , `name_de-DE` , `published` , `ordering`) VALUES (1 , 'Standard', 'Standardversand', '1', '1'),(2 , 'Express', 'Express', '1', '2');

INSERT INTO `#__jshopping_taxes` ( `tax_id` , `tax_name` , `tax_value` ) VALUES (1, 'Normal', '19%');

INSERT INTO `#__jshopping_shipping_method_price` VALUES (1, 1, 1, 10.00, 1, 0, 0, ''),(2, 2, 1, 25.00, 1, 0, 0, '');

INSERT INTO `#__jshopping_shipping_method_price_countries` VALUES (1, 239, 1), (2, 238, 1), (3, 237, 1), (4, 236, 1), (5, 235, 1), (6, 234, 1), (7, 233, 1), (8, 232, 1), (9, 231, 1), (10, 230, 1), (11, 229, 1), (12, 228, 1), (13, 227, 1), (14, 226, 1), (15, 225, 1), (16, 224, 1), (17, 223, 1), (18, 222, 1), (19, 221, 1), (20, 220, 1), (21, 219, 1), (22, 218, 1), (23, 217, 1), (24, 216, 1), (25, 215, 1), (26, 214, 1), (27, 213, 1), (28, 212, 1), (29, 211, 1), (30, 210, 1), (31, 209, 1), (32, 208, 1), (33, 207, 1), (34, 206, 1), (35, 205, 1), (36, 204, 1), (37, 203, 1), (38, 202, 1), (39, 201, 1), (40, 200, 1), (41, 199, 1), (42, 198, 1), (43, 197, 1), (44, 196, 1), (45, 195, 1), (46, 194, 1), (47, 193, 1), (48, 192, 1), (49, 191, 1), (50, 190, 1), (51, 189, 1), (52, 188, 1), (53, 187, 1), (54, 186, 1), (55, 185, 1), (56, 184, 1), (57, 183, 1), (58, 182, 1), (59, 181, 1), (60, 180, 1), (61, 179, 1), (62, 178, 1), (63, 177, 1), (64, 176, 1), (65, 175, 1), (66, 174, 1), (67, 173, 1), (68, 172, 1), (69, 171, 1), (70, 170, 1), (71, 169, 1), (72, 168, 1), (73, 167, 1), (74, 166, 1), (75, 165, 1), (76, 164, 1), (77, 163, 1), (78, 162, 1), (79, 161, 1), (80, 160, 1), (81, 159, 1), (82, 158, 1), (83, 157, 1), (84, 156, 1), (85, 155, 1), (86, 154, 1), (87, 153, 1), (88, 152, 1), (89, 151, 1), (90, 150, 1), (91, 149, 1), (92, 148, 1), (93, 147, 1), (94, 146, 1), (95, 145, 1), (96, 144, 1), (97, 143, 1), (98, 142, 1), (99, 141, 1), (100, 140, 1), (101, 139, 1), (102, 138, 1), (103, 137, 1), (104, 136, 1), (105, 135, 1), (106, 134, 1), (107, 133, 1), (108, 132, 1), (109, 131, 1), (110, 130, 1), (111, 129, 1), (112, 128, 1), (113, 127, 1), (114, 126, 1), (115, 125, 1), (116, 124, 1), (117, 123, 1), (118, 122, 1), (119, 121, 1), (120, 120, 1), (121, 119, 1), (122, 118, 1), (123, 117, 1), (124, 116, 1), (125, 115, 1), (126, 114, 1), (127, 113, 1), (128, 112, 1), (129, 111, 1), (130, 110, 1), (131, 109, 1), (132, 108, 1), (133, 107, 1), (134, 106, 1), (135, 105, 1), (136, 104, 1), (137, 103, 1), (138, 102, 1), (139, 101, 1), (140, 100, 1), (141, 99, 1), (142, 98, 1), (143, 97, 1), (144, 96, 1), (145, 95, 1), (146, 94, 1), (147, 93, 1), (148, 92, 1), (149, 91, 1), (150, 90, 1), (151, 89, 1), (152, 88, 1), (153, 87, 1), (154, 86, 1), (155, 85, 1), (156, 84, 1), (157, 83, 1), (158, 82, 1), (159, 81, 1), (160, 80, 1), (161, 79, 1), (162, 78, 1), (163, 77, 1), (164, 76, 1), (165, 75, 1), (166, 74, 1), (167, 73, 1), (168, 72, 1), (169, 71, 1), (170, 70, 1), (171, 69, 1), (172, 68, 1), (173, 67, 1), (174, 66, 1), (175, 65, 1), (176, 64, 1), (177, 63, 1), (178, 62, 1), (179, 61, 1), (180, 60, 1), (181, 59, 1), (182, 58, 1), (183, 57, 1), (184, 56, 1), (185, 55, 1), (186, 54, 1), (187, 53, 1), (188, 52, 1), (189, 51, 1), (190, 50, 1), (191, 49, 1), (192, 48, 1), (193, 47, 1), (194, 46, 1), (195, 45, 1), (196, 44, 1), (197, 43, 1), (198, 42, 1), (199, 41, 1), (200, 40, 1), (201, 39, 1), (202, 38, 1), (203, 37, 1), (204, 36, 1), (205, 35, 1), (206, 34, 1), (207, 33, 1), (208, 32, 1), (209, 31, 1), (210, 30, 1), (211, 29, 1), (212, 28, 1), (213, 27, 1), (214, 26, 1), (215, 25, 1), (216, 24, 1), (217, 23, 1), (218, 22, 1), (219, 21, 1), (220, 20, 1), (221, 19, 1), (222, 18, 1), (223, 17, 1), (224, 16, 1), (225, 15, 1), (226, 14, 1), (227, 13, 1), (228, 12, 1), (229, 11, 1), (230, 10, 1), (231, 9, 1), (232, 8, 1), (233, 7, 1), (234, 6, 1), (235, 5, 1), (236, 4, 1), (237, 3, 1), (238, 2, 1), (239, 1, 1), (240, 239, 2), (241, 238, 2), (242, 237, 2), (243, 236, 2), (244, 235, 2), (245, 234, 2), (246, 233, 2), (247, 232, 2), (248, 231, 2), (249, 230, 2), (250, 229, 2), (251, 228, 2), (252, 227, 2), (253, 226, 2), (254, 225, 2), (255, 224, 2), (256, 223, 2), (257, 222, 2), (258, 221, 2), (259, 220, 2), (260, 219, 2), (261, 218, 2), (262, 217, 2), (263, 216, 2), (264, 215, 2), (265, 214, 2), (266, 213, 2), (267, 212, 2), (268, 211, 2), (269, 210, 2), (270, 209, 2), (271, 208, 2), (272, 207, 2), (273, 206, 2), (274, 205, 2), (275, 204, 2), (276, 203, 2), (277, 202, 2), (278, 201, 2), (279, 200, 2), (280, 199, 2), (281, 198, 2), (282, 197, 2), (283, 196, 2), (284, 195, 2), (285, 194, 2), (286, 193, 2), (287, 192, 2), (288, 191, 2), (289, 190, 2), (290, 189, 2), (291, 188, 2), (292, 187, 2), (293, 186, 2), (294, 185, 2), (295, 184, 2), (296, 183, 2), (297, 182, 2), (298, 181, 2), (299, 180, 2), (300, 179, 2), (301, 178, 2), (302, 177, 2), (303, 176, 2), (304, 175, 2), (305, 174, 2), (306, 173, 2), (307, 172, 2), (308, 171, 2), (309, 170, 2), (310, 169, 2), (311, 168, 2), (312, 167, 2), (313, 166, 2), (314, 165, 2), (315, 164, 2), (316, 163, 2), (317, 162, 2), (318, 161, 2), (319, 160, 2), (320, 159, 2), (321, 158, 2), (322, 157, 2), (323, 156, 2), (324, 155, 2), (325, 154, 2), (326, 153, 2), (327, 152, 2), (328, 151, 2), (329, 150, 2), (330, 149, 2), (331, 148, 2), (332, 147, 2), (333, 146, 2), (334, 145, 2), (335, 144, 2), (336, 143, 2), (337, 142, 2), (338, 141, 2), (339, 140, 2), (340, 139, 2), (341, 138, 2), (342, 137, 2), (343, 136, 2), (344, 135, 2), (345, 134, 2), (346, 133, 2), (347, 132, 2), (348, 131, 2), (349, 130, 2), (350, 129, 2), (351, 128, 2), (352, 127, 2), (353, 126, 2), (354, 125, 2), (355, 124, 2), (356, 123, 2), (357, 122, 2), (358, 121, 2), (359, 120, 2), (360, 119, 2), (361, 118, 2), (362, 117, 2), (363, 116, 2), (364, 115, 2), (365, 114, 2), (366, 113, 2), (367, 112, 2), (368, 111, 2), (369, 110, 2), (370, 109, 2), (371, 108, 2), (372, 107, 2), (373, 106, 2), (374, 105, 2), (375, 104, 2), (376, 103, 2), (377, 102, 2), (378, 101, 2), (379, 100, 2), (380, 99, 2), (381, 98, 2), (382, 97, 2), (383, 96, 2), (384, 95, 2), (385, 94, 2), (386, 93, 2), (387, 92, 2), (388, 91, 2), (389, 90, 2), (390, 89, 2), (391, 88, 2), (392, 87, 2), (393, 86, 2), (394, 85, 2), (395, 84, 2), (396, 83, 2), (397, 82, 2), (398, 81, 2), (399, 80, 2), (400, 79, 2), (401, 78, 2), (402, 77, 2), (403, 76, 2), (404, 75, 2), (405, 74, 2), (406, 73, 2), (407, 72, 2), (408, 71, 2), (409, 70, 2), (410, 69, 2), (411, 68, 2), (412, 67, 2), (413, 66, 2), (414, 65, 2), (415, 64, 2), (416, 63, 2), (417, 62, 2), (418, 61, 2), (419, 60, 2), (420, 59, 2), (421, 58, 2), (422, 57, 2), (423, 56, 2), (424, 55, 2), (425, 54, 2), (426, 53, 2), (427, 52, 2), (428, 51, 2), (429, 50, 2), (430, 49, 2), (431, 48, 2), (432, 47, 2), (433, 46, 2), (434, 45, 2), (435, 44, 2), (436, 43, 2), (437, 42, 2), (438, 41, 2), (439, 40, 2), (440, 39, 2), (441, 38, 2), (442, 37, 2), (443, 36, 2), (444, 35, 2), (445, 34, 2), (446, 33, 2), (447, 32, 2), (448, 31, 2), (449, 30, 2), (450, 29, 2), (451, 28, 2), (452, 27, 2), (453, 26, 2), (454, 25, 2), (455, 24, 2), (456, 23, 2), (457, 22, 2), (458, 21, 2), (459, 20, 2), (460, 19, 2), (461, 18, 2), (462, 17, 2), (463, 16, 2), (464, 15, 2), (465, 14, 2), (466, 13, 2), (467, 12, 2), (468, 11, 2), (469, 10, 2), (470, 9, 2), (471, 8, 2), (472, 7, 2), (473, 6, 2), (474, 5, 2), (475, 4, 2), (476, 3, 2), (477, 2, 2), (478, 1, 2);

INSERT INTO `#__jshopping_shipping_ext_calc` (`id`, `name`, `alias`, `description`, `params`, `shipping_method`, `published`, `ordering`) VALUES (1, 'StandartWeight', 'sm_standart_weight', 'StandartWeight', '', '', 1, 1);

INSERT INTO `#__jshopping_usergroups` VALUES (1 , 'Default', '0.00', 'Default', 1, 'Default', 'Default');

INSERT INTO `#__jshopping_config_seo` (`alias`, `ordering`) VALUES ('category', 10),('manufacturers', 20), ('cart', 30), ('wishlist', 40), ('login', 50), ('register', 60), ('editaccount', 70), ('myorders', 80), ('myaccount', 90), ('search', 100), ('search-result', 110), ('myorder-detail', 120), ('vendors', 130),('content-agb', 140),('content-return_policy', 150),('content-shipping', 160),('content-privacy_statement',161),('checkout-address', 170),('checkout-payment', 180),('checkout-shipping', 190),('checkout-preview', 200),('lastproducts', 210),('randomproducts', 220),('bestsellerproducts', 230),('labelproducts', 240),('topratingproducts', 250),('tophitsproducts', 260),('all-products', 270);

INSERT INTO `#__jshopping_config_statictext` (`alias`) VALUES ('home'),('manufacturer'),('agb'),('return_policy'),('order_email_descr'),('order_email_descr_end'),('order_finish_descr'),('shipping'),('privacy_statement'),('cart'),('order_email_descr_manually');

INSERT INTO `#__jshopping_vendors` (`id`, `shop_name`, `company_name`, `url`, `logo`, `adress`, `city`, `zip`, `state`, `country`, `f_name`, `l_name`, `middlename`, `phone`, `fax`, `email`, `benef_bank_info`, `benef_bic`, `benef_conto`, `benef_payee`, `benef_iban`, `benef_swift`, `interm_name`, `interm_swift`, `identification_number`, `tax_number`, `additional_information`, `user_id`, `main`, `publish`) VALUES
(1, 'Shop name', 'Company', '', '', 'Address', 'City', 'Postal Code ', 'State', 81, 'First name ', 'Last name', '', '00000000', '00000000', 'email@email.com', 'test', 'test', 'test', 'test', 'test', 'test', 'test', 'test', '', '', 'Additional information', 0, 1, 1);