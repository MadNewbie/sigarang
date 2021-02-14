const axios = require ('axios');
const _data = window[`_districtFormData`];

document.addEventListener('DOMContentLoaded', (event) => {
    methods.initProvinceSelectListener();
});

const methods = {
    initProvinceSelectListener() {
        const elProvinceSelect = document.getElementById('province_option');
        const elCitySelect = document.getElementById('city_option');
        elProvinceSelect.addEventListener("change", (event)=>{
            const provinceId = event.target.value
            if (provinceId != null && provinceId != "") {
                const url = _data.routeAjaxGetCityByProvinceId.replace('999', provinceId);
                axios.get(url)
                    .then((response) => {
                        const defOpt = document.createElement("option");
                        defOpt.value = null;
                        defOpt.text = "Pilih Kabupaten / Kota";
                        elCitySelect.innerHTML = "";
                        elCitySelect.add(defOpt);
                        for (const [key, label] of Object.entries(response.data)) {
                            const opt = document.createElement("option");
                            opt.value = key;
                            opt.text = label;
                            elCitySelect.add(opt);
                            elCitySelect.removeAttribute("disabled");
                        }
                    })
            } else {
                const defOpt = document.createElement("option");
                defOpt.value = null;
                defOpt.text = "Pilih Kabupaten / Kota";
                elCitySelect.innerHTML = "";
                elCitySelect.add(defOpt);
                elCitySelect.setAttribute("disabled", true);
            }
        });
    },
};