(function($){
    $.fn.agregarDiscoForm=function(){
        if(!$(this).hasClass("agregarDiscoFormContainer")){
            $(this).addClass("agregarDiscoFormContainer");
            $(this).find("input[name='fechaPublicacion']").datepicker($.datepicker.regional['es']);
            $(this).find(".submitButton").click(function(){
                var form=$(this).parents(".agregarDiscoFormContainer").eq(0);
                form.find(".error").removeClass("error");
                var Data={
                    titulo:$.trim(form.find("input[name='titulo']").val()),
                    autor:$.trim(form.find("input[name='autor']").val()),
                    fechaPublicacion:form.find("input[name='fechaPublicacion']").datepicker( "getDate" ),
                    materialAdicional:form.find("input[name='materialAdicional']:checked").length>0?1:null
                };

                if(!Data.titulo||Data.titulo.length<1){
                    form.find(".tituloField").addClass("error");
                    throw "Falta título";
                }
                if(!Data.autor||Data.autor.length<1){
                    form.find(".autorField").addClass("error");
                    throw "Falta autor";
                }
                if(!Data.fechaPublicacion||Data.fechaPublicacion.length<1){
                    form.find(".fechaPublicacionField").addClass("error");
                    throw "Falta fechaPublicacion";
                }
                Data.fechaPublicacion=Data.fechaPublicacion.toISOString();

                $(form).modal('toggle');

                form.find("input[name='titulo']").val("");
                form.find("input[name='autor']").val("");
                form.find("input[name='fechaPublicacion']").val("");
                form.find("input[name='materialAdicional']").prop("checked",false);

                $.post("api/album",Data,function(resp){
                     if(resp.error){
                         alert(resp.errorMessage);
                     }else{
                         $(document).trigger("albumGuardado");
                     }
                });
            });
        }
    }
})(jQuery);


$.datepicker.regional['es'] = {
 closeText: 'Cerrar',
 prevText: '< Ant',
 nextText: 'Sig >',
 currentText: 'Hoy',
 monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
 monthNamesShort: ['Ene','Feb','Mar','Abr', 'May','Jun','Jul','Ago','Sep', 'Oct','Nov','Dic'],
 dayNames: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'],
 dayNamesShort: ['Dom','Lun','Mar','Mié','Juv','Vie','Sáb'],
 dayNamesMin: ['Do','Lu','Ma','Mi','Ju','Vi','Sá'],
 weekHeader: 'Sm',
 dateFormat: 'dd/mm/yy',
 firstDay: 1,
 isRTL: false,
 showMonthAfterYear: false,
 yearSuffix: ''
 };

(function($){
    $.fn.albums=function(conf){
      var cont=this;
      
      
      
      $(cont).addClass("albumContainer");
      
      
      $(cont).find(".album").remove();
      $(cont).empty();
      
      $.get("api/album",function(discos){
          $(cont).empty();
          for(var i in discos){
              var d=$(conf.model).clone(true);
              d.addClass("album");
              discos[i].fechaPublicacion=new Date(discos[i].fechaPublicacion);
              discos[i].fechaAlta=new Date(discos[i].fechaAlta);
              $(d).find(".titulo").text(discos[i].titulo);
              $(d).find(".nombreAutor").text(discos[i].autor.nombre);
              $(d).find(".pistas").text(parseInt(discos[i].pistas));
              $(d).find(".yearPublicacion").text(discos[i].fechaPublicacion.getFullYear());
              d.prop("disco",discos[i]);
              $(cont).append(d);
              
              
                $(d).find(".showDetails").click(function(){
                    
                    var disco=$(this).parents(".album").eq(0);
                    var container=$(this).parents(".albumContainer").eq(0);                    
                    $(container).find(".album").animate({
                            opacity:0
                        },500,function(){
                            $(this).remove();
                        });
                    $(document).trigger("albumSeleccionado",disco.prop("disco"));
                    
                });
              
          }
      });
    };
})(jQuery);

(function($){
    $.fn.albumShow=function(conf){
        $(this).hide();
        var cont=this;
        
        $(cont).find("#showInfo").click(function(){
                if($(cont).find(".cardInfo").prop("mostrado")){
                    $(cont).find(".cardInfo").hide();
                    $(cont).find(".cardInfo").prop("mostrado",false);
                    
                    
                }else{
                    $(cont).find(".cardInfo").show();
                    $(cont).find(".cardInfo").prop("mostrado",true);
                    $(cont).find(".cardInfo").css({
                        top:$(this).position().top,
                        left:$(this).position().left-$(cont).find(".cardInfo").width()
                    });
                }
            });
        
        $(document).bind("albumSeleccionado",function(ev,disco){
            $(cont).show(500);
            $(cont).find(".titulo").text(disco.titulo);
            $(cont).find(".nombreAutor").text(disco.autor.nombre);
            $(cont).find(".pistas").text(disco.pistas);
            $(cont).find(".activo").text(disco.activo?"Sí":"No");
            $(cont).find(".materialAdicional").text(disco.materialAdicional?"Sí":"No");
            $(cont).find(".fechaPublicacion").text($.format.date(disco.fechaPublicacion,"d / MM / yyyy"));
            $(cont).find(".fechaAlta").text($.format.date(disco.fechaAlta,"d / MM / yyyy"));
            
            if(!$(conf.pistaModel).hasClass("binded")){
                $(conf.pistaModel).addClass("binded");
                $(conf.pistaModel).find(".borrarPistaButton").click(function(){
                    var pistaCont=$(this).parents(".pista").eq(0);
                    if(confirm("¿En verdad desea borrar esta pista?")){
                        console.log("borrando");
                        $.ajax({
                            url: 'api/pista?id='+pistaCont.find("input[name='idPista[]']").val(),
                            type: 'DELETE',
                            success: function(result) {
                                pistaCont.remove();
                            }
                        });
                    }
                });

                $(conf.pistaModel).find(".guardarPistaButton").click(function(){
                    var pistaCont=$(this).parents(".pista").eq(0);
                    $(pistaCont).find(".error").removeClass("error");
                    var data={
                        id:pistaCont.find("input[name='idPista[]']").val(),
                        nombre:pistaCont.find("input[name='nombre[]']").val(),
                        album:pistaCont.find("input[name='idAlbum[]']").val()
                    };
                    if(!data.nombre || data.nombre.length<1){
                        $(pistaCont).find(".nombreField").addClass("error");
                        throw "Falta Nombre";
                    }
                    $.post("api/pista",data,function(nuevaPista){
                        if( pistaCont.hasClass("nuevo")){
                            $(cont).find(".pistas").append(pistaCont);
                            pistaCont.find("input[name='idPista[]']").val(nuevaPista.id);
                            pistaCont.removeClass("nuevo").addClass("saved");
                            var pistaModel=$(pistaCont).clone(true);
                            pistaModel.find("input[name='nombre[]']").val("");
                            pistaModel.find("input[name='idPista[]']").val("");
                            $(pistaModel).addClass("nuevo").addClass("pista").removeClass("saved");
                            $(cont).find(".nuevaPista").append(pistaModel);
                        }
                    });
                });
            
            }
            $(cont).find(".pistas").empty();
            $(cont).find(".pistas").sortable();
            
            
            $(cont).find("#cerrarDetalles").click(function(){
                $(cont).hide(500);
                $(document).trigger("albumDesSeleccionado");
            });
            
            
            
            
            $.get("api/pista?album="+disco.id,function(pistas){
                for(var i in pistas){
                    var pistaModel=$(conf.pistaModel).clone(true);
                    pistaModel.removeClass("nuevo").addClass("pista").addClass("saved");
                    pistaModel.find("input[name='idPista[]']").val(pistas[i].id);
                    pistaModel.find("input[name='nombre[]']").val(pistas[i].nombre);
                    pistaModel.find("input[name='idAlbum[]']").val(pistas[i].album.id);
                    $(cont).find(".pistas").append(pistaModel);
                }
            });
            
            var pistaModel=$(conf.pistaModel).clone(true);
            $(pistaModel).addClass("nuevo").addClass("pista");
            $(cont).find(".nuevaPista").empty().append(pistaModel);
            $(pistaModel).find("input[name='idAlbum[]']").val(disco.id);
            
            console.log(disco);
        });
    };
})(jQuery);

$(document).ready(function(){
    $("#addDiscoModal").agregarDiscoForm();
    $("#discos").albums({
        model:"#models>.disco"
    });
    
    $("#discView").albumShow({
        pistaModel:"#models>.pista"
    });
    $(document).bind("albumSeleccionado",function(){
       $("#discList").hide(500);
    });
    $(document).bind("albumGuardado",function(){
        
        
         $(".addDiscoButton").hide();
         setTimeout(function(){
             $(".addDiscoButton").show();
         },600000)
        
       $("#discos").albums({
            model:"#models>.disco"
        });
        
    });
    $(document).bind("albumDesSeleccionado",function(){
       $("#discList").show(500);
       $("#discos").albums({
            model:"#models>.disco"
        });
    });
});
