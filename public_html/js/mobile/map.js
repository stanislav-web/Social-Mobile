/**
 * Инициализация GoogleMap карты
 * @author Stanislav WEB | Lugansk <stanisov@gmail.com>
 * @filesource /public/js/mobile/map.js
 * @since Browsers support *
 */


$('#mapArea').on('pageinit', function(event){
    
    console.log('init Map');
    /**
     * объект карты Google
     * @type object Google Map 
     */
    var MAP = {
        // странца с картой
        section :   $(this),
        // странца с картой
        search  :   $('#locality'),
        // карта
        canvas  :   $('#map_canvas'),
        // Текущая позиция
        current : { 'center': '1,1', 'zoom': 2},
    };
    
    // инициализирую карту c автокомплитом
    
    MAP.canvas.gmap(MAP.current);    
    
    // инициализирую автопоиск
    MAP.canvas.gmap('autocomplete', MAP.search, function(ui) {
        console.log(ui);
        console.log(ui.item.position);
    });    
        
    // обновляю карту
    MAP.section.on("pageshow", function() {
        MAP.canvas.gmap('refresh');

    });
});