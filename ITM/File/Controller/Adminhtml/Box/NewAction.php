<?php
	
namespace ITM\File\Controller\Adminhtml\Box;
	
class NewAction extends \ITM\File\Controller\Adminhtml\Box {
	public function execute() {
		$this->_forward('edit');
	}
}