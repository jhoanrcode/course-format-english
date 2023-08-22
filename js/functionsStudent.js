// FUNCIONES JAVASCRIPT

//Avatar Menu Profile
function menu_profile(){
    if( document.querySelector('#usernavigation .usermenu') ){ // Copiamos el elemento original
        if ( document.querySelector('#usernavigation .usermenu img.userpicture') ) { // Si el usuario cuenta con imagen de perfil
            //Cambiamos la ruta de la imagen de perfil
            let path_img = (document.querySelector('#usernavigation .usermenu img.userpicture')).getAttribute("src"); 
            (document.querySelector('#usernavigation .usermenu img.userpicture')).setAttribute("src", path_img.replace("/f2?", "/f1?"));
            //Removemos atributos del elemento img
            (document.querySelector('#usernavigation .usermenu img.userpicture')).removeAttribute("width");
            (document.querySelector('#usernavigation .usermenu img.userpicture')).removeAttribute("height");
        }
        //Agregamos el nuevo menu de perfil
        document.getElementById('english-usernavigation').innerHTML = document.querySelector('#usernavigation .usermenu').outerHTML; 
        document.querySelector('#usernavigation .usermenu').remove();
    }
}

//Mostrar modal
function abrir_modal( url_frame, origin_action ){
    document.querySelector(".uniminuto-english-format .modal-units #urlActivity").style = "opacity: 0;";
    document.querySelector(".uniminuto-english-format .modal-units #urlActivity").src = url_frame;
    document.querySelector(".uniminuto-english-format .modal-units #openWindowActivity").onclick = function(){ abrir_ventana(url_frame) };
    document.querySelector(".uniminuto-english-format .modal-units #closeWindowActivity").onclick = function(){ cerrar_modal(origin_action) };

    document.getElementById("sectionsUnits").classList.add("show")
    window.setTimeout( function() { document.getElementById("sectionsUnits").classList.add("show--animation"); }, 250);
    let elementSombra = '<div class="modal-backdrop fade show" id="modalBackdrop"></div>';
    (document.querySelector('#region-main')).insertAdjacentHTML('afterend', elementSombra);
}

//Cerrar y limpiar modal
function cerrar_modal( origin_action ){
    document.getElementById("modalBackdrop").remove();
    document.getElementById("sectionsUnits").classList.remove("show");
    document.getElementById("sectionsUnits").classList.remove("show--animation");
    document.querySelector(".uniminuto-english-format .modal-units .modal-content").style.setProperty('--radius', '0');
    if( origin_action == "units" ){ location.reload(); } //Refrescamos la pagina actual
}

//Abrir link en nueva ventana
function abrir_ventana(url){ window.open(url); }

//Agregamos un loader mientras carga las URL en frame
function preload_frame(){ 
    let elementIFrame = document.querySelector('.uniminuto-english-format .modal-units #urlActivity');
    elementIFrame.onload = function() { 
        if(elementIFrame.contentWindow.location.href != 'about:blank'){
            limpiar_elementos_frame( elementIFrame );
            setTimeout(function(){ 
                if(window.innerWidth > 922){ elementIFrame.style = "opacity: 1;min-height: 519px;"; }
                else {elementIFrame.style = "opacity: 1;min-height: 341px;"; }
            },500);
        }
    }
}

//Limpiar elementos de frame en ventana modal
function limpiar_elementos_frame( clear_iFrame ){
    var frame = clear_iFrame.contentWindow.document; console.log( 'limpiar_elementos_frame' );
    
    if( frame.querySelector('footer') ){ frame.querySelector('footer').style.display = "none"; }
    if( frame.querySelectorAll('aside').length > 0 ){ frame.querySelectorAll('aside').forEach(componente => { componente.style.display = "none" }); }
    if( frame.getElementsByClassName('drawer-toggler drawer-left-toggle open-nav d-print-none').length > 0 ){ frame.getElementsByClassName('drawer-toggler drawer-left-toggle open-nav d-print-none')[0].style.display = "none"; }
    if( frame.getElementsByClassName('drawer-toggler drawer-right-toggle ml-auto d-print-none').length > 0 ){ frame.getElementsByClassName('drawer-toggler drawer-right-toggle ml-auto d-print-none')[0].style.display = "none"; }
    if( frame.querySelector('[data-region=right-hand-drawer].drawer') ){ frame.querySelector('[data-region=right-hand-drawer].drawer').style.display = "none"; }
    if( frame.querySelector('#theme_boost-drawers-courseindex') ){ frame.querySelector('#theme_boost-drawers-courseindex').style.display = "none"; }
    if( frame.querySelector('#page.drawers') ){ frame.querySelector('#page.drawers').style = "padding: 0;margin: 0;"; } 
    if( frame.getElementById('page-header') ){ frame.getElementById('page-header').style.display = "none"; } 
    if( frame.querySelector('.secondary-navigation') ){ frame.getElementsByClassName('secondary-navigation')[0].style.display = "none";  }
    if( frame.querySelector('.activity-navigation') ){ frame.getElementsByClassName('activity-navigation')[0].style.display = "none"; }
    if( frame.querySelector('#region-main #maincontent') ){ frame.querySelector('#region-main #maincontent').style.display = "none"; }

    if ( frame.querySelector('.new-height') ) { //Si el New Theme Uniminuto esta aplicado
        frame.querySelector('.new-height .custom-header').style.display = "none";
        if( frame.getElementById('modalRecourse') ){ frame.getElementById('modalRecourse').style.display = "none"; }
        if( frame.getElementById('print_html_page') ){ frame.getElementById('print_html_page').style.display = "none"; }
    } else { //Si el Theme Boost esta aplicado
        if( frame.querySelector('.navbar.fixed-top') ){ frame.querySelector('.navbar.fixed-top').style.display = "none"; }
    }

    //Limpieza de Frame segun actividad em ventana modal desplegada
    if( (clear_iFrame.contentWindow.location.pathname).indexOf("mod/hvp") !== -1 || (clear_iFrame.contentWindow.location.pathname).indexOf("mod/h5pactivity") !== -1){
        console.log( 'limpiar_elementos_frame H5P' ); 
        if( frame.getElementById('topofscroll') ){ frame.getElementById('topofscroll').style = "padding: 0;margin: 0;background-color: transparent;"; }
        if( frame.getElementById('region-main') ){ frame.querySelector('#region-main').style = "padding: 0; box-shadow: none;"; }
        if( frame.querySelector('#page.drawers .activity-header') ){ frame.querySelector('#page.drawers .activity-header').style.display = "none"; }
        if( frame.querySelector('#page-wrapper #page #page-content') ){ frame.querySelector('#page-wrapper #page #page-content').style = "padding: 0 !important;"; }
        if( frame.querySelector('#page.drawers div[role="main"]') ){ frame.querySelector('#page.drawers div[role="main"]').style = "padding: 0;"; }
    } else {
        console.log( 'limpiar_elementos_frame Recourse' ); 
        if( frame.querySelector('#page.drawers') ){ frame.querySelector('#page.drawers').style = "padding-left: 1rem;padding-right: 1rem;margin: 0;"; }
        if( frame.getElementById('topofscroll') ){ frame.getElementById('topofscroll').style = "padding: 1.5rem 0;margin: 0 auto;background-color: transparent;"; }
        if( frame.querySelector('#topofscroll .tertiary-navigation') ){ frame.querySelector('#topofscroll .tertiary-navigation').style.display = "none"; }
        if ( frame.querySelector('.activity-header') ){ frame.getElementsByClassName('activity-header')[0].style = "margin-left: 0;margin-right: 0;"; }
    }

}

//Calcular ancho y alto de pantalla
function sizeVideoWelcome(){
    if (window.innerHeight > 690) { elementHeight = (window.innerHeight - 355); }
    else { elementHeight = (window.innerHeight - 315); }
    elementWidth = (elementHeight * 1.351);
    elementBorderWidth = ((elementWidth*64)/1000)/2;
    frameWidth = elementWidth - (elementBorderWidth*2);
    document.querySelector(".uniminuto-english-format .background-video-frame .img-frame").style.setProperty('--hmax', elementHeight+'px');
    document.querySelector(".uniminuto-english-format .background-video-frame .embed-responsive").style.setProperty('--wmax', frameWidth+'px');
    document.querySelector(".uniminuto-english-format .background-video-frame .embed-responsive").style.setProperty('--bordermax', elementBorderWidth+'px');
}
function onWindowResize() { sizeVideoWelcome(); }
window.addEventListener('resize', onWindowResize);

//FUNCIONES DESDE JQUERY
$(document).ready(function(){

    sizeVideoWelcome(); //Resize imagen background para iFrameVideo
    preload_frame(); //Preload Frame Activity
    menu_profile(); //Menu Profile User

    //Agregar clase Active en links de Menu Header
    $('.english-header__bottom .navbar-nav .nav-item .nav-link').on('click', function(){
        $( ".english-header__bottom .navbar-nav .nav-item .nav-link" ).removeClass( "active" );
        $( ".tab-content .tab-pane" ).removeClass( "active" );
        $(this).addClass( "active" );
        $( '.tab-content #'+$(this).data( "target" ) ).addClass( "active" );
        if ( $(this).data( "target" ) !== "tab0" ) { $( ".english-header" ).addClass( "hide-half" ); }
        else { $( ".english-header" ).removeClass( "hide-half" ); }
    });

    //Presenta el recurso escogido en el Menu Recursos en la modal
    $('.container-menu .menu-resources__item--section').on('click', function(){
        $('#carouselSectionControls2').carousel($(this).data('recourse'));
    });

    //Presenta el iframe (Participantes o Calificaciones) desde Menu Recursos en la modal
    $('.container-menu .menu-resources__item--course').on('click', function(){
        let url_recourse_menu = $(this).data( "openmodal" );
        animation_loader_modal( url_recourse_menu, "recourse" ); //Activa animacion para abrir modal
    });
    
    //Agregar clase Move para activar animaciones de carga en actividades units 
    $('.container-units .unit .unit__detail').on('click', function(){

        let unit = $(this).parent();
        let url_activity_unit = $(this).data( "openmodal" );

        unit.addClass( "active" ); //Unidad se presenta por encima de todas
        $( ".container-units .activity .unit__category--progress" ).removeClass( "active" ); //Oculta los contenedores Category activados
        unit.parent().addClass( "move" ); //Activar animacion unidades

        animation_loader_modal( url_activity_unit, "units", unit );  //Activa animacion para abrir modal

    });
    
    //Agregar clase Active en boton Category
    $('.container-units .activity .unit__category').on('click', function(){
        $( ".container-units .activity .unit__category--progress" ).removeClass( "active" );
        $(this).next().addClass( "active" );
    });
    
    //Remover clase Active en boton Category
    $('.container-units .activity .unit__category--progress .close-category').on('click', function(){
        $(this).parent().parent().removeClass( "active" );
    });

    //Animacion para cargue de iFrames en modal
    function animation_loader_modal( url_frame, origin_action, element_unit = '' ){

        $( ".background--street-car" ).removeClass( "background--street-car--hidden"); //Activa clase animacion Car
        window.setTimeout( function() { //Esperamos para desplegar la modal
            abrir_modal( url_frame, origin_action ); 
        }, 1600); 
        window.setTimeout( function() {  
            $( ".background--street-car" ).addClass( "background--street-car--hidden"); //Ocultamos animacion Car
            if ( origin_action == "units" ) {
                element_unit.parent().removeClass( "move" ); //Quitamos la de animacion unidades
                element_unit.removeClass( "active" ); //Quitamos la unidad se presente por encima de todas
            }
        }, 3600);
        window.setTimeout( function() {  //Esperamos para agregar border radius en modal
            document.querySelector(".uniminuto-english-format .modal-units .modal-content").style.setProperty('--radius', '0.75rem');
        }, 4500);

	}

});