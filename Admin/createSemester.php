
<?php 
error_reporting(0);
include '../Includes/dbcon.php';
include '../Includes/session.php';

//------------------------SAVE--------------------------------------------------

if(isset($_POST['save'])){
    
    $semesterName=$_POST['semesterName'];
    $semId=$_POST['semId'];
    $dateCreated = date("Y-m-d");
   
    $query=mysqli_query($conn,"select * from tblsemester where semesterName ='$semesterName' and semId = '$semId'");
    $ret=mysqli_fetch_array($query);

    if($ret > 0){ 

        $statusMsg = "<div class='alert alert-danger' style='margin-right:700px;'>This Semester and Year Already Exists!</div>";
    }
    else{

        $query=mysqli_query($conn,"insert into tblsemester(semesterName,semId,isActive,dateCreated) value('$semesterName','$semId','0','$dateCreated')");

    if ($query) {
        
        $statusMsg = "<div class='alert alert-success'  style='margin-right:700px;'>Created Successfully!</div>";
    }
    else
    {
         $statusMsg = "<div class='alert alert-danger' style='margin-right:700px;'>An error Occurred!</div>";
    }
  }
}

//---------------------------------------EDIT-------------------------------------------------------------






//--------------------EDIT------------------------------------------------------------

 if (isset($_GET['Id']) && isset($_GET['action']) && $_GET['action'] == "edit")
	{
        $Id= $_GET['Id'];

        $query=mysqli_query($conn,"select * from tblsemester where Id ='$Id'");
        $row=mysqli_fetch_array($query);

        //------------UPDATE-----------------------------

        if(isset($_POST['update'])){
    
             $semesterName=$_POST['semesterName'];
    $semId=$_POST['semId'];
    $dateCreated = date("Y-m-d");
        
            $query=mysqli_query($conn,"update tblsemester set semesterName='$semesterName',semId='$semId',isActive='0' where Id='$Id'");

            if ($query) {
                
                echo "<script type = \"text/javascript\">
                window.location = (\"createSemester.php\")
                </script>"; 
            }
            else
            {
                $statusMsg = "<div class='alert alert-danger' style='margin-right:700px;'>An error Occurred!</div>";
            }
        }
    }


//--------------------------------DELETE------------------------------------------------------------------

  if (isset($_GET['Id']) && isset($_GET['action']) && $_GET['action'] == "delete")
	{
        $Id= $_GET['Id'];

        $query = mysqli_query($conn,"DELETE FROM tblsemester WHERE Id='$Id'");

        if ($query == TRUE) {

                echo "<script type = \"text/javascript\">
                window.location = (\"createSemester.php\")
                </script>";  
        }
        else{

            $statusMsg = "<div class='alert alert-danger' style='margin-right:700px;'>An error Occurred!</div>"; 
         }
      
  }


  //--------------------------------ACTIVATE------------------------------------------------------------------

  if (isset($_GET['Id']) && isset($_GET['action']) && $_GET['action'] == "activate")
	{
        $Id= $_GET['Id'];

        $query=mysqli_query($conn,"update tblsemester set isActive='0' where isActive='1'");

            if ($query) {
                
                $que=mysqli_query($conn,"update tblsemester set isActive='1' where Id='$Id'");

                if ($que) {
                    
                    echo "<script type = \"text/javascript\">
                    window.location = (\"createSemester.php\")
                    </script>";  
                }
                else
                {
                    $statusMsg = "<div class='alert alert-danger' style='margin-right:700px;'>An error Occurred!</div>";
                }
            }
            else
            {
                $statusMsg = "<div class='alert alert-danger' style='margin-right:700px;'>An error Occurred!</div>";
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
<?php include 'includes/title.php';?>
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
            <h1 class="h3 mb-0 text-gray-800">Create Academic Year and Semester</h1>
            <ol class="breadcrumb">
              <li class="breadcrumb-item"><a href="./">Home</a></li>
              <li class="breadcrumb-item active" aria-current="page">Create Academic Year and Semester<</li>
            </ol>
          </div>

          <div class="row">
            <div class="col-lg-12">
              <!-- Form Basic -->
              <div class="card mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                  <h6 class="m-0 font-weight-bold text-primary">Create Academic Year and Semester</h6>
                    <?php echo $statusMsg; ?>
                </div>
                <div class="card-body">
                  <form method="post">
                    <div class="form-group row mb-3">
                        <div class="col-xl-6">
                            <label class="form-control-label">Academic Year Name<span class="text-danger ml-2">*</span></label>
                      <input type="text" class="form-control" name="semesterName" value="<?php echo $row['semesterName'];?>" id="exampleInputFirstName" placeholder="Academic Year">
                        </div>
                        <div class="col-xl-6">
                            <label class="form-control-label">Semester<span class="text-danger ml-2">*</span></label>
                              <?php
                        $qry= "SELECT * FROM tblsem ORDER BY semName ASC";
                        $result = $conn->query($qry);
                        $num = $result->num_rows;		
                        if ($num > 0){
                          echo ' <select required name="semId" class="form-control mb-3">';
                          echo'<option value="">--Select Year--</option>';
                          while ($rows = $result->fetch_assoc()){
                          echo'<option value="'.$rows['Id'].'" >'.$rows['semName'].'</option>';
                              }
                                  echo '</select>';
                              }
                            ?>  
                        </div>
                    </div>
                      <?php
                    if (isset($Id))
                    {
                    ?>
                    <button type="submit" name="update" class="btn btn-warning">Update</button>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    <?php
                    } else {           
                    ?>
                    <button type="submit" name="save" class="btn btn-primary">Save</button>
                    <?php
                    }         
                    ?>
                  </form>
                </div>
              </div>

              <!-- Input Group -->
                 <div class="row">
              <div class="col-lg-12">
              <div class="card mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                  <h6 class="m-0 font-weight-bold text-primary">All Academic Years and Semesters </h6>
                  <h6 class="m-0 font-weight-bold text-danger">Note: <i>Click on the check icon to make Academic Year and Semester active!</i></h6>
                </div>
                <div class="table-responsive p-3">
                  <table class="table align-items-center table-flush table-hover" id="dataTableHover">
                    <thead class="thead-light">
                      <tr>
                        <th>#</th>
                        <th>Academic Year</th>
                        <th>Semester</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Activate</th>
                        <th>Edit</th>
                        <th>Delete</th>
                      </tr>
                    </thead>
                  
                    <tbody>

                  <?php
                      $query = "SELECT tblsemester.Id,tblsemester.semesterName,tblsemester.isActive,tblsemester.dateCreated,
                      tblsem.semName
                      FROM tblsemester
                      INNER JOIN tblsem ON tblsem.Id = tblsemester.semId";
                      $rs = $conn->query($query);
                      $num = $rs->num_rows;
                      $sn=0;
                      if($num > 0)
                      { 
                        while ($rows = $rs->fetch_assoc())
                          {
                            if($rows['isActive'] == '1'){$status = "Active";}else{$status = "InActive";}
                             $sn = $sn + 1;
                            echo"
                              <tr>
                                <td>".$sn."</td>
                                <td>".$rows['semesterName']."</td>
                                <td>".$rows['semName']."</td>
                                <td>".$status."</td>
                                <td>".$rows['dateCreated']."</td>
                                 <td><a href='?action=activate&Id=".$rows['Id']."'><i class='fas fa-fw fa-check'></i></a></td>
                                <td><a href='?action=edit&Id=".$rows['Id']."'><i class='fas fa-fw fa-edit'></i></a></td>
                                <td><a href='?action=delete&Id=".$rows['Id']."'><i class='fas fa-fw fa-trash'></i></a></td>
                              </tr>";
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

  <!-- Page level custom scripts -->
  <script>
    $(document).ready(function () {
      $('#dataTable').DataTable(); // ID From dataTable 
      $('#dataTableHover').DataTable(); // ID From dataTable with Hover
    });
  </script>
</body>

</html>