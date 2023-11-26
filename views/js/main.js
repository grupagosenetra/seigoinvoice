$(document).ready(() => {

    const formInvoice = () => {

        let dataSent = {
            'action': 'formInvoice',
            'id_order': id_order,
            'ajax': 1,
            'seigotoken': seigotoken
        };

        $.ajax({
            type: 'POST',
            headers: { "cache-control": "no-cache" },
            async: false,
            dataType: "json",
            url: SeigoInvoiceControllerUrl,
            data: dataSent,
            success: function(data) {
                $('.addSeigoInvoice').remove();
                $('.downloadFileInvoice').remove();
                $('.order-actions-print').after(data.form);
            },
            error: function(err) {
                console.log('error');
                console.log(err);
            }
        });

    }

    const btnFileInvoieAction = () => {
        $(document).on('click', '.btnFileInvoie', () => {
            $('.inputFileInvoice').click();
        });
    }
    const removeFileAction = () => {
        $(document).on('click', '.removeIvoice', () => {
            if (confirm('Na pewno')) {
                let dataSent = {
                    'action': 'removeInvoice',
                    'id_order': id_order,
                    'ajax': 1,
                    'seigotoken': seigotoken
                };

                $.ajax({
                    type: 'POST',
                    headers: { "cache-control": "no-cache" },
                    async: false,
                    dataType: "json",
                    url: SeigoInvoiceControllerUrl,
                    data: dataSent,
                    success: function(data) {
                        formInvoice();
                    },
                    error: function(err) {
                        console.log('error');
                        console.log(err);
                    }
                });
            }
        })
    }
    const uploadFileAction = () => {
        $(document).on('change', '.inputFileInvoice', () => {

            var fd = new FormData();
            var files = $('.inputFileInvoice')[0].files[0];
            fd.append('file', files);
            let dataUrl = SeigoInvoiceControllerUrl + '?id_order=' + id_order + '&ajax=1&action=uploadInvoice&seigotoken=' + seigotoken;

            $.ajax({
                type: 'POST',
                dataType: "json",
                url: dataUrl,
                data: fd,
                contentType: false,
                processData: false,
                success: function(data) {
                    $('.addSeigoInvoice').remove();
                    $('.downloadFileInvoice').remove();
                    $('.order-actions-print').after(data.form);
                },
                error: function(err) {
                    console.log('error');
                    console.log(err);
                }
            });

        });
    }
    if (id_order > 0) {
        formInvoice();
        btnFileInvoieAction();
        removeFileAction();
        uploadFileAction();
    }
});