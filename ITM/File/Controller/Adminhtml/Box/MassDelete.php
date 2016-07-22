<?php

namespace ITM\File\Controller\Adminhtml\Box;

class MassDelete extends \ITM\File\Controller\Adminhtml\Box {
	public function execute() {
		$itemsIds = $this->getRequest()->getParam('id');
		if (!is_array($itemsIds)) {
			$this->messageManager->addError(__('Please select item(s).'));
		} else {
			try {
				foreach ($itemsIds as $itemId) {
					$model = $this->_objectManager->create('ITM\File\Model\Box');
					$model->load($itemId);
					$model->delete();
				}
				$this->messageManager->addSuccess(
					__('A total of %1 record(s) have been deleted.', count($itemsIds))
				);
			} catch (\Magento\Framework\Exception\LocalizedException $e) {
				$this->messageManager->addError($e->getMessage());
			} catch (\Exception $e) {
				$this->messageManager->addException($e, __('An error occurred while deleting record(s).'));
			}
		}
		$this->_redirect('itm_file/*/');
	}
}