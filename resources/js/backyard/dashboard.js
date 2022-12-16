import {Loader} from '@googlemaps/js-api-loader';
const axios = require ('axios');
const _data = window[`_dashboardData`];
const defaultCenter = {lat: -7.044662, lng: 113.243100};
let priceGraph, stockGraph, map, mapsApi, areas = [], dataLayer;

document.addEventListener('DOMContentLoaded', (event) => {
    methods.initPriceGraph();
    methods.initStockGraph();
    methods.initMapSection();
    methods.initDatePicker();
});

const methods = {
    initDatePicker() {
        $('#map-date').datepicker({
            autoSize: true,
            changeMonth: true,
            changeYear: true,
            dateFormat: "dd MM yy",
            onSelect: (date) => {methods.getMapData(date)},
            beforeShow: function() {
                setTimeout(function(){
                    $('.ui-datepicker').css('z-index', 99999999999999);
                }, 0);
            }
        });
    },
    initMapSection() {
        const elDatePickerMap = document.getElementById('map-date');
        const rawDate = new Date();
        let date = moment().format("DD MMMM YYYY");
        elDatePickerMap.value = date;
        methods.getMapData(date);
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
    getMapData(date) {
        const csrfToken = document.querySelector('meta[name=csrf-token]').content;
        axios.post(_data.routeGetMapData, {_token: csrfToken, date: date})
        .then(res => {
            methods.drawMap(res.data);
        })
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
    async drawMap(data){
        const elMapSection = document.getElementById('map-section');
        const elMapLegend = document.getElementById('map-info-legend');
        const elMapBox = document.getElementById('map-info-box');
        const loader = new Loader({
            apiKey: 'AIzaSyC1rasZRBxyA3gnVTyYriUelsE2PqoC1MI',
        });
        mapsApi = await loader.load().then(() => google.maps );
        map = new mapsApi.Map(elMapSection, {
            center: defaultCenter,
            zoom: 10,
        });
        const options = {
            map: map,
            mapsApi: mapsApi,
            data: data,
        };
        if(areas.length > 0){
            methods.clearMap();
            areas = []
        }
        map.controls[mapsApi.ControlPosition.LEFT_BOTTOM].push(elMapLegend);
        map.controls[mapsApi.ControlPosition.LEFT_TOP].push(elMapBox);
        methods.generateArea(options);
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
    generateArea(options) {
        options.data.features.forEach(feature => {
            if(feature.geometry.coordinates.length > 0){
                const points = []
                const pointAreas = feature.geometry.coordinates[0];
                pointAreas.forEach(point => {
                    points.push(new mapsApi.LatLng(point[0],point[1]));
                });
                const area = new mapsApi.Polygon({
                    paths: points,
                    fillColor: feature.properties.color,
                    strokeColor: feature.properties.color,
                    completion_percentage: feature.properties.completion_percentage,
                    name: feature.properties.name
                });
                areas.push(area);
                mapsApi.event.addListener(area,"mouseover",(e) => {
                    const infoBox = document.getElementById('map-info-box');
                    const title = document.getElementById('map-info-box-title');
                    const note = document.getElementById('map-info-box-note');
                    title.innerHTML = area.name;
                    note.innerHTML = `${area.completion_percentage.toFixed(2)}%`;
                    infoBox.style.zIndex = 99;
                    infoBox.style.display = 'inline';
                });
                mapsApi.event.addListener(area,"mousemove",(e)=>{
                    const infoBox = document.getElementById('map-info-box');
                    let left = e.domEvent.offsetX + 20;
                    let top = e.domEvent.offsetY + 20;
                    infoBox.style.left = `${left}px`;
                    infoBox.style.top = `${top}px`;
                });
                mapsApi.event.addListener(area,"mouseout",(e) => {
                    const infoBox = document.getElementById('map-info-box');
                    infoBox.style.zIndex = -1;
                });
            }
        });
        areas.forEach(area => {
            area.setMap(options.map);
        });
    },
    clearMap(){
        areas.forEach(area => {
            area.setMap(null);
        });
    }
};
