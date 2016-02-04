var ItemType = {};

Object.defineProperty(ItemType, 'NORMAL', { value: 4 });

Object.defineProperty(ItemType, 'SERIE', { value: 5 });

Object.defineProperty(ItemType, 'LOTE', { value: 6 });

Object.defineProperty(ItemType, 'SERVICE', { value: 7 });


var Product = function(attr) {
	attr = attr || {};

	this.ID = attr.ID || '';
    this.UUID = attr.UUID || '';
    this.Code = attr.Code || '';
    this.barcode = attr.barcode || '';
    this.scanCode = attr.scanCode || '';
    this.Description = attr.Description || '';
    this.Price = parseFloat(attr.Price) || 0;
	this.basePrice = parseFloat(attr.basePrice) || 0;
    this.Quantity = parseFloat(attr.Quantity) || 1;
	this.QuantityOnHand = parseFloat(attr.QuantityOnHand) || 0;
	this.ItemTypeID = parseInt(attr.ItemTypeID) || 0;
	this.UseCombination = parseInt(attr.UseCombination) || 0;
	this.useSerial = parseInt(attr.useSerial) || 0;
	this.useBatch = parseInt(attr.useBatch) || 0;
	this.options = attr.options || [];
	this.itemType = attr.itemType || '';
	this.itemStatus = attr.itemStatus || '';
	this.CategoryBranchID = parseInt(attr.CategoryBranchID) || 0;

	this.SerialID = attr.SerialID || 0;
	this.SerialUUID = attr.SerialUUID || null;
	this.serialQuantity = parseFloat(attr.serialQuantity) || 0;
	this.serialBatch = attr.serialBatch || '';

	this.optionID = attr.optionID || 0;
	this.optionUUID = attr.optionUUID || '';
	this.optionName = attr.optionName || '';
	this.optionEID = attr.optionEID || '';
	this.optionDesc = attr.optionDesc || '';

    this.UnitID = parseInt(attr.UnitID) || 0;
    this.unitName = attr.unitName || '';
    this.unitDecimals = parseInt(attr.unitDecimals) || 0;
    this.unitFactor = parseFloat(attr.unitFactor) || 0;
	this.isSerialInformative = parseInt(attr.isSerialInformative) || 0;
	
	this.equals = function(obj){
		return (this.ID == obj.ID && this.barcode == obj.barcode && this.serialBatch == obj.serialBatch && this.optionEID == obj.optionEID && this.UnitID == obj.UnitID);
	};
};

Product.prototype.isSerialType = function () {
	//return this.ItemTypeID == ItemType.SERIE;
	return this.useSerial;
}

Product.prototype.isBatchType = function () {
	//return this.ItemTypeID == ItemType.LOTE;
	return this.useBatch;
}

Product.prototype.isServiceType = function () {
	return this.ItemTypeID == ItemType.SERVICE;
}

var productos = JSON.parse(window.localStorage.getItem('products'));

if(!productos){
    productos = [];
}

var oPrenote = function(attr) {
	attr = attr || {};
	
	this.uuid = attr.uuid || '';
	this.folio = attr.folio || 0;
	this.total = attr.total || 0;
	this.narticles = attr.narticles || 0;
	this.id_employee = attr.id_employee || 0;
	this.date = attr.date || '';
	this.product = attr.product || [];
    this.terminal = attr.terminal || 'A';
    this.employeeName = attr.employeeName || '';
	this.store_id = attr.store_id || null;
	this.store_name = attr.store_name || null;
	this.store_num = attr.store_num || null;
    this.printer = attr.printer || '';
    this.customerUUID = attr.customerUUID || null;
	this.cotizationNumber = attr.cotizationNumber || '';
	this.clientName = attr.clientName || '';
	this.changeClient = attr.changeClient || false;
	this.employeeLogin = attr.employeeLogin || '';
	this.barcodePath = attr.barcodePath || '';
	this.printTimes = attr.printTimes || 1;
};

var prenote = null;
/*var jsPrenote = window.localStorage.getItem('prenote');

if(jsPrenote!=null){
	prenote = new oPrenote(JSON.parse(jsPrenote));
}*/


var User = function(attr) {
	if(!attr) attr = {};
	
    this.ID = parseInt(attr.ID) || 0;
    this.Name = attr.Name || '';
    this.Login = attr.Login || '';
};

var user = new User(JSON.parse(window.localStorage.getItem('user')));

function arrayFindEq( obj, array){

	var i;
	var length = array.length;

	for(i=0; i < length; i++){
		if( obj.equals(array[i]) )
			return array[i];
	}

	return false;
}

function showLoadingScreen(enabled){
	var divLoading = document.getElementById('loading');

	if(divLoading){
		if(enabled === true){
			divLoading.style.visibility = 'visible';
		}
		else{
			divLoading.style.visibility = 'hidden';
		}
	}
}

function ajaxRequest(method, url, handle, data){
	var xmlhttp;

	if (window.XMLHttpRequest) {
		// code for IE7+, Firefox, Chrome, Opera, Safari
		xmlhttp = new XMLHttpRequest();
	} else { // code for IE6, IE5
		xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
	}

	showLoadingScreen(true);
	
	xmlhttp.onreadystatechange = function (){
		if (xmlhttp.readyState==4 && xmlhttp.status==200){
			handle(xmlhttp.responseText.trim());
		}
	    showLoadingScreen(false);
	};
	// Llamar al web service del lado del servidor.
	xmlhttp.open( method, url, true);
	xmlhttp.send(data);
}
