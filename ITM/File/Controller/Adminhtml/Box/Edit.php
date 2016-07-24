<?php
		
namespace ITM\File\Controller\Adminhtml\Box;

class Edit extends \ITM\File\Controller\Adminhtml\Box {

	public function execute() {
		$id = $this->getRequest()->getParam('id');
		$model = $this->_objectManager->create('ITM\File\Model\Box');
		if ($id) {
			$model->load($id);
			if (!$model->getId()) {
				$this->messageManager->addError(__('This item no longer exists.'));
				$this->_redirect('itm_file/*');
				return;
			}
		}
		$data = $this->_objectManager->get('Magento\Backend\Model\Session')->getPageData(true);
		if (!empty($data)) {
			$model->addData($data);
		}
		$resultPage = $this->resultPageFactory->create();
		if ($id) {
			$resultPage->getConfig()->getTitle()->prepend(__('Edit Items Entry'));
		}else{
			$resultPage->getConfig()->getTitle()->prepend(__('Add Items Entry'));
		}
		
		$this->_coreRegistry->register('current_itm_file_box', $model);
		$this->_initAction();
		$this->_view->getLayout()->getBlock('box_box_edit');
		$this->_view->renderLayout();
	}
}