var Product = function(attr) {
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
	this.options = attr.options || [];
	this.itemType = attr.itemType || '';
	this.itemStatus = attr.itemStatus || '';
	this.CategoryBranchID = parseInt(attr.CategoryBranchID) || 0;

	this.SerialID = attr.SerialID || 0;
	this.serialQuantity = parseFloat(attr.serialQuantity) || 0;
	this.serialBatch = attr.serialBatch || '';

	this.optionID = attr.optionID || 0;
	this.optionUUID = attr.optionUUID || '';
	this.optionName = attr.optionName || '';
	this.optionEID = attr.optionEID || '';

    this.UnitID = parseInt(attr.UnitID) || 0;
    this.unitName = attr.unitName || '';
    this.unitDecimals = parseInt(attr.unitDecimals) || 0;
    this.unitFactor = parseFloat(attr.unitFactor) || 0;
	this.isSerialInformative = parseInt(attr.isSerialInformative) || 0;
	
	this.equals = function(obj){
		return (this.ID == obj.ID && this.serialBatch == obj.serialBatch && this.optionEID == obj.optionEID && this.UnitID == obj.UnitID);
	};
};

var productos = JSON.parse(window.localStorage.getItem('products'));

if(!productos){
    productos = [];
}

var oPrenote = function(attr) {
	this.folio = attr.folio || 0;
	this.total = attr.total || 0;
	this.narticles = attr.narticles || 0;
	this.id_employee = attr.id_employee || 0;
	this.date = attr.date || '';
	this.product = attr.product || [];
    this.terminal = attr.terminal || '00';
    this.employeeName = attr.employeeName || '';
	//this.store_id = cfg.store.id || 0;
	//this.store_name = cfg.store.name || '';
    this.printer = attr.printer || '';
    //this.customerUUID = cfg.customerUUID || '';
	this.cotizationNumber = attr.cotizationNumber || '';
	this.clientName = attr.clientName || '';
};


var User = function(attr) {
	if(!attr) attr = {};
	
    this.ID = parseInt(attr.ID) || 0;
    this.Name = attr.Name || '';
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

function ajaxRequest(method, url, handle, data){
	var xmlhttp;

	if (window.XMLHttpRequest) {
		// code for IE7+, Firefox, Chrome, Opera, Safari
		xmlhttp = new XMLHttpRequest();
	} else { // code for IE6, IE5
		xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
	}
	
	xmlhttp.onreadystatechange = function (){
		if (xmlhttp.readyState==4 && xmlhttp.status==200){
			handle(xmlhttp.responseText.trim());
		}
	};
	// Llamar al web service del lado del servidor.
	xmlhttp.open( method, url, true);
	xmlhttp.send(data);
}