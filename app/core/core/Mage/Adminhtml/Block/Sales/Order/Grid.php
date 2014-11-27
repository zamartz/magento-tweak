<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml sales orders grid
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Sales_Order_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('sales_order_grid');
        $this->setUseAjax(true);
        $this->setDefaultSort('created_at');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }

    /**
     * Retrieve collection class
     *
     * @return string
     */
    protected function _getCollectionClass()
    {
        return 'sales/order_grid_collection';
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel($this->_getCollectionClass());
  		$collection->getSelect()->joinLeft('sales_flat_order_payment', 'main_table.entity_id = sales_flat_order_payment.parent_id',array('cc_type','method','cc_last4'));
		$collection->getSelect()->joinLeft('sales_flat_order_address', "main_table.entity_id = sales_flat_order_address.parent_id AND sales_flat_order_address.address_type='billing'", array('company'));
 
        $collection->getSelect()->joinLeft(array('sfoa' => 'sales_flat_order_address'), 'main_table.entity_id = sfoa.parent_id AND sfoa.address_type="shipping"', array('sfoa.street', 'sfoa.city', 'sfoa.region', 'sfoa.postcode', 'sfoa.telephone','sfoa.country_id'));
 
		$collection->getSelect()->joinLeft('sales_flat_order', "main_table.entity_id = sales_flat_order.entity_id", array('shipping_amount','shipping_tax_amount','subtotal','quote_id', 'coupon_code', 'is_virtual', 'tax_amount', 'shipping_amount', 'discount_amount', 'shipping_description'));  
		
		$collection->getSelect()->joinLeft('sales_flat_invoice', 'main_table.entity_id = sales_flat_invoice.order_id', 'increment_id as invoice_id');   
		 
   
   $this->setCollection($collection);

   return parent::_prepareCollection();
    }
	
    protected function _prepareColumns()
    {

        $this->addColumn('real_order_id', array(
            'header'=> Mage::helper('sales')->__('Order #'),
            'width' => '80px',
            'type'  => 'text',
            'index' => 'increment_id',
			'filter_index' => 'main_table.increment_id', 
        ));
		
		$this->addColumn('invoice_id',
        array(
            'header'=> $this->__('Invoice #'),
            'align' =>'right',
            'type=' => 'text',
            'index' => 'invoice_id',
			'filter_index' => 'sales_flat_invoice.increment_id', 
        	)
        );

        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('store_id', array(
                'header'    => Mage::helper('sales')->__('Purchased From (Store)'),
                'index'     => 'store_id',
                'type'      => 'store',
                'store_view'=> true,
                'display_deleted' => true,
				'filter_index' => 'main_table.store_id', 
            ));
        }

        $this->addColumn('created_at', array(
            'header' => Mage::helper('sales')->__('Purchased On'),
            'index' => 'created_at',
            'type' => 'datetime',
            'width' => '100px',
			'filter_index' => 'main_table.created_at',
        ));

        $this->addColumn('billing_name', array(
            'header' => Mage::helper('sales')->__('Bill to Name'),
            'index' => 'billing_name',
        ));

        $this->addColumn('shipping_name', array(
            'header' => Mage::helper('sales')->__('Ship to Name'),
            'index' => 'shipping_name',
        ));
		
        $this->addColumn('status', array(
            'header' => Mage::helper('sales')->__('Status'),
            'index' => 'status',
            'type'  => 'options',
            'width' => '70px',
            'options' => Mage::getSingleton('sales/order_config')->getStatuses(),
			'filter_index' => 'main_table.status', 
        ));
		

       /* if (Mage::getSingleton('admin/session')->isAllowed('sales/order/actions/view')) {
        $this->addColumn('action',
            array(
                'header'    => Mage::helper('sales')->__('Action'),
                'width'     => '50px',
                'type'      => 'action',
                //~ 'getter'     => 'getId',
                'getter'     => 'getParentId',
                'actions'   => array(
                    array(
                        'caption' => Mage::helper('sales')->__('View'),*/
                       // 'url'     => array('base'=>'*/sales_order/view'),
                       /* 'field'   => 'order_id',
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'is_system' => true,
				'filter_index' => 'main_table.stores', 
        ));
    }*/
		
		$this->addColumn('subtotal', array(
               'header' => Mage::helper('sales')->__('Subtotal'),
               'index' => 'subtotal',
               'type' => 'currency',
			   'currency' => 'order_currency_code',
			   'filter_index' => 'sales_flat_order.subtotal',
           ));
		   
		   $this->addColumn('shippinh_amount', array(
               'header' => Mage::helper('sales')->__('Shipping'),
               'index' => 'shipping_amount',
               'type' => 'currency',
			   'currency' => 'order_currency_code',
              'filter_index' => 'sales_flat_order.shipping_amount',
           ));
		
		$this->addColumn('shipping_tax_amount', array(
               'header' => Mage::helper('sales')->__('Shipping Tax Amount'),
               'index' => 'shipping_tax_amount',
               'type' => 'currency',
			   'currency' => 'order_currency_code',
               'filter_index' => 'sales_flat_order.shipping_tax_amount',
           ));
		
		$this->addColumn('tax_amount', array(
               'header' => Mage::helper('sales')->__('Total Tax Amount'),
               'index' => 'tax_amount',
               'type' => 'currency',
			   'currency' => 'order_currency_code',
               'filter_index' => 'sales_flat_order.tax_amount',
           ));

        $this->addColumn('base_grand_total', array(
            'header' => Mage::helper('sales')->__('G.T. (Base)'),
            'index' => 'base_grand_total',
            'type'  => 'currency',
            'currency' => 'base_currency_code',
			'filter_index' => 'main_table.base_grand_total',
        ));

        $this->addColumn('grand_total', array(
            'header' => Mage::helper('sales')->__('G.T. (Purchased)'),
            'index' => 'grand_total',
            'type'  => 'currency',
            'currency' => 'order_currency_code',
			'filter_index' => 'main_table.grand_total',
        ));
		
		$this->addColumn('discount_amount', array(
               'header' => Mage::helper('sales')->__('Discount Amount'),
               'index' => 'discount_amount',
               'type' => 'text',
				'filter' =>false,
           ));
		   
		   $this->addColumn('method', array(
            'header'    => Mage::helper('sales')->__('Payment Method Name'),
            'index'     => 'method',
            'type'      => 'options',
            'options'       => Mage::helper('payment')->getPaymentMethodList(),
            'option_groups' => Mage::helper('payment')->getPaymentMethodList(true, true, true),
			'filter_index' => ' sales_flat_order_payment.method',
        ));
		
		$this->addColumn('cc_type', array(
            'header'    => Mage::helper('sales')->__('Payment Type'),
            'index'     => 'cc_type',
            'type'      => 'text',
            'text'       =>Mage::helper('payment')->__('Credit Card Type: %s', $this->getCcTypeName()),
			'filter_index' => ' sales_flat_order_payment.cc_type',
        ));
		
		$this->addColumn('cc_last4', array(
               'header' => Mage::helper('sales')->__('CC Last4'),
               'index' => 'cc_last4',
               'type' => 'text',
			   'filter_index' => ' sales_flat_order_payment.cc_last4',
           ));
		
		$this->addColumn('shipping_description', array(
               'header' => Mage::helper('sales')->__('Shipping Method'),
               'index' => 'shipping_description',
			   'type' => 'text',
			   'filter_index' => 'sales_flat_order.shipping_description',
           ));
		   
		 $this->addColumn('country_id', array(
               'header' => Mage::helper('sales')->__('Country'),
               'index' => 'country_id',
			   'filter_index' => 'sfoa.country_id',
           ));
		   
		   $this->addColumn('region', array(
               'header' => Mage::helper('sales')->__('Region'),
               'index' => 'region',
               'filter_index' => 'sfoa.region',
           ));
           $this->addColumn('city', array(
               'header' => Mage::helper('sales')->__('City'),
               'index' => 'city', 
			   'filter_index' => 'sfoa.city',
           ));
           $this->addColumn('street', array(
               'header' => Mage::helper('sales')->__('Street'),
               'index' => 'street',
			   'filter_index' => 'sfoa.street',
           ));
           $this->addColumn('postcode', array(
               'header' => Mage::helper('sales')->__('Postcode'),
               'index' => 'postcode',
			   'filter_index' => 'sfoa.postcode',
           ));
		
		/*$this->addColumn('cc_number_enc', array(
            'header'    => Mage::helper('sales')->__('Card Last 4'),
            'index'     => 'cc_number_enc',
            'type'      => 'text',
            'text'       =>Mage::helper('payment')->__('Credit Card Number: xxxx-%s', $this->getCcLast4()) ,
			'filter'    => false,
            'sortable'  => true,
        ));*/
		
        $this->addRssList('rss/order/new', Mage::helper('sales')->__('New Order RSS'));

        $this->addExportType('*/*/exportCsv', Mage::helper('sales')->__('CSV'));
        $this->addExportType('*/*/exportExcel', Mage::helper('sales')->__('Excel XML'));

        return parent::_prepareColumns();
    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('entity_id');
        $this->getMassactionBlock()->setFormFieldName('order_ids');
        $this->getMassactionBlock()->setUseSelectAll(false);

        if (Mage::getSingleton('admin/session')->isAllowed('sales/order/actions/cancel')) {
            $this->getMassactionBlock()->addItem('cancel_order', array(
                 'label'=> Mage::helper('sales')->__('Cancel'),
                 'url'  => $this->getUrl('*/sales_order/massCancel'),
            ));
        }

        if (Mage::getSingleton('admin/session')->isAllowed('sales/order/actions/hold')) {
            $this->getMassactionBlock()->addItem('hold_order', array(
                 'label'=> Mage::helper('sales')->__('Hold'),
                 'url'  => $this->getUrl('*/sales_order/massHold'),
            ));
        }

        if (Mage::getSingleton('admin/session')->isAllowed('sales/order/actions/unhold')) {
            $this->getMassactionBlock()->addItem('unhold_order', array(
                 'label'=> Mage::helper('sales')->__('Unhold'),
                 'url'  => $this->getUrl('*/sales_order/massUnhold'),
            ));
        }

        $this->getMassactionBlock()->addItem('pdfinvoices_order', array(
             'label'=> Mage::helper('sales')->__('Print Invoices'),
             'url'  => $this->getUrl('*/sales_order/pdfinvoices'),
        ));

        $this->getMassactionBlock()->addItem('pdfshipments_order', array(
             'label'=> Mage::helper('sales')->__('Print Packingslips'),
             'url'  => $this->getUrl('*/sales_order/pdfshipments'),
        ));

        $this->getMassactionBlock()->addItem('pdfcreditmemos_order', array(
             'label'=> Mage::helper('sales')->__('Print Credit Memos'),
             'url'  => $this->getUrl('*/sales_order/pdfcreditmemos'),
        ));

        $this->getMassactionBlock()->addItem('pdfdocs_order', array(
             'label'=> Mage::helper('sales')->__('Print All'),
             'url'  => $this->getUrl('*/sales_order/pdfdocs'),
        ));

        $this->getMassactionBlock()->addItem('print_shipping_label', array(
             'label'=> Mage::helper('sales')->__('Print Shipping Labels'),
             'url'  => $this->getUrl('*/sales_order_shipment/massPrintShippingLabel'),
        ));

        return $this;
    }

    public function getRowUrl($row)
    {
        if (Mage::getSingleton('admin/session')->isAllowed('sales/order/actions/view')) {
            return $this->getUrl('*/sales_order/view', array('order_id' => $row->getId()));
        }
        return false;
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current'=>true));
    }

}
