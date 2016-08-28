<?php
	global $post;
	if($post->post_type != "rental" && $post->post_type != "property" && $post->post_type != "business")
		return;
?>

<button id="hidelogin" style="display: none" type="button" class="btn btn-primary" data-toggle="modal" data-target="#complete-dialog">Open dialog</button>
<div id="complete-dialog" class="modal fade" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title">Login</h4>
			</div>
			<div class="modal-body">
				<?php var_dump($template); ?>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary" data-dismiss="modal">Login</button>
			</div>
		</div>
	</div>
</div>

<div id="secondary" class="widget-area col-md-4 col-lg-4" role="complementary">
	<div id="sidebar-1"class="panel panel-default">
		<div class="panel-heading">Panel heading</div>
		<div class="panel-body">
			<fieldset id="fs1">
				<a id="BtnLike" href="javascript:void(0)" class="btn btn-raised btn-default" onclick="ChangeFav()" style="width: 100%" >
					<span style="font-size: 15px">Save to  Wish List</span>
				</a>
			</fieldset>
			<div id="div1"></div>
			<script>
				var userID = <?php global $user_ID; echo $user_ID;?>;
				var postID = <?php global $post; echo $post->ID;?>;
				var urlstr = "/api/0/favorites/user/";
				var favID;
				if (userID != 0) {
					jQuery.ajax({
						url: urlstr.concat(<?php global $user_ID; echo $user_ID;?>),
						dataType: "json",
						method: "Get",
						success: function (result) {
							for (var i = 0; i < result.data.length; i++) {
								if (result.data[i].fType == "post" && result.data[i].fValue == postID) {
									favID = result.data[i]._id;
									document.getElementById("BtnLike").className = 'btn btn-raised btn-danger';
									document.getElementById("BtnLike").innerHTML = "Saved to Wish List";
								}
							}
						}
					});
				}
				function ChangeFav(){
					if (userID == 0) {
						document.getElementById("hidelogin").click();
					}

					else {
						if (document.getElementById("BtnLike").className == "btn btn-raised btn-default") //Add to Wish List
						{
							jQuery.ajax({
								url: "/api/0/favorites",
								dataType: "json",
								method: "POST",
								data: {"fType": "post", "fValue": postID,"userID" : userID},
								success: function(result){
									favID = result.data._id;
									document.getElementById("BtnLike").className = 'btn btn-raised btn-danger';
									document.getElementById("BtnLike").innerHTML = "Saved to Wish List";
								}});
						} else { //Remove from Wish List
							var urldel = "/api/0/favorites/";
							jQuery.ajax({
								url: urldel + favID,
								dataType: "json",
								method: "DELETE",
								success: function(result){
									if (result.data.ok == 1) {
										document.getElementById("BtnLike").className = 'btn btn-raised btn-default';
										document.getElementById("BtnLike").innerHTML = "Save to Wish List";
									} else { //server error: failed to delete
										alert("Oops, we are encountering some server issue. Please try it later.");
									}
								}});
						}
					}
				}
			</script>
		</div>
	</div>

	<script>
		var distance = $('#secondary').offset().top;

		$(window).scroll(function() {
			if ( $(window).scrollTop() >= distance ) {
				document.getElementById("sidebar-1").style.position = "fixed";
				document.getElementById("sidebar-1").style.top = "0";
				document.getElementById("sidebar-1").style.zIndex = "1000";
				document.getElementById("sidebar-1").style.width = "360px";
			}
			if ( $(window).scrollTop() < distance ) {
				document.getElementById("sidebar-1").style.position = "absolute";
				document.getElementById("sidebar-1").style.width = "360px";
			}
		});
	</script>

	
		<?php dynamic_sidebar( 'sidebar-1' ); ?>
	
</div><!-- #secondary -->


