const axios = require ('axios');
const _data = window[`_priceIndexData`];
const elementTable = document.getElementById('price-table');
let mainDataTable, selectedIds = [], marketId='', goodsId='', typeStatus='';

document.addEventListener('DOMContentLoaded', (event) => {
    methods.initDataTable();
    methods.initMultiActionButton();
});

const methods = {
    initDataTable() {
        const columns = [
            {class:'', data:'date'},
            {class:'', data:'pic'},
            {class:'', data:'market_name'},
            {class:'', data:'goods_name'},
            {class:'', data:'price'},
            {class:'', data:'type_status'},
        ];
        if (_data.data.permissions.sigarang.multiAction) {
            columns.push(
                { class: '', sortable: false,
                    data(v) {
                        return `
                        <div class="checkbox text-center" style="width: 100%">
                            <input class="form-group" name="form-selected-id-checkbox" type="checkbox"
                                data-id="${v.id}"
                                />
                        </div>
                        `;
                    },
                },
            );
        }
        if (_data.isPrivilege) {
            columns.push({ sortable: false, class: 'nowrap', data: '_menu' });
        }
        columns.forEach(x => x.searchable = false);

        const preinitDt = ()=>{
            $(`#price-table_filter`).prepend(_data.data.template.marketList);
            $(`#price-table_filter`).prepend(_data.data.template.goodsList);
            $(`#price-table_filter`).prepend(_data.data.template.statusList);

            const elMarketSelect = document.getElementsByName('market')[0];
            const elGoodsSelect = document.getElementsByName('goods')[0];
            const elStatusSelect = document.getElementsByName('type_status')[0];

            elMarketSelect.addEventListener('change', (event) => {
                marketId = event.target.value;
                mainDataTable.draw(false);
            })
            elGoodsSelect.addEventListener('change', (event) => {
                goodsId = event.target.value;
                mainDataTable.draw(false);
            })
            elStatusSelect.addEventListener('change', (event) => {
                typeStatus = event.target.value;
                mainDataTable.draw(false);
            })
        }

        const afterDrawDt = ()=>{
            methods.initButtonDelete();
            methods.checkedSelectedIds();
            methods.initSelectedIdCheckBoxes();
            methods.initSelectedIdAllCheckBox();
            methods.uncheckedSelectedAllCheckBoxes();
        }

        mainDataTable = $(elementTable)
        .on('preInit.dt', preinitDt)
        .on('draw.dt', afterDrawDt)
        .DataTable({
            columns: columns,
            stateSave: true,
            processing: true,
            serverSide: true,
            scrollX: true,
            ajax: {
                url: _data.routeIndexData,
                type: "GET",
                data: {
                    market_id: () => {
                        return document.getElementsByName('market')[0].value;
                    },
                    goods_id: () => {
                        return document.getElementsByName('goods')[0].value;
                    },
                    type_status: () => {
                        return document.getElementsByName('type_status')[0].value;
                    },
                },
            },
            order: [[ 0, "desc" ]],
            stateSaveParams(settings, data) {
                data.market_id = marketId;
                data.goods_id = goodsId;
                data.type_status = typeStatus;
            },
            stateLoadParams(settings, data) {
                setTimeout(function() {
                        typeStatus = data.type_status;
                        marketId = data.market_id;
                        goodsId = data.goods_id;
                }, 1);
            },
        });
    },
    initButtonDelete() {
        const selector = '.btn-destroy';
        const deleteButtons = document.querySelectorAll(selector);
        deleteButtons.forEach(deleteButton => {
            deleteButton.addEventListener('click', (event) => {
                if(!confirm('The deleted data will be permanently deleted. Are you sure delete the data?')) return;
                let target = event.target;
                while(!target.matches(selector)){
                    target = target.parentNode;
                }
                const id = target.getAttribute('data-id');
                const url = _data.routeDestroyData.replace('999',id);
                axios.delete(url)
                    .then((response)=>{
                        if(response.data == 1){
                            alertify.success('Data has been deleted successfully');
                            mainDataTable.draw(false);
                        } else {
                            alertify.error(response.data);
                        }
                    });
            });
        });
    },
    initMultiActionButton() {
        const selector = '.btn-multi-action';
        const csrfToken = document.querySelector('meta[name=csrf-token]').content;
        const multiActionButtons = document.querySelectorAll(selector);
        multiActionButtons.forEach(button => {
            button.addEventListener('click', (event => {
                let target = event.target;
                const url = _data.routeMultiAction;
                while(!target.matches(selector)){
                    target = target.parentNode;
                }
                const tag = target.getAttribute('data-tag');
                axios.post(url, {_token: csrfToken, ids:selectedIds, tag: tag})
                .then(response => {
                    if(response.data.message){
                        alertify.success(response.data.message);
                        mainDataTable.draw(false);
                    }else{
                        alertify.error(response.data.error);
                    }
                });
            }));
        })
    },
    checkedSelectedIds() {
        const selectedIdRow = document.getElementsByName('form-selected-id-checkbox');
        selectedIdRow.forEach((row) => {
            if(selectedIds.find(data => data == row.getAttribute('data-id'))){
                row.checked = true;
            }
        });
    },
    initSelectedIdCheckBoxes(){
        const selectIdCheckBoxes = document.getElementsByName('form-selected-id-checkbox');
        selectIdCheckBoxes.forEach(element => {
            element.addEventListener('click', function(event) {
                const id = this.getAttribute('data-id');
                const el = document.getElementById('selected-ids');
                const val = event.target.checked;
                if (val) {
                    selectedIds.push(id);
                } else {
                    selectedIds.splice(selectedIds.indexOf(id), 1);
                }
                el.value = selectedIds;
            });
        });
    },
    initSelectedIdAllCheckBox(){
        const selectIdHeadCheckBoxes = document.getElementsByName('form-selected-id-all-checkbox');
        selectIdHeadCheckBoxes.forEach(element => {
            element.addEventListener('click', function() {
                const selectIdCheckBoxes = document.getElementsByName('form-selected-id-checkbox');
                selectIdCheckBoxes.forEach(e => {
                    if(e.checked!=this.checked){
                        e.click();
                    }
                });
            });
        });
    },
    uncheckedSelectedAllCheckBoxes(){
        const selectIdHeadCheckBoxes = document.getElementsByName('form-selected-id-all-checkbox');
        selectIdHeadCheckBoxes.forEach(element => {
            element.checked = false;
        });
    },
};