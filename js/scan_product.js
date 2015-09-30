var product=null,aItemUnits=null,hasName=null,keyboardIsShowed=!1,scanAProductMsg="Escanea un producto",aslOptionList=[{title:"Ver prenota",callback:function(){window.localStorage.setItem("aItemUnits",JSON.stringify(aItemUnits));window.localStorage.setItem("product",JSON.stringify(product));window.location="prenota.html"}},{title:"Detalles",callback:function(){product?(window.localStorage.setItem("aItemUnits",JSON.stringify(aItemUnits)),window.localStorage.setItem("product",JSON.stringify(product)),
window.location="scan_product_detail.html"):alert("Escanea un producto.")}},{title:"Salir",callback:function(){asl.exit()}}];asl.title(scanAProductMsg);asl.back(scan_product);asl.options(aslOptionList);scanner.enable();scanner.decodeEvent="doRequestProduct(%json)";function doRequestProduct(a){a&&request_product(a.source,a.data)}
asl.events.subscribe(asl.events.types.loaded,function(){document.getElementById("txt_serie").onfocus=function(){scanner.enable()};txt_serie.onchange=function(){serial_assign(txt_serie.value)};document.getElementById("p_opcion").onclick=function(){showCombinationList()};document.getElementById("p_serie").onclick=function(){showSerialList()};document.getElementById("txt_opcion").onclick=function(){product&&product.optionUUID&&product.optionDesc?alert("Opciones: "+product.optionDesc):alert("El art\u00edculo no tiene opciones asignadas.")};
document.getElementById("txt_cant").onchange=function(){checkNumberOfDecimals()};aItemUnits=JSON.parse(window.localStorage.getItem("aItemUnits"));var a=JSON.parse(window.localStorage.getItem("product"));null!=a&&window.localStorage.removeItem("product");null!=aItemUnits&&(window.localStorage.removeItem("aItemUnits"),1<aItemUnits.length&&(document.getElementById("dv_unit").onclick=function(){showUnitList()},document.getElementById("dv_unitl").onclick=function(){showUnitList()}));var b=JSON.parse(window.localStorage.getItem("aOptionDetail"));
if(null!=b){window.localStorage.removeItem("aOptionDetail");var c=b.length;if(0<c&&null!=a){for(var e=[],d=0;d<c;d++)e.push("Detail"+d+"="+b[d]);b=e.join("&");c=JSON.parse(window.localStorage.getItem("combExternalID"));null!=c?window.localStorage.removeItem("combExternalID"):c="";requestItemCombination(b,a.ID,c)}}b=JSON.parse(window.localStorage.getItem("nUnitSelected"));null!=b&&(window.localStorage.removeItem("nUnitSelected"),(b=aItemUnits[b])&&a&&(new Decimal(a.basePrice),a.UnitID=b.unitID,a.unitName=
b.name,a.unitFactor=b.factor,a.unitDecimals=b.decimals,a.Quantity=1));b=JSON.parse(window.localStorage.getItem("oSelectedSerial"));null!=b&&(window.localStorage.removeItem("oSelectedSerial"),a&&(a.serialBatch=b.SerialBatch,a.serialQuantity=b.Quantity,a.SerialID=b.ID,a.SerialUUID=b.UUID));null!=a&&show_product_info(a)});asl.events.subscribe(asl.events.types.loaded,function(){verifyWhichKeyboard()});asl.events.subscribe(asl.events.types.exit,function(){asl.badge(null);"function"==typeof asl.lock&&asl.lock(null)});
function verifyWhichKeyboard(){hasName=JSON.parse(window.localStorage.getItem("bhasName"));cfg.askClientName&&!hasName&&askClientName()}function showKBonFocus(){keyboardIsShowed&&(keyboardIsShowed=!1,verifyWhichKeyboard())}function saveName(a,b){hasName=!0;var c=JSON.stringify(b),e=JSON.stringify(hasName);window.localStorage.setItem("sclientName",c);window.localStorage.setItem("bhasName",e)}
function askClientName(){asl.showKeyboard({inputId:"askClientName",title:"Ingresa el cliente.",type:"text",scanner:!1,back:!0},saveName)}function scan_product(){window.setTimeout(function(){asl.showKeyboard({inputId:"scan_product",title:"Escanea un producto.",type:"text",scanner:!1,back:!0},request_product)},500)}
function request_product(a,b){b&&ajaxRequest("GET","php/db/get_product2.php?code="+b,function(a){try{var b=JSON.parse(a),d=null;if(!b)throw clear_product(),"No hubo respuesta del servidor";1==b.status?(d=b.data,show_product_info(d)):confirm(b.data,function(){clear_product()})}catch(f){clear_product(),alert("Error: "+a)}})}
function show_product_info(a){clear_product();a?(product=new Product(a),product.scanCode=product.barcode?product.barcode:product.Code,asl.title(product.scanCode),requestItemPrice(product.ID,product.UnitID,product.optionID),document.getElementById("p_description").innerHTML=product.Description,document.getElementById("txt_cant").value=product.Quantity,product.ItemTypeID!=ItemType.SERIE&&product.ItemTypeID!=ItemType.LOTE||1!=product.UseCombination||product.optionUUID||lockSerialInput(),1==product.UseCombination?
(p_opcion.style.visibility="visible",i_opcion.style.visibility="visible",a=document.getElementById("txt_opcion"),product.optionEID&&(a.innerHTML=product.optionDesc)):(p_opcion.style.visibility="hidden",i_opcion.style.visibility="hidden"),product.ItemTypeID==ItemType.SERIE?(blockQuantityField(),p_serie.innerHTML="Serie:",txt_serie.title="Serie",p_serie.style.visibility="visible",i_serie.style.visibility="visible",product.serialBatch&&(document.getElementById("txt_serie").value=product.serialBatch)):
product.ItemTypeID==ItemType.LOTE?(unblockQuantityField(),p_serie.innerHTML="Lote:",txt_serie.title="Lote",p_serie.style.visibility="visible",i_serie.style.visibility="visible",product.serialBatch&&(document.getElementById("txt_serie").value=product.serialBatch)):(unblockQuantityField(),p_serie.style.visibility="hidden",i_serie.style.visibility="hidden"),requestUnitList()):asl.title("Producto no registrado.")}
function clear_product(){product=null;asl.title(scanAProductMsg);p_description.innerHTML="";p_price.innerHTML="";dv_unit.innerHTML="";txt_cant.value="";txt_serie.value="";txt_opcion.innerHTML="";p_serie.style.visibility="hidden";i_serie.style.visibility="hidden";p_opcion.style.visibility="hidden";i_opcion.style.visibility="hidden"}
function add_product(){if(product){var a=parseFloat(txt_cant.value);if(!isNaN(a)&&0<a){if(product.ItemTypeID==ItemType.SERVICE)product.Quantity=a,productos.push(product);else{var b=arrayFindEq(product,productos),c=0;b&&(c=b.Quantity);a=(new Decimal(a||0)).plus(c||0).toNumber();if(product.UseCombination&&!product.optionEID){alert("No se agreg\u00f3 el producto: Opci\u00f3n inv\u00e1lida.");return}if(product.ItemTypeID==ItemType.SERIE)if(product.isSerialInformative){if(!product.serialBatch){alert("No se agreg\u00f3 el producto: No tiene serie.");
return}}else if(product.SerialID){if(b){alert("No se agreg\u00f3 el producto: Ya hay un producto con la misma serie en la prenota.");return}}else{alert("No se agreg\u00f3 el producto: Serie no registrada.");return}else if(product.ItemTypeID==ItemType.LOTE&&!product.SerialID){alert("No se agreg\u00f3 el producto: Lote no registrado.");return}b?b.Quantity=a:(product.Quantity=a,productos.push(product))}clear_product();window.localStorage.setItem("products",JSON.stringify(productos))}else alert("Introduce una cantidad v\u00e1lida.")}}
function getQuantiyOnPrenote(){var a=arrayFindEq(product,productos),b=0;a&&(b=a.Quantity);return b}function serial_assign(a){a?product.isSerialInformative?(product.SerialID=0,product.SerialUUID=null,product.serialQuantity=1,product.serialBatch=a):request_serial(a):(product.SerialID=0,product.SerialUUID=null,product.serialQuantity=0,product.serialBatch="")}
function request_serial(a){ajaxRequest("GET","php/db/get_item_serial.php?serial="+a+"&ID="+product.ID,function(a){try{var c=JSON.parse(a);!1!==c&&(product.SerialID=c.ID,product.SerialUUID=c.UUID,product.serialQuantity=parseFloat(c.Quantity),product.serialBatch=c.SerialBatch)}catch(e){alert("Error: "+a)}})}
function combination_assign(a){a?ajaxRequest("GET","php/db/get_item_combination.php?combination="+a+"&ID="+product.ID,function(a){try{var c=JSON.parse(a);!1!==c?(product.optionEID=c.ExternalID,product.optionName=c.Name,product.optionUUID=c.UUID,product.optionID=c.ID,product.QuantityOnHand=parseFloat(c.QuantityOnHand),requestItemPrice(product.ID,product.UnitID,product.optionID),product.ItemTypeID!=ItemType.SERIE&&product.ItemTypeID!=ItemType.LOTE||unlockSerialInput()):(clearCombination(),alert("Clave de opci\u00f3n incorrecta."))}catch(e){alert("Error: "+
a)}}):clearCombination()}function clearCombination(){product.optionEID="";product.optionName="";product.optionUUID="";product.optionDesc="";product.optionID=0;product.QuantityOnHand=0;if(product.ItemTypeID==ItemType.SERIE||product.ItemTypeID==ItemType.LOTE)document.getElementById("txt_serie").value="",lockSerialInput()}
function showCombinationList(){product&&product.options&&product.options.length&&(window.localStorage.setItem("optionListNum",JSON.stringify(0)),window.localStorage.setItem("aOptionDetail",JSON.stringify([])),window.localStorage.removeItem("combExternalID"),window.localStorage.setItem("aItemUnits",JSON.stringify(aItemUnits)),window.localStorage.setItem("product",JSON.stringify(product)),window.location="combination_list.html")}
function showSerialList(){!product||product.ItemTypeID!=ItemType.SERIE&&product.ItemTypeID!=ItemType.LOTE||(0==product.UseCombination||1==product.UseCombination&&product.optionUUID?(window.localStorage.setItem("aItemUnits",JSON.stringify(aItemUnits)),window.localStorage.setItem("product",JSON.stringify(product)),window.location="serial_list.html"):alert("Debes seleccionar primero las opciones del producto."))}
function subir_cant(){var a=document.getElementById("txt_cant");if(!a.disabled){var b=parseFloat(a.value)||0,b=b+1;product.Quantity=b;a.value=b}}function bajar_cant(){var a=document.getElementById("txt_cant");if(!a.disabled){var b=parseFloat(a.value)||0;--b;0<b&&(product.Quantity=b,a.value=b)}}function blockQuantityField(){document.getElementById("txt_cant").disabled=!0;document.getElementById("subir_cantidad").disabled=!0;document.getElementById("bajar_cantidad").disabled=!0}
function unblockQuantityField(){document.getElementById("txt_cant").disabled=!1;document.getElementById("subir_cantidad").disabled=!1;document.getElementById("bajar_cantidad").disabled=!1}function lockSerialInput(){document.getElementById("txt_serie").disabled=!0;document.getElementById("i_serie").onclick=function(){alert("Debes seleccionar primero las opciones del producto.")}}
function unlockSerialInput(){document.getElementById("txt_serie").disabled=!1;document.getElementById("i_serie").onclick=null}function requestUnitList(){ajaxRequest("GET","php/db/get_unit_list.php?itemID="+product.ID,function(a){try{aItemUnits=JSON.parse(a),!1!==aItemUnits&&null!==aItemUnits&&setUnitField()}catch(b){alert(a)}})}
function setUnitField(){var a=aItemUnits.length,b=document.getElementById("dv_unit"),c=document.getElementById("dv_unitl");b.onclick=null;b.innerHTML="";c.onclick=null;0===a?product.unitName?b.innerHTML=product.unitName:alert("Error: El producto no tiene configurado ninguna unidad."):1===a?(product.UnitID=aItemUnits[0].unitID,product.unitName=aItemUnits[0].name,product.unitFactor=aItemUnits[0].factor,b.innerHTML=product.unitName):(b.innerHTML=product.unitName,b.onclick=c.onclick=showUnitList)}
function showUnitList(){window.localStorage.setItem("aItemUnits",JSON.stringify(aItemUnits));window.localStorage.setItem("product",JSON.stringify(product));window.location="unit_list.html"}
function requestItemCombination(a,b,c){ajaxRequest("GET","php/db/get_item_combination2.php?ID="+b+"&combExternalID="+c+"&userID="+user.ID+"&"+a,function(a){try{var b=JSON.parse(a);if(b)if(1==b.status){var c=b.data;if(c){if(product.optionEID=c.ExternalID,product.optionUUID=c.UUID,product.optionID=c.ID,product.optionDesc=c.description,requestItemPrice(product.ID,product.UnitID,product.optionID),document.getElementById("txt_opcion").innerHTML=product.optionDesc,product.ItemTypeID==ItemType.SERIE||product.ItemTypeID==
ItemType.LOTE)document.getElementById("txt_serie").value=null,unlockSerialInput(),product.SerialID=0,product.SerialUUID=null,product.serialQuantity=0,product.serialBatch=""}else alert("No se pudo obtener la combinaci\u00f3n de opciones.")}else alert("Error: "+b.data);else alert("Mensaje: No hubo respuesta del servidor")}catch(g){alert("Error: "+a)}})}
function checkNumberOfDecimals(){if(product){var a=document.getElementById("txt_cant");if(a.value.match("\\.")&&!(new RegExp("[\\.][\\d]{0,"+product.unitDecimals+"}$")).test(a.value)){var b=null,b=product.unitDecimals?"La unidad solo permite "+product.unitDecimals+" decimales.":"La unidad no permite decimales.";alert("Cantidad '"+a.value+"' inv\u00e1lida: "+b);a.value=product.Quantity}else 0>=parseFloat(a.value)?(alert("Cantidad '"+a.value+"' inv\u00e1lida: La cantidad debe ser positiva."),a.value=
product.Quantity):product.Quantity=a.value}}function requestItemPrice(a,b,c){a="php/db/get_item_price.php?ItemID="+a;c&&(a+="&itemCombinationID="+c);b&&(a+="&unitID="+b);ajaxRequest("GET",a,function(a){try{var b=JSON.parse(a);"success"==b.status?(product.Price=parseFloat(b.msg)||0,document.getElementById("p_price").innerHTML="$"+product.Price.toFixed(2)):alert("Error: "+b.msg)}catch(c){alert(a)}})}
function requestCombinationDesc(a){ajaxRequest("GET","php/db/get_combination_desc.php?itemCombinationUUID="+a,function(a){try{var c=JSON.parse(a);if("success"==c.status){var e=c.data;if(e&&e.length){for(var c="",d=0;d<e.length;d++)0<d&&(c+=", "),c+=e[d].optionType+": "+e[d].optionName;alert("Opciones: "+c)}else alert("El art\u00edculo no tiene opciones")}else alert("Error"+c.data)}catch(f){alert(a)}})};