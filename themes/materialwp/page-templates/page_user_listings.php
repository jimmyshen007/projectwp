<?php
/*
Template Name: User Listings
*/

get_header();
?>
<?php
global $wpdb;
$query1 = 'SELECT id, post_title, guid FROM wp_posts where post_type = \'rental\' and post_author = '.$userdata->ID;
$results1 = $wpdb->get_results( $query1, ARRAY_A );
$rental_count = count($results1);
$post_list_str = '0';
foreach($results1 as $x => $x_value) {
    $post_list_str = $post_list_str.','.$x_value['id'];
}

$query2 = 'SELECT meta_key,meta_value FROM `wp_postmeta` '.
    ' WHERE post_id in ('.$post_list_str.') and (meta_key=\'property_rent\' or meta_key=\'property_rent_period\' or '.
    'meta_key=\'property_address_country\' or meta_key=\'property_address_street\' or meta_key=\'property_address_suburb\' '.
    'or meta_key=\'property_address_street_number\')'.
    'ORDER by post_id desc, meta_key asc';

$query3 ='SELECT p1.id, p3.meta_value, p1.id from wp_posts as p1, wp_posts as p2, wp_postmeta as p3'.
    ' where p1.id = p2.post_parent and p2.id = p3.post_id and p1.post_type = \'rental\' and p1.post_author ='.$userdata->ID.' and p3.meta_key = \'_wp_attached_file\''.
    ' ORDER by p1.id DESC, p2.id desc';

$results2 = $wpdb->get_results( $query2, ARRAY_A );
$results3 = $wpdb->get_results( $query3, ARRAY_A );
$subArraySz = 6;
/* results2
 * ix6+0: property_address_country
 * ix6+1: property_address_street
 * ix6+2:property_address_street_number
 * ix6+3: property_address_suburb
 * ix6+4: property_rent
 * ix6+5: property_rent_period
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
?>
<script>
    var dataSet = new Array();
    var table;
    var selectedID;
    var cellStatus;
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
                { "width": "10%", "targets": 4 },
                {"className": "dt-center", "targets": "_all"}
            ],
            "pageLength": 25,
            "lengthChange": false
        } );

        $('#appTable tbody').on( 'click', 'button', function () {
            var data = table.row( $(this).parents('tr') ).data();
            cellStatus = table.cell(table.row( $(this).parents('tr') ), table.column(7));
            var userID = data[1];
            var applicantName = data[3];
            var startDate = data[4];
            var term= data[5];
            var numTenant = data[6];
            var appStatus = data[7];
            var div = document.getElementById('app-detail');
            var apptable = '<table><tr><td><h4>Applicant : ' + applicantName + '</h4></td></tr>';
            apptable = apptable + '<tr><td><h4>Start Date: '+ startDate + '</td><td><h4> Term  : ' + term + '</h4></td></tr>';
            apptable = apptable + '<tr><td><h4>Tenants   : '+ numTenant + '</td><td><h4 id="status"> Status: ' + appStatus + '</h4></td></tr></table>';
            apptable = apptable + '<p><img style="width: 600px; height: 300px" src="' +data[2] +'"></p>';
            div.innerHTML = apptable;

            if(appStatus == "Approved" || appStatus == "Rejected")
            {
                document.getElementById("BtnApprove").disabled = true;
                document.getElementById("BtnReject").disabled = true;
            }

            selectedID = data[0];
        } );
    });
    function checkApp(id){
        var startDate;
        var term;
        var numTenant;
        var id;
        var userIDs = new Array();
        var urlstr = "/api/0/worders/post/";
        var postID = id.substring(3);
        var temp = new Array();
        var applicantName;
        var appStatus;
        var passport;
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
                    data: {userIDs: userIDs},
                    method: "Post",
                    dataType: "json",
                    success: function (result1) {
                        for (var i = 0; i < result.data.length; i++) {
                            var userID = result.data[i].userID;
                            //Search applicant name in result1 by userID
                            for(var j = 0; j < result1.data.length; j++)
                                if(result1.data[j].userID == userID)
                                {
                                    applicantName = result1.data[j].name;
                                    passport = result1.data[j].passport;
                                }
                            id = result.data[i]._id;
                            startDate = result.data[i].startDate.substring(0,10);
                            numTenant = result.data[i].numTenant;
                            term = result.data[i].term;
                            appStatus = result.data[i].appStatus;
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
        var urlstr = "/api/0/worders/";
        jQuery.ajax({
            url: urlstr.concat(selectedID),
            data: {appStatus: "Approved"},
            dataType: "json",
            method: "Post",
            success: function (result) {
                document.getElementById("BtnApprove").disabled = true;
                document.getElementById("BtnReject").disabled = true;
                var div = document.getElementById('status');
                div.innerHTML="Status: Approved";
                cellStatus.data("Approved").draw();
            }
        });
        /* Send notification email */
        jQuery.ajax({
            url: "/api/0/sendMail",
            data: {to: "ccyangtianfang@gmail.com", subject: "Test email", html: "<b>Hello world ✔</b>"},
            dataType: "json",
            method: "Post",
            success: function (result) {

            }
        });


        return;

    }
</script>

<div id="confirm-dialog" class="modal fade" style="z-index: 3000" tabindex="-1">
    <div class="modal-dialog" style="width: 450px; height:1000px;">
        <div class="modal-content">
            <div class="modal-header">
                <table>
                    <tbody>
                    <tr>
                        <td><button type="button" class="close" data-dismiss="modal" aria-hidden="true" style="float: right">&times;</button></td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div class="modal-body">
                <h4>Are you sure to approve this application?</h4>
                <p class="text-muted">Once you confirmed, we will send an notification email to the successful applicant.</p>
            </div>
            <div class="modal-footer">
                <table>
                    <tbody>
                    <tr>
                        <td width="15%"></td>
                        <td><button type="button" class="btn btn-success" data-dismiss="modal" onclick="AcceptApp()">Confirm</button></td>
                        <td><button type="button" class="btn btn-info" data-dismiss="modal">Cancel</button></td>
                        <td width="25%"></td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div id="detail-dialog" class="modal fade" style="z-index: 2000" tabindex="-1">
    <div class="modal-dialog" style="width: 750px; height:1000px;">
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
                            <td width="60%"></td>
                            <td><button id="BtnApprove" type="button" class="btn btn-success" data-toggle='modal' data-target='#confirm-dialog'>Approve</button></td>
                            <td><button id="BtnReject" type="button" class="btn btn-danger" data-dismiss="modal">Reject</button></td>
                            <td><button type="button" class="btn btn-primary" data-dismiss="modal">Dismiss</button></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div id="complete-dialog" class="modal fade" tabindex="-1">
    <div class="modal-dialog" style="width: 700px; height:400px;">
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
                        <th>Term</th>
                        <th>Tenants</th>
                        <th>Status</th>
                        <th>Op</th>
                    </tr>
                    </thead>
                    <tfoot>
                    <tr>
                        <th>id</th>
                        <th>userid</th>
                        <th>passport</th>
                        <th>Applicant</th>
                        <th>Start Date</th>
                        <th>Term</th>
                        <th>Tenants</th>
                        <th>Status</th>
                        <th>Op</th>
                    </tr>
                    </tfoot>

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
                                <li  class="active"><a href="/your-profile/users-listings/">Your Listings</a></li>
                                <li><a href="/your-profile/users-orders/">Orders</a></li>
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
                                                                <img class="circle" src=<?php echo '/wordpress/wp-content/uploads/'.$pic_arr[$results1[$i]['id']];?> alt="icon">
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
                                                                                        echo $results2[$i*$subArraySz+2]['meta_value'].'/';
                                                                                    echo $results2[$i*$subArraySz+1]['meta_value'].', '.$results2[$i*$subArraySz+3]['meta_value']
                                                                                    ?>
                                                                                </p>
                                                                            </td>
                                                                            <td align="right" valign="middle">
                                                                                <a href="<?php echo $results1[$i][guid]?>" class="btn btn-success btn-sm">View</a>
                                                                                <a href="" class="btn btn-danger btn-sm">Delete</a>
                                                                                <button id="app<?php echo $results1[$i][id];?>" type="button" class="btn btn-warning btn-sm" data-toggle="modal" data-target="#complete-dialog" onclick="checkApp(this.id)">Applies</button>

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



<?php get_footer(); ?>
