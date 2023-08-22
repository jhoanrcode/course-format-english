// FUNCIONES JAVASCRIPT
function slide_category() { 
    let elementsSelect = document.querySelectorAll('.modal-sections .list-activity--h5p .edit-activity .slide .custom-select');

    for (let i = 0; i < elementsSelect.length; i += 1) {
        elementsSelect[i].onchange = () => {
            let elementSelect = elementsSelect[i];
            let id_instance_hvp = elementSelect.dataset.hvp;
            let id_slide = elementSelect.dataset.slide;
            let category_selected = elementSelect.value;

            $.ajax({
                url:'format/uniminuto_english/methods/category_slide_hvp.php',
                data: {
                    opcn: 'update',
                    id_hvp: id_instance_hvp,
                    id_slide_category: id_slide,
                    category: category_selected
                },
                type: 'post',
                dataType: 'json',
            })
            .done(function(data) {
                if (data.state) { console.log("Category saved"); } 
                else { console.log("Category missing"); }
            });

        }
    }

}


//FUNCIONES DESDE JQUERY
$(document).ready(function(){

    slide_category(); //Agregamos evento a todos los select category para autoguardar registros
    
    $('.link-seccion').on('click', function(){
        $('#carouselSectionControls').carousel($(this).data('slide-to'));
        $('#carouselRecourseControls').carousel($(this).data('slide-to'));
    });

});
