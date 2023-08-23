
<?php 
error_reporting(0);
include '../Includes/dbcon.php';
include '../Includes/session.php';

    $query = "SELECT tblcourse.courseName,tbllecturerooms.lectureRoomName 
    FROM tblfaculty
    INNER JOIN tblcourse ON tblcourse.Id = tblfaculty.courseId
    INNER JOIN tbllecturerooms ON tbllecturerooms.Id = tblfaculty.lectureRoomId
    Where tblfaculty.Id = '$_SESSION[userId]'";
    $rs = $conn->query($query);
    $num = $rs->num_rows;
    $rrw = $rs->fetch_assoc();


//session and Term
        $querey=mysqli_query($conn,"select * from tblsemester where isActive ='1'");
        $rwws=mysqli_fetch_array($querey);
        $semesterId = $rwws['Id'];

        $dateTaken = date("Y-m-d");

        $qurty=mysqli_query($conn,"select * from tblattendance  where courseId = '$_SESSION[courseId]' and lectureRoomId = '$_SESSION[lectureRoomId]' and dateTimeTaken='$dateTaken'");
        $count = mysqli_num_rows($qurty);

        if($count == 0){ //if Record does not exsit, insert the new record

          //insert the students record into the attendance table on page load
          $qus=mysqli_query($conn,"select * from tblstudents  where courseId = '$_SESSION[courseId]' and lectureRoomId = '$_SESSION[lectureRoomId]'");
          while ($ros = $qus->fetch_assoc())
          {
              $qquery=mysqli_query($conn,"insert into tblattendance(admissionNo,courseId,lectureRoomId,semesterId,status,dateTimeTaken) 
              value('$ros[admissionNumber]','$_SESSION[courseId]','$_SESSION[lectureRoomId]','$semesterId','0','$dateTaken')");

          }
        }

  
      



if(isset($_POST['save'])){
    
    $admissionNo=$_POST['admissionNo'];

    $check=$_POST['check'];
    $N = count($admissionNo);
    $status = "";


//check if the attendance has not been taken i.e if no record has a status of 1
  $qurty=mysqli_query($conn,"select * from tblattendance  where courseId = '$_SESSION[courseId]' and lectureRoomId = '$_SESSION[lectureRoomId]' and dateTimeTaken='$dateTaken' and status = '1'");
  $count = mysqli_num_rows($qurty);

  if($count > 0){

      $statusMsg = "<div class='alert alert-danger' style='margin-right:700px;'>Attendance has been taken for today!</div>";

  }

    else //update the status to 1 for the checkboxes checked
    {

        for($i = 0; $i < $N; $i++)
        {
                $admissionNo[$i]; //admission Number

                if(isset($check[$i])) //the checked checkboxes
                {
                      $qquery=mysqli_query($conn,"update tblattendance set status='1' where admissionNo = '$check[$i]'");

                      if ($qquery) {

                          $statusMsg = "<div class='alert alert-success'  style='margin-right:700px;'>Attendance Taken Successfully!</div>";
                      }
                      else
                      {
                          $statusMsg = "<div class='alert alert-danger' style='margin-right:700px;'>An error Occurred!</div>";
                      }
                  
                }
          }
      }

   

}

if(isset($_POST['saveabs'])){
    
    $admissionNo=$_POST['admissionNo'];

    $check=$_POST['check'];
    $N = count($admissionNo);
    $status = "";


//check if the attendance has not been taken i.e if no record has a status of 1
  $qurty=mysqli_query($conn,"select * from tblattendance  where courseId = '$_SESSION[courseId]' and lectureRoomId = '$_SESSION[lectureRoomId]' and dateTimeTaken='$dateTaken' and status = '1'");
   
  $count = mysqli_num_rows($qurty);

  if($count > 0){

      $statusMsg = "<div class='alert alert-danger' style='margin-right:700px;'>Attendance has been taken for today!</div>";

  }

    else //update the status to 1 for the checkboxes checked
    {

        for($i = 0; $i < $N; $i++)
        {
                $admissionNo[$i]; //admission Number

                if(isset($check[$i])) //the checked checkboxes
                {
                     $quer="SELECT * FROM tblattendance WHERE admissionNo='$check[$i]'";
                    $result=mysqli_query($conn,$quer);
                    $result_fetch=mysqli_fetch_assoc($result);
                    $absent=$result_fetch[absence]+1;


                      $qquery=mysqli_query($conn,"update tblattendance set status='0',absence={$absent} where admissionNo = '$check[$i]'");

                      if ($qquery) {

                          $statusMsg = "<div class='alert alert-success'  style='margin-right:700px;'>Attendance Taken Successfully!</div>";

                          if($absent==3){
                       $quey="SELECT * FROM tblstudents WHERE admissionNumber='$check[$i]'";
    $result=mysqli_query($conn,$quey);
    $result_fetch=mysqli_fetch_assoc($result);
     $mail=$result_fetch['email'];

       $message = '
        <h3 align="center">Details</h3>
        <table border="1" width="100%" cellpadding="5" cellspacing="5">
            
            <tr>
                <td width="30%">Title</td>
                <td width="70%">'.$_POST["subject"].'</td>
            </tr>
            
        </table>
    ';
    $emailto=$mail;
    require '../class/class.phpmailer.php';
    $mail = new PHPMailer;
    $mail->IsSMTP();                                //Sets Mailer to send message using SMTP
    $mail->Host = 'smtp.gmail.com';     //Sets the SMTP hosts of your Email hosting, this for Godaddy
    $mail->Port = '587';                                //Sets the default SMTP server port
    $mail->SMTPAuth = true;                 //Sets SMTP authentication. Utilizes the Username and Password variables
    $mail->Username = 'petersangy49@gmail.com';                 //Sets SMTP username
    $mail->Password = 'pjdqldfltlcaprgg';                    //Sets SMTP password
    $mail->SMTPSecure = "tls";                          //Sets connection prefix. Options are "", "ssl" or "tls"
    $mail->From = 'petersangy49@gmail.com';                  //Sets the From email address for the message
    $mail->FromName = 'School Faculty';               //Sets the From name of the message
    $mail->AddAddress($emailto);        //Adds a "To" address
    $mail->WordWrap = 50;                           //Sets word wrapping on the body of the message to a given number of characters
    $mail->IsHTML(true);                            //Sets message type to HTML
    $mail->AddAttachment($path);                    //Adds an attachment from a path on the filesystem
    $mail->Subject = 'Course Attendance System';             //Sets the Subject of the message
    $mail->Body ='Greetings, kindly be notified that you have missed course attendance three times and an additional absentism will get you banned and logged out of the system<br><br>Regards;<br>School Faculty Team';                         //An HTML or plain text message body
    if($mail->Send())                               //Send an Email. Return true on success or false on error
    {
        $statusMsg = '<div class="alert alert-success">Email Successfully</div>';
       // unlink($path);
       header("Location:./takeAttendance.php");
    }
    else
    {
        $statusMsg = '<div class="alert alert-danger">An Error occured while sending mail, please try again!</div>';
    }


                      }

                     if($absent>3){

                        $que="SELECT * FROM tblstudents WHERE admissionNumber='$check[$i]'";
                        $resul=mysqli_query($conn,$que);
                        $resul_fetch=mysqli_fetch_assoc($resul);
                        $bn=$resul_fetch['ban'];
                        $qquey=mysqli_query($conn,"update tblstudents set ban='1' where admissionNumber = '$check[$i]'");
                        if($qquey){
                        echo"";
                         }





                     }


                      }
                      else
                      {
                          $statusMsg = "<div class='alert alert-danger' style='margin-right:700px;'>An error Occurred!</div>";
                      }
                  
                }
          }
      }

   

}

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">
  <link href="img/logo/attnlg.jpg" rel="icon">
  <title>Dashboard</title>
  <link href="../vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
  <link href="../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css">
  <link href="css/ruang-admin.min.css" rel="stylesheet">



   <script>
    function lectureRoomDropdown(str) {
    if (str == "") {
        document.getElementById("txtHint").innerHTML = "";
        return;
    } else { 
        if (window.XMLHttpRequest) {
            // code for IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp = new XMLHttpRequest();
        } else {
            // code for IE6, IE5
            xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
        }
        xmlhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                document.getElementById("txtHint").innerHTML = this.responseText;
            }
        };
        xmlhttp.open("GET","ajaxlectureRooms2.php?cid="+str,true);
        xmlhttp.send();
    }
}
</script>
</head>

<body id="page-top">
  <div id="wrapper">
    <!-- Sidebar -->
      <?php include "Includes/sidebar.php";?>
    <!-- Sidebar -->
    <div id="content-wrapper" class="d-flex flex-column">
      <div id="content">
        <!-- TopBar -->
       <?php include "Includes/topbar.php";?>
        <!-- Topbar -->

        <!-- Container Fluid-->
        <div class="container-fluid" id="container-wrapper">
          <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Mark Attendance (Today's Date : <?php echo $todaysDate = date("m-d-Y");?>)</h1>
            <ol class="breadcrumb">
              <li class="breadcrumb-item"><a href="./">Home</a></li>
              <li class="breadcrumb-item active" aria-current="page">All Students in Course</li>
            </ol>
          </div>

          <div class="row">
            <div class="col-lg-12">
              <!-- Form Basic -->


              <!-- Input Group -->
        <form method="post">
            <div class="row">
              <div class="col-lg-12">
              <div class="card mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                  <h6 class="m-0 font-weight-bold text-primary">All Student in (<?php echo $rrw['courseName'].' - '.$rrw['lectureRoomName'];?>) Class</h6>
                  <h6 class="m-0 font-weight-bold text-danger">Note: <i>Check on the boxes and mark attendance</i></h6>
                </div>
                <div class="table-responsive p-3">
                <?php echo $statusMsg; ?>
                  <table class="table align-items-center table-flush table-hover">
                    <thead class="thead-light">
                      <tr>
                        <th>#</th>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Email Address</th>
                        <th>Admission No</th>
                        <th>Course</th>
                        <th>Lecture Room</th>
                        <th>Check</th>
                      </tr>
                    </thead>
                    
                    <tbody>

                  <?php
                      $query = "SELECT tblstudents.Id,tblstudents.admissionNumber,tblcourse.courseName,tblcourse.Id As courseId,tbllecturerooms.lectureRoomName,tbllecturerooms.Id AS lectureRoomId,tblstudents.firstName,
                      tblstudents.lastName,tblstudents.email,tblstudents.admissionNumber,tblstudents.dateCreated
                      FROM tblstudents
                      INNER JOIN tblcourse ON tblcourse.Id = tblstudents.courseId
                      INNER JOIN tbllecturerooms ON tbllecturerooms.Id = tblstudents.lectureRoomId
                      where tblstudents.courseId = '$_SESSION[courseId]' and tblstudents.lectureRoomId = '$_SESSION[lectureRoomId]'";
                      $rs = $conn->query($query);
                      $num = $rs->num_rows;
                      $sn=0;
                      $status="";
                      if($num > 0)
                      { 
                        while ($rows = $rs->fetch_assoc())
                          {
                             $sn = $sn + 1;
                            echo"
                              <tr>
                                <td>".$sn."</td>
                                <td>".$rows['firstName']."</td>
                                <td>".$rows['lastName']."</td>
                                <td>".$rows['email']."</td>
                                <td>".$rows['admissionNumber']."</td>
                                <td>".$rows['courseName']."</td>
                                <td>".$rows['lectureRoomName']."</td>
                                <td><input name='check[]' type='checkbox' value=".$rows['admissionNumber']." class='form-control'></td>
                              </tr>";
                              echo "<input name='admissionNo[]' value=".$rows['admissionNumber']." type='hidden' class='form-control'>";
                          }
                      }
                      else
                      {
                           echo   
                           "<div class='alert alert-danger' role='alert'>
                            No Record Found!
                            </div>";
                      }
                      
                      ?>
                    </tbody>
                  </table>
                  <br>
                  <button type="submit" name="save" class="btn btn-primary">Mark Student Present</button>
                   <button type="submit" name="saveabs" class="btn btn-warning">Mark Student Absent</button>
                  </form>
                </div>
              </div>
            </div>
            </div>
          </div>
          <!--Row-->


        </div>
        <!---Container Fluid-->
      </div>
      <!-- Footer -->
       <?php include "Includes/footer.php";?>
      <!-- Footer -->
    </div>
  </div>

  <!-- Scroll to top -->
  <a class="scroll-to-top rounded" href="#page-top">
    <i class="fas fa-angle-up"></i>
  </a>

  <script src="../vendor/jquery/jquery.min.js"></script>
  <script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="../vendor/jquery-easing/jquery.easing.min.js"></script>
  <script src="js/ruang-admin.min.js"></script>
   <!-- Page level plugins -->
  <script src="../vendor/datatables/jquery.dataTables.min.js"></script>
  <script src="../vendor/datatables/dataTables.bootstrap4.min.js"></script>

  <!-- Page level custom scripts -->
  <script>
    $(document).ready(function () {
      $('#dataTable').DataTable(); // ID From dataTable 
      $('#dataTableHover').DataTable(); // ID From dataTable with Hover
    });
  </script>
</body>

</html>