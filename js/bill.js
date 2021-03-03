var EventDates = {
    $date: $('#date'),
    $clearDate: $('.date'),

    $form: $('form')[0],
    $saveBtn: $('a[data-ref="btn-pr-save"]'),

    init: function () {
    EventDates.initDateTimePickers();
    EventDates.$saveBtn.click(EventDates.submitForm);
    EventDates.$clearDate.click(function () {
    EventDates.$date.val('');
});
},
    initDateTimePickers: function () {

    var config_with_time = {
    dateFormat: 'Y-m-d'
};

    EventDates.$date.flatpickr(config_with_time);
},
    submitForm: function () {
    EventDates.$form.submit();
}
};

    $(document).ready(function () {
    EventDates.init();
});

function fileSelect(evt) {
    if (window.File && window.FileReader && window.FileList && window.Blob) {
        var files = evt.target.files;
        var info = $(evt.target).prev();

        var result = '';
        var file;
        for (var i = 0; file = files[i]; i++) {
            // if the file is not an image, continue

            if (!file.type.match('application/pdf')) {
                alert('bill should be in pdf format');
                return;
            }

            reader = new FileReader();
            reader.onload = (function (tFile) {
                return function (evt) {
                    PDFObject.embed(evt.target.result, '#filesInfo');
                };
            }(file));
            reader.readAsDataURL(file);
        }
    } else {
        alert('The File APIs are not fully supported in this browser.');
    }
}
if ($('#filesToUpload').length > 0)
{
    document.getElementById('filesToUpload').addEventListener('change', fileSelect, false);
}

$(document).ready(function () {
    $("#pID, #pId").select2({
        // theme: "classic",
        ajax: {
            url: CCM_TOOLS_PATH + '/fetchProperties',
            dataType: 'json',
            delay: 1000,
            data: function (params) {
                return {
                    query: params.term, // search term
                    page: params.page
                };
            },
            processResults: function (data, params) {
                // parse the results into the format expected by Select2
                // since we are using custom formatting functions we do not need to
                // alter the remote JSON data, except to indicate that infinite
                // scrolling can be used
                params.page = params.page || 1;

                return {
                    results: data.results,
                    pagination: {
                        more: (params.page * 30) < data.total_count
                    }
                };
            },
            cache: true
        },
        placeholder: 'Search Property',
        escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
        minimumInputLength: 1,
        templateResult: formatProperties,
        templateSelection: formatPropSelection
    });
});

function formatProperties (prop) {
    if (prop.loading) {
        return prop.text;
    }

    var markup = "<div class='select2-result-properties clearfix'>" +
        "<div class='select2-result-property__title'>" + prop.text + " </div>" +
        // "<span class='select2-result-property__caption' style='float: left;'>" + prop.caption + "</span>" +
        "</div>";

    return markup;
}
function formatPropSelection (prop) {
    return prop.text || prop.pID;
}