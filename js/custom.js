/** 
 * Isotope: An exquisite jQuery plugin for magical layouts
 * options: http://isotope.metafizzy.co/docs/options.html
 */
jQuery.noConflict();
jQuery(document).ready(function ($) {

    $(document).on('click', '.toolbar a[data-target]', function (e) {
        e.preventDefault();
        var target = $(this).data('target');
        $('.widget-box.visible').removeClass('visible');//hide others
        $(target).addClass('visible');//show target
    });

    // Filters at Portfolio Grid and Portfolio pages
    $('#filters a').click(function () {

        // don't proceed if already selected
        if ($(this).parent().hasClass('active')) {
            return false;
        }
        var $optionSet = $(this).parents('#filters');
        $optionSet.find('.active').removeClass('active');

        var selector = $(this).attr('data-filter');

        $('#portfolio, #gallery').isotope({
            filter: selector
        });

        $(this).parent().addClass('active');
        return false;
    });


    /**                         
 * Bootstrap: Sleek, intuitive, and powerful front-end framework
 * docs: http://twitter.github.com/bootstrap/
 */

    /**                     
     * Dropdown menu        
     * more information: http://twitter.github.com/bootstrap/javascript.html#dropdowns
     */
    $('.dropdown-toggle').dropdown();

    // make menu open on hover
    $(".dropdown").hover(
        function () {
            $(this).addClass("open");
        },
        function () {
            $(this).removeClass("open");
        }
    );

    // make bootstrap fix for ipad
    $('body')
        .on('touchstart.dropdown', '.dropdown-menu', function (e) {
            e.stopPropagation();
        })

    /**                     
     * Toggle Boxes         
     * more information: http://twitter.github.com/bootstrap/javascript.html#collapse
     */
    $('.accordion').collapse();
    // make correction status icons Toggle Boxes
    $('.accordion').on('shown', function () {
        $('.accordion-group').each(function () {
            if ($(this).children(".accordion-body").hasClass("in")) $(this).children(".accordion-heading").children("a").removeClass("collapsed");
            else $(this).children(".accordion-heading").children("a").addClass('collapsed');
        });
    })
    $('.accordion').css('height', 'auto');

    $(document).ready(function () {
        var $toggleBoxes = $(".toggle-box");
        $toggleBoxes.find('.tbox-heading').children(".collapsed").parent().next(".tbox-inner").css('display', 'none');
    });
    $(".toggle-box .tbox-heading a").click(function () {
        if ($(this).hasClass("collapsed")) {
            $(this).parent().next(".tbox-inner").slideDown('fast');
            $(this).removeClass("collapsed");
        } else {
            $(this).parent().next(".tbox-inner").slideUp('fast');
            $(this).addClass("collapsed");
        }
        return false;
    });
    /**                         
 * Other libraries section  
 *                          
 */

    /**                     
     * Fancybox - zooming functionality for images, html content.
     * documentation: http://fancyapps.com/fancybox/#docs
     */
    $("a.thumbnail, a.fancy").fancybox({
        'transitionIn': 'elastic',
        'transitionOut': 'elastic',
        'speedIn': 600,
        'speedOut': 200,
        'overlayShow': false
    });
    $(".fancybox").fancybox();


    /**                     
     * FlexSlider - responsive slider
     * documentation: http://flexslider.woothemes.com/
     */
    /** --- Portfolio Item page: slider and navigation in a carousel --- **/
    // navigation           
    $('#carousel').flexslider({
        animation: "slide",			//String: Select your animation type, "fade" or "slide"
        controlNav: false,			//Boolean: Create navigation for paging control of each clide? Note: Leave true for manualControls usage
        animationLoop: true,		//Boolean: Should the animation loop? If false, directionNav will received "disable" classes at either end
        slideshow: false,			//Boolean: Animate slider automatically
        itemWidth: 80,				//{NEW} Integer: Box-model width of individual carousel items, including horizontal borders and padding.
        itemMargin: 5,				//{NEW} Integer: Margin between carousel items.
        asNavFor: '#slider',			//{NEW} Selector: Internal property exposed for turning the slider into a thumbnail navigation for another slider
        useCSS: false
    });
    // slider               
    $('#slider').flexslider({
        animation: "slide",			//String: Select your animation type, "fade" or "slide"
        controlNav: false,			//Boolean: Create navigation for paging control of each clide? Note: Leave true for manualControls usage
        animationLoop: false,		//Boolean: Should the animation loop? If false, directionNav will received "disable" classes at either end
        slideshow: false,			//Boolean: Animate slider automatically
        sync: "#carousel",			//{NEW} Selector: Mirror the actions performed on this slider with another slider. Use with care.
        useCSS: false
    });


    /**                         
    * Custom scripts for extended interface functionality
    *                          
    */

    /**                     
     * html generation for the the select menu 
     * - menu list is converted to the form element <select>
     * - menu links are converted to the form elements <option>, 
     *  Text within <i> tags is removed from the select menu. 
     *                      
     * Select menu will be inserted in block with selector .buttons-container that should be present in the html code of the page
     */
    var select_menu = '<select class="nav-select">';
    $(".navbar-collapse a").each(function () {
        var el = $(this);
        select_menu += '<option value="' + el.attr("href") + '"';
        if (el.parent().hasClass("active")) select_menu += ' selected';
        select_menu += '>' + el.html().replace(/<i>.*<\/i>/gi, '') + '</option>';
    });
    select_menu += '</select>';
    $(select_menu).appendTo(".buttons-container");
    // to work select element as menu, go to the next page on change
    $(".buttons-container select").change(function () {
        window.location = $(this).find("option:selected").val();
    });

    /**                     
     * get twitter feed and output to the "list" tag in <div class="tweets-slide"><ul class="slides"></ul></div>
     * each message is scrolled by flexslider, which is initialized after messages loaded
     * messages, navigation arrows and twitter logo are centered vertically
     */
    //    url = 'http://eluniversal.com.feedsportal.com/c/33765/f/604546/index.rss';
    //    $.ajax({ // get content from twiter
    //     url: document.location.protocol + '//ajax.googleapis.com/ajax/services/feed/load?v=1.0&num=1000&callback=?&q=' + encodeURIComponent(url),
    //     type: 'GET',        
    //     dataType: 'json',  
    //     success: function(data) { 
    //         // html preparing and output to the page
    //         //values = data.responseData.feed.entries;
    //         //console.log(values);
    //         var html = '';  
    //         for (var i = 0; i < values.length; i++) {
    //             html = html +'<li class="latest-tweet"><p><a href="' + values[i].link + '" style="color:#ed6642;font-weight:bold" target="_blank">' + values[i].title + '</a>: ' + values[i].contentSnippet + '</p></li>';
    //         }               
    //         $(".tweets-slide ul").append($(html));
    //         var height_li = 30;
    //         $(".tweets-slide ul li").each(function() {
    //             $(this).css('height', '');
    //             if ($(this).outerHeight(true) > height_li) height_li = $(this).outerHeight(true);
    //         });             
    //         $(".tweets-slide ul li").each(function() {
    //             var margin = Math.floor((height_li-$(this).outerHeight(true))/2);
    //             $(this).css('height', height_li);
    //             $(this).children("p").css('margin-top', margin);
    //         });             
    //         // flexslider initialization
    //         $('.tweets-slide').flexslider({
    //             animation: "slide",			//String: Select your animation type, "fade" or "slide"
    //             keyboard: false,			//Boolean: Allow slider navigating via keyboard left/right keys
    //             controlNav: false, 			//Boolean: Create navigation for paging control of each clide? Note: Leave true for manualControls usage
    //             direction: "vertical",		//String: Select the sliding direction, "horizontal" or "vertical"
    //             pauseOnHover: true,			//Boolean: Pause the slideshow when hovering over slider, then resume when no longer hovering
    //             animationSpeed: 400,		//Integer: Set the speed of animations, in milliseconds
    //             slideshowSpeed: 3000,		//Integer: Set the speed of the slideshow cycling, in milliseconds
    //             controlsContainer: "#nav_t",	//{UPDATED} jQuery Object/Selector: Declare which container the navigation elements should be appended too. Default container is the FlexSlider element. Example use would be $(".flexslider-container"). Property is ignored if given element is not found.
    //             useCSS:false
    //         });             
    //         // twitter logo and navigation block position correction on page loaded and twitter messages loaded
    //         $("#nav_t").css('margin-top', Math.floor(((height_li - $("#nav_t").outerHeight(true))/2)));
    //         $(".follow_img").css('margin-top', Math.floor(((height_li - $(".follow_img").outerHeight(true))/2)));
    //     }                   
    // }); 

    const url = $(".publicaciones-slide").attr('data-url');

    if (url) {
        $.ajax({ // get content cartelera

            url: url + 'cartelera.php',
            dataType: 'json',
            type: 'GET',
            success: function (data) {
                // html preparing and output to the page

                values = data === null ? 0 : data;
                var html = '';
                for (var i = 0; i < values.length; i++) {
                    html = html + '<li class="latest-tweet"><p style=\'color:#fff\'>' + values[i] + '</p></li>';
                }
                $(".publicaciones-slide ul").append($(html));
                var height_li = 30;
                $(".publicaciones-slide ul li").each(function () {
                    $(this).css('height', '');
                    if ($(this).outerHeight(true) > height_li) height_li = $(this).outerHeight(true);
                });
                $(".publicaciones-slide ul li").each(function () {
                    var margin = Math.floor((height_li - $(this).outerHeight(true)) / 2);
                    $(this).css('height', height_li);
                    $(this).children("p").css('margin-top', margin);
                });
                // flexslider initialization
                $('.publicaciones-slide').flexslider({
                    animation: "slide",			//String: Select your animation type, "fade" or "slide"
                    keyboard: false,			//Boolean: Allow slider navigating via keyboard left/right keys
                    controlNav: false, 			//Boolean: Create navigation for paging control of each clide? Note: Leave true for manualControls usage
                    direction: "vertical",		//String: Select the sliding direction, "horizontal" or "vertical"
                    pauseOnHover: true,			//Boolean: Pause the slideshow when hovering over slider, then resume when no longer hovering
                    animationSpeed: 400,		//Integer: Set the speed of animations, in milliseconds
                    slideshowSpeed: 3000,		//Integer: Set the speed of the slideshow cycling, in milliseconds
                    controlsContainer: "#nav_t1",	//{UPDATED} jQuery Object/Selector: Declare which container the navigation elements should be appended too. Default container is the FlexSlider element. Example use would be $(".flexslider-container"). Property is ignored if given element is not found.
                    useCSS: false
                });
            }
        });
    }

    const tasa_cambio = $('li#tasa_cambio');
    
    if (tasa_cambio.length>0) {
        $.ajax({ // get content cartelera

            url: 'https://bcv.pronet21.net',
            dataType: 'json',
            type: 'GET',
            success: function (data) {
                if(data.usd !== null) {
                console.log(`Tasa del día: ${data.usd}`);
                const tasa = `<span id='tasabcv' class="bold" style="font-size:16px">${data.usd}<span>`;
                const t = Date.now();
                const fecha = new Date(t);
                console.log(fecha.toLocaleDateString());
                //tasa.text(data.usd.formatCurrency());
                tasa_cambio.append(tasa)
                .prepend(`Tasa de cambio BCV al ${fecha.toLocaleDateString()} `);
                }
                
            },
            error: function(err) {
                console.log('Error: ',err);
            }
        });
    }
    const ws = $('#WAButton');
    if(ws.length > 0) {
        $(function () {
            $('#WAButton').floatingWhatsApp({
                phone: '+584142392465', //WhatsApp Business phone number International format-
                //Get it with Toky at https://toky.co/en/features/whatsapp.
                headerTitle: 'Escríbenos al WS MHCalidad!',   //Popup Title
                popupMessage: '👋 ¿En qué puedo ayudarle?',   //Popup Message
                showPopup: true,                              //Enables popup display
                buttonImage: '<img src="https://rawcdn.githack.com/rafaelbotazini/floating-whatsapp/3d18b26d5c7d430a1ab0b664f8ca6b69014aed68/whatsapp.svg" />', //Button Image
                //headerColor: 'crimson', //Custom header color
                //backgroundColor: 'crimson', //Custom background button color
                position: "right"
            });
        });
    }

    
    /*$(function(){
        url = 'http://www.eluniversal.com/rss/eco_avances.xml';
        $.ajax({
        type: "GET",
        url: document.location.protocol + '//ajax.googleapis.com/ajax/services/feed/load?v=1.0&num=1000&callback=?&q=' + encodeURIComponent(url),
        dataType: 'json',
        error: function(){
            alert('Unable to load feed, Incorrect path or invalid feed');
        },
        success: function(xml){
            values = xml.responseData.feed.entries;
        }
    });
    });*/

    // twitter logo and navigation block position correction on window resize
    /*$(window).on('resize', function() {
        if ($(".tweets-slide ul li").length>0) {
            var height_li = 30;
            $(".tweets-slide ul li").each(function() {
                $(this).css('height', '');
                if ($(this).outerHeight(true) > height_li) height_li = $(this).outerHeight(true);
            });             
            $(".tweets-slide ul li").each(function() {
                var margin = Math.floor((height_li-$(this).outerHeight(true))/2);
                $(this).css('height', height_li);
                $(this).children("p").css('margin-top', margin);
            });             
            $("#nav_t").css('margin-top', Math.floor(((height_li - $("#nav_t").outerHeight(true))/2)+2));
            $(".follow_img").css('margin-top', Math.floor(((height_li - $(".follow_img").outerHeight(true))/2)+1));
        }                   
    	
    });*/

});

jQuery(window).load(function () {
    // Home page 
    jQuery('#home_responsive').isotope({
        itemSelector: '.hp-wrapper',
        layoutMode: 'masonry'
    });
    // Clients block at the bottom of each page
    jQuery('#clients').isotope({
        itemSelector: '.hp-wrapper',
        layoutMode: 'masonry'
    });
    // Gallery page
    jQuery('#gallery-main').isotope({
        itemSelector: '.hp-wrapper',
        layoutMode: 'masonry'
    });
    // Portfolio page
    jQuery('#portfolio').isotope({
        itemSelector: '.hp-wrapper',
        layoutMode: 'masonry'
    });
    // Portfolio Grid page
    jQuery('#gallery').isotope({
        itemSelector: '.hp-wrapper',
        layoutMode: 'masonry'
    });
});

