var PrintersList = {
    list: null,
    pagingTop: null,
    pagingBottom: null,
    pagingText: null,
    printers: [],
    
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
                PrintersList.show(PrintersList.pagingInfo.currentPage - 1);
            }
        };
        
        this.pagingBottom.onclick = function() {
            if(this.className != 'disabled') {
                PrintersList.show(PrintersList.pagingInfo.currentPage + 1);
            }
        };
        
        for(j=0; j < this.pagingInfo.perPage; j++) {
            this.list.children[j].children[0].onclick = this.selectPrinter;
        }
        this.requestPrinterList();
    },
    
    show: function(page) {
        var t = PrintersList.printers;

        this.pagingInfo.currentPage = page;
        this.pagingInfo.totalCount = (t.length > 0) ? t.length : 1;
        this.pagingText.innerText =  (page + 1) + '/' + ( Math.ceil(this.pagingInfo.totalCount / this.pagingInfo.perPage) );
        
        for(var j = 0, length = t.length, i = length - 1 - (page * this.pagingInfo.perPage); (i >= 0) && (j < this.pagingInfo.perPage); i--, j++) {
	        this.list.children[j].children[0].children[0].innerHTML = t[i].name;          
			this.list.children[j].children[0].children[1].innerHTML = '<div>' + t[i].ip + '</div> <div></div>';
			
            this.list.children[j].children[0].printer = JSON.stringify(t[i]);

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

    selectPrinter : function(e){
        //var printerNumber = parseInt(e.currentTarget.id);
        var printer = e.currentTarget.printer;

        window.localStorage.setItem('selectedPrinter', printer);
        window.location = 'prenota.html';
    },

    requestPrinterList: function(){

        var url = 'php/get_printer_list.php';

        var handle = function(aPrinters){
            try{
                if(aPrinters){
                    PrintersList.printers = JSON.parse(aPrinters);
                }
                PrintersList.show(0);
            }
            catch(e){
                alert(response);
            }
        };

        ajaxRequest('GET', url, handle);
    }
};

function answerCotization(){
	window.location = "cotization_number.html";
}

function cotization_verification(){
	asl.notify(asl.notifications.application,asl.priority.normal,'Mensaje:','¿Venta Pagos Fijos?:', ['SI','NO'], [answerCotization,null]);
}

asl.title('Impresoras');

asl.options(null);

asl.back(function(){
    window.location = 'prenota.html';
});

asl.events.subscribe(asl.events.types.exit, function() {
    asl.badge(null);
    if (typeof asl.lock == 'function'){
        asl.lock(null);
    }
});

var isCotization;

asl.events.subscribe(asl.events.types.loaded, function() {
	PrintersList.init();

	isCotization = JSON.parse(window.localStorage.getItem('answerOfCotization'));
    isFirstPrint = JSON.parse(window.localStorage.getItem('nueva_prenota'));
	if(isFirstPrint){
        if(isCotization == null){
            cotization_verification();
       }
    }
       
});