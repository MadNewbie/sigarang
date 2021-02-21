const axios = require ('axios');
const _data = window[`_stockIndexData`];
const elementTable = document.getElementById('stock-table');
let mainDataTable, selectedIds = [];

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
            {class:'', data:'stock'},
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

        const afterDrawDt = ()=>{
            methods.initButtonDelete();
            methods.checkedSelectedIds();
            methods.initSelectedIdCheckBoxes();
            methods.initSelectedIdAllCheckBox();
            methods.uncheckedSelectedAllCheckBoxes();
        }

        mainDataTable = $(elementTable)
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
            },
            order: [[ 0, "desc" ]],
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