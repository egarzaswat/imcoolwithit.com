<?php
$location = 'http://' . $_SERVER['SERVER_NAME'] . '/' . JText::_('LINK_EARN_TOKENS');

$complete_box = '<div class="earn-tokens-box col-xs-8 col-xs-offset-2">';
$complete_box.= '<div class="page-content row">';
$complete_box.= '<h1 class="title">' . JText::_('COMPLETE_TITLE') . '</h1>';
$complete_box.= '<span class="close-page"></span>';
$complete_box.= '<span class="earn-tokens-box-info">' . JText::sprintf('COMPLETE_INFO', $this->block_name, $this->tokens_count) . '</span>';
$complete_box.= '</div>';
$complete_box.= '</div>';
echo $complete_box;
?>