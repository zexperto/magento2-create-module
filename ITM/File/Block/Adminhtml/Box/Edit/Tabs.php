<?php
	
namespace ITM\File\Block\Adminhtml\Box\Edit;
	
class Tabs extends \Magento\Backend\Block\Widget\Tabs {
	protected function _construct() {
		parent::_construct ();
			$this->setId ( 'itm_file_box_edit_tabs' );
			$this->setDestElementId ( 'edit_form' );
			$this->setTitle ( __ ( 'Box' ) );
	}
}