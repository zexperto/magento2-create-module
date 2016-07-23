<?php
		
namespace ITM\File\Block\Adminhtml\Box;
		
class Edit extends \Magento\Backend\Block\Widget\Form\Container {

	protected $_coreRegistry = null;
	
	public function __construct(\Magento\Backend\Block\Widget\Context $context, \Magento\Framework\Registry $registry, array $data = []) {
		$this->_coreRegistry = $registry;
		parent::__construct ( $context, $data );
	}
	
	protected function _construct() {
		$this->_objectId = 'id';
		$this->_controller = 'adminhtml_box';
		$this->_blockGroup = 'ITM_File';
		parent::_construct ();
		$this->buttonList->add ( 'save_and_continue_edit', [
			'class' => 'save',
			'label' => __ ( 'Save and Continue Edit' ),
			'data_attribute' => [
				'mage-init' => [
					'button' => [
						'event' => 'saveAndContinueEdit',
						'target' => '#edit_form'
					] 
				] 
			] 
		], 10 );
	}
	public function getHeaderText() {
		$item = $this->_coreRegistry->registry ( 'current_itm_file_box' );
		if ($item->getId ()) {
			return __ ( "Edit Item '", $this->escapeHtml ( $item->getId () ) );
		} else {
			return __ ( 'New Item' );
		}
	}
}