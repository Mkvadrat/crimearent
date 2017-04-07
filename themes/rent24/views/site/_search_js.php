<?php
$isInner = isset($isInner) ? $isInner : 0;
$compact = param("useCompactInnerSearchForm", true);
$loc = (issetModule('location') && param('useLocation', 1)) ? 1 : 0;
?>

    var sliderRangeFields = <?php echo CJavaScript::encode(SearchForm::getSliderRangeFields());?>;
    var cityField = <?php echo CJavaScript::encode(SearchForm::getCityField());?>;
    var loc = <?php echo CJavaScript::encode($loc);?>;
    var countFiled = <?php echo CJavaScript::encode(SearchForm::getCountFiled() + ($loc ? 2 : 0));?>;
    var isInner = <?php echo CJavaScript::encode($isInner);?>;
    var heightField = 38;
    var advancedIsOpen = 0;
    var compact = <?php echo $compact ? 1 : 0;?>;
    var minHeight = isInner ? 80 : 260;
    var searchCache = [];
    var objType = <?php echo isset($this->objType) ? $this->objType : SearchFormModel::OBJ_TYPE_ID_DEFAULT;?>;
    var useSearchCache = false;

    var search = {
        init: function(){
            if(isInner) $("#inner-search-hidden-fields").show();

            if(sliderRangeFields){
                $.each(sliderRangeFields, function() {
                    search.initSliderRange(this.params);
                });
            }

            if(cityField){
                $("#city")
                    .multiselect({
                        noneSelectedText: "<?php echo Yii::t('common', 'select city')?>",
                        checkAllText: "<?php echo Yii::t('common', 'check all')?>",
                        uncheckAllText: "<?php echo Yii::t('common', 'uncheck all')?>",
                        selectedText: "<?php echo Yii::t('common', '# of # selected')?>",
                        //minWidth: cityField.minWidth,
                        classes: "search-input-new",
                        multiple: "false",
                        selectedList: 1,
                        width: 290,
                        _minWidth: 118,
                        minWidth: 300
                    }).multiselectfilter({
                        label: "<?php echo Yii::t('common', 'quick search')?>",
                        placeholder: "<?php echo Yii::t('common', 'enter initial letters')?>",
                        width: 185
                    });
            }

            if($("#search_term_text").length){
                search.initTerm();
            }

            // select menu
            $('select.filter-select').selectmenu();

            // checkbox button set
            $('.buttonset').buttonset();

            // show, hide inner search
            $('.hide-ico').click(function(e){
                e.preventDefault();
                e.stopPropagation();

                $('.filter-collapse').slideUp();
                $('.show-ico-wrapper').show();
            });
            $('.show-ico').click(function(e){
                e.preventDefault();
                e.stopPropagation();

                $('.filter-collapse').slideDown();
                $('.show-ico-wrapper').hide();
            });

            $('.cell-input-inline').blur(changeSearch);

            if(isInner && !advancedIsOpen) $("#inner-search-hidden-fields").hide();
            if (isInner && advancedIsOpen) $("#more-link-inner").html("<?php echo tc("Less options");?>");
        },

        initTerm: function(){
            $(".search-term input#search_term_text").keypress(function(e) {
                var code = (e.keyCode ? e.keyCode : e.which);
                if(code == 13) { // Enter keycode
                    prepareSearch();
                    return false;
                }
            });
        },

        initSliderRange: function(sliderParams){
            $( "#slider-range-"+sliderParams.field ).slider({
                range: true,
                min: sliderParams.min,
                max: sliderParams.max,
                values: [ sliderParams.min_sel , sliderParams.max_sel ],
                step: sliderParams.step,
                slide: function( e, ui ) {
                    $( "#"+sliderParams.field+"_min_val" ).html( ui.values[ 0 ] );
                    $( "#"+sliderParams.field+"_min" ).val( ui.values[ 0 ] );
                    $( "#"+sliderParams.field+"_max_val" ).html( ui.values[ 1 ] );
                    $( "#"+sliderParams.field+"_max" ).val( ui.values[ 1 ] );

                    $(ui.handle).attr('data-content', ui.value);
                    $(ui.handle).popover('show');
                },
                change: function(e, ui) {
                    $(ui.handle).attr('data-content', ui.value);
                    $(ui.handle).popover('show');
                },
                stop: function(e, ui) {  changeSearch(); }
            });

            var values = $( "#slider-range-"+sliderParams.field ).slider('values');
            for (var i=0; i<values.length; i++) {
                $( "#slider-range-"+sliderParams.field ).find('.ui-slider-handle:eq('+i+')').attr('data-content', values[i]);
            }

            $("#slider-range-"+sliderParams.field+" .ui-slider-handle").popover({
                trigger: 'manual',
                placement: 'top',
                animation: false,
                content: function() {
                    return $(this).attr('data-content');
                }
            });

            $("#slider-range-"+sliderParams.field+" .ui-slider-handle").popover('show');
        },

        indexSetNormal: function(){
            $("div.index-header-form").animate({"height" : "310"});
            $("#more-options-link").html("<?php echo tc("More options");?>");
            advancedIsOpen = 0;
        },

        indexSetAdvanced: function(){
            $("div.index-header-form").animate({"height" : 580});
            $("#more-options-link").html("<?php echo tc("Less options");?>");
            advancedIsOpen = 1;
        },

        innerSetNormal: function(){
            $("#inner-search-hidden-fields").hide();
            $("div.searchformInner").animate({"height" : "200"});
            $("#more-link-inner").html("<?php echo tc("More options");?>");
            advancedIsOpen = 0;
        },

        innerSetAdvanced: function(){
            $("div.searchformInner").animate({"height" : "270"}, function(){
                $("#inner-search-hidden-fields").show();
            });
            $("#more-link-inner").html("<?php echo tc("Less options");?>");
            advancedIsOpen = 1;
        },

        getHeight: function(){
            var height = countFiled * heightField + 30;

            if(height < minHeight){
                return minHeight;
            }

            return isInner ? height/2 + 20 : height;
        },

        renderForm: function(obj_type_id){
            $('#search_form').html(searchCache[obj_type_id].html);
            sliderRangeFields = searchCache[obj_type_id].sliderRangeFields;
            cityField = searchCache[obj_type_id].cityField;
            countFiled = searchCache[obj_type_id].countFiled + (loc ? 2 : 0);
            search.init();
            if(!useSearchCache){
                delete(searchCache[obj_type_id]);
            }
            changeSearch();
        }
    }

    $(function(){
        search.init();

        $('#objType').live('change selectmenuchange', function(){
            var obj_type_id = $(this).val();
            if(typeof searchCache[obj_type_id] == 'undefined'){
                $.ajax({
                    url: BASE_URL + '/quicksearch/main/loadForm?' + $('#search-form').serialize(),
                    dataType: 'json',
                    type: 'GET',
                    data: { obj_type_id: obj_type_id, is_inner: <?php echo CJavaScript::encode($isInner);?>, compact: advancedIsOpen ? 0 : 1 },
                    success: function(data){
                        if(data.status == 'ok'){
                            searchCache[obj_type_id] = [];
                            searchCache[obj_type_id].html = data.html;
                            searchCache[obj_type_id].sliderRangeFields = data.sliderRangeFields;
                            searchCache[obj_type_id].cityField = data.cityField;
                            searchCache[obj_type_id].countFiled = data.countFiled;
                            search.renderForm(obj_type_id);
                        }
                    }
                })
            } else {
                search.renderForm(obj_type_id);
            }
        });

        if(isInner){
            $("#more-options-link-inner, #more-options-img, #more-link-inner").live('click', function(){
                if (advancedIsOpen) {
                    search.innerSetNormal();
                } else {
                    search.innerSetAdvanced();
                }
            });
        } else {
            $("#more-options-link").live('click', function(){
                if(advancedIsOpen){
                    search.indexSetNormal();
                } else {
                    search.indexSetAdvanced();
                }
            });
        }
    });


function prepareSearch() {
    var term = $(".search-term input#search_term_text").val();

    if (term != <?php echo CJavaScript::encode(tc("Search by description or address")) ?>) {
        if (term.length >= <?php echo (int) Yii::app()->controller->minLengthSearch ?>) {
            term = term.split(" ");
            term = term.join("+");
            $("#do-term-search").val(1);
                window.location.replace("<?php echo Yii::app()->createAbsoluteUrl('/quicksearch/main/mainsearch') ?>?term="+term+"&do-term-search=1");
            } else {
                alert(<?php echo CJavaScript::encode(Yii::t('common', 'Minimum {min} characters.', array('{min}' => Yii::app()->controller->minLengthSearch))) ?>);
        }
    }
}