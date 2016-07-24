<?php

namespace ITM\File\Model;
				
class Box extends \Magento\Framework\Model\AbstractModel {
	
		protected function _construct() {
			parent::_construct();
			$this->_init('ITM\File\Model\Resource\Box');
		}
}