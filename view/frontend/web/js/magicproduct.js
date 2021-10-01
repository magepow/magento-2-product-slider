/**
 * Magiccart 
 * @category 	Magiccart 
 * @copyright 	Copyright (c) 2014 Magiccart (http://www.magiccart.net/) 
 * @license 	http://www.magiccart.net/license-agreement.html
 * @Author: DOng NGuyen<nguyen@dvn.com>
 * @@Create Date: 2014-04-25 13:16:48
 * @@Modify Date: 2021-02-25 11:08:12
 * @@Function:
 */

define([
    'jquery',
    'slick'
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
            actionmore 				: '.action-more',
			loadmoreSelector		: '.load-more',
			loadendSelector			: '.load-end',
			loadmoreDisabledClass	: 'disabled'
        };

        var settings = $.extend(defaults, options);
        var selector 	= settings.selector;
        var classes		= selector + ' ' + settings.classes;
        var product 	= settings.product;
        var $content 	= $(this);
        var $product 	= $(product, $content);
        var actionmore 	= $(settings.actionmore, $content);
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
						var productsActivated = $product.find(typeClass).addClass('activated');
						if ("IntersectionObserver" in window) {
							var style 	= methods.getStyleCLS(options);
							var styleId = selector.replace(/[.]/g, '_');
							$head.append('<style type="text/css" id="' + styleId +  '" >'+style+'</style>');
							let productsObserver = new IntersectionObserver(function(entries, observer) {
								entries.forEach(function(entry) {
									if (entry.isIntersecting) {
										// let el = entry.target;
						                $head.find('#' + styleId).remove();
										methods.gridSlider(productsActivated);
										productsObserver.unobserve(entry.target);
									}
								});
							});
						    productsActivated.each(function(){
						    	productsObserver.observe(this);
						    });
						} else {
							methods.gridSlider(productsActivated);
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
							var nextPage = productsActivated.data('next-page');
							methods.loadMoreButton(nextPage);
							productsActivated.siblings().removeClass('activated'); //.hide();  // not fadeOut()
							productsActivated = productsActivated.find('.products.items');
							if(isGrid){
								if(!productsActivated.hasClass('grid-init')) productsActivated.addClass('grid-init');
								methods.playAnimate(productsActivated); //require for Animate
							} else  methods.productSlider(options, productsActivated);
						}
				});
				methods.loadMore();
            },

            getStyleCLS : function (options) {
            	if(!options.slidesToShow) return;
            	var style 		= '';
            	var padding 	= options.padding;
				var responsive 	= options.responsive;
				var length = Object.keys(responsive).length;
				var nthChild =  options.slidesToShow + 1;
				style += selector + ' .content-products .item:nth-child(n+ ' + nthChild + ')' + '{display: none;} ' + selector +  ' .item{float:left};';
				var gridResponsive = [];
				$.each( responsive, function( key, value ) { 
					var breakpoint = {};
					breakpoint[value.breakpoint] = options.vertical ? parseInt(options.rows) : parseInt(value.settings.slidesToShow);
					gridResponsive.push(breakpoint);
				 });
				var girdOptions = Object.assign({}, options);
				girdOptions.responsive  = gridResponsive.reverse();
				style += methods.productGrid(girdOptions, true);
				return style;
            },

            gridSlider : function(productsActivated) {
				var nextPage = productsActivated.data('next-page');
				var products = productsActivated.find('.products.items');
				methods.loadMoreButton(nextPage);
				if(options.slidesToShow){
					var float  = $('body').hasClass('rtl') ? 'right' : 'left';
					$head.append('<style type="text/css">' + classes + '{float: ' + float + '; padding-left: '+padding+'px; padding-right:'+padding+'px} ' + selector + ' .content-products' + '{margin-left: -'+padding+'px; margin-right: -'+padding+'px}</style>');
					methods.productSlider(options, products);
				} else{
					isGrid = true;
					methods.productGrid(options);
				}
				products.addClass('grid-init');
            },

            loadMore : function() {
            	var loadmoreButton = actionmore.find(settings.loadmoreSelector);
            	if(loadmoreButton.length) loadmoreButton.data('text', loadmoreButton.text());
				$content.on("click", settings.actionmore + ' .load-more', function(){
					if($(this).hasClass(settings.loadmoreDisabledClass)) return;
					var $this = $tabs.find('.item.activated');
					var type  = $this.data('type');
					if(type == undefined) return;
					var typeClass = '.mc-'+type;
					if($this.hasClass('loaded')){
						var productsActivated = $product.find(typeClass); //.fadeIn(); // not show()
						var nextPage = productsActivated.data('next-page');
						methods.loadMoreButton(nextPage);
						if(nextPage < 2) return;
						var info = $.extend(infotabs, { 'p' : nextPage});
						methods.disableLoadmoreButton(actionmore);
						methods.sendAjax(type, info, nextPage);
					} 
				});
            },

            loadMoreButton : function(nextPage=0){
            	if(nextPage){
            		actionmore.show();
            	}else {
            		actionmore.hide();
            	}
            	if(nextPage > 1){
					actionmore.find(settings.loadmoreSelector).show();
					actionmore.find(settings.loadendSelector).hide();
            	}else {
					actionmore.find(settings.loadmoreSelector).hide();
					actionmore.find(settings.loadendSelector).show();
            	}
            },

            productSlider : function(options, el) {
				if(el.hasClass('slick-initialized')) el.slick("refresh"); // slide.resize(); // $(window).trigger('resize');
				else{ // var selector = $content.selector; // '.' + $content.attr('class').trim().replace(/ /g , '.');
					if( !options.vertical && $('body').hasClass('rtl') ) el.attr('dir', 'rtl');
					var galleryPlaceholder = el.find('.gallery-placeholder.autoplay');
                    el.on('init', function(event, slick){
                        $('body').trigger('contentUpdated');
                 		if(galleryPlaceholder.length) methods.gallerySlider(galleryPlaceholder);
                    });
					el.slick(options);
				}
            }, 

            gallerySlider : function(el) {
                el.each(function() {
	                var gallery = $(this).find('.gallery-items');
	                if(gallery.hasClass('slick-initialized')) return;
	                var nav        = $(this).find('.slider-nav');
	                var galleryCfg = gallery.data();
	                var navCfg 	   = nav.data();
	                var isRTL 	   = false;
	                if( !navCfg.vertical && $('body').hasClass('rtl') ){
	                	gallery.attr('dir', 'rtl');
	                	nav.attr('dir', 'rtl');
	                	isRTL 	   = true;
	                }
	                var galleryCfg = $.extend(galleryCfg, {'asNavFor': nav, 'rtl': isRTL});
	                var navCfg 	   = $.extend(navCfg, {'asNavFor': gallery, 'rtl': isRTL});
	                gallery.slick(galleryCfg);
	                nav.slick(navCfg);
	            }); 
            },

            productGrid : function(options, returnStyle=false) {
            	if(style) return;
            	var padding 	= options.padding;
				var responsive 	= options.responsive;
				var length = Object.keys(responsive).length;
				var float  = $('body').hasClass('rtl') ? 'right' : 'left';
				style += (typeof padding !== 'undefined') ? classes + '{float: ' + float + '; padding-left: '+padding+'px; padding-right:'+padding+'px} ' + selector + ' .content-products' + '{margin-left: -'+padding+'px; margin-right: -'+padding+'px}' : '';
				
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

				if(returnStyle) return style;

				$head.append('<style type="text/css">'+style+'</style>');
            },

            sendAjax : function(type, infotabs, nextPage=1) {
            	$content.addClass('loading');
				$loading.show();
				$.ajax({
					type: 'post',
					data: { type: type, info: infotabs, p : nextPage },
					url : $loading.data('url'),
					success:function(data){
						$loading.hide();
						$content.removeClass('loading');
						// banner tab
						$content.find('.category-banner').slideUp(500).removeClass('activated');
						$content.find('.banner-'+ type ).slideDown(500).addClass('activated');
						// end banner tab
						var typeClass = '.mc-'+type;
						var products  = $content.find(product);
						var productsActivated = products.find(typeClass);
						var productMore = $(data);
						var nextPage 	= productMore.data('next-page');
						if(productsActivated.length){
							var productsActivated = $content.find(product).find(typeClass).addClass('activated');
							productsActivated.data('next-page', nextPage);
							productsActivated.find('.products.items').append(productMore.find('.products.items').html());
							nextPage++; // nextPage + 1 is ajax.
							methods.enableLoadmoreButton(actionmore);
						} else {
							var productsActivated = products.append(data).find(typeClass).addClass('activated');
						}
						methods.loadMoreButton(nextPage); 
						productsActivated.trigger('contentUpdated');
						productsActivated.siblings().removeClass('activated'); //.hide();  // not fadeOut()
						productsActivated = productsActivated.find('.products.items');
						productsActivated.addClass('grid-init');
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

            disableLoadmoreButton: function () {
                var loadmoreButton = actionmore.find(settings.loadmoreSelector);
                loadmoreButton.text(loadmoreButton.data('loading'));
                loadmoreButton.attr('title', loadmoreButton.data('loading'));
            },

            enableLoadmoreButton: function () {
                var loadmoreButton = actionmore.find(settings.loadmoreSelector);
                loadmoreButton.text(loadmoreButton.data('loaded'));
                loadmoreButton.attr('title', loadmoreButton.data('loaded'));

                setTimeout(function () {
                    loadmoreButton.removeClass(settings.loadmoreDisabledClass);
                    loadmoreButton.text(loadmoreButton.data('text'));
                    loadmoreButton.attr('title', loadmoreButton.data('text'));
                }, 1000);
            }

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
				var selector  = $(this).attr('class').split(" ");
				selector.forEach(item => {
					if(item.indexOf('alo-content-') === 0) {
					    selector = item.replace(/[.]/g, ' ').trim();
					}
				});	    		
	    		$(this).magicproduct({selector: selector}); // don't use $(this)
	    	}
	    });
    });

});
