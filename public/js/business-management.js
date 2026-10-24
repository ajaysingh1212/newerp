(function () {
    'use strict';
    const text = value => String(value ?? '');
    const escape = value => text(value).replace(/[&<>"']/g, character => ({'&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'}[character]));
    window.BusinessTables = {
        escape,
        init(table, options = {}) {
            if (!window.jQuery || !jQuery.fn.DataTable) return null;
            if (jQuery.fn.dataTable.isDataTable(table)) return jQuery(table).DataTable();
            return jQuery(table).DataTable(Object.assign({
                // Override the admin layout's bulk-selection and export defaults.
                columnDefs: [], select: false, buttons: [], order: [], autoWidth: false,
                scrollX: true, pageLength: 25, lengthMenu: [10, 25, 50, 100],
                dom: '<"bm-dt-top"lf>rt<"bm-dt-bottom"ip>',
                language: {url: '', search: '', searchPlaceholder: 'Search records...', lengthMenu: 'Show _MENU_', emptyTable: 'No records to display.', zeroRecords: 'No matching records.', info: '_START_–_END_ of _TOTAL_ records', infoEmpty: '0 records', processing: 'Loading records...'},
            }, options));
        },
        destroy(table) {
            if (window.jQuery && jQuery.fn.dataTable.isDataTable(table)) jQuery(table).DataTable().destroy();
        },
        textColumn(data, name, extra = {}) {
            return Object.assign({data, name: name || 'business_devices.' + data, defaultContent: '', render: (value, type) => type === 'display' ? escape(value) : text(value)}, extra);
        },
        statusColumn() {
            return {data: 'status', name: 'business_devices.status', render: (value, type) => type === 'display' ? '<span class="bm-status ' + (value === 'activated' ? 'activated' : 'pending') + '">' + escape(value === 'activated' ? 'Activated' : 'Pending') + '</span>' : value};
        },
        moneyColumn() {
            return {data: 'sales_price', name: 'business_devices.sales_price', render: (value, type) => type === 'display' ? Number(value).toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2}) : value};
        },
    };
    jQuery(function () {
        document.querySelectorAll('.bm-workspace table[data-business-table]').forEach(table => {
            const compact = table.dataset.businessTable === 'compact';
            BusinessTables.init(table, compact ? {paging: false, searching: false, ordering: false, info: false, dom: 't'} : {});
        });
    });
})();
