<?php
/*
Template Name: User Listings
*/

get_header();
?>
<?php
global $wpdb;

$is_tenant_arr = get_user_meta( $user_ID, "is_tenant", false);
$is_tenant = (count($is_tenant_arr) > 0) ? $is_tenant_arr[0] : 0;
$is_host_arr = get_user_meta( $user_ID, "is_host", false);
$is_host = (count($is_host_arr) > 0) ? $is_host_arr[0] : 0;

$query1 = 'SELECT id, post_title, guid FROM wp_posts WHERE post_type = \'rental\' and post_author = '.$userdata->ID .' ORDER BY id DESC' ;
$results1 = $wpdb->get_results( $query1, ARRAY_A );
$rental_count = count($results1);
$post_list_str = '0';
foreach($results1 as $x => $x_value) {
    $post_list_str = $post_list_str.','.$x_value['id'];
}

$query2 = 'SELECT meta_key,meta_value FROM `wp_postmeta` '.
    ' WHERE post_id in ('.$post_list_str.') and (meta_key=\'property_rent\' or meta_key=\'property_rent_period\' or '.
    'meta_key=\'property_address_country\' or meta_key=\'property_address_street\' or meta_key=\'property_address_suburb\' '.
    'or meta_key=\'property_address_street_number\' or meta_key= \'short_long_term\')'.
    'ORDER by post_id DESC, meta_key ASC';

$query3 ='SELECT p1.id, p3.meta_value, p1.id FROM wp_posts as p1, wp_posts as p2, wp_postmeta as p3'.
    ' WHERE p1.id = p2.post_parent and p2.id = p3.post_id and p1.post_type = \'rental\' and p1.post_author ='.$userdata->ID.' and p3.meta_key = \'_wp_attached_file\''.
    ' ORDER by p1.id DESC, p2.id DESC';

$results2 = $wpdb->get_results( $query2, ARRAY_A );
$results3 = $wpdb->get_results( $query3, ARRAY_A );

$subArraySz = 7;
/* results2
 * ixsubArraySz+0: property_address_country
 * ixsubArraySz+1: property_address_street
 * ixsubArraySz+2: property_address_street_number
 * ixsubArraySz+3: property_address_suburb
 * ixsubArraySz+4: property_rent
 * ixsubArraySz+5: property_rent_period
 * ixsubArraySz+6: short_long_term
 * */
/* Find the first pic for each post */
$pic_arr=array();
for($i = 0; $i < count($results1); $i++){
    for($j = 0; $j < count($results3); $j++) {
        if($results1[$i]['id'] == $results3[$j]['id']) {
            $pic_arr[$results1[$i]['id']] = $results3[$j]['meta_value'];
            break;
        }
    }
}

$short_term_arr=array();
for($i = 0; $i < count($results1); $i++) {
    if($results2[$i*$subArraySz+6]['meta_key'] == "short_long_term")
        $short_term_arr[$results1[$i]['id']] = $results2[$i*$subArraySz+6]['meta_value'];
}

?>
<link rel="stylesheet" type="text/css" href="/wp-content/plugins/loading_spinner/loading.css">

<script>
    var dataSet = new Array();
    var table;
    var selectedID;
    var cellStatus;
    var userID = <?php global $user_ID; echo $user_ID;?>;
    var postID = "";
    var applicantID;
    $(document).ready(function() {
        table = $('#appTable').DataTable( {
            data: dataSet,
            columnDefs: [
                {
                    "targets": -1,
                    "data": null,
                    "defaultContent": "<button id='' type='button' class='btn btn-info btn-sm' data-toggle='modal' data-target='#detail-dialog'>DETAILS</button>"
                },
                {
                    "targets": [ 0 ],
                    "visible": false,
                    "searchable": false
                },
                {
                    "targets": [ 1 ],
                    "visible": false,
                    "searchable": false
                },
                {
                    "targets": [ 2 ],
                    "visible": false,
                    "searchable": false
                },
                {
                    "targets": [ 6 ],
                    "visible": false,
                    "searchable": false
                },
                { "width": "10%", "targets": 4 },
                {"className": "dt-center", "targets": "_all"}
            ],
            "pageLength": 25,
            "lengthChange": false
        } );

        $('#appTable tbody').on( 'click', 'button', function () {
            var data = table.row( $(this).parents('tr') ).data();
            cellStatus = table.cell(table.row( $(this).parents('tr') ), table.column(7));
            applicantID = data[1];
            var applicantName = data[3];
            var startDate = data[4];
            var term= data[5];
            var numTenant = data[6];
            var appStatus = data[7];
            var div = document.getElementById('app-detail');
            var apptable = '<table style="width: 70%"><tr><td><h4>Applicant : ' + applicantName + '</h4></td></tr>';
            var end = new Date(term);
            var str = (end != "Invalid Date")? 'End Date' : 'Term';
            apptable = apptable + '<tr><td><h4>Start Date: '+ startDate + '</td><td><h4>' + str + ': ' + term + '</h4></td></tr>';
            apptable = apptable + '<tr><td><h4>Tenants   : '+ numTenant + '</td><td><h4 id="status"> Status: ' + appStatus + '</h4></td></tr></table>';
            if(appStatus != "Completed")
            {
                apptable = apptable + '<p style="width: 80%" class="text-muted">We have collected the copy of applicant\'s passport and verfied its authenticity. You will be able to see the clear version once the application is completed.</p>';
            }
            apptable = apptable + '<p><img style="width: 500px; height: 300px" src="' +data[2] +'"></p>';
            div.innerHTML = apptable;

            if(appStatus == "Approved" || appStatus == "Rejected" || appStatus == "Completed")
            {
                document.getElementById("BtnApprove").disabled = true;
                document.getElementById("BtnReject").disabled = true;
            }
            else {
                document.getElementById("BtnApprove").disabled = false;
                document.getElementById("BtnReject").disabled = false;
            }

            selectedID = data[0];
        } );
    });

    $body = $("body");

    $(document).on({
        ajaxStart: function() { $body.addClass("loading");    },
        ajaxStop: function() { $body.removeClass("loading"); }
    });

    function checkApp(btnId, short_term){
        var startDate;
        var term;
        var numTenant;
        var id;
        var userIDs = new Array();
        var urlstr = "/api/0/worders/post/";
        postID = btnId.substring(3);
        var temp = new Array();
        var applicantName;
        var appStatus;
        var passport;
        var isShortTerm = (short_term == "short") ? true : false;

        if(isShortTerm)
            document.getElementById("termlabel").innerHTML = "End Date";
        else
            document.getElementById("termlabel").innerHTML = "Term";

        jQuery.ajax({
            url: urlstr.concat(postID),
            dataType: "json",
            method: "Get",
            success: function (result) {
                table.clear().draw();
                for (var i = 0; i < result.data.length; i++) {
                    userIDs[i] = result.data[i].userID;
                }
                jQuery.ajax({
                    url: "/wp-content/themes/materialwp/userinfo.php",
                    data: {userIDs: userIDs, action: "getInfo"},
                    method: "Post",
                    dataType: "json",
                    success: function (result1) {
                        for (var i = 0; i < result.data.length; i++) {
                            var userID = result.data[i].userID;
                            appStatus = result.data[i].appStatus;

                            //Search applicant name in result1 by userID
                            for(var j = 0; j < result1.data.length; j++)
                                if(result1.data[j].userID == userID)
                                {
                                    applicantName = result1.data[j].name;
                                    if(appStatus == "Completed")
                                        passport = result1.data[j].passport;
                                    else
                                        passport = result1.data[j].passport_blur;
                                }
                            id = result.data[i]._id;
                            startDate = result.data[i].startDate.substring(0,10);
                            numTenant = result.data[i].numTenant;
                            if(isShortTerm) {
                                term = result.data[i].endDate.substring(0,10);
                                /*short term, convert term to end date*/
                                /*term = result.data[i].term;
                                var end  = new Date(2000, 0, 1);
                                var start = new Date(startDate.concat("T15:00:00Z"));
                                var one_day = 1000*60*60*24;

                                end.setTime(start.getTime() + term * one_day);
                                / *store end date in term variable* /
                                term = end.toISOString().substring(0,10);*/
                            } else {
                                term = result.data[i].term;
                                if(term > 85 && term < 95) // =91
                                    term = "3 months";
                                else if(term > 175 && term < 185) // =182
                                    term = "6 months";
                                else if(term > 360 && term < 370) // =365
                                    term = "12 months";
                            }

                            var para = document.createElement("p");
                            var node = document.createTextNode("This is new.");
                            para.appendChild(node);
                            table.row.add( [
                                id, userID, passport, applicantName, startDate, term, numTenant, appStatus
                            ] ).draw( false );
                        }
                    }
                });

            }
        });

        /*$('#button').click( function () {
            alert(table.row('.selected').data());
        } );*/


    } //);

    function AcceptApp()
    {
        /* Change order status */
        var chargeId = "";
        var found = false;

        // Disable the button to avoid re-clicking
        document.getElementById("BtnApprove").disabled = true;
        document.getElementById("BtnReject").disabled = true;

        // Find the applicant's charge(s) for this post
        jQuery.ajax({
            url: '/api/0/wcharges/user/' + applicantID,
            dataType: "json",
            method: "get",
            success: function (result) {
                for (var i = 0; i < result.data.length && found == false; i++) {
                    if (result.data[i].postID == postID) {
                        // Check if the charge is uncaptured or not, in case there is an existing charge created for
                        // previous booking.
                        chargeId = result.data[i]._id;
                        jQuery.ajax({
                            url: '/api/0/charges/' + chargeId,
                            dataType: "json",
                            method: "get",
                            success: function (result) {
                                // Found the uncaptured charge. Capture the charge and update status to approved
                                if (result.data[0].captured == false){
                                    if(found == false) {
                                        found = true;
                                        jQuery.ajax({
                                            url: '/api/0/charges/capture/stripe/' + result.data[0].id,
                                            dataType: "json",
                                            method: "Post",
                                            success: function (result) {
                                                jQuery.ajax({
                                                    url: '/api/0/worders/' + selectedID,
                                                    data: {appStatus: "Approved"},
                                                    dataType: "json",
                                                    method: "Post",
                                                    success: function (result) {
                                                        document.getElementById("BtnApprove").disabled = true;
                                                        document.getElementById("BtnReject").disabled = true;
                                                        var div = document.getElementById('status');
                                                        div.innerHTML="Status: Approved";
                                                        cellStatus.data("Approved").draw();
                                                        /* Send notification email */
                                                        jQuery.ajax({
                                                            url: "/api/0/sendMail",
                                                            data: {to: "ccyangtianfang@gmail.com", subject: "Test email", html: "<b>Hello world ✔</b>"},
                                                            dataType: "json",
                                                            method: "Post",
                                                            success: function (result) {
                                                            }
                                                        });
                                                    }
                                                });
                                            }
                                        });
                                    }
                                }
                            }
                        });
                    }
                }
            }
        });

        return;
    }

    function RejectApp()
    {
        /* Change order status */
        var urlstr = "/api/0/worders/";
        jQuery.ajax({
            url: urlstr.concat(selectedID),
            data: {appStatus: "Rejected"},
            dataType: "json",
            method: "Post",
            success: function (result) {
                document.getElementById("BtnApprove").disabled = true;
                document.getElementById("BtnReject").disabled = true;
                var div = document.getElementById('status');
                div.innerHTML="Status: Rejected";
                cellStatus.data("Rejected").draw();
            }
        });

        /* Send notification email */
        /*jQuery.ajax({
            url: "/api/0/sendMail",
            data: {to: "ccyangtianfang@gmail.com", subject: "Test email", html: "<b>Hello world ✔</b>"},
            dataType: "json",
            method: "Post",
            success: function (result) {

            }
        });
        */
        return;

    }
</script>

<div id="confirm-dialog" class="modal fade" style="z-index: 3000; top:100px" tabindex="-1">
    <div class="modal-dialog" style="width: 450px; height:1000px;">
        <div class="modal-content" style="border: 1px solid #009688;border-radius: 4px;">
            <div class="modal-header">
            </div>
            <div class="modal-body">
                <h4>Are you sure to approve this application?</h4>
                <p class="text-muted">Once you confirmed, the successful applicant will be required to finish the
                    payment within 48 hours. Failing to pay in time results in the application aborts, and you will be
                    able to accpect other applcation. </p>
            </div>
            <div class="modal-footer">
                <table>
                    <tbody>
                    <tr>
                        <td width="15%"></td>
                        <td><button type="button" class="btn btn-default btn-sm" data-dismiss="modal" onclick="AcceptApp()" style="width: 86px;border: 1px solid #4caf50; border-radius: 3px"><span style="color: #4caf50">Confirm</span></button></td>
                        <td><button type="button" class="btn btn-default btn-sm" data-dismiss="modal" style="width: 86px;border: 1px solid #03a9f4; border-radius: 3px"><span style="color: #03a9f4">Cancel</span></button></td>
                        <td width="25%"></td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div id="reject-dialog" class="modal fade" style="z-index: 3000; top:100px" tabindex="-1">
    <div class="modal-dialog" style="width: 450px; height:1000px;">
        <div class="modal-content" style="border: 1px solid #009688;border-radius: 4px;">
            <div class="modal-header"></div>
            <div class="modal-body">
                <h4>Are you sure to reject this application?</h4>
                <p class="text-muted">Once you confirmed, we will send an notification email to the applicant.</p>
            </div>
            <div class="modal-footer">
                <table>
                    <tbody>
                    <tr>
                        <td width="15%"></td>
                        <td><button type="button" class="btn btn-default btn-sm" data-dismiss="modal" onclick="RejectApp()" style="width: 86px;border: 1px solid #4caf50; border-radius: 3px"><span style="color: #4caf50">Confirm</span></button></td>
                        <td><button type="button" class="btn btn-default btn-sm" data-dismiss="modal" style="width: 86px;border: 1px solid #03a9f4; border-radius: 3px"><span style="color: #03a9f4">Cancel</span></button></td>
                        <td width="25%"></td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div id="detail-dialog" class="modal fade" style="z-index: 2000" tabindex="-1">
    <div class="modal-dialog" style="width: 660px; height:1000px;">
        <div class="modal-content">
            <div class="modal-header">
                <table>
                    <tbody>
                    <tr>
                        <td><h3 class="modal-title">Application</h3></td>
                        <td><button type="button" class="close" data-dismiss="modal" aria-hidden="true" style="float: right">&times;</button></td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div class="modal-body">
                <div id="app-detail"></div>
            </div>
            <div class="modal-footer">
                <table>
                    <tbody>
                        <tr>
                            <td width="2%"></td>
                            <td width="20%"><button id="BtnApprove" type="button" class="btn btn-success btn-sm" style="width: 100px; float: left" data-toggle='modal' data-target='#confirm-dialog'>Approve</button></td>
                            <td width="20%"><button id="BtnReject" type="button" class="btn btn-danger btn-sm" style="width: 100px; float: left" data-toggle='modal' data-target='#reject-dialog'>Reject</button></td>
                            <td width="20%"><button type="button" class="btn btn-primary btn-sm" style="width: 100px; float: left" data-dismiss="modal">Dismiss</button></td>
                            <td width="38%"></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div id="complete-dialog" class="modal fade" tabindex="-1">
    <div class="modal-dialog" style="width: 650px; height:400px;">
        <div class="modal-content">
            <div class="modal-header">
                <table>
                    <tbody>
                        <tr>
                            <td><h3 class="modal-title">Applications</h3></td>
                            <td><button type="button" class="close" data-dismiss="modal" aria-hidden="true" style="float: right">&times;</button></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="modal-body">
                <table id="appTable"  class="table table-striped table-hover " width="100%"">
                    <thead>
                    <tr>
                        <th>id</th>
                        <th>userid</th>
                        <th>passport</th>
                        <th>Applicant</th>
                        <th>Start Date</th>
                        <th id="termlabel">Term</th>
                        <th>Tena(s)</th>
                        <th>Status</th>
                        <th>Op</th>
                    </tr>
                    </thead>
                </table>
                <style>
                    th.dt-center, td.dt-center { text-align: center; vertical-align: middle !important;}
                </style>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal">Dismiss</button>
            </div>
        </div>
    </div>
</div>
<div class="container">
    <div class="row">
        <div id="primary" class="col-md-12 col-lg-12">
            <main id="main" class="site-main" role="main">
                <div class="card">
                    <div class="entry-img"></div>
                    <div class="entry-container">
                        <p></p>
                        <div>
                            <ul class="nav nav-pills" style="margin-bottom: 35px; margin-left: 0px;margin-top: -15px;">
                                <li><a href="/your-profile/">Profile</a></li>
                                <li><a href="/your-profile/wish-list/">Wish List</a></li>
                                <?php if($is_host == 1) {?>
                                    <li  class="active"><a href="/your-profile/users-listings/">Your Listings</a></li>
                                <?php }?>
                                <?php if($is_tenant == 1) {?>
                                    <li><a href="/your-profile/users-orders/">Orders</a></li>
                                <?php }?>
                                <?php if($is_host == 1) {?>
                                    <li><a href="/your-profile/account/">Account</a></li>
                                <?php }?>
                            </ul>
                        </div>
                        <div class="panel panel-default">
                            <div class="panel-heading">Your listings: <?php echo $rental_count?></div>
                            <table>
                                <tbody>
                                    <tr>
                                        <td style="width: 8%"></td>
                                        <td style="width: 84%">
                                            <div class="panel-body">
                                                <div class="list-group">
                                                    <?php for($i=0;$i<$rental_count;$i++) { ?>
                                                        <div class="list-group-item">
                                                            <div class="row-picture">
                                                                <img class="circle" src=<?php echo '/wp-content/uploads/'.$pic_arr[$results1[$i]['id']];?> alt="icon">
                                                            </div>
                                                            <div class="row-content">
                                                                <table>
                                                                    <tbody>
                                                                        <tr>
                                                                            <td >
                                                                                <h1 class="list-group-item-heading"><?php echo $results1[$i][post_title]?></h1>
                                                                                <p class="list-group-item-text">
                                                                                    <?php
                                                                                    if ($results2[$i*$subArraySz+2]['meta_value'] !='')
                                                                                        echo $results2[$i*$subArraySz+2]['meta_value']. ' ';
                                                                                    echo $results2[$i*$subArraySz+1]['meta_value'].', '.$results2[$i*$subArraySz+3]['meta_value']
                                                                                    ?>
                                                                                </p>
                                                                            </td>
                                                                            <td align="right" valign="middle">
                                                                                <a href="<?php echo $results1[$i][guid]?>" class="btn btn-default btn-sm" style="width: 86px;border: 1px solid #4caf50; border-radius: 3px"><span style="color: #4caf50">View</span></a>
                                                                                <a href="" class="btn btn-default btn-sm" style="width: 86px;border: 1px solid #f44336; border-radius: 3px"><span style="color: #f44336">Delete</span></a>
                                                                                <button id="app<?php echo $results1[$i][id];?>" type="button" class="btn btn-default btn-sm" style="width: 86px;border: 1px solid #03a9f4; border-radius: 3px" data-toggle="modal" data-target="#complete-dialog" onclick="checkApp(this.id, '<?php echo $short_term_arr[$results1[$i][id]];?>')"><span style="color: #03a9f4">Applies</span></button>

                                                                            </td>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                        <div class="list-group-separator"></div>
                                                    <?php } ?>
                                                </div>
                                            </div>
                                        </td>
                                        <td style="width: 8%"></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </main><!-- #main -->
        </div><!-- #primary -->

    </div> <!-- .row -->
</div> <!-- .container -->

<div class="spinning"><!-- Place at bottom of page --></div>

<?php get_footer(); ?>
