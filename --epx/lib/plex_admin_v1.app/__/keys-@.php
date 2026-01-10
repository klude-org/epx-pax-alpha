<?php

$this->ui->load('__/views/base')->prt(function($view){
    
    ?>
    <div class="container-fluid mt-5">
        <!-- Editable List Card -->
        <form id="lineListForm" method="post">
            <input hidden name="--action" value="save">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0">Access List</h6>
                <button type="submit" class="btn btn-sm btn-primary">
                    Save
                </button>
                </div>

                <div class="card-body">
                <div id="lineListContainer">
                    <!-- Rows will be injected here -->
                </div>

                <div class="d-flex justify-content-end mt-3">
                    <button type="button" class="btn btn-outline-primary btn-sm" id="btnAddLine">
                    + Add
                    </button>
                </div>
                </div>
            </div>
        </form>
    </div>
    <script>
        window.xui.ds.access_list = <?=\json_encode((function(){ 
            return $this->ACCESS_LIST['linelist'] ?? [];
        })())?> 
        
        $(function () {
            const container = document.getElementById('lineListContainer');
            const btnAdd = document.getElementById('btnAddLine');

            // Generate a reasonably unique key (timestamp + random)
            function makeKey() {
                const ts = Date.now().toString(36);
                const rnd = Math.random().toString(36).slice(2, 6);
                return ts + rnd;
            }

            function createRow(kkey = null, value = {path:'',lib:''}) {
                const key = kkey ?? makeKey();
                const path = value.path;
                const start_at = value.start_at ?? '';
                const url = value.url ?? 'javascript:void()';

                const row = document.createElement('div');
                row.className = 'input-group mb-2';
                row.dataset.key = key;

                row.innerHTML = `
                    <a class="btn btn-outline-secondary btn-sm" type="button" data-role="go" href="${url}">Go</a>
                    <a class="btn btn-outline-secondary btn-sm" type="button" data-role="go" href="${url}/--@">@</a>
                    <input 
                    type="text" 
                    class="form-control form-control-sm" 
                    name="ff[linelist][${key}][path]" 
                    value="${path.replace(/"/g, '&quot;')}"
                    placeholder="Enter value"
                    ${path ? 'readonly' : ''}
                    >
                    <select 
                    class="form-select form-select-sm xui-select2" 
                    name="ff[linelist][${key}][start_at]" 
                    value="${start_at.replace(/"/g, '&quot;')}"
                    >
                    </select>
                    <button class="btn btn-outline-danger btn-sm" type="button" data-role="remove">
                    &times;
                    </button>
                `;

                // Attach events
                const btnGo = row.querySelector('[data-role="go"]');
                const btnRemove = row.querySelector('[data-role="remove"]');

                // btnGo.addEventListener('click', function () {
                //     const input = row.querySelector('input');
                //     // TODO: implement your "Go" logic here:
                //     // e.g., window.location.href = input.value;
                //     console.log('Go clicked with value:', input.value);
                // });

                btnRemove.addEventListener('click', function () {
                    $(row).closest('form').append(`<input hidden name="ff[linelist][${key}][state]" value="-1">`);
                    row.remove();
                });

                container.appendChild(row);
                // Initializing the Select2 dropdown
                var select$ = $(row).find('.xui-select2');
                select$.select2({
                    data: window.xui.ds.start_options || [],
                    // width: '140px'
                });
                select$.val(select$.attr('value')).trigger('change');
            }

            // Add button
            btnAdd.addEventListener('click', function () {
                createRow();
            });

            var list = window.xui.ds.access_list;
            Object.keys(list).forEach(key => {
                console.log({key, value: list[key]});
                createRow(key, list[key]);
            });
            
            
            // Start with one empty row
            //createRow();

            // Optional: prevent default submit for now
            // document.getElementById('lineListForm').addEventListener('submit', function (e) {
            //   e.preventDefault();
            //   const data = new FormData(this);
            //   // handle save...
            //   console.log('saving...', Array.from(data.entries()));
            // });
        });
    </script>
    <?php 
    
    
    
});