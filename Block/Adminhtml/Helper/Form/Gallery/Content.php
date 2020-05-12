<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Catalog product form gallery content
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 *
 * @method \Magento\Framework\Data\Form\Element\AbstractElement getElement()
 */
namespace Magiccart\Magicproduct\Block\Adminhtml\Helper\Form\Gallery;

use Magento\Backend\Block\Media\Uploader;
use Magento\Framework\View\Element\AbstractBlock;
use Magento\Framework\App\Filesystem\DirectoryList;

class Content extends \Magento\Catalog\Block\Adminhtml\Product\Helper\Form\Gallery\Content
{

    /**
     * @var string
     */
    protected $_template = 'helper/gallery.phtml';

    /**
     * @var string
     */
    // protected $_template = 'Magento_Catalog:catalog/product/helper/gallery.phtml';
    

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magiccart\Magicproduct\Model\Media\Config $mediaConfig,
        array $data = []
    ) {
        $this->_jsonEncoder = $jsonEncoder;
        $this->_mediaConfig = $mediaConfig;
        parent::__construct($context, $jsonEncoder, $mediaConfig, $data);
    }

    protected function _prepareLayout()
    {
        $this->addChild('uploader', 'Magento\Backend\Block\Media\Uploader');
        $this->getChildBlock('uploader')->setTemplate('Magiccart_Magicproduct::media/uploader.phtml');

        $this->getUploader()->getConfig()->setUrl(
            // $this->_urlBuilder->addSessionParam()->getUrl('magicproduct/index_gallery/upload')
            $this->_urlBuilder->getUrl('magicproduct/index_gallery/upload')
        )->setFileField(
            'image'
        )->setFilters(
            [
                'images' => [
                    'label' => __('Images (.gif, .jpg, .png)'),
                    'files' => ['*.gif', '*.jpg', '*.jpeg', '*.png'],
                ],
            ]
        );


        $this->_eventManager->dispatch('catalog_product_gallery_prepare_layout', ['block' => $this]);
        // $this->setTemplate('catalog/product/helper/gallery.phtml');
        // $this->setTemplate('catalog/video/helper/gallery.phtml');
        // return parent::_prepareLayout();
    }
 

}
