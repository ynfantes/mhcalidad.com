// var $mjq for better adaptation and work with other libraries
var $mjq = jQuery.noConflict();
$mjq(function(){
	$mjq(document).ready(function(){
            $mjq(".help-inline").each(function() {
                $mjq(this).css('display', 'none');
            });
	});
        
        $mjq("#inputTipo").bind('blur', is_valid_tipo);
        $mjq("#textarea").bind('blur', is_valid_comment);
        
        $mjq('#contactForm').bind('submit', function(e) {
        
        if (!is_valid_form())
            return false;
        $mjq("#result").html('');
        e.preventDefault();
        var data = $mjq(this).serialize();

        $mjq.ajax({
            url: "../../includes/contact_condominio.php",
            type: "post",
            //dataType : "json",//
            data: data,
            success: function(data) {
                //var alertClass;
                //if(data.error === true){
                //    alertClass = 'alert-error';
                //}else{
                //    alertClass = 'alert-success';
                $mjq('#contactForm').closest('form').find("input[type=text], textarea").val("");
                $mjq('#inputTipo option:eq(0)').attr('selected','selected');
                //}
                $mjq("#result").html(data);
            },
            error: function(data) {
                $mjq("#result").html(returnHtml('alert-error', 'No se puedo enviar el mensaje'));
            }
        });
    });
});

function returnHtml(alertClass, html){
    return '<div class="alert  '+alertClass+'"><button type="button" class="close" data-dismiss="alert">&times;</button>'+html+'</div>';
}


// Comment validate
function is_valid_comment() {
	$this = $mjq("#textarea");
	if($this.val().length>0){ // valid
            if ($this.closest(".control-group").hasClass("error")) 
                    $this.closest(".control-group").removeClass("error");
            $this.siblings(".help-inline").css("display", "none");
            return true
	} else { // error
            if (!$this.closest(".control-group").hasClass("error")) 
                    $this.closest(".control-group").addClass("error");
            $this.siblings(".help-inline").css("display", "block");
            return false;
	}
}

// area validate
function is_valid_tipo() {
    $this = $mjq("#inputTipo");
    
    if ($this.val().length>0) { // valid
        if ($this.closest(".control-group").hasClass("error")) 
                $this.closest(".control-group").removeClass("error");
            $this.siblings(".help-inline").css("display", "none");
            return true
    } else { // error
        if (!$this.closest(".control-group").hasClass("error")) 
                $this.closest(".control-group").addClass("error");
        $this.siblings(".help-inline").css("display", "block");
        return false;
    }
}
// Form validate
function is_valid_form() {
    var ret = true;
    if (!is_valid_tipo()) var ret = false;
    if (!is_valid_comment()) var ret = false;
    return ret;
}