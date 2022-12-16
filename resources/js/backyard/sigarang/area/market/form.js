const axios = require ('axios');
const _data = window[`_marketFormData`];

document.addEventListener('DOMContentLoaded', (event) => {
    methods.initProvinceSelectListener();
    methods.initDistrictSelectListener();
});

const methods = {
    initProvinceSelectListener() {
        const elProvinceSelect = document.getElementById('province_option');
        const elCitySelect = document.getElementById('city_option');
        elProvinceSelect.addEventListener("change", (event)=>{
            const provinceId = event.target.value
            if (provinceId != null && provinceId != "" && provinceId != "null") {
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
    initDistrictSelectListener() {
        const elCitySelect = document.getElementById('city_option');
        const elDistrictSelect = document.getElementById('district_option');
        elCitySelect.addEventListener("change", (event)=>{
            const cityId = event.target.value
            if (cityId != null && cityId != "" && cityId != "null") {
                const url = _data.routeAjaxGetDistrictByCityId.replace('999', cityId);
                axios.get(url)
                    .then((response) => {
                        const defOpt = document.createElement("option");
                        defOpt.value = null;
                        defOpt.text = "Pilih Kecamatan";
                        elDistrictSelect.innerHTML = "";
                        elDistrictSelect.add(defOpt);
                        for (const [key, label] of Object.entries(response.data)) {
                            const opt = document.createElement("option");
                            opt.value = key;
                            opt.text = label;
                            elDistrictSelect.add(opt);
                            elDistrictSelect.removeAttribute("disabled");
                        }
                    })
            } else {
                const defOpt = document.createElement("option");
                defOpt.value = null;
                defOpt.text = "Pilih Kecamatan";
                elDistrictSelect.innerHTML = "";
                elDistrictSelect.add(defOpt);
                elDistrictSelect.setAttribute("disabled", true);
            }
        });
    },
};