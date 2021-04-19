import {Loader} from '@googlemaps/js-api-loader';
const axios = require ('axios');
const _data = window[`_dashboardData`];
const defaultCenter = {lat: -7.044662, lng: 113.243100};
let priceGraph, stockGraph, map, mapsApi, areas = [], dataLayer;

document.addEventListener('DOMContentLoaded', (event) => {
    methods.initPriceGraph();
    methods.initStockGraph();
    methods.initLeaflet();
    methods.initDatePicker();
});

const methods = {
    initDatePicker() {
        $('#map-date').datepicker({
            autoSize: true,
            changeMonth: true,
            changeYear: true,
            dateFormat: "dd MM yy",
            onSelect: (date) => {methods.getLeafletData(date)},
            beforeShow: function() {
                setTimeout(function(){
                    $('.ui-datepicker').css('z-index', 99999999999999);
                }, 0);
            }
        });
    },
    initPriceGraph() {
        const elSelectPriceMarket = document.getElementById('price-market-select');
        const elSelectPriceGoods = document.getElementById('price-goods-select');
        let marketId = elSelectPriceMarket.value;
        let goodsId = elSelectPriceGoods.value;
        elSelectPriceMarket.addEventListener("change",(event) => {
            marketId = event.target.value;
            methods.getPriceGraphData(marketId, goodsId);
        });
        elSelectPriceGoods.addEventListener("change",(event) => {
            goodsId = event.target.value;
            methods.getPriceGraphData(marketId, goodsId);
        });
        methods.getPriceGraphData(marketId, goodsId);
    },
    initStockGraph() {
        const elSelectStockMarket = document.getElementById('stock-market-select');
        const elSelectStockGoods = document.getElementById('stock-goods-select');
        let marketId = elSelectStockMarket.value;
        let goodsId = elSelectStockGoods.value;
        elSelectStockMarket.addEventListener("change",(event) => {
            marketId = event.target.value;
            methods.getStockGraphData(marketId, goodsId);
        });
        elSelectStockGoods.addEventListener("change",(event) => {
            goodsId = event.target.value;
            methods.getStockGraphData(marketId, goodsId);
        });
        methods.getStockGraphData(marketId, goodsId);
    },
    getPriceGraphData(marketId, goodsId){
        const csrfToken = document.querySelector('meta[name=csrf-token]').content;
        axios.post(_data.routeGetPriceGraphData, {_token: csrfToken, market_id:marketId, goods_id:goodsId})
        .then(res => {
            methods.drawPriceGraph(res.data);
        });
    },
    getStockGraphData(marketId, goodsId){
        const csrfToken = document.querySelector('meta[name=csrf-token]').content;
        axios.post(_data.routeGetStockGraphData, {_token: csrfToken, market_id:marketId, goods_id:goodsId})
        .then(res => {
            methods.drawStockGraph(res.data);
        });
    },
    drawPriceGraph(data){
        const priceCanvas = document.getElementById('price-graph');
        const options = {
            graphName: "priceGrap",
            canvas: priceCanvas,
            typeGraph: "line",
            data: {
                datasets : [data,],
            },
            options: {
                datasets: {
                    line: {
                        backgroundColor: 'rgba(32, 201, 151, 1)',
                    },
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
                    yAxes: [{
                        scaleLabel: {
                            display: true,
                            labelString: 'Value',
                        },
                    }],
                },
            },
        };
        if(priceGraph!=undefined){
           methods.deleteChart(priceGraph);
        }
        priceGraph = methods.drawGraph(options);
    },
    drawStockGraph(data) {
        const stockCanvas = document.getElementById('stock-graph');
        const options = {
            graphName: "stockGraph",
            canvas: stockCanvas,
            typeGraph: "line",
            data: {
                datasets : [data,],
            },
            options: {
                datasets: {
                    line: {
                        backgroundColor: 'rgba(32, 201, 151, 1)',
                    },
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
                    yAxes: [{
                        scaleLabel: {
                            display: true,
                            labelString: 'Value',
                        },
                    }],
                },
            },
        };
        if(stockGraph!=undefined){
            methods.deleteChart(stockGraph);
        }
        stockGraph = methods.drawGraph(options);
    },
    drawGraph(params) {
        return new Chart(params.canvas, {
            type: params.typeGraph,
            data: params.data,
            options: params.options,
        });
    },
    deleteChart(chart) {
        chart.destroy();
    },
    initLeaflet(){
        const elDatePickerMap = document.getElementById('map-date');
        const rawDate = new Date();
        let date = moment().format("DD MMMM YYYY");
        elDatePickerMap.value = date;
        methods.drawLeafletMap();
        methods.getLeafletData(date);
    },
    drawLeafletMap(){
        map = L.map('map-section',{
            zoomControl:false,
            scrollWheelZoom: false,
        }).setView([defaultCenter.lat, defaultCenter.lng],10);

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

        const mapInfoBox = L.control({position:'topleft'});
        mapInfoBox.onAdd = (map) => {
            this._div = L.DomUtil.get('map-info-box');
            this._div.style.zIndex = 500;
            return this._div;
        }
        mapInfoBox.addTo(map);
    },
    getLeafletData(date){
        const csrfToken = document.querySelector('meta[name=csrf-token]').content;
        axios.post(_data.routeGetMapData, {_token: csrfToken, date: date})
            .then((res) => {
                if(dataLayer){
                    dataLayer.clearLayers();
                }
                dataLayer = L.geoJSON(res.data.features, {
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
                        return {color:feature.properties.color};
                    },
                    coordsToLatLng: function (coords) {
                        return new L.LatLng(coords[0], coords[1], coords[2]);
                    },
                }).addTo(map);
            });
    },
    onMouseEnterEvent(data){
        const infoBox = document.getElementById('map-info-box');
        const title = document.getElementById('map-info-box-title');
        const note = document.getElementById('map-info-box-note');
        title.innerHTML = data.name;
        note.innerHTML = `${data.completion_percentage.toFixed(2)}%`;
        infoBox.style.background = '#fff';
        infoBox.style.borderRadius = '5%';
        infoBox.style.border = '2px solid black';
        infoBox.style.zIndex = 500;
        infoBox.style.visibility = 'visible';
    },
    onMouseMoveEvent(e) {
        const infoBox = document.getElementById('map-info-box');
        let left = e.originalEvent.layerX + 5;
        let top = e.originalEvent.layerY + 5;
        infoBox.style.left = `${left}px`;
        infoBox.style.top = `${top}px`;
    },
    onMouseLeaveEvent() {
        const infoBox = document.getElementById('map-info-box');
        infoBox.style.visibility = 'hidden';
    },
};
