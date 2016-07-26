<?php
	
namespace ITM\File\Block\Adminhtml;
	
class Box extends \Magento\Backend\Block\Widget\Grid\Container {
	
	protected function _construct() {
		$this->_controller = 'adminhtml_box';
		$this->_blockGroup = 'ITM_File';
		$this->_headerText = __ ( 'Box' );
		$this->_addButtonLabel = __ ( 'Add New Entry' );
		parent::_construct ();
	}
}