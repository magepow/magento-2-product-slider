/**
 * Magiccart 
 * @category 	Magiccart 
 * @copyright 	Copyright (c) 2014 Magiccart (http://www.magiccart.net/) 
 * @license 	http://www.magiccart.net/license-agreement.html
 * @Author: DOng NGuyen<nguyen@dvn.com>
 * @@Create Date: 2014-04-25 13:16:48
 * @@Modify Date: 2020-05-18 11:08:12
 * @@Function:
 */

define([
    'jquery',
    'magiccart/slick'
    ], function ($) {
    "use strict";
    $.fn.magicproduct = function (options) {
        var defaults = {
            selector : '.magicproduct', // Selector product grid
            classes  : '.product-item', // Selector product grid
            tabs 	 : '.magictabs',
            loading  : '.ajax_loading',
            product  : '.content-products',
            padding  : 15, // padding item
        };

        var settings = $.extend(defaults, options);
        var selector 	= settings.selector;
        var classes		= selector + ' ' + settings.classes;
        var product 	= settings.product;
        var $content 	= $(this);
        var $product 	= $(product, $content);
		if( !$product.data( 'vertical') && $('body').hasClass('rtl') ){
			$product.attr('dir', 'rtl');
			$product.data( 'rtl', true );
			// $product.data( 'vertical-reverse', true );
		}
        var options 	= $product.data();
        var padding 	= ((options || {}).padding === void 0) ? settings.padding : options.padding;
		var $tabs 		= $(settings.tabs, $content);
		var infotabs 	= $tabs.data('ajax')
		var $itemtabs 	= $('.item',$tabs);
		var $loading 	= $(settings.loading, $content);
		var $head 		= $('head');
		var style 		= '';
		var isGrid 		= false;
        /******************************
        Public Methods
        *******************************/
        var methods = {
            init : function() {
                return this.each(function() {
                    methods.magicproductLoad();
                });
            },
            
            /******************************
            Initialize Items
            Fully initialize everything. Plugin is loaded and ready after finishing execution
        *******************************/
            // initialize : function(options) {
            //     Object.extend(settings, options);
            // },

            magicproductLoad: function(buttonClass){
            	// over tab
				var products = $content.find('.content-products');
				var zIndex = 0; 
				products.find('.per-product').hover(function() {
					zIndex = products.css('zIndex');
					products.css('zIndex', zIndex + 35);
				}, function() {
					products.css('zIndex', zIndex);
				});
				// end over tab
				$itemtabs.each(function() {
					var $this = $(this);
					var type = $this.data('type');
					var typeClass = '.mc-'+type;
					if($this.hasClass('activated')){
						var productsActivated = $product.find(typeClass).addClass('activated').find('.products-grid .items');
						if(options.slidesToShow){
							var float  = $('body').hasClass('rtl') ? 'right' : 'left';
							$head.append('<style type="text/css">' + classes + '{float: ' + float + '; padding-left: '+padding+'px; padding-right:'+padding+'px} ' + selector + ' .content-products' + '{margin-left: -'+padding+'px; margin-right: -'+padding+'px}</style>');
							methods.productSlider(options, productsActivated);
						} else{
							isGrid = true;
							methods.productGrid(options);
						}
					}
				});

				$tabs.on("click", '.item', function(){
						var $this = $(this);
						var type = $this.data('type');
						var typeClass = '.mc-'+type;
						if($this.hasClass('activated')) return;
						$itemtabs.removeClass('activated');
						$this.addClass('activated');
						if(! $this.hasClass('loaded')){
							if(type == undefined) return;
							methods.sendAjax(type, infotabs);
						} else {
							// banner tab
							$content.find('.category-banner').slideUp(500).removeClass('activated');
							$content.find('.banner-'+ type ).slideDown(500).addClass('activated');
							// end banner tab
							var productsActivated = $product.find(typeClass).addClass('activated'); //.fadeIn(); // not show()
							productsActivated.siblings().removeClass('activated'); //.hide();  // not fadeOut()
							productsActivated = productsActivated.find('.products-grid .items');
							if(isGrid) methods.playAnimate(productsActivated); //require for Animate
							else  methods.productSlider(options, productsActivated);
						}
				});

            },

            productSlider : function(options, el) {
				if(el.hasClass('slick-initialized')) el.slick("refresh"); // slide.resize(); // $(window).trigger('resize');
				else{ // var selector = $content.selector; // '.' + $content.attr('class').trim().replace(/ /g , '.');
					if( !options.vertical && $('body').hasClass('rtl') ) el.attr('dir', 'rtl');
                    el.on('init', function(event, slick){
                        $('body').trigger('contentUpdated');
                    });
					el.slick(options);
				}
            }, 

            productGrid : function(options) {
            	if(style) return;
            	var padding 	= options.padding;
				var responsive 	= options.responsive;
				var length = Object.keys(responsive).length;
				var float  = $('body').hasClass('rtl') ? 'right' : 'left';
				style += padding ? classes + '{float: ' + float + '; padding-left: '+padding+'px; padding-right:'+padding+'px} ' + selector + ' .content-products' + '{margin-left: -'+padding+'px; margin-right: -'+padding+'px}' : '';
				
				$.each( responsive, function( key, value ) { // data-responsive="[{"1":"1"},{"361":"1"},{"480":"2"},{"640":"3"},{"768":"3"},{"992":"4"},{"1200":"4"}]"
					var col = 0;
					var maxWith = 0;
					var minWith = 0;
					$.each( value , function(size, num) { minWith = parseInt(size) + 1; col = num;});
					if(key+2<length){
						$.each( responsive[key+1], function( size, num) { maxWith = size; col = num;});
						// padding = options.padding*(maxWith/1200); // padding responsive
						style += ' @media (min-width: '+minWith+'px) and (max-width: '+maxWith+'px)';
					} else { 
						if(key+2 == length) return; // don't use key = length - 1;
						$.each( responsive[key], function( size, num) { maxWith = size; col = num;});
						style += ' @media (min-width: '+maxWith+'px)';
					}
					style += ' {'+selector + ' .content-products' + '{margin-left: -'+padding+'px; margin-right: -'+padding+'px}'+classes+'{padding-left: '+padding+'px; padding-right:'+padding+'px; width: '+(Math.floor((10/col) * 100000000000) / 10000000000)+'%} '+classes+':nth-child('+col+'n+1){clear: ' + float + ';}}';
				});
				// $.each( responsive, function( key, value ) { // data-responsive="[{"col":"1","min":1,"max":360},{"col":"2","min":361,"max":479},{"col":"3","min":480,"max":639},{"col":"3","min":640,"max":767},{"col":"4","min":768,"max":991},{"col":"4","min":992,"max":1199},{"col":"4","min":1200,"max":3600}]"
				// var padding = options.padding*(value.max/1200); // padding responsive
				// 	style += ' @media (min-width: '+value.min+'px) and (max-width: '+value.max+'px) {'+selector + '{margin-left: -'+padding+'px; margin-right: -'+padding+'px}'+classes+'{padding-left: '+padding+'px; padding-right:'+padding+'px; width: '+(Math.floor((10/value.col) * 100000000000) / 10000000000)+'%} '+classes+':nth-child('+value.col+'n+1){clear: ' + float + ';}}';
				// });

				$head.append('<style type="text/css">'+style+'</style>');

            },

            sendAjax : function(type, infotabs) {
				$loading.show();
				$.ajax({
					type: 'post',
					data: { type: type, info: infotabs },
					url : $loading.data('url'),
					success:function(data){
						$loading.hide();
						// banner tab
						$content.find('.category-banner').slideUp(500).removeClass('activated');
						$content.find('.banner-'+ type ).slideDown(500).addClass('activated');
						// end banner tab
						var typeClass = '.mc-'+type;
						var productsActivated = $content.find(product).append(data).find(typeClass).addClass('activated');
						productsActivated.trigger('contentUpdated');
						productsActivated.siblings().removeClass('activated'); //.hide();  // not fadeOut()
						productsActivated = productsActivated.find('.products-grid .items');
						$itemtabs.each(function(){
							if($(this).data('type') == type) $(this).addClass('loaded');
						});

						if(isGrid) methods.playAnimate(productsActivated); //require for Animate
						else  methods.productSlider(options, productsActivated);
						if($.fn.timer !== undefined){
							var countdown = productsActivated.find('.alo-count-down');
							if(countdown.lenght){
								countdown.timer({
									classes	: '.countdown',
									layout	: alo_timer_layout, 
									timeout : alo_timer_timeout
								});
							}
						}
						if($.fn.mage !== undefined){
							// $.mage.catalogAddToCart;
							// $.mage.apply;
				        	// $('.action.tocart' ).unbind( "click" ).click(function() { // Callback Ajax Add to Cart
					        // 	var form = $(this).closest('form');
		            		// 	var widget = form.catalogAddToCart({ bindSubmit: false });
					        //     widget.catalogAddToCart('submitForm', form);
					        //     return false;
				        	// });
						}
					}
				});
            }, 

			// Effect
			playAnimate : function(cnt) {
				// var parent = cnt.parent();
				// $('.products-grid', parent).removeClass("play");
				// $('.products-grid .item', parent).removeAttr('style');
				// var animate = cnt;
				// var  time = time || 300; // if(typeof time == 'undefined') {time =300}
				// var $_items = $('.item-animate', animate);
				// $_items.each(function(i){
				// 	$(this).attr("style", "-webkit-animation-delay:" + i * time + "ms;"
				// 		                + "-moz-animation-delay:" + i * time + "ms;"
				// 		                + "-o-animation-delay:" + i * time + "ms;"
				// 		                + "animation-delay:" + i * time + "ms;");
				// 	if (i == $_items.size() -1){
				// 		$('.products-grid', animate).addClass("play");  // require for Animate
				// 	}
				// });
			},

        };

        if (methods[options]) { // $("#element").pluginName('methodName', 'arg1', 'arg2');
            return methods[options].apply(this, Array.prototype.slice.call(arguments, 1));
        } else if (typeof options === 'object' || !options) { // $("#element").pluginName({ option: 1, option:2 });
            return methods.init.apply(this);
        } else {
            $.error('Method "' + method + '" does not exist in magiccart plugin!');
        }
		
    }

    $( document ).ready(function($) {
	    $("*[class^='alo-content-']").each(function() {
	    	if($(this).hasClass('autoplay')){
	    		var selector = '.' + $(this).attr('class').trim().replace(/ /g , '.');
	    		$(this).magicproduct({selector: selector}); // don't use $(this)
	    	}
	    });
    });

});
