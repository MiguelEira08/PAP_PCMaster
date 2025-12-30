$(document).ready(function () {

    if ($('.datatable').length) {
        $('.datatable').DataTable({
            language: {
                url: 'https://cdn.datatables.net/plug-ins/2.3.6/i18n/pt-PT.json'
            }
        });
    }

});
