<?php 
error_reporting(0);
include '../Includes/dbcon.php';
include '../Includes/session.php';

?>
        <table border="1">
        <thead>
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
            <th>Academic Year</th>
            <th>Semester</th>
            <th>Status</th>
            <th>Date</th>
            </tr>
        </thead>

<?php 
$filename="Attendance list";
$dateTaken = date("Y-m-d");

$cnt=1;			
$ret = mysqli_query($conn,"SELECT tblattendance.Id,tblattendance.status,tblattendance.dateTimeTaken,tblattendance.clock_in,tblattendance.clock_out,tblcourse.courseName,
        tbllecturerooms.lectureRoomName,tblsemester.semesterName,tblsemester.semId,tblsem.semName,
        tblstudents.firstName,tblstudents.lastName,tblstudents.email,tblstudents.admissionNumber
        FROM tblattendance
        INNER JOIN tblcourse ON tblcourse.Id = tblattendance.courseId
        INNER JOIN tbllecturerooms ON tbllecturerooms.Id = tblattendance.lectureRoomId
        INNER JOIN tblsemester ON tblsemester.Id = tblattendance.semesterId
        INNER JOIN tblsem ON tblsem.Id = tblsemester.semId
        INNER JOIN tblstudents ON tblstudents.admissionNumber = tblattendance.admissionNo
        where tblattendance.dateTimeTaken = '$dateTaken'");

if(mysqli_num_rows($ret) > 0 )
{
while ($row=mysqli_fetch_array($ret)) 
{ 
    
    if($row['status'] == '1'){$status = "Present"; $colour="#00FF00";}else{$status = "Absent";$colour="#FF0000";}

echo '  
<tr>  
<td>'.$cnt.'</td> 
<td>'.$clockIn=$row['clock_in'].'</td>
 <td>'.$clockOut=$row['clock_out'].'</td>
<td>'.$firstName= $row['firstName'].'</td> 
<td>'.$lastName= $row['lastName'].'</td> 
<td>'.$email= $row['email'].'</td> 
<td>'.$admissionNumber= $row['admissionNumber'].'</td> 
<td>'.$courseName= $row['courseName'].'</td> 
<td>'.$lectureRoomName=$row['lectureRoomName'].'</td>	
<td>'.$semesterName=$row['semesterName'].'</td>	 
<td>'.$semName=$row['semName'].'</td>	
<td>'.$status=$status.'</td>	 	
<td>'.$dateTimeTaken=$row['dateTimeTaken'].'</td>	 					
</tr>  
';
header("Content-type: application/octet-stream");
header("Content-Disposition: attachment; filename=".$filename."-report.xls");
header("Pragma: no-cache");
header("Expires: 0");
			$cnt++;
			}
	}
?>
</table>