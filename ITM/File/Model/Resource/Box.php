<?php
	
namespace ITM\File\Model\Resource;
	
class Box extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb {
	protected function _construct() {
		$this->_init('itm_file_box', 'id');
	}
}