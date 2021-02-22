const axios = require ('axios');
const _data = window[`_priceIndexData`];
let priceGrap, stockGraph;

document.addEventListener('DOMContentLoaded', (event) => {
    methods.initPriceGraph();
    methods.initStockGraph();
});

const methods = {
    initPriceGraph() {
        const priceCanvas = document.getElementById('price-graph');
        const options = {
            graphName: "priceGrap",
            canvas: priceCanvas,
            typeGraph: "line",
            data: {
                datasets : [{
                    label: "Harga Bawang Putih",
                    data: [
                        {
                            x: moment("02/01/2021").format("DD-MM-YYYY"),
                            y: 11000,
                        },
                        {
                            x: moment("02/05/2021").format("DD-MM-YYYY"),
                            y: 12000,
                        },
                        {
                            x: moment("02/10/2021").format("DD-MM-YYYY"),
                            y: 11500,
                        },
                        {
                            x: moment("02/15/2021").format("DD-MM-YYYY"),
                            y: 12500,
                        },
                        {
                            x: moment("02/20/2021").format("DD-MM-YYYY"),
                            y: 12000,
                        },
                    ],
                },],
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
        priceGrap = methods.drawGraph(options);
    },
    initStockGraph() {
        const stockCanvas = document.getElementById('stock-graph');
        const options = {
            graphName: "stockGrap",
            canvas: stockCanvas,
            typeGraph: "line",
            data: {
                datasets : [{
                    label: "Stok Bawang Putih dalam satuan Kilogram (Kg)",
                    data: [
                        {
                            x: moment("02/01/2021").format("DD-MM-YYYY"),
                            y: 500,
                        },
                        {
                            x: moment("02/05/2021").format("DD-MM-YYYY"),
                            y: 400,
                        },
                        {
                            x: moment("02/10/2021").format("DD-MM-YYYY"),
                            y: 350,
                        },
                        {
                            x: moment("02/15/2021").format("DD-MM-YYYY"),
                            y: 600,
                        },
                        {
                            x: moment("02/20/2021").format("DD-MM-YYYY"),
                            y: 550,
                        },
                    ],
                },],
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
        stockGrap = methods.drawGraph(options);
    },
    drawGraph(params) {
        return new Chart(params.canvas, {
            type: params.typeGraph,
            data: params.data,
            options: params.options,
        });
    }
};