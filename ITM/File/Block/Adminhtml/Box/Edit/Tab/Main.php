<?php
	
namespace ITM\File\Block\Adminhtml\Box\Edit\Tab;
	
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Registry;
use Magento\Framework\Data\FormFactory;
use Magento\Cms\Model\Wysiwyg\Config;
use ITM\File\Model\System\Config\Status;
	
class Main extends Generic implements TabInterface {
	protected $_status;
	public function __construct(Context $context, Registry $registry, FormFactory $formFactory, Config $wysiwygConfig, Status $status, array $data = []) {
		$this->_status = $status;
		parent::__construct ( $context, $registry, $formFactory, $data );
	}
	
	public function getTabLabel() {
		return __ ( 'Item Information');
	}
	
	public function getTabTitle() {
		return __ ( 'Item Information' );
	}
	
	public function canShowTab() {
		return true;
	}
	public function isHidden() {
		return false;
	}
	protected function _prepareForm() {
		$model = $this->_coreRegistry->registry ( 'current_itm_file_box' );
		$form = $this->_formFactory->create ();
		$form->setHtmlIdPrefix ( 'item_' );
		$fieldset = $form->addFieldset ( 'base_fieldset', [
			'legend' => __ ( 'Item Information' )
		] );
		if ($model->getId ()) {
			$fieldset->addField ( 'id', 'hidden', [
				'name' => 'id'
			] );
		}
		$fieldset->addField ( 'status', 'select', [
			'name' => 'status',
			'label' => __ ( 'Status' ),
			'options' => $this->_status->toOptionArray ()
			] );
		$form->setValues ( $model->getData () );
		$this->setForm ( $form );
		return parent::_prepareForm ();
	}
}
