var mobileWidth = 768;

var toCartAnimation = function(obj) {
	var cv = obj.innerWidth();
	var ch = obj.innerHeight();
	var ot = obj.offset().top - $(document).scrollTop();
	var ol = obj.offset().left;

	obj.clone()
		.css({
			'position' : 'fixed',
			'z-index' : '100',
			'width': cv,
			'height': ch,
			'top': ot,
			'left': ol,
			'opasity': 0.5
		})  
		.appendTo('body')
		.addClass('product-scale')
		.removeClass('slick-slide')
		.animate({
			opacity: '0',
			marginTop: -ch,
			marginLeft: -cv,
			top: 0,
			left:'100%',
			opacity: 0,
		}, 
		500, function() {
			$(this).remove();
		});
}

function fixedFooter() {
	var footer = $('.footer');
	if (footer.length) {
		var height = $(window).height() - footer.position().top - footer.innerHeight();
		if (height > 0) {
			footer.css({
				'margin-top': height + 'px'
			});
		}
	}
}

$(document).ready(function() {

	$('.stocks ul').slick({
		nextArrow: '<button type="button" class="slick-btn slick-next"></button>',
		prevArrow: '<button type="button" class="slick-btn slick-prev"></button>',
		// autoplay: true,
		// autoplaySpeed: 5000,
		// speed: 1500
	});

	$('#search').hover(
        function () {
            $(this).find('.feild-to-fill').stop().fadeIn(1).css('display', 'flex');
        },
        function () {
            $(this).find('.feild-to-fill').stop().fadeOut(1).css('display', 'flex');
        }
    );

    $('.pannel-bottom-content nav ul li a[href="/assortiment"]').addClass('to-sub-menu');

	$('.pannel-bottom-content nav ul li').hover(
        function () {
            $(this).find('.sub-menu__1').stop().fadeIn(300).css('display', 'flex');
        },
        function () {
            $(this).find('.sub-menu__1').stop().fadeOut(300).css('display', 'flex');
        }
    );


	fixedFooter();

	$('.nav a').click(function() {
		var ul = $(this).next('ul');
		if (ul.length && $(window).width() < mobileWidth) {
			ul.slideToggle(300);
			return false;
		}

		return true;
	});

	$('.nav-btn').click(function() {
		$('.nav').slideToggle(300);
	});

	$(window).resize(function() {
		fixedFooter();
		
		if ($(window).width() > mobileWidth && $('.nav').is(':hidden')) {
			$('.nav').removeAttr('style');
		}
	});

	var header = $('.header').length ? $('.header').outerHeight() : 0;
	var navbar = $('.navbar');
	if (header && navbar.length) {
		$(window).scroll(function() {
			if ($(this).scrollTop() > header) {
				navbar.addClass('navbar-fixed');
				$('body').css('padding-top', navbar.innerHeight() + 'px');
			} else {
				navbar.removeClass('navbar-fixed');
				$('body').css('padding-top', 0);
			}
		});
	}

	$(document).on('click', '.js__in-cart', function(){
		toCartAnimation($(this).closest(".product-item").find('img'));
		$(this).addClass('in-cart-active');
	});

	$(document).on('click', '.js__photo-in-cart', function(){
		toCartAnimation($(".js__main-photo"));
		$(this).addClass('in-cart-active');
	});

	// Скрипт оберзки текста
	(function(selector) {
		var maxHeight=100, // максимальная высота свернутого блока
			togglerClass="read-more", // класс для ссылки Читать далее
			smallClass="small", // класс, который добавляется к блоку, когда текст свернут
			labelMore="Подробнее", 
			labelLess="Свернуть";
			
		$(selector).each(function() {
			var $this=$(this),
				$toggler=$($.parseHTML('<a href="#" class="'+togglerClass+'">'+labelMore+'</a>'));
			$this.after(["<div>",$toggler,"</div>"]);
			$toggler.on("click", $toggler, function(){
				$this.toggleClass(smallClass);
				$this.css('height', $this.hasClass(smallClass) ? maxHeight : $this.attr("data-height"));
				$toggler.text($this.hasClass(smallClass) ? labelMore : labelLess);
				return false;
			});
			$this.attr("data-height", $this.height());
			if($this.height() > maxHeight) {
				$this.addClass(smallClass);
				$this.css('height', maxHeight);
			}
			else {
				$toggler.hide();
			}
		});
	})(".is_read_more"); // это селектор элементов для которых навешивать обрезку текста.

	var fancyboxImages = $('a.image-full'); 
	if (fancyboxImages.length) {
		$(fancyboxImages).fancybox({
			overlayColor: '#333',
			overlayOpacity: 0.8,
			titlePosition : 'over',
			helpers: {
			overlay: {
					locked: false
				}
			}
		});
	}

	$('body').on('click', '.yiiPager li', function(){
		$('html, body').animate({ scrollTop: $('.content').offset().top }, 500); // анимируем скроолинг к элементу scroll_el
	});

    $(document).on("click", "#totop", function(){$('body,html').animate({scrollTop:0},800);});
    $(window).on("scroll", function(){($(this).scrollTop() != 0)?$('#totop').fadeIn():$('#totop').fadeOut();});
});

$(window).load(function() {
	fixedFooter();
});