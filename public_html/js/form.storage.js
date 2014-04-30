/**
 * Локальное хранилище для данных ввода в формы
 * @author (автор): Stanislav WEB | Lugansk <stanisov@gmail.com>
 * @filesource (Файл): /public/js/form.storage.js
 * @since Browsers Support:
 *  - Internet Explorer 8+
 *  - Mozilla Firefox 3.6+
 *  - Opera 10.5+
 *  - Safari 4+
 *  - iPhone Safari
 *  - Android Web Browser
 */

if(window.localStorage)
{
    // let's get started! Check if the Storage is available
    console.log('Storage Init...');

    var OBJECTS = $('[data-storage]'); // add this attribute for any input / textarea tags data-storage="KEY"

    var STORAGE =   {
        'set'       :   function()
                        {
                            if(OBJECTS.length > 0)
                            {
                                console.log('ok');
                                OBJECTS.focusout(function(){
                                    console.log($(this).val());
                                    window.localStorage.setItem($(this).data('storage'), $(this).val());
                                });
                            }
                            return this;
                        },
        'get'       :   function()
                        {
                            if(OBJECTS.length > 0)
                            {
                                OBJECTS.each(function(){
                                    $(this).val(window.localStorage.getItem($(this).data('storage')));
                                });
                            }
                            return this;
                        },
        'init'       :  function()
                        {
                            this.set(); // setup all input / texarea wich have an attribute data-storage
                            this.get(); // get all storge data into elements text / texarea
                            return this;
                        },
        'remove'    :   function(key)
                        {
                            window.localStorage.removeItem(key); // remove the key from a storage
                            return this;
                        },
        'clear'       : function()
                        {
                            window.localStorage.clear(); // remove all items
                            return this;
                        }
    }
    STORAGE.init(); // Start WEB Storage
}
