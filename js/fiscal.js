// Referencias de jQuery
const listado       = jQuery('#lista_publicaciones');
const btncliente    = jQuery('#id_empresa');
const inputReporte  = jQuery('input[name="reporte"]');
const alert         = jQuery('article > div.alert');

function listarPublicaciones(datos) {
    
    let contenido;
    
    if (datos.stats.affected_rows===0) {
        contenido = '<div class="alert alert-danger">\
        <span>No hay publicados reportes, de este tipo, para este cliente.</span>\
        </div>'
    } else {
        contenido = `<table class="table table-bordered table-striped responsive-utilities">
        <thead>
            <tr>
                <th style="text-align: center">Periodo</th>
                <th style="text-align: center">Archivo</th>
                <th style="text-align: center">Acción</th>
            </tr>
        </thead>
        <tbody>`
        datos.data.forEach( reg => {
            
            let periodo;
            let yy = '-';
            let mm = '-';
            let pe = '';
            if (!reg.periodo.startsWith('0000-00-00')) {
                periodo = reg.periodo.split('-');
                yy = periodo[0];
                mm = reg.periodo.includes('-12-31') ? '' : periodo[1] + '-';
            }
            if(reg.pquincena > 0) {
               
                pe = reg.pquincena + 'º Quin. '; 
            }
            
            contenido = contenido + `<tr>
                <td style="text-align: center">${pe}${mm}${yy}</td>
                <td><a href=/fiscal/documentos/${reg.archivo} target="_blank">${reg.archivo}</a></td>
                <td align="center"><a 
                data-item=${reg.pid} 
                href="#"
                title="Eliminar Publicación" 
                class="btn-eliminar"><i class="fa fa-trash-o fa-2x"></i></a></td>
            </tr>`;
        
        }

            
        )
        contenido = contenido + `</tbody></table>`;
    }
    
    listado.html(contenido);


}
// Cambiar Estaus Cliente/Empresa
jQuery(document).on('click', '.cambiar-status-cliente', function(e) {
    e.preventDefault();
    const confirma = confirm("¿Seguro desea cambiar el estatus de este cliente?");

    if (confirma) {    

        $item   = jQuery(this).data('item');
        $status = jQuery(this).attr('data-status') == 0 ? 1 : 0;
        
        const data = { 
            item: $item,
            status: $status 
        };

        const respuesta = fetch('/fiscal/mantenimiento/', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify( data )
        })
        .then( res => { return res.json() })
        .catch( err => console.log( 'POST error:', err ));    

        respuesta.then(data => {
            if (data.suceed) {
                jQuery(this).attr('data-status', $status);
                icono = $status == 0 ? 'minus' : 'plus';
                titulo = $status == 0 ? 'Desactivar' : 'Activar';
                jQuery(this).attr('title',titulo + ' Cliente');
                contenido = `<i class="fa fa-${icono}-circle fa-2x"></i>`;
                jQuery(this).html(contenido);
                console.log('[v] Registro actualizado con éxito.(' + data.stats.affected_rows + ')');
            } else {
                console.log("Error: ", data.stats.error);
            }
        });
    }
});

// Eliminar cliente
jQuery(document).on('click', '.btn-eliminar-cliente', function(e) {
    e.preventDefault();
    const cliente = jQuery(this).closest('tr').find('td:eq(0)');
    
    const confirma = confirm("Se dispone a eliminar al cliente:" + cliente.text() + "Presione [Aceptar] para confirmar");

    if (confirma) {
        const data = {
            id : jQuery(this).data('item')
        };
        const respuesta = fetch('/fiscal/mantenimiento/index.php', {
            method: 'DELETE',
            headers: {
                'Content-Type' : 'application/json'
            },
            body: JSON.stringify( data )
        })
        .then( res => { return res.json() })
        .catch( err => console.log( 'DELETE error:', err ));

        respuesta.then(data => {
            if (data.suceed) {
                jQuery(this).closest('tr').remove();
                console.log('[v] Cliente eliminado con éxito.(' + data.stats.affected_rows + ')');
            } else {
                console.log("Error: ", data.stats.error);
            }
        });    
    }
    
})

// Eliminar publicacion
jQuery(document).on('click', '.btn-eliminar', function(e) {
    e.preventDefault();
    
    const confirma = confirm("¿Seguro desea eliminar la publicación seleccionada?");
    
    if (confirma) {
        const data = { 
            item: jQuery(this).data('item') 
        };
        const respuesta = fetch('/fiscal/api.php', {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify( data )
        })
        .then( res => { return res.json() })
        .catch( err => console.log( 'DELETE error:', err ));    

        respuesta.then(data => {
            if (data.suceed) {
                jQuery(this).closest('tr').remove();
                console.log('[v] Registro actualizado con éxito.(' + data.stats.affected_rows + ')');
            } else {
                console.log("Error: ", data.stats.error);
            }
        });
    }
    
})

btncliente.on('change', () => {
    
    const id_empresa = btncliente.val();
    const reporte = inputReporte.val();
    
    if(!id_empresa=='') {
        
        contenido = '<div class="alert alert-info">\
        <span>Espere un momento, estamos consultando las publicaciones de este cliente...</span></div>';
        listado.html(contenido);

        var data = {
            accion: 'listar',
            reporte: reporte,
            id_empresa: id_empresa
        };

        alert.addClass('hidden');

        fetch('/fiscal/api.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify( data )
        })
        .then( res => res.json() )
        .then( res => listarPublicaciones(res))
        .catch( err => console.log( 'POST error:', err ));
    }
    
    

})