import {Loader} from '@googlemaps/js-api-loader';
import { ceil } from 'lodash';
const axios = require ('axios');
const _data = window[`_landingPageData`];
const defaultCenter = {lat: -7.032801, lng: 113.228436};
let map, mapsApi, areas = [], mapGoodsId, mapDate, graphMarketId, graphDate;

document.addEventListener('DOMContentLoaded', (event) => {
    methods.initMapSection();
    methods.initGraphSection();
});

const methods = {
    initMapSection() {
        mapGoodsId = document.getElementById('map-goods').value;
        mapDate = moment().format("DD MMMM YYYY");
        const elDatePickerMap = document.getElementById('map-date');
        elDatePickerMap.value = mapDate;
        methods.initMapDatePicker();
        methods.initMapGoodsSelect();
        methods.getMapData(mapDate, mapGoodsId);
    },
    initMapDatePicker() {
        $('#map-date').datepicker({
            autoSize: true,
            changeMonth: true,
            changeYear: true,
            dateFormat: "dd MM yy",
            onSelect: (date) => {
                mapDate = date;
                methods.getMapData(mapDate, mapGoodsId);
            },
        });
    },
    initMapGoodsSelect() {
        const elGoodsSelectMap = document.getElementById('map-goods');
        elGoodsSelectMap.addEventListener('change', (event) => {
            mapGoodsId = event.target.value;
            methods.getMapData(mapDate, mapGoodsId);
        })
    },
    getMapData(date, goodsId) {
        const csrfToken = document.querySelector('meta[name=csrf-token]').content;
        axios.post(_data.routeGetMapData, {_token: csrfToken, date: date, goods_id: goodsId})
        .then(res => {
            methods.drawMap(res.data.data.dataPrice);
            const elMapAvgValue = document.getElementById('map-info-box-avg-value');
            elMapAvgValue.innerHTML = `Rp. ${res.data.data.avgPrice}`;
        });
    },
    async drawMap(data) {
        const elMapSection = document.getElementById('map-section');
        const elMapLegend = document.getElementById('map-info-legend');
        const loader = new Loader({
            apiKey: 'AIzaSyC1rasZRBxyA3gnVTyYriUelsE2PqoC1MI',
        });
        mapsApi = await loader.load().then(() => google.maps );
        map = new mapsApi.Map(elMapSection, {
            center: defaultCenter,
            zoom: 11,
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
        methods.generateArea(options);
    },
    generateArea(options) {
        const elMapLegend = document.getElementById('map-info-legend');
        options.data.features.forEach(feature => {
            if(feature.geometry.coordinates.length > 0){
                const points = []
                const pointAreas = feature.geometry.coordinates[0];
                pointAreas.forEach(point => {
                    points.push(new mapsApi.LatLng(point[0],point[1]));
                });
                const area = new mapsApi.Polygon({
                    paths: points,
                    fillColor: feature.properties.fillColor,
                    strokeColor: feature.properties.fillColor,
                });
                areas.push(area);
                mapsApi.event.addListener(area,"mouseover",(e) => {
                    const infoBox = document.getElementById('map-info-box');
                    const title = document.getElementById('map-info-box-title');
                    const price = document.getElementById('map-info-box-price');
                    const note = document.getElementById('map-info-box-note');
                    title.innerHTML = feature.properties.name;
                    price.innerHTML = feature.properties.price > 0 ? `Rp. ${feature.properties.price}`: 'Belum ada data';
                    note.innerHTML = feature.properties.note;
                    infoBox.style.zIndex = 99;
                    infoBox.style.display = 'inline';
                });
                mapsApi.event.addListener(area,"mousemove",(e)=>{
                    const infoBox = document.getElementById('map-info-box');
                    let left = e.domEvent.offsetX + 75;
                    let top = e.domEvent.offsetY + 75;
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
        elMapLegend.style.display = 'inline';
    },
    clearMap() {
        areas.forEach(area => {
            area.setMap(null);
        });
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
            methods.injectingDataToDom(res.data.data);
        });
    },
    injectingDataToDom(data) {
        const dataArray = Object.values(data);
        const total = dataArray.length;
        const numberOfContainer = total % 9 == 0 ? floor(total / 9) : ceil(total / 9);
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
                    let formattedHistPrice = dataArray[dataCounter].hist_price.map((key, value)=>{
                        return {
                            x: value,
                            y: key,
                        };
                    });
                    methods.renderChart(newInfoBox.children[1].children[0], formattedHistPrice);
                    newRow.appendChild(newInfoBox);
                    dataCounter++;
                }
                newElCarouselContainer[i].appendChild(newRow);
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
                        type: 'linear',
                    }],
                },
            },
        })
    },
}