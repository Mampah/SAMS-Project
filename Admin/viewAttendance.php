
<?php 
error_reporting(0);
include '../Includes/dbcon.php';
include '../Includes/session.php';



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
            <h1 class="h3 mb-0 text-gray-800">View Course Attendance</h1>
            <ol class="breadcrumb">
              <li class="breadcrumb-item"><a href="./">Home</a></li>
              <li class="breadcrumb-item active" aria-current="page">View Course Attendance</li>
            </ol>
          </div>

          <div class="row">
            <div class="col-lg-12">
              <!-- Form Basic -->
              <div class="card mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                  <h6 class="m-0 font-weight-bold text-primary">View Course Attendance</h6>
                    <?php echo $statusMsg; ?>
                </div>
                <div class="card-body">
                  <form method="post">
                    <div class="form-group row mb-3">
                        <div class="col-xl-6">
                        <label class="form-control-label">Select Date<span class="text-danger ml-2">*</span></label>
                            <input type="date" class="form-control" name="dateTaken" id="exampleInputFirstName" placeholder="Class Arm Name">
                        </div>
                       
                    </div>
                    <button type="submit" name="view" class="btn btn-primary">View Attendance</button>
                  </form>
                </div>
              </div>

              <!-- Input Group -->
                 <div class="row">
              <div class="col-lg-12">
              <div class="card mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                  <h6 class="m-0 font-weight-bold text-primary">Course Attendance</h6>
                   <a href="#" class="btn btn-xs btn-info fas fa-print" value="print" onclick="PrintDiv();">Print Report</a>
                </div>
                <div class="table-responsive p-3">
                <div id="chartContainer" style="height: 270px; width: 100%;"></div>
                    <script src="https://cdn.canvasjs.com/canvasjs.min.js"></script>

                    <span id="divToPrint">
                   
                  <table class="table align-items-center table-flush table-hover" id="dataTableHover">

                    <thead class="thead-light">
                      <tr>
                        <th>#</th>
                        <th>Clock In Time</th>
                        <th>Clock Out Time</th>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Email Address</th>
                        <th>Admission No</th>
                        <th>Course</th>
                        <th>Lecture Room</th>
                        <th>Year</th>
                        <th>Semester</th>
                        <th>Status</th>
                        <th>Date</th>
                      </tr>
                    </thead>
                   
                    <tbody>

                  <?php

                    if(isset($_POST['view'])){
                      if($_POST['dateTaken'] !="dd/mm/yyyy"){

                      $dateTaken =  $_POST['dateTaken'];

                      $query = "SELECT tblattendance.Id,tblattendance.status,tblattendance.dateTimeTaken,tblattendance.clock_in,tblattendance.clock_out,tblcourse.courseName,
                      tbllecturerooms.lectureRoomName,tblsemester.semesterName,tblsemester.semId,tblsem.semName,
                      tblstudents.firstName,tblstudents.lastName,tblstudents.email,tblstudents.admissionNumber
                      FROM tblattendance
                      INNER JOIN tblcourse ON tblcourse.Id = tblattendance.courseId
                      INNER JOIN tbllecturerooms ON tbllecturerooms.Id = tblattendance.lectureRoomId
                      INNER JOIN tblsemester ON tblsemester.Id = tblattendance.semesterId
                      INNER JOIN tblsem ON tblsem.Id = tblsemester.semId
                      INNER JOIN tblstudents ON tblstudents.admissionNumber = tblattendance.admissionNo
                      where tblattendance.dateTimeTaken = '$dateTaken'";
                      $rs = $conn->query($query);
                      $num = $rs->num_rows;
                      $sn=0;
                      $status="";
                    
                      if($num > 0)
                      { 
                        //select count of students from db
                        $sql="select count(*) as total from tblstudents";
                         $reslt=mysqli_query($conn,$sql);
                         $data=mysqli_fetch_assoc($reslt);
                          $diff=$data['total'] - $num;
                        //
                        if($data['total'] > $num)
                        {
                         //data visualization
                         $dataPoints = array(
                          array("x"=> 10, "y"=> $data['total'], "indexLabel"=> "Total"),
                          array("x"=> 20, "y"=> $num, "indexLabel"=> "Present"),
                          array("x"=> 30, "y"=> $diff, "indexLabel"=> "Absent"),
                        );

                         //

                          while ($rows = $rs->fetch_assoc())
                          {
                              if($rows['status'] == '1'){$status = "Present"; $colour="#00FF00";}else{$status = "Absent";$colour="#FF0000";}
                             $sn = $sn + 1;
                            echo"
                              <tr>
                                <td>".$sn."</td>
                                <td>".$rows['clock_in']."</td>
                                <td>".$rows['clock_out']."</td>
                                 <td>".$rows['firstName']."</td>
                                <td>".$rows['lastName']."</td>
                                <td>".$rows['email']."</td>
                                <td>".$rows['admissionNumber']."</td>
                                <td>".$rows['courseName']."</td>
                                <td>".$rows['lectureRoomName']."</td>
                                <td>".$rows['semesterName']."</td>
                                <td>".$rows['semName']."</td>
                                <td style='background-color:".$colour."'>".$status."</td>
                                <td>".$rows['dateTimeTaken']."</td>
                              </tr>";
                          }
                          $qry ="SELECT tblstudents.firstName,tblstudents.lastName,tblstudents.email,tblstudents.admissionNumber,
                          tblcourse.courseName,tbllecturerooms.lectureRoomName FROM tblstudents
                                                INNER JOIN tblcourse ON tblcourse.Id = tblstudents.courseId
                                                INNER JOIN tbllecturerooms ON tbllecturerooms.Id = tblstudents.lectureRoomId                                  
                                                   LEFT JOIN tblattendance ON tblattendance.admissionNo = tblstudents.admissionNumber
                                                WHERE admissionNo is NULL";
                                                $res = $conn->query($qry);
                                                $num = $res->num_rows;
                                                $asn=$sn;
                                                $status="Absent";
                                                if($num > 0)
                                                { 
                                                  while ($rows = $res->fetch_assoc())
                                                    {
                                                      error_reporting(1);
                                                       $asn = $asn + 1;
                                                      echo"
                                                        <tr>
                                                          <td>".$asn."</td>
                                                          <td>null</td>
                                                          <td>null</td>
                                                           <td>".$rows['firstName']."</td>
                                                          <td>".$rows['lastName']."</td>
                                                          <td>".$rows['email']."</td>
                                                          <td>".$rows['admissionNumber']."</td>
                                                          <td>".$rows['courseName']."</td>
                                                          <td>".$rows['lectureRoomName']."</td>
                                                          <td>2023/2024</td>
                                                          <td>First</td>
                                                          <td style='background-color:red;color:white'>Absent</td>
                                                          <td>00:00</td>
                                                        </tr>";
                                                    }
                                                }

                        }else{
                        while ($rows = $rs->fetch_assoc())
                          {
                              if($rows['status'] == '1'){$status = "Present"; $colour="#00FF00";}else{$status = "Absent";$colour="#FF0000";}
                             $sn = $sn + 1;
                            echo"
                              <tr>
                                <td>".$sn."</td>
                                <td>".$rows['clock_in']."</td>
                                <td>".$rows['clock_out']."</td>
                                 <td>".$rows['firstName']."</td>
                                <td>".$rows['lastName']."</td>
                                <td>".$rows['email']."</td>
                                <td>".$rows['admissionNumber']."</td>
                                <td>".$rows['courseName']."</td>
                                <td>".$rows['lectureRoomName']."</td>
                                <td>".$rows['semesterName']."</td>
                                <td>".$rows['semName']."</td>
                                <td style='background-color:".$colour."'>".$status."</td>
                                <td>".$rows['dateTimeTaken']."</td>
                              </tr>";
                          }
                       }
                      }
                       else if($num == 0){
                        $qry ="SELECT tblstudents.firstName,tblstudents.lastName,tblstudents.email,tblstudents.admissionNumber,
                        tblcourse.courseName,tbllecturerooms.lectureRoomName FROM tblstudents 
                                              INNER JOIN tblcourse ON tblcourse.Id = tblstudents.courseId
                                              INNER JOIN tbllecturerooms ON tbllecturerooms.Id = tblstudents.lectureRoomId                                  
                                              ";
                                              $res = $conn->query($qry);
                                              $num = $res->num_rows;
                                              $sn=0;
                                              $status="Absent";
                                              if($num > 0)
                                              { 
                                                while ($rows = $res->fetch_assoc())
                                                  {
                                                    error_reporting(1);
                                                     $sn = $sn + 1;
                                                    echo"
                                                      <tr>
                                                        <td>".$sn."</td>
                                                        <td>null</td>
                                                        <td>null</td>
                                                         <td>".$rows['firstName']."</td>
                                                        <td>".$rows['lastName']."</td>
                                                        <td>".$rows['email']."</td>
                                                        <td>".$rows['admissionNumber']."</td>
                                                        <td>".$rows['courseName']."</td>
                                                        <td>".$rows['lectureRoomName']."</td>
                                                        <td>2023/2024</td>
                                                        <td>First</td>
                                                        <td style='background-color:red;color:white'>Absent</td>
                                                        <td>00:00</td>
                                                      </tr>";
                                                  }
                                              }
                        
                        
                      }
                      else
                      {
                           echo   
                           "<div class='alert alert-danger' role='alert'>
                            No Record Found!
                            </div>";
                      }
                    }
                  } else
                  {
                       echo   
                       "<div class='alert alert-warning' role='alert'>
                        Select a valid date
                        </div>";
                  }
                      ?>
                    </tbody>
                   
                  </table>
                   </span>

                </div>
              </div>
            </div>
            </div>
          </div>
        

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
  <script>
window.onload = function () {
 
var chart = new CanvasJS.Chart("chartContainer", {
	animationEnabled: true,
	exportEnabled: false,
	theme: "light1", // "light1", "light2", "dark1", "dark2"
	title:{
		text: ""
	},
	axisY:{
		includeZero: true
	},
	data: [{
		type: "column", //change type to bar, line, area, pie, etc
		//indexLabel: "{y}", //Shows y value on all Data Points
		indexLabelFontColor: "#5A5757",
		indexLabelPlacement: "outside",   
		dataPoints: <?php echo json_encode($dataPoints, JSON_NUMERIC_CHECK); ?>
	}]
});
chart.render();
 
}

</script>
 
  <script type="text/javascript">     
    function PrintDiv() {    
      //window.print();
      var mywindow = window.open('', 'new div', 'height=800,width=600');
      mywindow.document.write('<html><head><title></title>');
      mywindow.document.write('<link rel="stylesheet" href="css/ruang-admin.css" type="text/css" />');
      mywindow.document.write('</head><u><h3>Course Attendance</h3></u><body >');
      mywindow.document.write('<html><body onload="window.print()">' + divToPrint.innerHTML + '</html>');
      mywindow.document.write('</body></html>');
      mywindow.document.close();
    ;

        //var divToPrint = document.getElementById('divToPrint');
        //var popupWin = window.open('', '_blank', 'width=800,height=800');
      //  popupWin.document.open();
      //  popupWin.document.write('<html><body onload="window.print()">' + divToPrint.innerHTML + '</html>');
      //   popupWin.document.close();


            }
        
 </script>
 

</body>

</html>