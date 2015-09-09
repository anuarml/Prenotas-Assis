"use strict"
asl.options(null);
asl.title('Login');
asl.back(null);

asl.events.subscribe(asl.events.types.exit, function() {
    asl.badge(null);
    if (typeof asl.lock == 'function'){
        asl.lock(null);
    }
});

var username = null;
var newUser;

document.getElementById('txt_username').onchange = function(e){
    requestUser(this.value);
};

function requestUser(txtUserVal){ 
    //var oTxtUsername = document.getElementById('txt_username');
    //username = oTxtUsername.value; 
    username = txtUserVal;
    var url = 'php/db/get_user.php?login='+username;

    if(!username){
        //asl.notify(asl.notifications.application,asl.priority.low,'Mensaje','Ingresa un usuario.',['Ok'],[null]);
        alert('Ingresa un usuario.');
        return;
    }

    var handle = function(data){
        try{
            var response = JSON.parse(data);

            if(!response){
                throw 'No hubo respuesta del servidor.';
            }

            if(response.status == 'success'){

                newUser = new User(response.data);

                if(productos.length > 0 && user && user.ID != newUser.ID){
                    //asl.notify(asl.notifications.application,asl.priority.low,'Existe una prenota del usuario '+user.Name,'Si inicias sesión se borrará la prenota.',['Iniciar sesión','Cancelar'],[deleteLastPrenote,null]);
                    confirm('Existe una prenota del usuario '+user.Name+', si inicias sesión se borrará la prenota. ¿Continuar?',function(answer){
                        if(answer){
                            deleteLastPrenote();
                        }
                    });
                }
                else {
                    login();
                }
            }
            else{
                //keyboardIsShowed = false;
                //asl.notify(asl.notifications.application,asl.priority.low,'Mensaje:',response.data,['OK'],[null]);
                alert(response.data);
            }
        }
        catch(e){
            //asl.notify(asl.notifications.application,asl.priority.low,'Error en el servidor:',data,['OK'],[null]);
            alert(data);
        }
    };
    ajaxRequest('GET', url, handle);
    
}

function login(){
    asl.badge(cfg.badge);
    if (typeof asl.lock == 'function'){
        asl.lock(cfg.badge);
    }
    window.localStorage.setItem('user', JSON.stringify(newUser));
    window.location = 'scan_product.html';
}

function deleteLastPrenote() {
    window.localStorage.removeItem('products');
    window.localStorage.removeItem('bhasName');
    window.localStorage.removeItem('sclientName');
    productos.length = 0;
    login();
}
