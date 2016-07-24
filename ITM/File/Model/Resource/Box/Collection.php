<?php
				
namespace ITM\File\Model\Resource\Box ;
				
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection {
	protected function _construct() {
		$this->_init('ITM\File\Model\Box', 'ITM\File\Model\Resource\Box');
	}
}