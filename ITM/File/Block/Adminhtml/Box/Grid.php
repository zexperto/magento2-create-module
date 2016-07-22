<?php

namespace ITM\File\Block\Adminhtml\Box;

use ITM\File\Model\System\Config\Status;

class Grid extends \Magento\Backend\Block\Widget\Grid\Extended {
	protected $_status;
	protected $_collectionFactory;
	public function __construct(
			\Magento\Backend\Block\Template\Context $context,
			\Magento\Backend\Helper\Data $backendHelper,
			\ITM\File\Model\Resource\Box\Collection $collectionFactory,
			Status $status,
			array $data = []
		) {
			$this->_status = $status;
			$this->_collectionFactory = $collectionFactory;
			parent::__construct($context, $backendHelper, $data);
		}
	protected function _construct() {
		parent::_construct();
		$this->setId('boxGrid');
		$this->setDefaultSort('id');
		$this->setDefaultDir('DESC');
		$this->setSaveParametersInSession(true);
		$this->setUseAjax(false);
	}
	protected function _getStore() {
		$storeId = ( int ) $this->getRequest ()->getParam ( 'store', 0 );
		return $this->_storeManager->getStore ( $storeId );
	}
	protected function _prepareCollection() {
		try {
			$collection = $this->_collectionFactory->load ();
			$this->setCollection ( $collection );
			parent::_prepareCollection ();
			return $this;
		} catch ( Exception $e ) {
			echo $e->getMessage ();
			die ();
		}
	}
	protected function _prepareColumns() {
		$this->addColumn ( 'id', [ 
				'header' => __ ( 'ID' ),
				'type' => 'number',
				'index' => 'id',
				'header_css_class' => 'col-id',
				'column_css_class' => 'col-id'
		] );
		$this->addColumn ( 'status', [
				'header' => __ ( 'Status' ),
				'index' => 'status',
				'class' => 'status',
				'type' => 'options',
				'options' => $this->_status->getOptionArray ()
		] );
		$block = $this->getLayout ()->getBlock ( 'grid.bottom.links' );
		if ($block) {
			$this->setChild ( 'grid.bottom.links', $block );
		}
		return parent::_prepareColumns ();
	}
	protected function _prepareMassaction() {
		$this->setMassactionIdField ( 'id' );
		$this->getMassactionBlock ()->setFormFieldName ( 'id' );
		$this->getMassactionBlock ()->addItem ( 'delete', array( 
				'label' => __ ( 'Delete' ),
				'url' => $this->getUrl ( 'itm_file/*/massDelete' ),
				'confirm' => __ ( 'Are you sure?' )
		) );
		return $this;
	}
	public function getGridUrl() {
		return $this->getUrl ( 'itm_file/*/index', [
			'_current' => true 
		] );
	}
	public function getRowUrl($row) {
		return $this->getUrl ( 'itm_file/*/edit', [
			'store' => $this->getRequest ()->getParam ( 'store' ),
			'id' => $row->getId ()
		] );
	}
}