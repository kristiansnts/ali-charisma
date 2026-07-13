/*  ---------------------------------------------------
    Template Name: Male Fashion
    Description: Male Fashion - ecommerce teplate
    Author: Colorib
    Author URI: https://www.colorib.com/
    Version: 1.0
    Created: Colorib
---------------------------------------------------------  */

'use strict';

(function ($) {

    /*------------------
        Preloader
    --------------------*/
    $(window).on('load', function () {
        $(".loader").fadeOut();
        $("#preloder").delay(200).fadeOut("slow");

        /*------------------
            Gallery filter
        --------------------*/
        $('.filter__controls li').on('click', function () {
            $('.filter__controls li').removeClass('active');
            $(this).addClass('active');
        });
        if ($('.product__filter').length > 0) {
            var containerEl = document.querySelector('.product__filter');
            var mixer = mixitup(containerEl);
        }
    });

    /*------------------
        Background Set
    --------------------*/
    $('.set-bg').each(function () {
        var bg = $(this).data('setbg');
        $(this).css('background-image', 'url(' + bg + ')');
    });

    // Predictive search (This Is April style)
    var searchConfig = window.malefashionSearch || {};
    var predictiveSearchTimer = null;

    function openPredictiveSearch() {
        $('.search-model').addClass('is-open').attr('aria-hidden', 'false').fadeIn(200);
        $('body').addClass('predictive-search-open');
        setTimeout(function () {
            $('[data-predictive-search-input]').trigger('focus');
        }, 50);
    }

    function closePredictiveSearch() {
        $('.search-model').removeClass('is-open').attr('aria-hidden', 'true').fadeOut(200, function () {
            $('[data-predictive-search-input]').val('');
            $('[data-predictive-search-results]').html(
                '<p class="predictive-search__hint">Start typing to search products.</p>'
            );
        });
        $('body').removeClass('predictive-search-open');
    }

    function fetchPredictiveSearch(query) {
        if (!searchConfig.predictiveUrl) {
            return;
        }

        $.ajax({
            url: searchConfig.predictiveUrl,
            method: 'GET',
            data: { q: query },
            dataType: 'json',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        }).done(function (response) {
            $('[data-predictive-search-results]').html(response.html);
        });
    }

    $('.search-switch').on('click', function (e) {
        e.preventDefault();
        openPredictiveSearch();
    });

    $('.search-close-switch').on('click', function (e) {
        e.preventDefault();
        closePredictiveSearch();
    });

    $(document).on('input', '[data-predictive-search-input]', function () {
        var query = $(this).val();
        clearTimeout(predictiveSearchTimer);
        predictiveSearchTimer = setTimeout(function () {
            fetchPredictiveSearch(query);
        }, 220);
    });


    $(document).on('keydown', function (e) {
        if (e.key === 'Escape' && $('.search-model').hasClass('is-open')) {
            closePredictiveSearch();
        }
    });

    /*------------------
		Navigation
	--------------------*/
    $(".mobile-menu").slicknav({
        prependTo: '#mobile-menu-wrap',
        allowParentLinks: true
    });

    /*------------------
        Accordin Active
    --------------------*/
    $('.collapse').on('shown.bs.collapse', function () {
        $(this).prev().addClass('active');
    });

    $('.collapse').on('hidden.bs.collapse', function () {
        $(this).prev().removeClass('active');
    });

    //Canvas Menu
    $(".canvas__open").on('click', function () {
        $(".offcanvas-menu-wrapper").addClass("active");
        $(".offcanvas-menu-overlay").addClass("active");
    });

    $(".offcanvas-menu-overlay").on('click', function () {
        $(".offcanvas-menu-wrapper").removeClass("active");
        $(".offcanvas-menu-overlay").removeClass("active");
    });

    /*-----------------------
        Hero Slider
    ------------------------*/
    $(".hero__slider").owlCarousel({
        loop: true,
        margin: 0,
        items: 1,
        dots: true,
        nav: true,
        navText: ["<span class='arrow_left'><span/>", "<span class='arrow_right'><span/>"],
        animateOut: 'fadeOut',
        animateIn: 'fadeIn',
        smartSpeed: 800,
        autoHeight: false,
        autoplay: true,
        autoplayTimeout: 5000,
        autoplayHoverPause: true
    });

    /*-----------------------
        Home product carousels (April-style)
    ------------------------*/
    $(".product-carousel").owlCarousel({
        loop: false,
        margin: 24,
        items: 4,
        dots: false,
        nav: true,
        navText: ["<span class='arrow_left'></span>", "<span class='arrow_right'></span>"],
        smartSpeed: 500,
        responsive: {
            0: { items: 1, margin: 16 },
            576: { items: 2, margin: 16 },
            768: { items: 3, margin: 20 },
            992: { items: 4, margin: 24 }
        }
    });

    $(".shop-the-look__carousel").owlCarousel({
        loop: false,
        margin: 16,
        items: 3,
        dots: false,
        nav: true,
        navText: ["<span class='arrow_left'></span>", "<span class='arrow_right'></span>"],
        smartSpeed: 500,
        responsive: {
            0: { items: 1, margin: 12 },
            576: { items: 2, margin: 14 },
            992: { items: 3, margin: 16 }
        }
    });

    /*--------------------------
        Select
    ----------------------------*/
    $("select").niceSelect();

    /*-------------------
		Radio Btn
	--------------------- */
    $(".product__color__select label, .shop__sidebar__size label, .product__details__option__size label").on('click', function () {
        $(".product__color__select label, .shop__sidebar__size label, .product__details__option__size label").removeClass('active');
        $(this).addClass('active');
    });

    /*-------------------
		Scroll
	--------------------- */
    $(".nice-scroll").niceScroll({
        cursorcolor: "#0d0d0d",
        cursorwidth: "5px",
        background: "#e5e5e5",
        cursorborder: "",
        autohidemode: true,
        horizrailenabled: false
    });

    /*------------------
        CountDown
    --------------------*/
    // For demo preview start
    var today = new Date();
    var dd = String(today.getDate()).padStart(2, '0');
    var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
    var yyyy = today.getFullYear();

    if(mm == 12) {
        mm = '01';
        yyyy = yyyy + 1;
    } else {
        mm = parseInt(mm) + 1;
        mm = String(mm).padStart(2, '0');
    }
    var timerdate = mm + '/' + dd + '/' + yyyy;
    // For demo preview end


    // Uncomment below and use your date //

    /* var timerdate = "2020/12/30" */

    $("#countdown").countdown(timerdate, function (event) {
        $(this).html(event.strftime("<div class='cd-item'><span>%D</span> <p>Days</p> </div>" + "<div class='cd-item'><span>%H</span> <p>Hours</p> </div>" + "<div class='cd-item'><span>%M</span> <p>Minutes</p> </div>" + "<div class='cd-item'><span>%S</span> <p>Seconds</p> </div>"));
    });

    /*------------------
		Magnific
	--------------------*/
    $('.video-popup').magnificPopup({
        type: 'iframe'
    });

    $('.work__gallery').magnificPopup({
        delegate: 'a',
        type: 'image',
        gallery: {
            enabled: true
        }
    });

    /*-------------------
		Quantity change
	--------------------- */
    var proQty = $('.pro-qty');
    proQty.prepend('<span class="fa fa-angle-up dec qtybtn"></span>');
    proQty.append('<span class="fa fa-angle-down inc qtybtn"></span>');
    proQty.on('click', '.qtybtn', function () {
        var $button = $(this);
        var oldValue = $button.parent().find('input').val();
        if ($button.hasClass('inc')) {
            var newVal = parseFloat(oldValue) + 1;
        } else {
            // Don't allow decrementing below zero
            if (oldValue > 0) {
                var newVal = parseFloat(oldValue) - 1;
            } else {
                newVal = 0;
            }
        }
        $button.parent().find('input').val(newVal);
    });

    var proQty = $('.pro-qty-2');
    proQty.prepend('<span class="fa fa-angle-left dec qtybtn"></span>');
    proQty.append('<span class="fa fa-angle-right inc qtybtn"></span>');
    proQty.on('click', '.qtybtn', function () {
        var $button = $(this);
        var oldValue = $button.parent().find('input').val();
        if ($button.hasClass('inc')) {
            var newVal = parseFloat(oldValue) + 1;
        } else {
            // Don't allow decrementing below zero
            if (oldValue > 0) {
                var newVal = parseFloat(oldValue) - 1;
            } else {
                newVal = 0;
            }
        }
        $button.parent().find('input').val(newVal);
    });

    /*------------------
        Achieve Counter
    --------------------*/
    $('.cn_num').each(function () {
        $(this).prop('Counter', 0).animate({
            Counter: $(this).text()
        }, {
            duration: 4000,
            easing: 'swing',
            step: function (now) {
                $(this).text(Math.ceil(now));
            }
        });
    });


    /*------------------
        Compare modal
    --------------------*/
    var compareConfig = window.malefashionCompare || {};

    function compareCsrfHeaders() {
        return {
            'X-CSRF-TOKEN': compareConfig.csrfToken,
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        };
    }

    function updateCompareCount(count) {
        $('[data-compare-count]').text(count);
    }

    function openCompareModal() {
        $('#compare-modal').addClass('is-open').attr('aria-hidden', 'false');
        $('body').addClass('compare-modal-open');
    }

    function closeCompareModal() {
        $('#compare-modal').removeClass('is-open').attr('aria-hidden', 'true');
        $('body').removeClass('compare-modal-open');
    }

    function refreshCompareModal(openAfter) {
        return $.ajax({
            url: compareConfig.indexUrl,
            method: 'GET',
            headers: compareCsrfHeaders(),
            dataType: 'json'
        }).done(function (response) {
            $('#compare-modal-body').html(response.html);
            updateCompareCount(response.count);
            if (openAfter) {
                openCompareModal();
            }
        });
    }

    $(document).on('click', '[data-compare-open]', function (e) {
        e.preventDefault();
        refreshCompareModal(true);
    });

    $(document).on('click', '[data-compare-close]', function (e) {
        e.preventDefault();
        closeCompareModal();
    });

    $(document).on('click', '[data-compare-add]', function (e) {
        e.preventDefault();
        var productId = $(this).data('compare-add');
        if (!productId || !compareConfig.storeUrlTemplate) {
            return;
        }

        $.ajax({
            url: compareConfig.storeUrlTemplate + '/' + productId,
            method: 'POST',
            headers: compareCsrfHeaders()
        }).done(function (response) {
            updateCompareCount(response.count);
            // Auto-open only when comparing 2+ products; 1 item just updates the badge.
            if (response.count > 1) {
                refreshCompareModal(true);
            }
        }).fail(function (xhr) {
            if (xhr.responseJSON && xhr.responseJSON.message) {
                window.alert(xhr.responseJSON.message);
            }
            if (xhr.responseJSON && typeof xhr.responseJSON.count !== 'undefined') {
                updateCompareCount(xhr.responseJSON.count);
            }
        });
    });

    $(document).on('click', '[data-compare-remove]', function (e) {
        e.preventDefault();
        var productId = $(this).data('compare-remove');
        $.ajax({
            url: compareConfig.storeUrlTemplate + '/' + productId,
            method: 'DELETE',
            headers: compareCsrfHeaders()
        }).done(function (response) {
            updateCompareCount(response.count);
            if (response.count > 1) {
                refreshCompareModal(true);
            } else {
                closeCompareModal();
            }
        });
    });

    $(document).on('click', '[data-compare-clear]', function (e) {
        e.preventDefault();
        $.ajax({
            url: compareConfig.clearUrl,
            method: 'DELETE',
            headers: compareCsrfHeaders()
        }).done(function () {
            updateCompareCount(0);
            closeCompareModal();
        });
    });

    $(document).on('keydown', function (e) {
        if (e.key === 'Escape') {
            closeCompareModal();
            closeCartUpsell();
        }
    });

    /*------------------
        Wishlist
    --------------------*/
    var wishlistConfig = window.malefashionWishlist || {};

    function updateWishlistCount(count) {
        $('[data-wishlist-count]').text(count);
    }

    $(document).on('click', '[data-wishlist-toggle]', function (e) {
        e.preventDefault();
        var $btn = $(this);
        var key = $btn.data('wishlist-key');
        if (!key || !wishlistConfig.storeUrl) {
            return;
        }

        $.ajax({
            url: wishlistConfig.storeUrl,
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': wishlistConfig.csrfToken,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            data: {
                key: key,
                name: $btn.data('wishlist-name'),
                price: $btn.data('wishlist-price'),
                image: $btn.data('wishlist-image'),
                product_id: $btn.data('wishlist-product-id') || null
            }
        }).done(function (response) {
            updateWishlistCount(response.count);
            $btn.addClass('is-active');
        }).fail(function (xhr) {
            if (xhr.responseJSON && xhr.responseJSON.message) {
                window.alert(xhr.responseJSON.message);
            }
        });
    });

    $(document).on('click', '[data-wishlist-remove]', function (e) {
        e.preventDefault();
        var key = $(this).data('wishlist-remove');
        if (!key || !wishlistConfig.destroyUrlTemplate) {
            return;
        }

        $.ajax({
            url: wishlistConfig.destroyUrlTemplate + '/' + encodeURIComponent(key),
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': wishlistConfig.csrfToken,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        }).done(function (response) {
            updateWishlistCount(response.count);
            window.location.reload();
        });
    });

    /*------------------
        Cart upsell dialog (This Is April style)
    --------------------*/
    var cartConfig = window.malefashionCart || {};

    function updateCartChrome(count, total) {
        $('[data-cart-count]').text(count);
        $('[data-cart-total]').text(total);
    }

    function openCartUpsell(html) {
        $('#cart-upsell-modal-body').html(html);
        $('#cart-upsell-modal').addClass('is-open').attr('aria-hidden', 'false');
        $('body').addClass('cart-upsell-open');
    }

    function closeCartUpsell() {
        $('#cart-upsell-modal').removeClass('is-open').attr('aria-hidden', 'true');
        $('body').removeClass('cart-upsell-open');
    }

    $(document).on('click', '[data-cart-upsell-close]', function (e) {
        e.preventDefault();
        closeCartUpsell();
    });

    $(document).on('click', '[data-add-to-cart]', function (e) {
        e.preventDefault();
        var $btn = $(this);
        if (!cartConfig.storeUrl) {
            return;
        }

        $.ajax({
            url: cartConfig.storeUrl,
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': cartConfig.csrfToken,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            data: {
                key: $btn.data('cart-key'),
                name: $btn.data('cart-name'),
                price: $btn.data('cart-price'),
                price_label: $btn.data('cart-price-label'),
                image: $btn.data('cart-image'),
                product_id: $btn.data('cart-product-id') || null
            }
        }).done(function (response) {
            updateCartChrome(response.count, response.total);
            openCartUpsell(response.html);
        });
    });

})(jQuery);