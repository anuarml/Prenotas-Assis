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
				this.list.children[j].children[0].children[1].innerHTML = '<div>$' + t[i].Price + '</div> <div>Cant: ' + t[i].Quantity + '</div>';
				
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
        var total = 0;

        for (var i = 0; i < productos.length; i++) {
            total += productos[i].Price * productos[i].Quantity;
        }

        return total;
    },
	
	cantidadTotal: function(){
		var total = 0;
		
		for (var i = 0; i < productos.length; i++) {
			total += productos[i].Quantity;	
		}
		
		return total;
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
});

asl.events.subscribe(asl.events.types.loaded, function() {
    Prenota.init();

    selectedPrinter = JSON.parse(window.localStorage.getItem('selectedPrinter'));

    if(selectedPrinter !== null){
        window.localStorage.removeItem('selectedPrinter');
        asl.notify(asl.notifications.application,asl.priority.normal,'Mensaje:','¿Seguro que desea imprimir la prenota?',['SI','NO'],[save,null]);
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
            asl.notify(asl.notifications.application,asl.priority.normal,'Mensaje:','¿Seguro que desea eliminar la prenota?',['SI','NO'],[delete_prenote,null]);
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
				product: productos,
				narticles: nCant,
				terminal : terminal.number,
				employeeName : user.Name,
				printer : selectedPrinter,
				cotizationNumber : ncotizationNumber,
				clientName : sclientName
				
		});
		
		json_prenote = JSON.stringify(gprenota);
		//window.localStorage.setItem('lastPrenote', json_prenote);
	}else{
		json_prenote = window.localStorage.getItem('lastPrenote');
	}
	
	 save_prenote();
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
                var aResponse = JSON.parse(response);

                var saved = aResponse[0];
                var printed = aResponse[1];
                var prenote = aResponse[2];
					
                if(!saved){
                    asl.notify(asl.notifications.application,asl.priority.normal,'Error:','No se pudo guardar el ticket.',['OK'],[null]);
                }
                else if(!printed){
                    asl.notify(asl.notifications.application,asl.priority.normal,'Error:','No se pudo imprimir el ticket.',['OK'],[null]);
                }
                else if(typeof(prenote) == 'object'){
                    asl.notify(asl.notifications.application,asl.priority.normal,'Mensaje:','Ticket impreso con folio: '+ prenote.folio,['OK'],[null]);
                    
                    if(window.localStorage.getItem('nueva_prenota')){
                        var jPrenote = JSON.stringify(prenote);
                        window.localStorage.setItem('lastPrenote', jPrenote);
                        delete_prenote();
                    }
                }
            }
            catch(e){
                asl.notify(asl.notifications.application,asl.priority.normal,'Error en el servidor: ',response,['OK'],[null]);
            }
        }
    }

    // Llamar al web service del lado del servidor.
    xmlhttp.open("POST","php/db/save_ticket.php",true);
    xmlhttp.setRequestHeader("Content-Type","application/x-www-form-urlencoded charset=utf-8");
    xmlhttp.send("prenote=" +json_prenote);
}
