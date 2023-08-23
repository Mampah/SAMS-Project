<?php

include '../Includes/dbcon.php';

    $cid = intval($_GET['cid']);//

        $queryss=mysqli_query($conn,"select * from tbllecturerooms where courseId=".$cid."");                        
        $countt = mysqli_num_rows($queryss);

        echo '
        <select required name="lectureRoomId" class="form-control mb-3">';
        echo'<option value="">--Select Lecture Room--</option>';
        while ($row = mysqli_fetch_array($queryss)) {
        echo'<option value="'.$row['Id'].'" >'.$row['lectureRoomName'].'</option>';
        }
        echo '</select>';
?>

