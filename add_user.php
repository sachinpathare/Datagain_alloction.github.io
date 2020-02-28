<?php
include('header.php');
include '../sql.php';
//strip the incoming text of any unwanted characters (SQL Injection attacks)
function quote_smart($value, $db_handle) {
   if (get_magic_quotes_gpc()) {
       $value = stripslashes($value);
   }
   if (!is_numeric($value)) {
       $value = "'" . mysqli_real_escape_string($db_handle,$value) . "'";
   }
   return $value;
}

$msg = "";
$exist = false;
$timailhan = false;

if(isset($_POST['insert'])){
	
	$users = $_POST['uname'];
	$Email = $_POST['email'];
	$pass = $_POST['pword'];
	$grp = $_POST['group'];
	$pos = $_POST['position'];
	
	
	$SQL = "SELECT * FROM info";
	$result = mysqli_query($db_handle,$SQL);
	while ($db_field = mysqli_fetch_assoc($result)) {
		if ($Email == $db_field['email']){
			$exist = true;
			break;
		}
	}

	if ($exist){
		//die("<SCRIPT LANGUAGE='JavaScript'>alert('Email ID already exist.')</script><script>location.href = 'add_user.php'</script>");
		$msg = '<div class="alert alert-danger">User Email ID already exist!</div>';
		mysqli_close($db_handle);
	}
	else{
		
		$SQL = "SELECT * FROM info WHERE groups = '$grp' AND position = 'leader'";
		$result = mysqli_query($db_handle,$SQL);
		while($db_field = mysqli_fetch_assoc($result)){
			$led = $db_field['username'];
			if($led != ""){
				$timailhan = true;
			}
		}
		$bui_pos = $pos;
		$bui_grp = $grp;
		$bui_user = $users;
		if($pos == "QCer"){
			if($timailhan){
				die("<SCRIPT LANGUAGE='JavaScript'>alert('Group has already a leader.')</script><script>location.href = 'add_user.php'</script>");
			}
		} 
		
		//unwanted HTML (scripting attacks)
		$users = htmlspecialchars($users);
		$Email = htmlspecialchars($Email);
		$pass = htmlspecialchars($pass);
		$grp = htmlspecialchars($grp);
		$pos = htmlspecialchars($pos);
		
		//function
		$users = quote_smart($users, $db_handle);
		$Email = quote_smart($Email, $db_handle);
		$pass = quote_smart($pass, $db_handle);
		$grp = quote_smart($grp, $db_handle);
		$pos = quote_smart($pos, $db_handle);
		
		
		$SQL = "INSERT INTO info (`username`,`email`, `password`, `groups`, `position`) VALUES ($users,$Email,$pass, $grp, $pos)";
		$test=mysqli_query($db_handle,$SQL);
		if(!$test)
		{
			mysqli_error($db_handle);
			//die("<SCRIPT LANGUAGE='JavaScript'>alert('User name already exist.')</script><script>location.href = 'add_user.php'</script>");
			
			
		}
			if($bui_pos == "QCer"){
			$SQL = "UPDATE group_title SET group_leader = '$bui_user' WHERE group_name = '$bui_grp'";
			mysqli_query($db_handle,$SQL);
		}
		 
		 
		mysqli_close($db_handle);
		$msg = '<div class="alert alert-success">User succesfully added.</div>';
		 
	}
}

?>

      <section class="wrapper site-min-height">
        <h3>Add User</h3>
		<p><?php echo $msg; ?></p>
            <div class="form-panel">
              <div class=" form">
                <form class="cmxform form-horizontal style-form" id="commentForm" method="POST" name='add_form' action="">
                  <div class="form-group ">
                    <label for="cname" class="control-label col-lg-2">Name (required)</label>
                    <div class="col-lg-5">
                      <input class=" form-control" id="cname" name="uname" minlength="2" type="text" required />
                    </div>
                  </div>
                  <div class="form-group ">
                    <label for="cemail" class="control-label col-lg-2">E-Mail (required)</label>
                    <div class="col-lg-5">
                      <input class="form-control " id="cemail" type="email" name="email" required />
                    </div>
                  </div>
                   <div class="form-group ">
                    <label for="cname" class="control-label col-lg-2">Password</label>
                    <div class="col-lg-5">
                      <input class=" form-control" id="cname" name="pword" minlength="6" type="text" required />
                    </div>
                  </div>
                  <div class="form-group ">
                    <label for="ccomment" class="control-label col-lg-2">Shift</label>
                    <div class="col-lg-5">
					<select name = 'group' class=" form-control">
	
<?php
	include '../sql.php';
	
	$SQL = "SELECT * FROM group_title ORDER BY group_name ASC";
	$result = mysqli_query($db_handle,$SQL);
	while ($db_field = mysqli_fetch_assoc($result)){
		$list = $db_field['group_name'];
		if($list != "admin"){
			print("<option>$list");
		}
	}
	mysqli_close($db_handle);
?>

	</select>
                      
                    </div>
                  </div>
				  <div class="form-group ">
                    <label for="ccomment" class="control-label col-lg-2">Positon</label>
                    <div class="col-lg-5">
					<select name = 'position' class=" form-control">
					<option>member</option>
					<option>QC</option>

						</select>
                      
                    </div>
                  </div>
				  
                  <div class="form-group">
                    <div class="col-lg-offset-2 col-lg-10">
                      <button class="btn btn-theme" type="submit" name="insert">Save</button>
                      <button class="btn btn-theme04" type="button" onclick="window.location.href = 'users.php';">Cancel</button>
                    </div>
                  </div>
                </form>
              </div>
            </div>
          <!-- page end-->
        </div>
        <!-- /row -->
      </section>
      <!-- /wrapper -->
    </section>
    <!-- /MAIN CONTENT -->
    <!--main content end-->
    <!--footer start-->
   <?php include('footer.php'); ?>