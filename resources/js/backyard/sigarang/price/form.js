document.addEventListener('DOMContentLoaded', (event) => {
    methods.initDatePicker();
});

const methods = {
    initDatePicker() {
        $('.datepicker').datepicker({
            autoSize: true,
            changeMonth: true,
            changeYear: true,
            dateFormat: "yy-mm-dd",
        });
    },
};