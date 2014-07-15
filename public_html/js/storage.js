/**
 * Storage. Browser Local Storage Manager
 * @author Stanislav WEB | Lugansk <stanisov@gmail.com>
 * @extends jQuery
 * @since Browsers Support:
 *  - Internet Explorer 8+
 *  - Mozilla Firefox 3.6+
 *  - Opera 10.5+
 *  - Safari 4+
 *  - iPhone Safari
 *  - Android Web Browser
 *  
 *  @see 
 *  add to input an attribute data-storage="NAME"
 *  
 */
if(window.localStorage)
{
    // let's get started! Check if the Storage is available

    var STORAGE =   {
        
        'object'    :   $('[data-storage]'),
        
        'set'       :   function()
                        {
                            if(this.object.length > 0)
                            {
                                this.object.focusout(function(){
                                    window.localStorage.setItem($(this).data('storage'), $(this).val());
                                });
                            }
                            return this;
                        },
        'get'       :   function()
                        {
                            if(this.object.length > 0)
                            {
                                this.object.each(function(){
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
