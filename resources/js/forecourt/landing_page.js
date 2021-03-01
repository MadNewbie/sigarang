import {Loader} from '@googlemaps/js-api-loader';
const axios = require ('axios');
const _data = window[`_landingPageData`];
const defaultCenter = {lat: -7.162324474003386, lng: 113.18244296265054};
let map, mapsApi, areas = [];

document.addEventListener('DOMContentLoaded', (event) => {
    methods.initMapSection();
    methods.initMapDatePicker();
});

const methods = {
    initMapSection() {
        const elDatePickerMap = document.getElementById('map-date');
        let date = moment().format("DD MMMM YYYY");
        elDatePickerMap.value = date;
        // methods.getMapData(date);
        methods.drawMap(null);
    },
    getMapData(date) {
        const csrfToken = document.querySelector('meta[name=csrf-token]').content;
        axios.post(_data.routeGetMapData, {_token: csrfToken, date: date})
        .then(res => {
            methods.drawMap(res.data);
        })
    },
    async drawMap(data){
        const elMapSection = document.getElementById('map-section');
        const loader = new Loader({
            apiKey: 'AIzaSyC1rasZRBxyA3gnVTyYriUelsE2PqoC1MI',
        });
        mapsApi = await loader.load().then(() => google.maps );
        map = new mapsApi.Map(elMapSection, {
            center: defaultCenter,
            zoom: 12,
        });
        const options = {
            map: map,
            mapsApi: mapsApi,
            // data: data,
        };
        // if(areas.length > 0){
        //     methods.clearMap();
        //     areas = []
        // }
        // methods.generateArea(options);
    },
    generateArea(options) {
        options.data.forEach(area => {
            if(area.area!=null){
                const points = [];
                let raw = area.area.area;
                let raw1 = raw.replace('POLYGON(','');
                let raw2 = raw1.substr(0,raw1.length - 1);
                let raw3 = raw2.substr(1);
                let raw4 = raw3.substr(0,raw3.length - 1);
                let rawPoints = raw4.split(',');
                rawPoints.forEach(rawPoint => {
                    let tmp = rawPoint.split(' ');
                    points.push(new mapsApi.LatLng(tmp[0],tmp[1]));
                });
                areas.push(new mapsApi.Polygon({
                    paths: points,
                    fillColor: area.color,
                    strokeColor: area.color,
                }));
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
    },
    initMapDatePicker() {
        $('#map-date').datepicker({
            autoSize: true,
            changeMonth: true,
            changeYear: true,
            dateFormat: "dd MM yy",
            onSelect: (date) => {methods.getMapData(date)},
        });
    },
}