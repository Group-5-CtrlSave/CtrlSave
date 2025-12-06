<?php
if (isset($_SESSION['successtag'])){
    echo '<div class="alert alert-success" role="alert" id="myAlert">'.$_SESSION['successtag'].'</div>';
    unset($_SESSION['successtag']);

  
}
?>