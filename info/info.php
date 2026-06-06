<?php
session_start();
// hide all error
error_reporting(0);
if (!isset($_SESSION["mikhmon"])) {
    header("Location:../admin.php?id=login");
}?>
<div class="row">
    <div class="col-8">
        <div class="card box-bordered">
            <div class="card-header">
                <h3><i class="fa fa-question-circle "></i> Info </h3>
            </div>
        </div>
        <div class="card box-bordered">
            <div class="card-body" style="font-size:20px;font-family:times;">
				<br>&nbsp&nbsp <i class="fa fa-calendar-check-o fa-2x" aria-hidden="true"></i> <span style="font-size:36px;"> &nbsp<?=explode("|",$info)[0]?> </span>
			</div>
		</div>
		<div class="card-body">
			<center><b style="font-size:20px;font-family:times;"><?=explode("|",$info)[1]?></b></center><hr>
			<?=explode("|",$info)[2]?><hr>
		</div>
	</div>
</div>
