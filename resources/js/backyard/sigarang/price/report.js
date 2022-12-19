document.addEventListener('DOMContentLoaded', (event) => {
    methods.initDatePicker();
    methods.initOnClickBtnPdf();
    methods.initBtnPilihSemuaListener();
    methods.initBtnHapusPilihanListener();
});

const methods = {
    initDatePicker() {
        $('.datepicker').datepicker({
            autoSize: true,
            changeMonth: true,
            changeYear: true,
            dateFormat: "dd-mm-yy",
        });
    },
    initOnClickBtnPdf() {
        const btnPdf = document.getElementById("btnPdf");
        btnPdf.addEventListener('click', function(e){
            methods.downloadPdf(e);
        });
    },
    initBtnPilihSemuaListener() {
        const btnPilihSemua = document.getElementById("btn-pilih-semua");
        const stocks = document.getElementsByName("goods[]");
        btnPilihSemua.addEventListener('click', (e) => {
            e.preventDefault();
            stocks.forEach(stock => {
                stock.checked = true;
            });
        });
    },
    initBtnHapusPilihanListener() {
        const btnHapusSemua = document.getElementById("btn-hapus-semua");
        const stocks = document.getElementsByName("goods[]");
        btnHapusSemua.addEventListener('click', (e) => {
            e.preventDefault();
            stocks.forEach(stock => {
                stock.checked = false;
            });
        });
    },
    downloadPdf() {
        const link = window['_reportFormData'].pdfLink;
        const forms = document.forms;
        let startDate, endDate, marketId, goodIds, _token;
        _token = forms[0].elements["_token"].value;
        startDate = forms[0].elements["start_date"].value;
        endDate = forms[0].elements["end_date"].value;
        marketId = forms[0].elements["market_id"].value;
        goodIds = [];
        goods = forms[0].elements["goods[]"];
        for(key=0; key < goods.length; key++){
            if(goods[key].checked){
                goodIds.push(goods[key].value);
            }
        }
        fetch(link, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": _token,
            },
            body: JSON.stringify({
                'start_date':startDate, 'end_date':endDate, 'market_id':marketId, 'goods':goodIds
            })
        })
        .then(response=>response.blob())
        .then(data => window.open(URL.createObjectURL(data)));
    }
};