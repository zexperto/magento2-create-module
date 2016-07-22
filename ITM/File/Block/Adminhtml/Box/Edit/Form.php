<?php

namespace ITM\File\Block\Adminhtml\Box\Edit;

class Form extends \Magento\Backend\Block\Widget\Form\Generic {
	protected function _construct() {
		parent::_construct ();
		$this->setId ( 'itm_box_form' );
		$this->setTitle ( __ ( 'Box Information' ) );
	}
	protected function _prepareForm() {
		$form = $this->_formFactory->create ( [
			'data' => [
				'id' => 'edit_form',
				'action' => $this->getUrl ( 'itm_file/box/save' ),
				'method' => 'post'
			]
			
		] );
		$form->setUseContainer ( true );
		$this->setForm ( $form );
		return parent::_prepareForm ();
	}
}