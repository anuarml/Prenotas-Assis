var json_prenote = null;
var Prenota = {
    list: null,
    pagingTop: null,
    pagingBottom: null,
    pagingText: null,
    
    pagingInfo: {
        currentPage: 0,
        totalCount: 0,
        perPage: cfg.itemsPerPage
    },
    
    init: function() {
        this.list = document.getElementById('list');
        this.pagingTop = document.getElementById('paging_top');
        this.pagingBottom = document.getElementById('paging_bottom');
        this.pagingText = document.getElementById('paging_text');
        
        
        this.pagingTop.onclick = function() {
            if(this.className != 'disabled') {
                Prenota.mostrarProductos(Prenota.pagingInfo.currentPage - 1);
            }
        };
        
        this.pagingBottom.onclick = function() {
            if(this.className != 'disabled') {
                Prenota.mostrarProductos(Prenota.pagingInfo.currentPage + 1);
            }
        };
        
        for(j=0; j < this.pagingInfo.perPage; j++) {
            this.list.children[j].children[1].onclick = this.removeProduct;
        }
		
		for(j=0; j < this.pagingInfo.perPage; j++) {
            this.list.children[j].children[0].onclick = this.productDetails;
        }
		
        this.mostrarProductos(0);
    },
    
    mostrarProductos: function(page) {
        var t = productos;

        this.pagingInfo.currentPage = page;
        this.pagingInfo.totalCount = (t.length > 0) ? t.length : 1;
        this.pagingText.innerText =  (page + 1) + '/' + ( Math.ceil(this.pagingInfo.totalCount / this.pagingInfo.perPage) );
        
        for(var j = 0, length = t.length, i = length - 1 - (page * this.pagingInfo.perPage); (i >= 0) && (j < this.pagingInfo.perPage); i--, j++) {
	            this.list.children[j].children[0].children[0].innerHTML = t[i].Description;          
				this.list.children[j].children[0].children[1].innerHTML = '<div>$' + (t[i].Price).toFixed(2) + '</div> <div>Cant: ' + t[i].Quantity + '</div>';
				
                this.list.children[j].children[1].id = i;
				this.list.children[j].children[0].id = i;

	            this.list.children[j].style.setProperty('visibility', 'visible');
	           
        }
    
        if(j < this.pagingInfo.perPage) {
            for(j; j < this.pagingInfo.perPage; j++) {
                this.list.children[j].children[0].children[0].innerHTML = 'Empty';
                this.list.children[j].style.setProperty('visibility', 'hidden');
            }
        }
        
        //Disable the arrow if we reach the end
        if( (page + 1) ==  Math.ceil(this.pagingInfo.totalCount / this.pagingInfo.perPage) ) {
            this.pagingBottom.className = 'disabled';
        }
        else {
            this.pagingBottom.className = 'enabled';
        }
        
        if( page == 0 ) {
            this.pagingTop.className = 'disabled';
        }
        else {
            this.pagingTop.className = 'enabled';
        }
    },

    removeProduct: function(e){
        var element_id = parseInt(e.currentTarget.id);

        productos.splice(element_id,1);
        window.localStorage.setItem('products', JSON.stringify(productos));

        Prenota.mostrarProductos(0);
    },

    calcularTotal: function(){
        var total = new Decimal(0);

        for (var i = 0; i < productos.length; i++) {
            //total += productos[i].Price * productos[i].Quantity;
            var dPrice = new Decimal(productos[i].Price);
            var dAmount = dPrice.times(productos[i].Quantity);

            total = total.plus(dAmount);
        }

        return total.toNumber().toFixed(2);
    },
	
	cantidadTotal: function(){
		var total = new Decimal(0);
        var maxUnitDecimals = 0;
		
		for (var i = 0; i < productos.length; i++) {
			//total += productos[i].Quantity;	
            total = total.plus(productos[i].Quantity);

            if(productos[i].unitDecimals > maxUnitDecimals){
                maxUnitDecimals = productos[i].unitDecimals;
            }
		}
		
		return total.toNumber().toFixed(maxUnitDecimals);
	},

	productDetails: function(e){
		var element_id = parseInt(e.currentTarget.id);
		window.localStorage.setItem('details',element_id);
		window.location = 'product_details.html';
	}
};

asl.title('Prenota');

asl.back(function(){
    window.location = 'scan_product.html';
});

asl.events.subscribe(asl.events.types.exit, function() {
    asl.badge(null);
    if (typeof asl.lock == 'function'){
        asl.lock(null);
    }
});

asl.events.subscribe(asl.events.types.loaded, function() {
    Prenota.init();

    selectedPrinter = JSON.parse(window.localStorage.getItem('selectedPrinter'));

    if(selectedPrinter !== null){
        window.localStorage.removeItem('selectedPrinter');
        //asl.notify(asl.notifications.application,asl.priority.normal,'Mensaje:','¿Seguro que desea imprimir la prenota?',['SI','NO'],[save,null]);
        save();
    }

});

asl.options([
    {
        title: 'Ver total',
        callback: function(){
            alert('Total: $' + Prenota.calcularTotal());
        }
    },
    {
        title: 'Imprimir prenota',
        callback: function(){
			window.localStorage.setItem('nueva_prenota',true);
			window.location = 'printers_list.html';
        }
    },
	{
        title: 'Imprimir última prenota',
        callback: function(){           
			window.localStorage.removeItem('nueva_prenota');
		    window.location = 'printers_list.html';
        }
    },
    {
        title: 'Eliminar prenota',
        callback: function(){
            //asl.notify(asl.notifications.application,asl.priority.normal,'Mensaje:','¿Seguro que desea eliminar la prenota?',['SI','NO'],[delete_prenote,null]);
            confirm('¿Seguro que desea eliminar la prenota?',function(confirmed){
                if(confirmed){
                    delete_prenote();
                }
            });
        }
    },
    {
        title: 'Salir',
        callback: exit
    }
]);

function delete_prenote(){
    window.localStorage.removeItem('products');
	window.localStorage.removeItem('bhasName');
	window.localStorage.removeItem('sclientName');
    productos.length = 0;
    Prenota.mostrarProductos(0);
	
}

function save(){
try{
    var jPrenote = window.localStorage.getItem('prenote');

    if(jPrenote != null){
        window.localStorage.removeItem('prenote');
        json_prenote = jPrenote;
        save_prenote();
        return;
    }
    
	if(window.localStorage.getItem('nueva_prenota')){
		var nTotal = Prenota.calcularTotal();
		var nCant = Prenota.cantidadTotal();
		var ncotizationNumber = JSON.parse(window.localStorage.getItem('nCotizationNumber'));
		var sclientName = JSON.parse(window.localStorage.getItem('sclientName'));
		window.localStorage.removeItem('answerOfCotization');
		window.localStorage.removeItem('nCotizationNumber');

		var gprenota = new oPrenote({
				total: nTotal,
				id_employee: user.ID,
                employeeLogin: user.Login,
				product: productos,
				narticles: nCant,
				//terminal : terminal.number,
				employeeName : user.Name,
				printer : selectedPrinter,
				cotizationNumber : ncotizationNumber,
				clientName : sclientName
		});

		json_prenote = JSON.stringify(gprenota);
		//window.localStorage.setItem('lastPrenote', json_prenote);
	}else{
		json_prenote = window.localStorage.getItem('lastPrenote');

        if(json_prenote != null){
            var oLastPrenote = JSON.parse(json_prenote);
            oLastPrenote.printer = selectedPrinter;
            json_prenote = JSON.stringify(oLastPrenote);
        }
	}
	
	 save_prenote();
    }
    catch(e){
        //asl.notify(asl.notifications.application,asl.priority.normal,e.message,JSON.stringify(e),['OK'],[null]);
        alert(e.message);
    }
}

function exit(){
    confirm('Esta seguro que quiere salir de la aplicación?', function(res){
        try{
            if(res){ 
                asl.exit();
            }
        }
        catch(e){
            alert(e);
        }
    });
}

function save_prenote() { 
    var xmlhttp;

    if (window.XMLHttpRequest) {
        // code for IE7+, Firefox, Chrome, Opera, Safari
        xmlhttp = new XMLHttpRequest();
    } else { // code for IE6, IE5
        xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
    }
    
    xmlhttp.onreadystatechange=function() {
        if (xmlhttp.readyState==4 && xmlhttp.status==200) {

            var response = xmlhttp.responseText.trim();
			//asl.notify(asl.notifications.application,asl.priority.normal,'Error:',response,['OK'],[null]);
            try{
                var oResponse = JSON.parse(response);

                if(!oResponse){
                    //asl.notify(asl.notifications.application,asl.priority.normal,'Mensaje:','No se obtuvo respuesta del servidor.',['OK'],[null]);
                    alert('No se obtuvo respuesta del servidor.');
                    return;
                }

                //var aResponse = JSON.parse(response);
                var status = oResponse.status;
                var message = oResponse.message || '';

                var saved = status.saved;
                var printed = status.printed;
                var validClient = status.validClient;
                //var serverPrenote = status.prenote;
					
                if(!saved){
                    //asl.notify(asl.notifications.application,asl.priority.normal,'No se pudo guardar el ticket',message,['OK'],[null]);
                    alert('No se pudo guardar el ticket: '+message);
                    return;
                }

                prenote = new oPrenote(status.prenote || {});

                if(!validClient){
                    prenote.changeClient = true;
                    window.localStorage.setItem('prenote', JSON.stringify(prenote));
                    //asl.notify(asl.notifications.application,asl.priority.normal,'Mensaje','Ya existe una prenota con el cliente: \''+prenote.clientName+'\'.',['Cambiar'],[askClientName]);
                    confirm(message, function(confirmed){
                        if(confirmed){
                            askClientName();
                        }
                    });
                    return;
                }
                
                if(!printed){
                    window.localStorage.setItem('prenote', JSON.stringify(prenote));

                    //asl.notify(asl.notifications.application,asl.priority.normal,'No se pudo imprimir el ticket.','',['OK'],[null]);
                    alert('No se pudo imprimir el ticket.');
                    return;
                }
                
                if(typeof(prenote) == 'object'){

                    window.localStorage.removeItem('prenote');
                    //asl.notify(asl.notifications.application,asl.priority.normal,'Mensaje:','Ticket impreso con folio: '+ prenote.folio,['OK'],[null]);
                    alert('Ticket impreso con folio: '+ prenote.folio);
                    
                    if(window.localStorage.getItem('nueva_prenota')){
                        var jPrenote = JSON.stringify(prenote);
                        window.localStorage.setItem('lastPrenote', jPrenote);
                        delete_prenote();
                    }
                }
            }
            catch(e){
                //asl.notify(asl.notifications.application,asl.priority.normal,'Error en el servidor: ',response,['OK'],[null]);
                alert('Error: '+ response);
            }
        }
    }

    // Llamar al web service del lado del servidor.
    xmlhttp.open("POST","php/db/save_ticket.php",true);
    xmlhttp.setRequestHeader("Content-Type","application/x-www-form-urlencoded charset=utf-8");
    xmlhttp.send("prenote=" +json_prenote);
}

function saveName(inputId, value){
    var clientName;
    clientName = value;
    hasName = true;
    var jclientName = JSON.stringify(clientName);
    var jhasName = JSON.stringify(hasName);
    //asl.notify(asl.notifications.application,asl.priority.normal,'Mensaje:','Client guardado.',['OK'],[null]);
    window.localStorage.setItem('sclientName', jclientName);
    window.localStorage.setItem('bhasName', jhasName);
    
    prenote.clientName = clientName;
    prenote.changeClient = true;

    json_prenote = JSON.stringify(prenote);

    //window.localStorage.setItem('prenote', json_prenote);

    save_prenote();
}

function askClientName(){
    asl.showKeyboard({
        inputId: 'askClientName',
        title : "Ingresa el cliente.",
        type : 'text',
        scanner: true,
        back: false
    }, saveName );
}
