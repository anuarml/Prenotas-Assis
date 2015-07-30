var CombinationList = {
    list: null,
    pagingTop: null,
    pagingBottom: null,
    pagingText: null,
    aCombination: [],
    
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
                CombinationList.show(CombinationList.pagingInfo.currentPage - 1);
            }
        };
        
        this.pagingBottom.onclick = function() {
            if(this.className != 'disabled') {
                CombinationList.show(CombinationList.pagingInfo.currentPage + 1);
            }
        };
        
        for(j=0; j < this.pagingInfo.perPage; j++) {
            this.list.children[j].onclick = this.selectCombination;
        }
        //asl.notify(asl.notifications.application, asl.priority.normal,'m','1',['OK'],[null]);
        //this.requestCombinationList();
        var optionListNum = JSON.parse(window.localStorage.getItem('optionListNum'));
        
        var product = JSON.parse(window.localStorage.getItem('product'));
        asl.title(product.options[optionListNum].name);

        this.requestOptionList( product.ID, product.options[optionListNum].optionID);
    },

    requestOptionList: function(itemID, optionID){
        var aOptionDetail = window.localStorage.getItem('aOptionDetail');

        var url = 'php/db/get_item_option_list.php?optionID='+optionID+'&itemId='+itemID+'&aOptionDetails='+aOptionDetail;

        var handle = function(response){
            try{
                var aOptions = JSON.parse(response);

                if(aOptions){
                    var product = JSON.parse(window.localStorage.getItem('product'));
                    var optionListNum = JSON.parse(window.localStorage.getItem('optionListNum'));

                    if( product.options[optionListNum].mandatory == 0 ) {
                        aOptions.push({
                            id: 0,
                            name: 'Omitir',
                            externalID: '',
                            quantity: ''
                        });
                    }

                    CombinationList.aCombination = aOptions;
                }
                CombinationList.show(0);
            }
            catch(e){
                alert(response);
            }
        };

        ajaxRequest('GET', url, handle);
    },
    
    show: function(page) {
        var t = CombinationList.aCombination;

        this.pagingInfo.currentPage = page;
        this.pagingInfo.totalCount = (t.length > 0) ? t.length : 1;
        this.pagingText.innerText =  (page + 1) + '/' + ( Math.ceil(this.pagingInfo.totalCount / this.pagingInfo.perPage) );
        
        for(var j = 0, length = t.length, i = length - 1 - (page * this.pagingInfo.perPage); (i >= 0) && (j < this.pagingInfo.perPage); i--, j++) {
	        this.list.children[j].children[0].children[0].innerHTML = t[i].name;          
			this.list.children[j].children[0].children[1].innerHTML = '<div>' + t[i].externalID + '</div> <div></div>';
			
            this.list.children[j].id = t[i].id;
            if (t[i].id) {
                this.list.children[j].children[1].innerHTML = parseInt(t[i].quantity) || 0;
            } else {
                this.list.children[j].children[1].innerHTML = '';
            }

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

    selectCombination : function(e){

        var optionID = parseInt(e.currentTarget.id);
        var optionListNum = JSON.parse(window.localStorage.getItem('optionListNum'));
        var product = JSON.parse(window.localStorage.getItem('product'));
		
		if(parseInt(e.currentTarget.children[1].innerHTML) == 0){
			asl.notify(asl.notifications.application, asl.priority.normal,'Mensaje:' ,'No hay artículos disponibles con esa opción',['OK'],[null]);
			return;
		}

        if ( optionID ) {
            var aOptionDetail = JSON.parse( window.localStorage.getItem('aOptionDetail'));

            aOptionDetail.push(optionID);

            window.localStorage.setItem('aOptionDetail',JSON.stringify(aOptionDetail));
        }

        if ( optionListNum+1 < product.options.length ) {
            window.localStorage.setItem('optionListNum',JSON.stringify(optionListNum+1));
            window.location = 'combination_list.html';
        } else {
            window.location = 'scan_product.html';
        }
    }
};

function backToProducts(){
	window.localStorage.removeItem('aOptionDetail');
	window.location = "scan_product.html";
}

asl.options(null);

asl.back(backToProducts);

asl.events.subscribe(asl.events.types.exit, function() {
    asl.badge(null);
});

asl.events.subscribe(asl.events.types.loaded, function() {
    CombinationList.init();
});
