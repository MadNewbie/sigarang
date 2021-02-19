const _data = window[`_priceImportData`];

document.addEventListener('DOMContentLoaded', (event) => {
    methods.initFileUpload();
});

const methods = {
    initFileUpload() {
        const fileAdd = (e, data) => {
            data.loadingTmp = parseInt( Math.random() * 1000000 );
            var progressBar = `<div class="progress progress-mini progress-tiny progress-${data.loadingTmp}"><div class="progress-bar progress-bar-success" style=""></div></div>`;
            data.loadingId = alertify.warning(`${progressBar} Mengupload ${data.files[0].name} <br />Harap tunggu hingga proses selesai`, 0);
            data.submit();
        }
        const fileProgress = (e, data) => {
            var progress = parseInt(data.loaded / data.total * 100, 10);
            $('.progress.progress-' + data.loadingTmp + ' .progress-bar').css('width',progress + '%');
        };
        const fileDone = (e, data) => {
            for (let i in data.result) {
                let v = data.result[i];
                if (v.error) {
                    const alert = alertify.error(`${v.file}</br>${v.error}`,0);
                    $(alert).on('click', () => alert.dismiss());
                } else {
                    const alert = alertify.success(`${v.file}</br>${v.message}`,0);
                    $(alert).on('click', () => alert.dismiss());
                }
                data.loadingId.dismiss();
                document.getElementById('file-upload').value = null;
            }
        };
        $('#file-upload').fileupload({
            url: _data.routePriceUpload,
            dataType: 'json',
            add: fileAdd,
            done: fileDone,
            progress: fileProgress,
            fail: function(e, data) {
                alertify.error('Proses Upload Gagal');
            }
        });
    },
};