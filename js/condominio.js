jQuery.noConflict();
jQuery(document).ready(function($) {
var url = $(".cartelera-slide").attr('data-url');

$.ajax({ // get content cartelera
        
        url: url + 'enlinea/listarInformacionCartelera.php',
        dataType: 'json',
        type: 'GET',        
        success: function(data) { 
            // html preparing and output to the page
            
            values = data;
            
            let html = '';  
            for (var i = 0; i < values.length; i++) {
                html = html +'<li class="latest-tweet"><p>' + values[i] + '</p></li>';
            }               
            $(".cartelera-slide ul").append($(html));
            let height_li = 30;
            $(".cartelera-slide ul li").each(function() {
                $(this).css('height', '');
                if ($(this).outerHeight(true) > height_li) height_li = $(this).outerHeight(true);
            });             
            $(".cartelera-slide ul li").each(function() {
                let margin = Math.floor((height_li-$(this).outerHeight(true))/2);
                $(this).css('height', height_li);
                $(this).children("p").css('margin-top', margin);
            });             
            // flexslider initialization
            $('.cartelera-slide').flexslider({
                animation: "slide",			    //String: Select your animation type, "fade" or "slide"
                keyboard: false,			    //Boolean: Allow slider navigating via keyboard left/right keys
                controlNav: false, 			    //Boolean: Create navigation for paging control of each clide? Note: Leave true for manualControls usage
                direction: "vertical",		    //String: Select the sliding direction, "horizontal" or "vertical"
                pauseOnHover: true,			    //Boolean: Pause the slideshow when hovering over slider, then resume when no longer hovering
                animationSpeed: 400,		    //Integer: Set the speed of animations, in milliseconds
                slideshowSpeed: 3000,		    //Integer: Set the speed of the slideshow cycling, in milliseconds
                controlsContainer: "#nav_t",	//{UPDATED} jQuery Object/Selector: Declare which container the navigation elements should be appended too. Default container is the FlexSlider element. Example use would be $(".flexslider-container"). Property is ignored if given element is not found.
                useCSS:false
            });
            }                   
    });
    
});
Number.prototype.formatCurrency = function() {
    var number = new String(this);
    var splitStr = number.split('.');
    var splitLef = splitStr[0];
    if (splitStr.length > 1 ) {
        if (splitStr[1].length > 2) {
            var decimale = parseInt(splitStr[1].substring(0,3) / 10);
            splitStr[1] = decimale.toString();
        }
        if (splitStr[1].length == 1) splitStr[1] += '0';
    }
    var splitRig = splitStr.length > 1 ? ',' + splitStr[1] :',00';
    var regx = /(\d+)(\d{3})/;
    
    while (regx.test(splitLef)) {
        splitLef = splitLef.replace(regx, '$1' + '.' + '$2');
    }
    return splitLef + splitRig;
};