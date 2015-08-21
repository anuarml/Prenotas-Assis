var UnitList = {
    list: null,
    pagingTop: null,
    pagingBottom: null,
    pagingText: null,
    aUnit: [],
    
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
                UnitList.show(UnitList.pagingInfo.currentPage - 1);
            }
        };
        
        this.pagingBottom.onclick = function() {
            if(this.className != 'disabled') {
                UnitList.show(UnitList.pagingInfo.currentPage + 1);
            }
        };
        
        for(j=0; j < this.pagingInfo.perPage; j++) {
            this.list.children[j].children[0].onclick = this.selectUnit;
        }
        //asl.notify(asl.notifications.application, asl.priority.normal,'m','1',['OK'],[null]);
        //this.requestCombinationList();
        UnitList.aUnit = JSON.parse(window.localStorage.aItemUnits);
        this.show(0);
    },
    
    show: function(page) {
        var t = UnitList.aUnit;

        this.pagingInfo.currentPage = page;
        this.pagingInfo.totalCount = (t.length > 0) ? t.length : 1;
        this.pagingText.innerText =  (page + 1) + '/' + ( Math.ceil(this.pagingInfo.totalCount / this.pagingInfo.perPage) );
        
        for(var j = 0, length = t.length, i = length - 1 - (page * this.pagingInfo.perPage); (i >= 0) && (j < this.pagingInfo.perPage); i--, j++) {
	        this.list.children[j].children[0].children[0].innerHTML = t[i].name;          
			this.list.children[j].children[0].children[1].innerHTML = '<div></div> <div></div>';
			
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

    selectUnit : function(e){
        var nUnitSelected = parseInt(e.currentTarget.id);

        window.localStorage.setItem('nUnitSelected', JSON.stringify(nUnitSelected));
        window.location = 'scan_product.html';
    }
};

asl.title(' ');

asl.options(null);

asl.back(null);

asl.events.subscribe(asl.events.types.exit, function() {
    asl.badge(null);
    if (typeof asl.lock == 'function'){
        asl.lock(null);
    }
});

asl.events.subscribe(asl.events.types.loaded, function() {
    UnitList.init();
});
