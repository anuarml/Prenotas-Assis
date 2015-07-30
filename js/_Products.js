var Product = function(attr) {
	this.ID = attr.ID || '';
    this.UUID = attr.UUID || '';
    this.Code = attr.Code || '';
    this.barcode = attr.barcode || '';
    this.scanCode = attr.scanCode || '';
    this.Description = attr.Description || '';
    this.Price = parseFloat(attr.Price) || 0;
	this.basePrice = parseFloat(attr.Price) || 0;
    this.Quantity = parseFloat(attr.Quantity) || 0;
	this.QuantityOnHand = parseFloat(attr.QuantityOnHand) || 0;
	this.ItemTypeID = parseInt(attr.ItemTypeID) || 0;
	this.UseCombination = parseInt(attr.UseCombination) || 0;
	this.options = attr.options || [];

	this.SerialID = attr.SerialID || 0;
	this.serialQuantity = parseFloat(attr.serialQuantity) || 0;
	this.serialBatch = attr.serialBatch || '';

	this.optionID = attr.optionID || 0;
	this.optionUUID = attr.optionUUID || '';
	this.optionName = attr.optionName || '';
	this.optionEID = attr.optionEID || '';

    this.UnitID = parseInt(attr.UnitID) || 0;
    this.unitName = attr.unitName || '';
    this.unitFactor = parseFloat(attr.unitFactor) || 0;
	
	this.equals = function(obj){
		return (this.ID == obj.ID && this.SerialID == obj.SerialID && this.optionEID == obj.optionEID);
	};
};
