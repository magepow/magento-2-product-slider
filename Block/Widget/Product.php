<?php
/**
 * Magiccart 
 * @category    Magiccart 
 * @copyright   Copyright (c) 2014 Magiccart (http://www.magepow.com/) 
 * @license     http://www.magepow.com/license-agreement.html
 * @Author: DOng NGuyen<nguyen@dvn.com>
 * @@Create Date: 2016-02-14 20:26:27
 * @@Modify Date: 2020-10-22 16:14:15
 * @@Function:
 */

namespace Magiccart\Magicproduct\Block\Widget;

class Product extends \Magento\Framework\View\Element\Template implements \Magento\Widget\Block\BlockInterface, \Magento\Framework\DataObject\IdentityInterface
{
    const DEFAULT_CACHE_TAG = 'MAGICCART_MAGICPRODUCT';

    protected $_magicproduct;
    protected $_types;
    protected $_tabs = array();
    protected $_typeId = '1';
    protected $_options = array('limit', 'speed', 'timer', 'cart', 'compare', 'wishlist', 'review'); //'widthImages', 'heightImages'
    protected $_images = array();
 
     /**
     * @var \Magento\Backend\Model\UrlInterface
     */
    protected $backendUrl;

    /**
     * @var \Magiccart\Magicproduct\Model\MagicproductFactory
     */
    protected $magicproductFactory;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Backend\Model\UrlInterface $backendUrl,
        \Magiccart\Magicproduct\Model\MagicproductFactory $magicproductFactory,
        \Magiccart\Magicproduct\Model\System\Config\Types $types,
        array $data = []
    ) {
        $this->backendUrl          = $backendUrl;
        $this->magicproductFactory = $magicproductFactory;
        $this->_types              = $types;

        parent::__construct($context, $data);
    }

    protected function _construct()
    {
        if ($this->hasData('identifier')) {$this->_jnitWidget();}
        parent::_construct();
    }

    protected function getCacheLifetime()
    {
        return parent::getCacheLifetime() ?: 86400;
    }

    public function getCacheKeyInfo()
    {
        $keyInfo     =  parent::getCacheKeyInfo();
        $currencyCode = $this->_storeManager->getStore()->getCurrentCurrency()->getCode();
        if($this->getMagicproduct()) $keyInfo[] = $this->getMagicproduct()->getId() . '_' . $currencyCode;
        return $keyInfo;
    }

    /**
     * @return array
     */
    public function getIdentities()
    {
        $currencyCode = $this->_storeManager->getStore()->getCurrentCurrency()->getCode();
        return [ self::DEFAULT_CACHE_TAG, self::DEFAULT_CACHE_TAG . '_' . $this->getMagicproduct()->getId() . '_' . $currencyCode ];
    }

    protected function _jnitWidget()
    {
        $identifier = $this->getIdentifier();
        $this->_magicproduct = $this->magicproductFactory->create()->getCollection( $identifier, 'identifier')
                                    ->addFieldToFilter('identifier', $identifier)
                                    ->addFieldToFilter('type_id', $this->_typeId)
                                    ->setPageSize(1)
                                    ->getFirstItem();
        if (!$this->_magicproduct){
            echo '<div class="message-error error message">Identifier "'. $identifier . '" not exist.</div> ';          
            return;
        }
        if (!$this->_magicproduct->getStatus()){
            return;
        }
        $config = $this->_magicproduct->getConfig();
        $data = @unserialize($config);

        if($data['slide']){
            $breakpoints = $this->getResponsiveBreakpoints();
            $total = count($breakpoints);
            $responsive = '[';
            foreach ($breakpoints as $size => $screen) {
                $total--;
                if(!isset($data[$screen])) continue;
                $responsive .= '{"breakpoint": '.$size.', "settings": {"slidesToShow": '. $data[$screen] .'}}';
                if($total > 0) $responsive .= ', ';
            }
            $responsive .= ']';
            $data['responsive'] = $responsive;
            $data['slides-To-Show'] = $data['visible'];
            // $data['swipe-To-Slide'] = 'true';
            $data['vertical-Swiping'] = $data['vertical'];
            if(!isset($data['fade'])) $data['fade'] = 'false';
            if(!isset($data['center-Mode'])) $data['center-Mode'] = 'false';
            // if(!isset($data['rows'])  || $data['rows'] == 1 ) $data['rows'] = 0;
        }
        $data['jnit_widget'] =1 ;
        if(is_array($data)) $this->addData($data);
    }

    public function getMagicproduct()
    {
        return $this->_magicproduct;
    }

    public function getAdminUrl($adminPath, $routeParams=[], $storeCode = 'default' ) 
    {
        $routeParams[] = [ '_nosid' => true, '_query' => ['___store' => $storeCode]];
        return $this->backendUrl->getUrl($adminPath, $routeParams);
    }

    public function getQuickedit()
    {
        return;      
    }

    /**
     * Prepare config products
     *
     * @return \Magento\Framework\View\Element\AbstractBlock
     */
    protected function _beforeToHtml()
    {
        if (!$this->hasData('jnit_widget')) {$this->_jnitWidget();}
        return parent::_beforeToHtml();
    }

    public function getTabs()
    {
        if(!$this->_tabs){
            $tabs = array();
            $cfg = $this->getTypes();
            if($cfg){
                $types = $this->_types->toOptionArray();
                foreach ($types as $type) {
                    if(in_array($type['value'], $cfg)) $tabs[$type['value']] = $type['label'];
                }                
            }
            $this->_tabs = $tabs;
        }
        return $this->_tabs;
    }

    public function getTabActivated()
    {
        $activated = $this->getActivated(); // get form Widget
        $tabs = $this->getTabs();
        $types = array_keys($tabs);
        if(!in_array($activated, $types)){
            $activated = isset($types[0]) ? $types[0] : 0;            
        }
        return $activated;
    }

    public function setTemplateProduct($template)
    {
        $this->setData('template_product', $template);
    }

    public function getContent($template='')
    {
        if($template) $this->setTemplateProduct($template);
        $content = '';
        $tabs = ($this->getAjax()) ? $tabs = array($this->getTabActivated() => 'Activated') : $this->getTabs();
        foreach ($tabs as $type => $name) {
            $content .= $this->getLayout()->createBlock('Magiccart\Magicproduct\Block\Product\GridProduct') // , "magicproduct.product.$type"
            ->setActivated($type) //or ->setData('activated', $this->getTabActivated())
            ->setCfg($this->getData())
            ->setTemplate($template)
            ->toHtml();
        }
        return $content;
    }

    public function getAjaxCfg()
    {
        if(!$this->getAjax()) return 0;
        $ajax = array();
        foreach ($this->_options as $option) {
            $ajax[$option] = $this->getData($option);
        }
        $template = $this->getTemplateProduct();
        if($template) $ajax['template'] = $template;
        if($this->getData('parameters')) $ajax['identifier'] =  $this->getIdentifier();
        return json_encode($ajax);
    }

    public function getPrcents()
    {
        return array(1 => '100%', 2 => '50%', 3 => '33.333333333%', 4 => '25%', 5 => '20%', 6 => '16.666666666%', 7 => '14.285714285%', 8 => '12.5%', 9 => '11.111111111%');
    }

    public function getResponsiveBreakpoints()
    {
        return array(1921=>'visible', 1920=>'widescreen', 1480=>'desktop', 1200=>'laptop', 992=>'notebook', 768=>'tablet', 576=>'landscape', 480=>'portrait', 361=>'mobile', 1=>'mobile');
    }

    public function getSlideOptions()
    {
        return array('autoplay', 'arrows', 'fade', 'center-Mode', 'autoplay-Speed', 'dots', 'infinite', 'padding', 'vertical', 'vertical-Swiping', 'responsive', 'rows', 'slides-To-Show');
    }

    public function getFrontendCfg()
    { 
        if($this->getSlide()) return $this->getSlideOptions();

        $this->addData(array('responsive' =>json_encode($this->getGridOptions())));
        return array('padding', 'responsive');

        // return $this->getGridStyle();

    }

    public function getGridOptions()
    {
        $options = array();
        $breakpoints = $this->getResponsiveBreakpoints(); ksort($breakpoints);
        foreach ($breakpoints as $size => $screen) {
            $options[]= array($size-1 => $this->getData($screen));
        }
        return $options;

        // $breakpoints = $this->getResponsiveBreakpoints(); ksort($breakpoints);
        // $total= count($breakpoints);
        // $i = $tmp = 1;
        // $options = array();
        // foreach ($breakpoints as $key => $value) {
        //     $tmpKey = ( $i == 1 || $i == $total ) ? $value : current($breakpoints);
        //     if($i >1){
        //         $options[] = ['col' => $this->getData($value), 'min' => $tmp, 'max' => ($key-1)];
        //         next($breakpoints);
        //     }
        //     if($i == $total) $options[] = ['col' => $this->getData($value), 'min' => $key, 'max' => 3600,];
        //     $tmp = $key;
        //     $i++;
        // }
        // return $options;

    }

    function getGridStyle($selector=' .products-grid .product-item')
    {
        $styles  = $selector . '{float: left}';
        $listCfg = $this->getData();
        $padding = $listCfg['padding'];
        $prcents = $this->getPrcents();
        $breakpoints = $this->getResponsiveBreakpoints(); ksort($breakpoints);
        $total= count($breakpoints);
        $i = $tmp = 1;
        foreach ($breakpoints as $key => $value) {
            $tmpKey = ( $i == 1 || $i == $total ) ? $value : current($breakpoints);;
            if($i >1){
                $styles .= ' @media (min-width: '. $tmp .'px) and (max-width: ' . ($key-1) . 'px) {' .$selector. '{padding: 0 '.$padding.'px; width: '.$prcents[$listCfg[$value]] .'} ' .$selector. ':nth-child(' .$listCfg[$value]. 'n+1){clear: left;}}';
                next($breakpoints);
            }
            if( $i == $total) $styles .= ' @media (min-width: ' . $key . 'px) {' .$selector. '{padding: 0 '.$padding.'px; width: '.$prcents[$listCfg[$value]] .'} ' .$selector. ':nth-child(' .$listCfg[$value]. 'n+1){clear: left;}}';
            $tmp = $key;
            $i++;
        }
        return '<style type="text/css">' .$styles. '</style>';       
    }

    public function getMediaUrl()
    {
        return $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
    }

    public function getBanner()
    {
        if(!$this->_images){
            $gallery = $this->getData('media_gallery');
            if( isset($gallery['images']) ) $this->_images = $gallery['images'];
        }
        return $this->_images;
    }

    public function getImage($file='')
    {
        if($file) $file = (strpos($file, 'magiccart/magicproduct') !== false) ? $file : 'magiccart/magicproduct' . $file;
        return $file ? $this->getMediaUrl() . $file : $this->getData('image');
    }

    public function getVideo($data){
        $url = str_replace('vimeo.com', 'player.vimeo.com/video', $data['video_url']) .'?byline=0&amp;portrait=0&amp;api=1';
        $video = array(
            'url' => $url,
            'width' => '100%',
            'height' => '100%'
        );
        $file = 'magiccart/magicproduct'. $data['file'];
        $absPath = $this->_filesystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath().$file;
        $image = $this->_imageFactory->create();
        $image->open($absPath);
        $video['width'] = $image->getOriginalWidth();
        $video['height'] = $image->getOriginalHeight();

        return $video;
    }

}
