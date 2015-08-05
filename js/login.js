//asl.events.subscribe('onloaded', authorize_user);
//asl.events.subscribe(asl.events.types.focus, showKBonFocus);
//var keyboardIsShowed = false;

			
/*function authorize_user() {
    if(!keyboardIsShowed) {
        keyboardIsShowed = true;
        asl.showKeyboard({
            inputId: 'user_login',
            title : "Ingresa usuario",
            type : 'text',
            scanner: true,
            back: false
        }, request_user);
    }
}*/
"use strict"
asl.options(null);
asl.title('Login');
asl.back(null);

var username = null;
var newUser;

function requestUser(){ 
    var oTxtUsername = document.getElementById('txt_username');
    username = oTxtUsername.value; 
    var url = 'php/db/get_user.php?login='+username;

    if(!username){
        asl.notify(asl.notifications.application,asl.priority.normal,'Mensaje','Ingresa un usuario.',['Ok'],[null]);
        return;
    }

    var handle = function(response){
        try{
            var userInfo = JSON.parse(response);

            if(userInfo){
                newUser = new User(userInfo);

                if(productos.length > 0 && user && user.ID != newUser.ID){
                    asl.notify(asl.notifications.application,asl.priority.normal,'Existe una prenota del usuario '+user.Name,'Si inicias sesión se borrará la prenota.',['Iniciar sesión','Cancelar'],[deleteLastPrenote,null]);
                }
                else {
                    login();
                }
                //asl.badge('http://192.168.96.3/LeeWs/scan_products/badge.html');
                //asl.badge('http://192.168.96.3/LeeWs/DesarrolloPrenotas/scan_products/badge.html');

            }else{
                //keyboardIsShowed = false;
                asl.notify(asl.notifications.application,asl.priority.normal,'Mensaje:','Usuario no registrado.',['OK'],[null]);
            }
        }
        catch(e){
            asl.notify(asl.notifications.application,asl.priority.normal,'Error en el servidor:',response,['OK'],[null]);
        }
    };
    ajaxRequest('GET', url, handle);
    
}

function login(){
    asl.badge(cfg.badge);
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

/*function request_user(inputId, userID){

    if (!userID) {
        keyboardIsShowed = false;
        authorize_user();
        return;
    } 
    var url = 'php/db/get_user.php?login='+userID;

    var handle = function(response){
        try{
            var userInfo = JSON.parse(response);

            if(userInfo){
                user = new User(userInfo);

                //asl.badge('http://192.168.96.3/LeeWs/scan_products/badge.html');
				//asl.badge('http://192.168.96.3/LeeWs/DesarrolloPrenotas/scan_products/badge.html');
                asl.badge(cfg.badge);
                window.localStorage.setItem('user', JSON.stringify(user));
                window.location = 'scan_product.html';
            }else{
                keyboardIsShowed = false;
                asl.notify(asl.notifications.application,asl.priority.normal,'Mensaje:','Usuario no registrado.',['OK'],[authorize_user]);
            }
        }
        catch(e){
            asl.notify(asl.notifications.application,asl.priority.normal,'Error en el servidor:',response,['OK'],[authorize_user]);
        }
    };

    ajaxRequest('GET', url, handle);
}*/

/*function showKBonFocus(){
	if(keyboardIsShowed){
		keyboardIsShowed = false;
		authorize_user();
	}
}*/