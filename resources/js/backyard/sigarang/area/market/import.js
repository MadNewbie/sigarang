const _data = window[`_marketImportData`];

document.addEventListener('DOMContentLoaded', (event) => {
    methods.initFileUpload();
});

const methods = {
    initFileUpload() {
        $('#file-input').dropzone({
            url:_data.routeMarketUpload,
        });
    },
};