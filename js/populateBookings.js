$(document).ready(function () {
    $("#bId").select2({
        // theme: "classic",
        ajax: {
            url: CCM_TOOLS_PATH + '/fetchBookings',
            dataType: 'json',
            delay: 1000,
            data: function (params) {
                return {
                    query: params.term, // search term
                    page: params.page,
                    ccm_token: $('#fetch-booking-token').val()
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
        placeholder: 'Search Booking',
        escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
        minimumInputLength: 1,
        templateResult: formatUsers,
        templateSelection: formatUserSelection
    });
});

function formatUsers (user) {
    if (user.loading) {
        return user.text;
    }

    var markup = "<div class='select2-result-usererties clearfix'>" +
        "<div class='select2-result-usererty__title'>" + user.text + "</div>" +
        // "<span class='select2-result-usererty__caption' style='float: left;'>" + user.caption + "</span>" +
        "</div>";

    return markup;
}
function formatUserSelection (user) {
    return user.text || user.uID;
}