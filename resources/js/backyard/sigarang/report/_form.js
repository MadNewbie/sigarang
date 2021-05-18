import Vue from 'vue';
import JqPriceformat from '../../../components/JqueryPriceformatComponent';

const axios = require ('axios');
const _data = window[`_reportFormData`];

document.addEventListener('DOMContentLoaded', (event) => {
    methods.initPriceFormat();
    methods.initApps();
});

const data = {
    app: null,
};

const methods = {
    initPriceFormat() {
        $('.priceformat').priceFormat({
            prefix: '',
            thousandsSeparator: '.',
            centsLimit: 0,
        });
    },
    initApps() {
        data.app = new Vue({
            el: '#formApp',
            components: {
                JqPriceformat,
            },
            data() {
                const defPlaceholder = _data.data.placeholder;
                const flag = _data.data.flag;
                const idPasar = 0;
                return {
                    placeholder: defPlaceholder,
                    flag: flag,
                    idPasar : idPasar,
                };
            },
            methods: {
                onMarketSelect() {
                    if(this.flag==='price'){
                        const url = _data.routes.getPricePlaceholder;
                        axios.post(url, {
                            id_pasar: this.idPasar,
                        }).then((res) => {
                            this.placeholder = res.data;
                        })
                    } else {
                        const url = _data.routes.getStockPlaceholder;
                        axios.post(url, {
                            id_pasar: this.idPasar,
                        }).then((res) => {
                            this.placeholder = res.data;
                        })
                    }
                }
            },
        });
    },
};
