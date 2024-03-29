import { ceil, forIn } from 'lodash';
const axios = require ('axios');
const _data = window[`_landingPageData`];
const defaultCenter = {lat: -7.032801, lng: 113.228436};
let map, mapsApi, areas = [], mapGoodsId, mapDate, graphMarketId, graphDate, stockGraphMarketId, stockGraphDate, dataLayer, isMobile = window.orientation > -1;

document.addEventListener('DOMContentLoaded', (event) => {
    methods.initMapSectionWithLeaflet();
    methods.initGraphSection();
    methods.initStockGraphSection();
});

const methods = {
    initMapDatePicker() {
        $('#map-date').datepicker({
            autoSize: true,
            changeMonth: true,
            changeYear: true,
            dateFormat: "dd MM yy",
            onSelect: (date) => {
                mapDate = date;
                methods.getLeafletData(mapDate, mapGoodsId);
            },
        });
    },
    initMapGoodsSelect() {
        const elGoodsSelectMap = document.getElementById('map-goods');
        elGoodsSelectMap.addEventListener('change', (event) => {
            mapGoodsId = event.target.value;
            methods.getLeafletData(mapDate, mapGoodsId);
        })
    },
    initGraphSection() {
        graphMarketId = document.getElementById('graph-market').value;
        graphDate = moment().format("DD MMMM YYYY");
        const elDatePickerGraph = document.getElementById('graph-date');
        elDatePickerGraph.value = graphDate;
        methods.initGraphDatePicker();
        methods.initGraphMarketSelect();
        methods.getGraphData(graphDate, graphMarketId);
    },
    initGraphDatePicker() {
        $('#graph-date').datepicker({
            autoSize: true,
            changeMonth: true,
            changeYear: true,
            dateFormat: "dd MM yy",
            onSelect: (date) => {
                graphDate = date;
                methods.getGraphData(graphDate, graphMarketId);
            },
        });
    },
    initGraphMarketSelect() {
        const elMarketSelectGraph = document.getElementById('graph-market');
        elMarketSelectGraph.addEventListener('change', (event) => {
            graphMarketId = event.target.value;
            methods.getGraphData(graphDate, graphMarketId);
        })
    },
    getGraphData(date, marketId) {
        const csrfToken = document.querySelector('meta[name=csrf-token]').content;
        axios.post(_data.routeGetGraphData, {_token: csrfToken, date: date, market_id: marketId})
        .then(res => {
            methods.injectingDataToDom(res.data);
        });
    },
    injectingDataToDom(data) {
        const dataArray = Object.values(data.data[0]);
        const total = dataArray.length;
        const numberOfContainer = isMobile ? total : total % 9 == 0 ? floor(total / 9) : ceil(total / 9);
        const newElementCarouselContainer = document.querySelectorAll('.carousel-item')[0].cloneNode();
        newElementCarouselContainer.classList.remove("active");
        const newElementInfoBoxRow = document.querySelectorAll('.carousel-item>.row')[0].cloneNode();
        const newElementInfoBox = document.querySelectorAll('.carousel-item>.row>.info-box')[0].cloneNode(true);
        const elCarouselInner = document.querySelectorAll('.carousel-inner')[0];
        const newElCarouselNextControl = document.querySelectorAll('.carousel-control-next')[0].cloneNode(true);
        const newElCarouselPrevControl = document.querySelectorAll('.carousel-control-prev')[0].cloneNode(true);
        elCarouselInner.innerHTML = "";
        const newCarouselContainer = newElementCarouselContainer.cloneNode();
        elCarouselInner.appendChild(newCarouselContainer);
        for (let i = 0; i < numberOfContainer - 1; i++) {
            elCarouselInner.children[0].after(newElementCarouselContainer.cloneNode());
        }
        elCarouselInner.children[elCarouselInner.children.length - 1].after(newElCarouselNextControl.cloneNode(true));
        elCarouselInner.children[elCarouselInner.children.length - 1].after(newElCarouselPrevControl.cloneNode(true));
        let dataCounter = 0;
        const newElCarouselContainer = document.querySelectorAll('.carousel-item');
        newElCarouselContainer[0].classList.add('active');
        if (isMobile) {
            for (let i = 0; i < numberOfContainer; i++) {
                let newRow = newElementInfoBoxRow.cloneNode();
                let newInfoBox = newElementInfoBox.cloneNode(true);
                newInfoBox.children[0].children[0].innerHTML = dataArray[dataCounter].name;
                newInfoBox.children[1].innerHTML = "";
                let newInfoSec = document.createElement('DIV');
                let newGraphSec = document.createElement('CANVAS');
                newInfoSec.setAttribute('class','col-md-6 content-info');
                newGraphSec.setAttribute('class','col-md-6 content-info');
                newInfoBox.children[1].appendChild(newGraphSec);
                newInfoBox.children[1].appendChild(newInfoSec);
                newInfoBox.children[1].children[1].innerHTML = `${dataArray[dataCounter].curr_price} </br> per ${dataArray[dataCounter].unit} </br> ${dataArray[dataCounter].status} ${dataArray[dataCounter].diff_percentage} % (${dataArray[dataCounter].diff_last_price})`;
                let formattedHistPrice = [];
                for(const data in dataArray[dataCounter].hist_price){
                    formattedHistPrice.push({
                        x: data,
                        y: dataArray[dataCounter].hist_price[data],
                    });
                }
                methods.renderChart(newInfoBox.children[1].children[0], formattedHistPrice);
                newRow.appendChild(newInfoBox);
                dataCounter++;
                newElCarouselContainer[i].appendChild(newRow);
            }
        } else {
            elCarouselInner.setAttribute('style', 'height:75vh')
            for (let i = 0; i < numberOfContainer; i++) {
                let maxRow = 3;
                if(i == numberOfContainer-1){
                    if (total % 9 <= 3) {
                        maxRow = 1;
                    } else if (total % 9 <= 6) {
                        maxRow = 2;
                    }
                }
                for (let j = 0; j < maxRow; j++) {
                    let maxInfoBox = 3;
                    if (j == maxRow - 1 && i == numberOfContainer-1){
                        maxInfoBox = total % 3 > 0 ? total % 3 : 3;
                    }
                    let newRow = newElementInfoBoxRow.cloneNode();
                    for (let k = 0; k < maxInfoBox; k++) {
                        let newInfoBox = newElementInfoBox.cloneNode(true);
                        newInfoBox.children[0].children[0].innerHTML = dataArray[dataCounter].name;
                        newInfoBox.children[1].innerHTML = "";
                        let newInfoSec = document.createElement('DIV');
                        let newGraphSec = document.createElement('CANVAS');
                        newInfoSec.setAttribute('class','col-md-6 content-info');
                        newGraphSec.setAttribute('class','col-md-6 content-info');
                        newInfoBox.children[1].appendChild(newGraphSec);
                        newInfoBox.children[1].appendChild(newInfoSec);
                        newInfoBox.children[1].children[1].innerHTML = `${dataArray[dataCounter].curr_price} </br> per ${dataArray[dataCounter].unit} </br> ${dataArray[dataCounter].status} ${dataArray[dataCounter].diff_percentage} % (${dataArray[dataCounter].diff_last_price})`;
                        let formattedHistPrice = [];
                        for(const data in dataArray[dataCounter].hist_price){
                            formattedHistPrice.push({
                                x: data,
                                y: dataArray[dataCounter].hist_price[data],
                            });
                        }
                        methods.renderChart(newInfoBox.children[1].children[0], formattedHistPrice);
                        newRow.appendChild(newInfoBox);
                        dataCounter++;
                    }
                    newElCarouselContainer[i].appendChild(newRow);
                }
            }
        }
    },
    renderChart (el, data) {
        const chart = new Chart(el, {
            type: 'line',
            data: {
                datasets: [{
                    data: data,
                }],
            },
            options: {
                elements: {
                    point: {
                        backgroundColor: 'rgba(242,243,243,1)',
                        borderColor: 'rgba(242,243,243,1)',
                    },
                    line: {
                        borderColor: 'rgba(242,243,243,1)',
                    },
                    arc: {
                        backgroundColor: 'rgba(242,243,243,1)',
                        borderColor: 'rgba(242,243,243,1)',
                    },
                },
                legend: {
                    display: false,
                },
                scales: {
                    xAxes: [{
                        type: 'time',
                        time: {
                            parser: "DD-MM-YYYY",
                            tooltipFormat: "DD MMMM YYYY",
                        },
                        scaleLabel: {
                            display: true,
                            labelString: 'Date'
                        },
                    }],
                },
            },
        })
    },
    initMapSectionWithLeaflet() {
        mapGoodsId = document.getElementById('map-goods').value;
        mapDate = moment().format("DD MMMM YYYY");
        const elDatePickerMap = document.getElementById('map-date');
        elDatePickerMap.value = mapDate;
        methods.initMapDatePicker();
        methods.initMapGoodsSelect();
        methods.drawMap();
        methods.getLeafletData(mapDate, mapGoodsId);
    },
    getLeafletData(date, goodsId) {
        const csrfToken = document.querySelector('meta[name=csrf-token]').content;
        axios.post(_data.routeGetMapData, {_token: csrfToken, date: date, goods_id: goodsId})
            .then((res) => {
                let avgValue = L.DomUtil.get('map-avg-value');
                avgValue.innerHTML = `Rp.${res.data.avgPrice},00`;
                if(dataLayer){
                    dataLayer.clearLayers();
                }
                dataLayer = L.geoJSON(res.data.dataPrice.features, {
                    onEachFeature: (feature, layer) => {
                        layer.on('mouseover', (e) => {
                            methods.onMouseEnterEvent(feature.properties);
                        });
                        layer.on('mousemove', (e) => {
                            methods.onMouseMoveEvent(e);
                        });
                        layer.on('mouseout', (e) => {
                            methods.onMouseLeaveEvent();
                        });
                    },
                    style: (feature) => {
                        return {color:feature.properties.fillColor};
                    },
                    coordsToLatLng: function (coords) {
                        return new L.LatLng(coords[0], coords[1], coords[2]);
                    },
                }).addTo(map);
            });
    },
    drawMap() {
        map = L.map('map-section',{
            zoomControl:false,
            scrollWheelZoom: false,
        }).setView([defaultCenter.lat, defaultCenter.lng],11);

        L.tileLayer('https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token=pk.eyJ1IjoibWFkbmV3YmllMzkiLCJhIjoiY2ttcnJ3d3BsMGFwZjJvcXl5cmR0ejN6YyJ9.TjAJY-ecJO_hT3vOuUwl1Q', {
            attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors, Imagery © <a href="https://www.mapbox.com/">Mapbox</a>',
            maxZoom: 13,
            id: 'mapbox/streets-v11',
            tileSize: 512,
            zoomOffset: -1,
            accessToken: 'pk.eyJ1IjoibWFkbmV3YmllMzkiLCJhIjoiY2ttcnJ3d3BsMGFwZjJvcXl5cmR0ejN6YyJ9.TjAJY-ecJO_hT3vOuUwl1Q',
        }).addTo(map);

        L.control.zoom({
            position: 'bottomright',
        }).addTo(map);

        const mapLegend = L.control({position:'bottomleft'});
        mapLegend.onAdd = (map) => {
            this._div = L.DomUtil.get('map-info-legend');
            this._div.style.zIndex = 500;
            this._div.style.display = 'inline';
            return this._div;
        };
        mapLegend.addTo(map);

        const mapAvgPrice = L.control({position:'bottomleft'});
        mapAvgPrice.onAdd = (map) => {
            this._div = L.DomUtil.get('map-info-avg-price');
            this._div.style.zIndex = 500;
            this._div.style.display = 'inline';
            return this._div;
        }
        mapAvgPrice.addTo(map);
    },
    onMouseEnterEvent(data){
        const infoBox = document.getElementById('map-info-box');
        const title = document.getElementById('map-info-box-title');
        const price = document.getElementById('map-info-box-price');
        const note = document.getElementById('map-info-box-note');
        const stock = document.getElementById('map-info-box-stock');
        title.innerHTML = data.name;
        price.innerHTML = data.price > 0 ? `Rp. ${data.price}`: 'Belum ada data';
        note.innerHTML = data.note;
        stock.innerHTML = data.stock > 0 ? `Stok tersedia: ${data.stock} ${data.unit}` : 'Stok tersedia: Belum ada data';
        infoBox.style.visibility = 'visible';
        infoBox.style.zIndex = 500;
        infoBox.style.display = 'inline';
    },
    onMouseMoveEvent(e) {
        const infoBox = document.getElementById('map-info-box');
        let left = e.originalEvent.offsetX + 5;
        let top = e.originalEvent.offsetY + 5;
        infoBox.style.left = `${left}px`;
        infoBox.style.top = `${top}px`;
    },
    onMouseLeaveEvent() {
        const infoBox = document.getElementById('map-info-box');
        infoBox.style.visibility = 'hidden';
    },
    /* Fitur gak jelas */
    initStockGraphSection() {
        stockGraphMarketId = document.getElementById('stock-market').value;
        stockGraphDate = moment().format("DD MMMM YYYY");
        const elDatePickerGraph = document.getElementById('stock-date');
        elDatePickerGraph.value = stockGraphDate;
        methods.initStockGraphDatePicker();
        methods.initStockGraphMarketSelect();
        methods.getStockGraphData(stockGraphDate, stockGraphMarketId);
    },
    initStockGraphDatePicker() {
        $('#stock-date').datepicker({
            autoSize: true,
            changeMonth: true,
            changeYear: true,
            dateFormat: "dd MM yy",
            onSelect: (date) => {
                stockGraphDate = date;
                methods.getStockGraphData(stockGraphDate, stockGraphMarketId);
            },
        });
    },
    initStockGraphMarketSelect() {
        const elMarketSelectGraph = document.getElementById('stock-market');
        elMarketSelectGraph.addEventListener('change', (event) => {
            stockGraphMarketId = event.target.value;
            methods.getStockGraphData(stockGraphDate, stockGraphMarketId);
        })
    },
    getStockGraphData(date, marketId) {
        const csrfToken = document.querySelector('meta[name=csrf-token]').content;
        axios.post(_data.routeGetStockGraphData, {_token: csrfToken, date: date, market_id: marketId})
        .then(res => {
            methods.injectingStockDataToDom(res.data);
        });
    },
    injectingStockDataToDom(data) {
        const dataArray = Object.values(data.data[0]);
        const total = dataArray.length;
        const numberOfContainer = isMobile ? total : total % 9 == 0 ? floor(total / 9) : ceil(total / 9);
        const stockGraphSection = document.getElementById('stockCarousel');
        const newElementCarouselContainer = stockGraphSection.childNodes[1].childNodes[1].cloneNode();
        newElementCarouselContainer.classList.remove("active");
        const newElementInfoBoxRow = stockGraphSection.childNodes[1].childNodes[1].childNodes[1].cloneNode();
        const newElementInfoBox = stockGraphSection.childNodes[1].childNodes[1].childNodes[1].childNodes[1].cloneNode(true);
        const elCarouselInner = stockGraphSection.childNodes[1];
        const newElCarouselNextControl = document.querySelector('#stockCarousel>.carousel-inner>.carousel-control-next').cloneNode(true);
        const newElCarouselPrevControl = document.querySelector('#stockCarousel>.carousel-inner>.carousel-control-prev').cloneNode(true);
        elCarouselInner.innerHTML = "";
        const newCarouselContainer = newElementCarouselContainer.cloneNode();
        elCarouselInner.appendChild(newCarouselContainer);
        for (let i = 0; i < numberOfContainer - 1; i++) {
            elCarouselInner.children[0].after(newElementCarouselContainer.cloneNode());
        }
        elCarouselInner.children[elCarouselInner.children.length - 1].after(newElCarouselNextControl.cloneNode(true));
        elCarouselInner.children[elCarouselInner.children.length - 1].after(newElCarouselPrevControl.cloneNode(true));
        let dataCounter = 0;
        const newElCarouselContainer = stockGraphSection.childNodes[1].childNodes;
        elCarouselInner.childNodes[0].classList.add('active');
        if (isMobile) {
            for (let i = 0; i < numberOfContainer; i++) {
                let newRow = newElementInfoBoxRow.cloneNode();
                let newInfoBox = newElementInfoBox.cloneNode(true);
                newInfoBox.children[0].children[0].innerHTML = dataArray[dataCounter].name;
                newInfoBox.children[1].innerHTML = "";
                let newInfoSec = document.createElement('DIV');
                let newGraphSec = document.createElement('CANVAS');
                newInfoSec.setAttribute('class','col-md-6 content-info');
                newGraphSec.setAttribute('class','col-md-6 content-info');
                newInfoBox.children[1].appendChild(newGraphSec);
                newInfoBox.children[1].appendChild(newInfoSec);
                newInfoBox.children[1].children[1].innerHTML = `${dataArray[dataCounter].curr_stock} ${dataArray[dataCounter].unit} </br> ${dataArray[dataCounter].status} ${dataArray[dataCounter].diff_percentage} % (${dataArray[dataCounter].diff_last_stock})`;
                let formattedHistStock = [];
                for(const data in dataArray[dataCounter].hist_stock){
                    formattedHistStock.push({
                        x: data,
                        y: dataArray[dataCounter].hist_stock[data],
                    });
                }
                methods.renderChart(newInfoBox.children[1].children[0], formattedHistStock);
                newRow.appendChild(newInfoBox);
                dataCounter++;
                newElCarouselContainer[i].appendChild(newRow);
            }
        } else {
            elCarouselInner.setAttribute('style', 'height:75vh');
            for (let i = 0; i < numberOfContainer; i++) {
                let maxRow = 3;
                if(i == numberOfContainer-1){
                    if (total % 9 <= 3) {
                        maxRow = 1;
                    } else if (total % 9 <= 6) {
                        maxRow = 2;
                    }
                }
                for (let j = 0; j < maxRow; j++) {
                    let maxInfoBox = 3;
                    if (j == maxRow - 1 && i == numberOfContainer-1){
                        maxInfoBox = total % 3 > 0 ? total % 3 : 3;
                    }
                    let newRow = newElementInfoBoxRow.cloneNode();
                    for (let k = 0; k < maxInfoBox; k++) {
                        let newInfoBox = newElementInfoBox.cloneNode(true);
                        newInfoBox.children[0].children[0].innerHTML = dataArray[dataCounter].name;
                        newInfoBox.children[1].innerHTML = "";
                        let newInfoSec = document.createElement('DIV');
                        let newGraphSec = document.createElement('CANVAS');
                        newInfoSec.setAttribute('class','col-md-6 content-info');
                        newGraphSec.setAttribute('class','col-md-6 content-info');
                        newInfoBox.children[1].appendChild(newGraphSec);
                        newInfoBox.children[1].appendChild(newInfoSec);
                        newInfoBox.children[1].children[1].innerHTML = `${dataArray[dataCounter].curr_stock} ${dataArray[dataCounter].unit} </br> ${dataArray[dataCounter].status} ${dataArray[dataCounter].diff_percentage} % (${dataArray[dataCounter].diff_last_stock})`;
                        let formattedHistStock = [];
                        for(const data in dataArray[dataCounter].hist_stock){
                            formattedHistStock.push({
                                x: data,
                                y: dataArray[dataCounter].hist_stock[data],
                            });
                        }
                        methods.renderChart(newInfoBox.children[1].children[0], formattedHistStock);
                        newRow.appendChild(newInfoBox);
                        dataCounter++;
                    }
                    newElCarouselContainer[i].appendChild(newRow);
                }
            }
        }
    },
}
