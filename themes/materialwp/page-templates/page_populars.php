<?php
/*
Template Name: populars
*/

get_header();
ajaxHelpers();

?>
    <script>
        function dataCallback(pois){
            var pois = pois.data;
            feed2Tabs(pois);
        }
        var pathname = window.location.pathname;
        if(pathname.indexOf('cities') != -1) {
            getAjaxData(NODE_API_INIT + 'cities', dataCallback);
        }else if(pathname.indexOf('schools') != -1) {
            getAjaxData(NODE_API_INIT + 'schools', dataCallback);
        }

        var allTabs = null;
        var countryDict = {};
        function feed2Tabs(data){
            $(document).ready(function() {
                for (var i = 0; i < data.length; i++) {
                    if (!countryDict[data[i].country]) {
                        countryDict[data[i].country] = [data[i]];
                    } else {
                        countryDict[data[i].country].push(data[i]);
                    }
                }
                allTabs = Object.keys(countryDict).sort();
                if(allTabs) {
                    //Batch processing html for performance.
                    var tabHtml = '';
                    for(var i = 0; i < allTabs.length; i++) {
                        tabHtml += '<li><a id="tab-' + allTabs[i] + '" class="btn">' + allTabs[i]
                            + '</a></li>';

                    }
                    $('ul#countryTabs').append(tabHtml);
                    for(var i = 0; i < allTabs.length; i++){
                        $('a#tab-' + allTabs[i]).on('click', function(x){
                            feed2Lists(countryDict[allTabs[x]]);
                        }.bind(null, i));
                    }
                    // By default, display the first tab content.
                    feed2Lists(countryDict[allTabs[0]]);
                }
            });
        }

        function feed2Lists(states){
            var stateDict = {};
            for(var i = 0; i < states.length; i++){
                if (!stateDict[states[i].state]) {
                    stateDict[states[i].state] = [states[i]];
                } else {
                    stateDict[states[i].state].push(states[i]);
                }
            }
            var allStates = Object.keys(stateDict).sort();
            var listHtml = '';
            for(var i = 0; i < allStates.length; i++) {
                listHtml += '<h3>' + allStates[i]
                    + '</h3>';

                var pois = stateDict[allStates[i]];
                var listGroupOpen = '<div class="col-md-4"><div class="bs-component"><div class="list-group">';
                var listGroupClose = '</div></div></div>';
                var numColums = 3;
                var numRows = Math.ceil(pois.length / numColums);
                listHtml += listGroupOpen;
                for(var j = 0; j < pois.length; j++){
                    listHtml += '<div class="list-group-item"><a href="">' + pois[j].name + '</a></div>';
                    // If the items reach to the max row number, open a new column.
                    if((j+1) % numRows == 0){
                        // If not the last one.
                        if(j+1 < pois.length) {
                            listHtml += listGroupClose + listGroupOpen;
                        }
                    }
                }
                listHtml += listGroupClose;
            }
            $('div#stateList').html(listHtml);
        }
    </script>
    <div class="container">
        <div class="row">
            <div id="primary" class="col-md-12 col-lg-12">
                <main id="main" class="site-main" role="main">
                    <ul id="countryTabs" class="nav nav-tabs" style="">
                    </ul>
                    <div id="stateList"></div>
                </main><!-- #main -->
            </div><!-- #primary -->

        </div> <!-- .row -->
    </div> <!-- .container -->

<?php get_footer(); ?>