document.addEventListener('DOMContentLoaded', (event) => {
    methods.initPriceFormat();
});

const methods = {
    initPriceFormat() {
        $('.priceformat').priceFormat({
            prefix: '',
            thousandsSeparator: '.',
            centsLimit: 0,
        });
    },
};