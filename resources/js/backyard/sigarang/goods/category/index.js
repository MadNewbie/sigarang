const axios = require ('axios');
const _data = window[`_categoryIndexData`];
const elementTable = document.getElementById('category-table');
let mainDataTable;

document.addEventListener('DOMContentLoaded', (event) => {
    methods.initDataTable();
});

const methods = {
    initDataTable() {
        const columns = [
            {class:'', data:'name'},
        ];
        if (_data.isPrivilege) {
            columns.push({ sortable: false, class: 'nowrap', data: '_menu' });
        }
        columns.forEach(x => x.searchable = false);

        const afterDrawDt = ()=>{
            methods.initButtonDelete();
        }

        mainDataTable = $(elementTable)
        .on('draw.dt', afterDrawDt)
        .DataTable({
            columns: columns,
            stateSave: true,
            processing: true,
            serverSide: true,
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
};