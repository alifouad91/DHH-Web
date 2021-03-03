var PopulateProperty = {
    $propertyName: null,
    init: function () {
        PopulateProperty.$propertyName   = $('.property-names');
        PopulateProperty.$window   = $(window);
        PopulateProperty.$document = $(document);

        // PopulateProperty.$keywords.keyup(PopulateProperty.filterProgrammes);
        PopulateProperty.$window.on('scroll', PopulateProperty.onScroll);
        PopulateProperty.$propertyName.live('keyup',PopulateProperty.delayLoad);
    },
    delayLoad: function () {

        PopulateProperty.resetPage();
        PopulateProperty.showLoader();
        PopulateProperty.disableAutoLoad();

        clearInterval(PopulateProperty.timer);
        PopulateProperty.timer = setTimeout(function () {
            PopulateProperty.filterProperties();
        }, 1000);
    },
    filterProperties: function () {
        $.ajax({
            dataType: 'html',
            type: 'GET',
            data: {
                page: PopulateProperty.page,
                community: PopulateProperty.getCommunity(),
                type: PopulateProperty.getType(),
                bedrooms: PopulateProperty.getBedrooms(),
                minPrice: PopulateProperty.getMinPrice(),
                maxPrice: PopulateProperty.getMaxPrice(),
                area: typeof PopulatePropertyMap != "undefined" ? PopulatePropertyMap.getNewCenter() ? PopulatePropertyMap.getNewCenter() : '' : '',
                isAjax: true
            },
            url: getCurrentPagePath(),
            success: function (response) {
                var data;
                if(!response){
                    $('.google_map_outer').addClass('no_map');
                    data = '<div class="col-sm-12"><h2> No Properties Found </h2></div>';
                }
                else{
                    $('.google_map_outer').removeClass('no_map');
                    data = $(response);
                }

                PopulateProperty.$grid.empty();
                PopulateProperty.$grid.append(data);
                PopulateProperty.initPopulatePropertyItems();
                if (PopulateProperty.$grid.find('.property-item').length < 3){
                    $('.google_map_outer').addClass('smaller_height');
                }
                else {
                    $('.google_map_outer').removeClass('smaller_height');
                }
                PopulateProperty.enableAutoLoad();
            }
        });
        PopulateProperty.addPage();
    },
    setActivePopulateProperty: function (e) {
        PopulateProperty.removeAllActiveClass(PopulateProperty.$properties);
        PopulateProperty.setActiveClass(this);
        PopulatePropertyMap.setActivePin(e);
    },
    removeAllActiveClass: function(items){
        items.each(function(){
            PopulateProperty.removeActiveClass(this);
        });
    },
    removeActiveClass: function(item){
        $(item).removeClass('active');
    },
    setActiveClass: function(item){
        $(item).addClass('active');
    },
    hideLoader: function () {
        PopulateProperty.$loader.fadeOut("slow");
    },
    showLoader: function () {
        PopulateProperty.$loader.fadeIn("slow");
    },
    enableAutoLoad: function () {
        PopulateProperty.autoLoad = true;
    },
    disableAutoLoad: function () {
        PopulateProperty.autoLoad = false;
    },
    isAtBottom: function (scrollTop) {
        return scrollTop + PopulateProperty.$window.height() > PopulateProperty.$document.height() - 700;
    },
    addPage: function () {
        PopulateProperty.page += 1;
    },
    resetPage: function () {
        PopulateProperty.page = 0;
    },
    getCommunity: function () {
        return PopulateProperty.$community.val() ? encodeURI(PopulateProperty.$community.val()) : '';
    },
    getType: function () {
        return PopulateProperty.$type.val() ? encodeURI(PopulateProperty.$type.val()) : '';
    },
    getBedrooms: function () {
        return PopulateProperty.$bedrooms.val() ? encodeURI(PopulateProperty.$bedrooms.val()) : '';
    },
    getMinPrice: function () {
        return PopulateProperty.$minPrice.text() ? encodeURI(PopulateProperty.$minPrice.text()) : '';
    },
    getMaxPrice: function () {
        return PopulateProperty.$maxPrice.text() ? encodeURI(PopulateProperty.$maxPrice.text()) : '';
    }
};

function getCurrentPagePath() {
    return location.href;
}

$(window).ready(function () {
    setTimeout(function () {
        PopulateProperty.init();
    }, 1000)
});
