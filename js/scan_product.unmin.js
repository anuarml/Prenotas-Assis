var product = null;
//var aCombinations = null;
var aItemUnits = null;
var hasName = null;
var keyboardIsShowed = false;
var aslOptionList = [
	{
		title : 'Ver prenota',
		callback: function(){
			window.localStorage.setItem('aItemUnits', JSON.stringify(aItemUnits));
			window.localStorage.setItem('product', JSON.stringify(product));
			window.location = 'prenota.html';
		}
	},
	/*{
		title : 'Descripción de opciones',
		callback: function(){
			if(product && product.optionID){
				requestCombinationDesc(product.optionID);
			}
			else{
				asl.notify(asl.notifications.application, asl.priority.normal,'Mensaje' ,'El artículo no tiene opciones asignadas.',['OK'],[null]);
			}
		}
	},*/
	{
		title:'Detalles',
		callback: function(){
			if(product){
    			window.localStorage.setItem('aItemUnits', JSON.stringify(aItemUnits));
				window.localStorage.setItem('product', JSON.stringify(product));
    			window.location = 'scan_product_detail.html';
    		}else{
    			alert('Escanea un producto.');
    		}
		}
	},
	{
		title: 'Salir',
		callback: function(){
			asl.exit();
		}
	}
];

asl.title(' ');
asl.back(scan_product);
asl.options(aslOptionList);

asl.events.subscribe(asl.events.types.loaded, function() {
	/*document.getElementById('txt_opcion').onfocus = function(){
		scanner.enable();
	};*/
	document.getElementById('txt_serie').onfocus = function(){
		scanner.enable();
	};

	txt_serie.onchange = function(){ 	
		serial_assign(txt_serie.value);				
	};

	/*txt_opcion.onchange = function(){
		combination_assign(txt_opcion.value);				
	};*/

	document.getElementById('p_opcion').onclick = function(){
		showCombinationList();
	};

	document.getElementById('p_serie').onclick = function(){
		showSerialList();
	};

	document.getElementById('txt_opcion').onclick =  function(){
		if(product && product.optionUUID && product.optionDesc){
			//requestCombinationDesc(product.optionUUID);
			//asl.notify(asl.notifications.application, asl.priority.normal,'Opciones' ,product.optionDesc,['OK'],[null]);
			alert('Opciones: '+product.optionDesc);
		}
		else{
			//asl.notify(asl.notifications.application, asl.priority.normal,'Mensaje' ,'El artículo no tiene opciones asignadas.',['OK'],[null]);
			alert('El artículo no tiene opciones asignadas.');
		}
	};

	document.getElementById('txt_cant').onchange = function(){
		checkNumberOfDecimals();
	};

	//aCombinations = JSON.parse(window.localStorage.getItem('aCombinations'));
	aItemUnits = JSON.parse(window.localStorage.getItem('aItemUnits'));

	var jProduct = JSON.parse(window.localStorage.getItem('product'));

	if(jProduct!=null) window.localStorage.removeItem('product');

    /*if(aCombinations != null){
        window.localStorage.removeItem('aCombinations');

        if(aCombinations.length>1){
        	document.getElementById('p_opcion').onclick = function(){
				showCombinationList();
			};
        }
	}*/

	if(aItemUnits != null){
        window.localStorage.removeItem('aItemUnits');

        if(aItemUnits.length > 1){
        	document.getElementById('dv_unit').onclick = function(){ showUnitList();};
        	document.getElementById('dv_unitl').onclick = function(){ showUnitList();};
        }
	}

	var aOptionDetail = JSON.parse( window.localStorage.getItem('aOptionDetail'));

    if (aOptionDetail != null) {
        window.localStorage.removeItem('aOptionDetail');

        var optionDetailLength = aOptionDetail.length;
        if ( optionDetailLength > 0 ) {

	        //var jProduct = JSON.parse(window.localStorage.getItem('product'));

			if (jProduct!=null) {
				//window.localStorage.removeItem('product');

				var aParams = [];
				
				for (var i = 0; i < optionDetailLength; i++) {

					aParams.push('Detail'+i+'='+aOptionDetail[i]);
				}

				var sParams = aParams.join('&');

				var combExternalID = JSON.parse( window.localStorage.getItem('combExternalID'));
				
				if (combExternalID != null) {
        			window.localStorage.removeItem('combExternalID');
        		}
        		else{
        			combExternalID = '';
        		}

				requestItemCombination(sParams, jProduct.ID, combExternalID);

				//show_product_info(jProduct);
			}
		}
    }

    var nSelectedUnit = JSON.parse(window.localStorage.getItem('nUnitSelected'));
	/*var PreviousSelected = JSON.parse(window.localStorage.getItem('PreviousSelected'));;
	
	
	var jProduct = JSON.parse(window.localStorage.getItem('product'));
	if(jProduct){

		jProduct.UnitID = oUnit.unitID;
		jProduct.unitName = oUnit.name;
		jProduct.unitFactor = oUnit.factor;

	}
	
	window.localStorage.setItem('PreviousSelected', JSON.stringify(jProduct));
	
	asl.notify(asl.notifications.application, asl.priority.normal,'Mensaje:' ,'Producto ' + PreviousSelected.unitName,['OK'],[null]);*/

	if(nSelectedUnit != null){
        window.localStorage.removeItem('nUnitSelected');

        //var jProduct = JSON.parse(window.localStorage.getItem('product'));
		//if(jProduct!=null) window.localStorage.removeItem('product');

        var oUnit = aItemUnits[nSelectedUnit];

        if(oUnit && jProduct){
			var productPrice = new Decimal(jProduct.basePrice);
			jProduct.UnitID = oUnit.unitID;
			jProduct.unitName = oUnit.name;
			jProduct.unitFactor = oUnit.factor;
			jProduct.unitDecimals = oUnit.decimals;

			jProduct.Quantity = 1;
			//jProduct.Price = productPrice.times(oUnit.factor).toNumber();
			//jProduct.Price = oUnit.factor * jProduct.basePrice;
			//show_product_info(jProduct);
		}
    }


    var oSelectedSerial = JSON.parse(window.localStorage.getItem('oSelectedSerial'));

    if(oSelectedSerial != null){
        window.localStorage.removeItem('oSelectedSerial');

        if(jProduct){
			jProduct.serialBatch = oSelectedSerial.SerialBatch;
			jProduct.serialQuantity = oSelectedSerial.Quantity;
			jProduct.SerialID = oSelectedSerial.ID;
		}
    }

    if (jProduct!=null) {
    	show_product_info(jProduct);
    }
    
});

//asl.events.subscribe(asl.events.types.loaded, askClientName);

asl.events.subscribe(asl.events.types.loaded, function() {
	verifyWhichKeyboard();
});

asl.events.subscribe(asl.events.types.focus, function() {
	scanner.disable();
});

asl.events.subscribe(asl.events.types.exit, function() {
	asl.badge(null);
	if (typeof asl.lock == 'function'){
		asl.lock(null);
	}
});

function verifyWhichKeyboard(){
	hasName = JSON.parse(window.localStorage.getItem('bhasName'));
	if(!hasName){
		askClientName();
	}else{
		if(!product){
    		scan_product();
    	}
	}
}

function showKBonFocus(){
	if(keyboardIsShowed){
		keyboardIsShowed = false;
		verifyWhichKeyboard();
	}
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
	if(!product){
    	scan_product();
    }
}

function askClientName(){
	asl.showKeyboard({
        inputId: 'askClientName',
        title : "Ingresa el cliente.",
        type : 'text',
        scanner: true,
        back: true
    }, saveName );
}

function scan_product() {
    asl.showKeyboard({
        inputId: 'scan_product',
        title : "Escanea un producto.",
        type : 'text',
        scanner: true,
        back: true
    }, request_product );
}

// Pide la información de un producto a la base de datos, si encuentra el producto lo muestra.
// Entrada: Código de barras o código de artículo.
function request_product(inputId, value) {
	
	if (!value) {
		scan_product();
		return;
	} 
	
	var url = 'php/db/get_product2.php?code=' + value;

	var handle = function(response){
		try{
			var oResponse = JSON.parse(response);

			var productInfo = null;

			if(!oResponse){
				clear_product();
				throw 'No hubo respuesta del servidor';
			}

			if(oResponse.status == true){
				productInfo = oResponse.data;
				show_product_info(productInfo);
			}
			else {
				//asl.notify(asl.notifications.application, asl.priority.normal,'Error:' , oResponse.data,['OK'],[scan_product]);
				confirm(oResponse.data,function(){
					clear_product();
					scan_product();
				});
			}
		}
		catch(e){
			clear_product();
			//asl.notify(asl.no tifications.application, asl.priority.normal,'Error en el servidor:' ,response,['OK'],[null]);
			alert('Error: '+response);
		}
	};

	ajaxRequest('GET', url, handle);
}

// Muestra la información de un producto en la pantalla.
function show_product_info(productInfo){
	clear_product();
	if(productInfo){

		product = new Product(productInfo);

		if(product.barcode){
			product.scanCode = product.barcode;
		}
		else {
			product.scanCode = product.Code;
		}

		asl.title(product.scanCode);

		requestItemPrice(product.ID, product.UnitID, product.optionID);
		
		document.getElementById('p_description').innerHTML = product.Description;
		//document.getElementById('p_price').innerHTML = '$' + product.Price;
		document.getElementById('txt_cant').value = product.Quantity;

		if ( (product.ItemTypeID == ItemType.SERIE || product.ItemTypeID == ItemType.LOTE) &&
		    product.UseCombination == 1 && !product.optionUUID) {

			lockSerialInput();
		}

		if(product.UseCombination == 1){
			p_opcion.style.visibility = 'visible';
			i_opcion.style.visibility = 'visible';

			var txtOption = document.getElementById('txt_opcion');
			
			if(product.optionEID){
				//txtOption.value = product.optionEID;
				txtOption.innerHTML = product.optionDesc;
			}

			/*if(product.options && product.options.length){
				txtOption.disabled = false;
			}
			else {
				txtOption.disabled = true;
			}*/
		}
		else{
			p_opcion.style.visibility = 'hidden';
			i_opcion.style.visibility = 'hidden';
		}

		if(product.ItemTypeID == ItemType.SERIE){
			blockQuantityField();

			p_serie.innerHTML = 'Serie:';
			txt_serie.title = 'Serie';

			p_serie.style.visibility = 'visible';
			i_serie.style.visibility = 'visible';
			if(product.serialBatch){
				document.getElementById('txt_serie').value = product.serialBatch;
			}
		}
		else if(product.ItemTypeID == ItemType.LOTE){
			unblockQuantityField();

			p_serie.innerHTML = 'Lote:';
			txt_serie.title = 'Lote';

			p_serie.style.visibility = 'visible';
			i_serie.style.visibility = 'visible';

			if(product.serialBatch){
				document.getElementById('txt_serie').value = product.serialBatch;
			}
		}
		else{
			unblockQuantityField();
			p_serie.style.visibility = 'hidden';
			i_serie.style.visibility = 'hidden';
		}

		requestUnitList();
	}
	else{
		asl.title("Producto no registrado.");
	}
}

function clear_product(){
	product = null;
	asl.title(' ');
	p_description.innerHTML = '';
	p_price.innerHTML = '';
	dv_unit.innerHTML = '';
	txt_cant.value = '';
	txt_serie.value = '';
	txt_opcion.innerHTML = '';

	p_serie.style.visibility = 'hidden';
	i_serie.style.visibility = 'hidden';
	p_opcion.style.visibility = 'hidden';
	i_opcion.style.visibility = 'hidden';
}

// Agrega un producto a la prenota.
function add_product(){
    
    if(product){

    	var quantity = parseFloat(txt_cant.value);
        
        if(!isNaN(quantity) && quantity > 0){

        	if(product.ItemTypeID == ItemType.SERVICE){
        		product.Quantity = quantity;
        		productos.push(product);

				clear_product();
				window.localStorage.setItem('products', JSON.stringify(productos));
				//asl.notify(asl.notifications.application, asl.priority.normal, 'Mensaje:', 'Producto agregado.', ['OK'], [scan_product]);
				scan_product();
				return;
        	}

			//var quantityOnPrenote = getQuantiyOnPrenote();
			var productFound = arrayFindEq( product, productos);

        	var quantityOnPrenote = 0;

        	if(productFound){
        		quantityOnPrenote = productFound.Quantity;
        	}

			//var totalQuantity = quantity + quantityOnPrenote;
			var totalQuantity = new Decimal(quantity || 0).plus(quantityOnPrenote || 0).toNumber();

			if(product.UseCombination){
				if(!product.optionEID){
					//addProduct = false;
					//asl.notify(asl.notifications.application,asl.priority.normal,'No se agrego el producto:','Opción no registrada.',['OK'],[null]);
					alert('No se agregó el producto: Opción inválida.');
					return;
				}
			}
			
			if(product.ItemTypeID == ItemType.SERIE){
				if(product.isSerialInformative){
					if(!product.serialBatch){
						//asl.notify(asl.notifications.application,asl.priority.normal,'No se pudo agregar el producto:', 'No tiene serie', ['OK'], [null]);
						alert('No se agregó el producto: No tiene serie.');
						return;
					}
				}
				else{
					if(product.SerialID){
						/*if(totalQuantity > product.serialQuantity){
							var availableSerial = product.serialQuantity - quantityOnPrenote;
							var msg = 'Solo hay ' + availableSerial + ' producto(s) disponibles con ese serial.';

							if(availableSerial == 0){
								msg = 'No hay productos disponibles con ese serial.';
							}

							//addProduct = false;
							asl.notify(asl.notifications.application,asl.priority.normal,'No se pudo agregar el producto:', msg, ['OK'], [null]);
							return;
						}*/
						if(productFound){
							alert('No se agregó el producto: Ya hay un producto con la misma serie en la prenota.');
							return;
						}
					}
					else{
						//asl.notify(asl.notifications.application,asl.priority.normal,'No se agrego el producto:','Serial no registrado.',['OK'],[null]);
						//addProduct = false;
						alert('No se agregó el producto: Serie no registrada.');
						return;
					}
				}
			}else if(product.ItemTypeID == ItemType.LOTE){
				
				if(product.SerialID){
					/*if(totalQuantity > product.serialQuantity){
						var availableSerial = product.serialQuantity - quantityOnPrenote;
						var msg = 'Solo hay ' + availableSerial + ' producto(s) disponibles en el lote.';

						if(availableSerial == 0){
							msg = 'No hay productos disponibles en el lote.';
						}

						//addProduct = false;
						asl.notify(asl.notifications.application,asl.priority.normal,'No se pudo agregar el producto:', msg, ['OK'], [null]);
						return;
					}*/
				}
				else{
					//asl.notify(asl.notifications.application,asl.priority.normal,'No se agrego el producto:','Lote no registrado.',['OK'],[null]);
					//addProduct = false;
					alert('No se agregó el producto: Lote no registrado.');
					return;
				}
			}

			
        	var availableQuantity = product.QuantityOnHand - quantityOnPrenote;

			/*if(totalQuantity > product.QuantityOnHand){
				var msg = 'Solo hay ' + availableQuantity + ' producto(s) disponibles.';

				if(availableQuantity == 0){
					msg = 'No hay productos disponibles.';
				}

				asl.notify(asl.notifications.application,asl.priority.normal,'No se pudo agregar el producto:', msg, ['OK'], [null]);
				return;
			}*/

			//if(addProduct){
				
				if(productFound){
					productFound.Quantity = totalQuantity;
				}
				else{
					product.Quantity = totalQuantity;
					productos.push(product);
				}

				clear_product();
				window.localStorage.setItem('products', JSON.stringify(productos));
				//asl.notify(asl.notifications.application, asl.priority.normal, 'Mensaje:', 'Producto agregado.', ['OK'], [scan_product]);
				scan_product();
			//}
        }
        else{ //asl.notify(asl.notifications.application,asl.priority.normal,'Mensaje:','Introduce una cantidad válida.',['OK'],[null]); 
        	alert('Introduce una cantidad válida.');
    	}
    }
    else{ //asl.notify(asl.notifications.application,asl.priority.normal,'Mensaje:','Escanee un producto.',['OK'],[scan_product]); 
    	scan_product();
    }
}

function getQuantiyOnPrenote(){
	var productFound = arrayFindEq( product, productos);

	var quantityOnPrenote = 0;

	if(productFound){
		quantityOnPrenote = productFound.Quantity;
	}

	return quantityOnPrenote;
}

function serial_assign(serial){
	
	if (!serial) {
		product.SerialID = 0;
		product.serialQuantity = 0;
		product.serialBatch = '';
		return;
	}
	if(product.isSerialInformative){
		product.SerialID = 0;
		product.serialQuantity = 1;
		product.serialBatch = serial;
	}else{
		request_serial(serial);
	}
	
	/*var url = 'php/db/get_item_serial.php?serial='+serial + '&ID=' + product.ID;

	var handle = function(response){
		try{
			var itemSerial =  JSON.parse(response);

			if(itemSerial !== false){
				product.SerialID = itemSerial.ID;
				product.serialQuantity = parseFloat(itemSerial.Quantity);
				product.serialBatch = itemSerial.SerialBatch;
			}
		}
		catch(e){
			asl.notify(asl.notifications.application, asl.priority.normal,'Error en el servidor: ',response,['OK'],[null]);
		}
	};

	ajaxRequest('GET', url, handle);*/
}

function request_serial(serial){
	var url = 'php/db/get_item_serial.php?serial='+serial + '&ID=' + product.ID;

	var handle = function(response){
		try{
			var itemSerial =  JSON.parse(response);

			if(itemSerial !== false){
				product.SerialID = itemSerial.ID;
				product.serialQuantity = parseFloat(itemSerial.Quantity);
				product.serialBatch = itemSerial.SerialBatch;
			}
		}
		catch(e){
			//asl.notify(asl.notifications.application, asl.priority.normal,'Error en el servidor: ',response,['OK'],[null]);
			alert('Error: '+response);
		}
	};

	ajaxRequest('GET', url, handle);
}

function combination_assign(combination){
	
	if (!combination) {
		clearCombination();
		return;
	} 

	var url = 'php/db/get_item_combination.php?combination='+combination + '&ID=' + product.ID;

	var handle = function(response){
		try{
			var itemCombination =  JSON.parse(response);

			if(itemCombination !== false){
				product.optionEID = itemCombination.ExternalID;
				product.optionName = itemCombination.Name;
				product.optionUUID = itemCombination.UUID;
				product.optionID = itemCombination.ID;
				product.QuantityOnHand = parseFloat(itemCombination.QuantityOnHand);

				requestItemPrice(product.ID, product.UnitID, product.optionID);

				if(product.ItemTypeID == ItemType.SERIE || product.ItemTypeID == ItemType.LOTE){
					unlockSerialInput();
				}
			}
			else{
				clearCombination();
				//asl.notify(asl.notifications.application, asl.priority.normal,'Mensaje:','La clave de opciones es inválida.',['OK'],[null]);
				alert('Clave de opción incorrecta.');
			}
		}
		catch(e){
			//asl.notify(asl.notifications.application, asl.priority.normal,'Error en el servidor:',response,['OK'],[null]);
			alert('Error: '+ response);
		}
	};

	ajaxRequest('GET', url, handle);
}

function clearCombination(){
	product.optionEID = '';
	product.optionName = '';
	product.optionUUID = '';
	product.optionDesc = '';
	product.optionID = 0;
	product.QuantityOnHand = 0;

	if(product.ItemTypeID == ItemType.SERIE || product.ItemTypeID == ItemType.LOTE){
		document.getElementById('txt_serie').value = '';
		lockSerialInput();
	}
}

/*function requestCombinationList(){
    var productId = product.ID;

    if(productId){

        var url = 'php/db/get_item_combination_list.php?ID=' + productId;

        var handle = function(response){
            try{
                //asl.notify(asl.notifications.application, asl.priority.normal,'m',response,['OK'],[null]);
                aCombinations =  JSON.parse(response);

                if(aCombinations !== false && aCombinations != null){
                    setOptionField();
                }
                else{
                	 asl.notify(asl.notifications.application, asl.priority.normal,'Error en el servidor:', 'No se pudieron obtener las combinaciones asociadas al producto.',['OK'],[null]);
                }
            }
            catch(e){
                asl.notify(asl.notifications.application, asl.priority.normal,'Error en el servidor:',response,['OK'],[null]);
            }
        };

        ajaxRequest('GET', url, handle);
    }
}*/

/*function setOptionField(){
	var nCombinationsLen = aCombinations.length;
	var oPOpcion = document.getElementById('p_opcion');
	var oTxtOpcion = document.getElementById('txt_opcion');

	oPOpcion.onclick = null;
	oTxtOpcion.value = null;
	oTxtOpcion.disabled = false;

    if(nCombinationsLen === 0){
        asl.notify(asl.notifications.application, asl.priority.normal,'Error:', 'El producto usa combinaciones pero no tiene ninguna asociada.',['OK'],[null]);
    }
    else if(nCombinationsLen === 1){
    	product.optionEID = aCombinations[0].optionEID;
		product.optionName = aCombinations[0].optionDetailName;
		product.optionUUID = aCombinations[0].UUID;
		product.optionID = aCombinations[0].ID;
		product.QuantityOnHand = parseFloat(aCombinations[0].QuantityOnHand);

		oTxtOpcion.value = product.optionEID;
		oTxtOpcion.disabled = true;
    }
    else{
    	oPOpcion.onclick = function(){
			showCombinationList();
		};
    }

    oPOpcion.style.visibility = 'visible';
	i_opcion.style.visibility = 'visible';
}*/

function showCombinationList(){
	if (product && product.options && product.options.length) {
		window.localStorage.setItem('optionListNum',JSON.stringify(0));
		window.localStorage.setItem('aOptionDetail',JSON.stringify([]));
		window.localStorage.setItem('aItemUnits', JSON.stringify(aItemUnits));
		window.localStorage.setItem('product', JSON.stringify(product));
		window.location = 'combination_list.html';
	}
}

function showSerialList(){
	if (product && (product.ItemTypeID == ItemType.SERIE || product.ItemTypeID == ItemType.LOTE)) {

		if(product.UseCombination == 0 || product.UseCombination == 1 && product.optionUUID){

			window.localStorage.setItem('aItemUnits', JSON.stringify(aItemUnits));
			window.localStorage.setItem('product', JSON.stringify(product));
			window.location = 'serial_list.html';
		} else {
			//asl.notify(asl.notifications.application, asl.priority.normal,'Mensaje:', 'Debes seleccionar primero las opciones del producto.',['OK'],[null]);
			alert('Debes seleccionar primero las opciones del producto.');
		}
	}
}

function subir_cant(){
	var txt = document.getElementById('txt_cant');

	if(!txt.disabled){
		var cant = parseFloat(txt.value) || 0;
		cant += 1;
		product.Quantity = cant;
		txt.value = cant;
	}
}

function bajar_cant(){
	var txt = document.getElementById('txt_cant');

	if(!txt.disabled){
		var cant = parseFloat(txt.value) || 0;
		cant -= 1;
		if(cant > 0){
			product.Quantity = cant;
			txt.value = cant;
		}
	}
}

function blockQuantityField(){
	document.getElementById('txt_cant').disabled = true;
	document.getElementById('subir_cantidad').disabled = true;
	document.getElementById('bajar_cantidad').disabled = true;
}

function unblockQuantityField(){
	document.getElementById('txt_cant').disabled = false;
	document.getElementById('subir_cantidad').disabled = false;
	document.getElementById('bajar_cantidad').disabled = false;
}

function lockSerialInput(){
	document.getElementById('txt_serie').disabled = true;
	document.getElementById('i_serie').onclick = function(){
		//asl.notify(asl.notifications.application, asl.priority.normal,'Mensaje:', 'Debes seleccionar primero las opciones del producto.',['OK'],[null]);
		alert('Debes seleccionar primero las opciones del producto.');
	};
}

function unlockSerialInput(){
	document.getElementById('txt_serie').disabled = false;
	document.getElementById('i_serie').onclick = null;
}

function requestUnitList(){
	var url = 'php/db/get_unit_list.php?itemID='+product.ID;

    var handle = function(response){
        try{
            aItemUnits = JSON.parse(response);
            //alert(aItemUnits.length);

            if(aItemUnits !== false && aItemUnits !== null){
				setUnitField();
            }
        }
        catch(e){
            alert(response);
        }
    };

    ajaxRequest('GET', url, handle);
}

function setUnitField(){
	var nItmUnitsLen = aItemUnits.length;
	var oDivUnit = document.getElementById('dv_unit');
	var oDivUnitLabel = document.getElementById('dv_unitl');

	oDivUnit.onclick = null;
	oDivUnit.innerHTML = '';
	oDivUnitLabel.onclick = null;

    if(nItmUnitsLen === 0){
    	if(product.unitName){
    		oDivUnit.innerHTML = product.unitName;
    	}
    	else{
        	//asl.notify(asl.notifications.application, asl.priority.normal,'Error:', 'El producto no tiene configurado ninguna unidad.',['OK'],[null]);
        	alert('Error: El producto no tiene configurado ninguna unidad.')
    	}
    }
    else if(nItmUnitsLen === 1){
    	product.UnitID = aItemUnits[0].unitID;
    	product.unitName = aItemUnits[0].name;
    	product.unitFactor = aItemUnits[0].factor;

		oDivUnit.innerHTML = product.unitName;
    }
    else{
		oDivUnit.innerHTML = product.unitName;
    	oDivUnit.onclick = oDivUnitLabel.onclick = showUnitList;
    }
}

function showUnitList(){
	//window.localStorage.setItem('aCombinations',JSON.stringify(aCombinations));
	window.localStorage.setItem('aItemUnits', JSON.stringify(aItemUnits));
	window.localStorage.setItem('product', JSON.stringify(product));
	window.location = 'unit_list.html';
}

function requestItemCombination(sParams, productID, combExternalID){
	var url = 'php/db/get_item_combination2.php?ID='+productID+'&combExternalID='+combExternalID+'&userID='+user.ID+'&'+sParams;

    var handle = function(response){
        try{
        	var oResponse = JSON.parse(response);

        	if(!oResponse){
				//asl.notify(asl.notifications.application, asl.priority.normal,'Mensaje:' , 'No hubo respuesta del servidor',['OK'],[null]);
				alert('Mensaje: No hubo respuesta del servidor');
				return;
			}

			if(oResponse.status == true){
				var oItemCombination = oResponse.data;

				if(oItemCombination){
					product.optionEID = oItemCombination.ExternalID;
					product.optionUUID = oItemCombination.UUID;
					product.optionID = oItemCombination.ID;
					product.optionDesc = oItemCombination.description;
					//product.QuantityOnHand = parseFloat(oItemCombination.QuantityOnHand) || 0;
					
					requestItemPrice(product.ID, product.UnitID, product.optionID);

					//document.getElementById('txt_opcion').innerHTML = product.optionEID;
					document.getElementById('txt_opcion').innerHTML = product.optionDesc;

					if(product.ItemTypeID == ItemType.SERIE || product.ItemTypeID == ItemType.LOTE){
						var oTxtSerie = document.getElementById('txt_serie');
						
						oTxtSerie.value = null;
						unlockSerialInput();

						// Se borra la serie, ya que depende de la combinación.
						product.SerialID = 0;
						product.serialQuantity = 0;
						product.serialBatch = '';
					}
                }
                else{
                	//asl.notify(asl.notifications.application, asl.priority.normal,'Error:' , 'No se pudo obtener la combinación de opciones.',['OK'],[null]);
                	alert('No se pudo obtener la combinación de opciones.');
                }
			}
			else {
				//asl.notify(asl.notifications.application, asl.priority.normal,'Error:' , oResponse.data,['OK'],[null]);
				alert('Error: '+oResponse.data);
			}
        }
        catch(e){
            //asl.notify(asl.notifications.application, asl.priority.normal,'Error:' , response,['OK'],[null]);
            alert('Error: '+response);
        }
    };

    ajaxRequest('GET', url, handle);
}

function checkNumberOfDecimals() {
	

	if (product) {
		var oTxtCant = document.getElementById('txt_cant');

		if ( oTxtCant.value.match('\\.') ) {
			var decimalRegExp = new RegExp('[\\.][\\d]{0,'+product.unitDecimals+'}$');

			var isValidNumber = decimalRegExp.test(oTxtCant.value);

			if (!isValidNumber) {
				var msg = null;

				if(product.unitDecimals){
					msg = 'La unidad solo permite '+product.unitDecimals+' decimales.';
				}
				else{
					msg = 'La unidad no permite decimales.';
				}

				//asl.notify(asl.notifications.application, asl.priority.normal,'Cantidad \''+oTxtCant.value+'\' inválida:', msg,['OK'],[null]);
				alert('Cantidad \''+oTxtCant.value+'\' inválida: '+msg);
				oTxtCant.value = product.Quantity;
				return;
			}
		}

		if(parseFloat(oTxtCant.value) <= 0) {
			//asl.notify(asl.notifications.application, asl.priority.normal,'Cantidad \''+oTxtCant.value+'\' inválida:', 'La cantidad debe ser positiva.',['OK'],[null]);
			alert('Cantidad \''+oTxtCant.value+'\' inválida: La cantidad debe ser positiva.');
			oTxtCant.value = product.Quantity;
			return;
		}

		product.Quantity = oTxtCant.value;
	}
}

function requestItemPrice(itemID, unitID, itemCombinationID){
	var url = 'php/db/get_item_price.php?ItemID='+itemID;

	if(itemCombinationID){
		url += '&itemCombinationID='+itemCombinationID;
	}

	if(unitID){
		url += '&unitID='+unitID;
	}

    var handle = function(response){
        try{
            var oResponse = JSON.parse(response);
            
            if(oResponse.status == 'success'){

            	var itemPrice = oResponse.msg;

				product.Price = parseFloat(itemPrice) || 0;
				
				document.getElementById('p_price').innerHTML = '$' + product.Price.toFixed(2);
            }
            else{
            	//asl.notify(asl.notifications.application, asl.priority.normal,'Error', oResponse.msg,['OK'],[null]);
            	alert('Error: '+ oResponse.msg);
            }
        }
        catch(e){
            alert(response);
        }
    };

    ajaxRequest('GET', url, handle);
}

function requestCombinationDesc(itemCombinationUUID){
	var url = 'php/db/get_combination_desc.php?itemCombinationUUID='+itemCombinationUUID;

    var handle = function(response){
        try{
            var oResponse = JSON.parse(response);
            
            if(oResponse.status == 'success'){

            	var optionsDescList = oResponse.data;

            	if(optionsDescList && optionsDescList.length){

            		var optionsDesc = '';

            		for (var i = 0; i < optionsDescList.length; i++) {
            			if(i > 0)
            				optionsDesc += ', ';
            			optionsDesc += optionsDescList[i].optionType +': '+ optionsDescList[i].optionName;
            		};

					//asl.notify(asl.notifications.application, asl.priority.normal,'Opciones:', optionsDesc,['OK'],[null]);
					alert('Opciones: '+ optionsDesc);
				}
				else {
					//asl.notify(asl.notifications.application, asl.priority.normal,'Mensaje:', 'El artículo no tiene opciones',['OK'],[null]);
					alert('El artículo no tiene opciones');
				}
            }
            else{
            	//asl.notify(asl.notifications.application, asl.priority.normal,'Error', oResponse.data,['OK'],[null]);
            	alert('Error'+ oResponse.data);
            }
        }
        catch(e){
            alert(response);
        }
    };

    ajaxRequest('GET', url, handle);
}