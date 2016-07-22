<?php

namespace ITM\File\Controller\Adminhtml\Box;

class Index extends \ITM\File\Controller\Adminhtml\Box {
	public function execute() {
		$resultPage = $this->resultPageFactory->create();
		$resultPage->setActiveMenu('ITM_File::file');
		$resultPage->getConfig()->getTitle()->prepend(__('Box'));
		$resultPage->addBreadcrumb(__('ITM'), __('ITM'));
		$resultPage->addBreadcrumb(__('Box'), __('Box'));
		return $resultPage;
	}
}