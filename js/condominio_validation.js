// var $mjq for better adaptation and work with other libraries
var $mjq = jQuery.noConflict();
$mjq(function(){
	$mjq(document).ready(function(){
            $mjq(".help-inline").each(function() {
                $mjq(this).css('display', 'none');
            });
	});
        
        $mjq("#inputUsuario").bind('blur', is_valid_campo("#inputUsuario")); 
        $mjq("#inputPassword").bind('blur', is_valid_campo("#inputPassword"));
        $mjq("#inputEmail").bind('blur', is_valid_campo("#inputEmail"));
        
        $mjq('#condominioForm').bind('submit', function(e) {
        
        if (!is_valid_form('login'))
            return false;
        $mjq("#result").html('');
        e.preventDefault();
        var data = $mjq(this).serialize();

        $mjq.ajax({
            url: "enlinea/index2.php",
            type: "post",
            data: data,
            dataType: 'json',
            success: function(data) {
                console.log(data);
                $mjq('#condominioForm').closest('form').find("input[type=text]").val("");
                //}
                //$mjq("#result").html(data);
                switch(data.suceed) {
                  case true:
                      $mjq("#result").html(returnHtml('alert-success',"Lo estamos redireccionando..."));
                      window.location.href = 'enlinea/index.php';
                      break;
//                  case 'Administrador':
//                      $mjq("#result").html("Lo estamos redireccionando...")
//                      window.location.href = 'enlinea/index.php';
//                      break;
                  default:
                      $mjq("#result").html(returnHtml('alert-error', data.error));
                      break;
                }
            },
            error: function() {
                $mjq("#result").html(returnHtml('alert-error', 'Ha ocurrido un error durante el inico de sesión.<br>Si el problema persiste póngase en contacto con el Administrador'));
            }
        });
        });
    
        $mjq('#recoveryForm').bind('submit', function(e) {
        
        if (!is_valid_form('recovery'))
            return false;
        $mjq("#result2").html('');
        e.preventDefault();
        var data = $mjq(this).serialize();
        $mjq.ajax({
            url: "enlinea/index2.php",
            type: "post",
            data: data,
            dataType: 'json',
            success: function(data) {
                console.log(data);
                $mjq('#recoveryForm').closest('form').find("input[type=text]").val("");
                switch(data.suceed) {
                  case true:
                      $mjq("#result2").html(returnHtml('alert-success',data.mensaje));
                      break;
                  default:
                      $mjq("#result2").html(returnHtml('alert-error', data.mensaje));
                      break;
                }
            },
            error: function(data) {
                console.log(data);
                $mjq("#result2").html(returnHtml('alert-error', 'Ha ocurrido un error durante el envío de las credenciales.<br>Si el problema persiste póngase en contacto con el Administrador'));
            }
        });
        });
});

function returnHtml(alertClass, html){
    return '<div class="alert  '+alertClass+'" style=\"padding-left: 20px;padding-bottom: 20px;\">'+html+'</div>';
}


function is_valid_campo(campo) {
    $this = $mjq(campo);
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
// Form validate
function is_valid_form(formulario) {
    var ret = true;
    if (formulario==='login') {
        if (!is_valid_campo("#inputUsuario") || !is_valid_campo("#inputPassword")) var ret = false;
    } else {
        if (!is_valid_campo("#inputEmail")) var ret = false;
    }
    
    return ret;
}