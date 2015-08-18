var SerialList = {
    list: null,
    pagingTop: null,
    pagingBottom: null,
    pagingText: null,
    aSerial: [],
    
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
                SerialList.show(SerialList.pagingInfo.currentPage - 1);
            }
        };
        
        this.pagingBottom.onclick = function() {
            if(this.className != 'disabled') {
                SerialList.show(SerialList.pagingInfo.currentPage + 1);
            }
        };
        
        for(j=0; j < this.pagingInfo.perPage; j++) {
            this.list.children[j].onclick = this.selectSerial;
        }
        //asl.notify(asl.notifications.application, asl.priority.normal,'m','1',['OK'],[null]);
        //this.requestCombinationList();
        //var optionListNum = JSON.parse(window.localStorage.getItem('optionListNum'));
        
        var product = JSON.parse(window.localStorage.getItem('product'));
        var sTitle = '';
        
        if(product){
            if(product.ItemTypeID == ItemType.SERIE){
                sTitle = 'Serial';
            } else if(product.ItemTypeID == ItemType.LOTE){
                sTitle = 'Lote';
            }

            this.requestSerialList( product.ID, product.optionID);
        }
        
        asl.title(sTitle);

        
    },

    requestSerialList: function(itemID, combinationID){

        var url = 'php/db/get_item_serial_list.php?ItemID='+itemID+'&combinationID='+combinationID;

        var handle = function(response){
            try{
                var aSerials = JSON.parse(response);

                if(aSerials){

                    SerialList.aSerial = aSerials;
                }
                SerialList.show(0);
            }
            catch(e){
                alert(response);
            }
        };

        ajaxRequest('GET', url, handle);
    },
    
    show: function(page) {
        var t = SerialList.aSerial;

        this.pagingInfo.currentPage = page;
        this.pagingInfo.totalCount = (t.length > 0) ? t.length : 1;
        this.pagingText.innerText =  (page + 1) + '/' + ( Math.ceil(this.pagingInfo.totalCount / this.pagingInfo.perPage) );
        
        for(var j = 0, length = t.length, i = length - 1 - (page * this.pagingInfo.perPage); (i >= 0) && (j < this.pagingInfo.perPage); i--, j++) {
	        this.list.children[j].children[0].children[0].innerHTML = t[i].SerialBatch;          
			//this.list.children[j].children[0].children[1].innerHTML = '<div></div> <div></div>';
			
            this.list.children[j].id = i;
            this.list.children[j].children[0].children[1].children[1].innerHTML = parseFloat(t[i].Quantity) || 0;

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

    selectSerial : function(e){

        var aSerialsOffset = parseInt(e.currentTarget.id);
		
		/*if(parseInt(e.currentTarget.children[1].innerHTML) == 0){
			asl.notify(asl.notifications.application, asl.priority.normal,'Mensaje:' ,'No hay artículos disponibles.',['OK'],[null]);
			return;
		}*/

        window.localStorage.setItem('oSelectedSerial',JSON.stringify(SerialList.aSerial[aSerialsOffset]));

        window.location = 'scan_product.html';
    }
};

function backToProducts(){
	window.location = "scan_product.html";
}

asl.options(null);

asl.back(backToProducts);

asl.events.subscribe(asl.events.types.exit, function() {
    asl.badge(null);
});

asl.events.subscribe(asl.events.types.loaded, function() {
    SerialList.init();
});
