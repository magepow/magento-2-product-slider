<?php
/**
 * Magiccart 
 * @category    Magiccart 
 * @copyright   Copyright (c) 2014 Magiccart (http://www.magiccart.net/) 
 * @license     http://www.magiccart.net/license-agreement.html
 * @Author: DOng NGuyen<nguyen@dvn.com>
 * @@Create Date: 2016-01-05 10:40:51
 * @@Modify Date: 2016-02-02 16:47:40
 * @@Function:
 */

namespace Magiccart\Magicproduct\Controller\Adminhtml\Category;

use Magento\Framework\App\Filesystem\DirectoryList;

class ExportXml extends \Magiccart\Magicproduct\Controller\Adminhtml\Action
{
    public function execute()
    {
        $fileName = 'categorys.xml';

        /** @var \\Magento\Framework\View\Result\Page $resultPage */
        $resultPage = $this->_resultPageFactory->create();
        $content = $resultPage->getLayout()->createBlock('Magiccart\Magicproduct\Block\Adminhtml\Category\Grid')->getXml();

        return $this->_fileFactory->create($fileName, $content, DirectoryList::VAR_DIR);
    }
}
