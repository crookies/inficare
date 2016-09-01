<page 
   orientation=landscape
   format="A4" 
	backcolor="#FEFEFE" 
	backimg="classes/reports_layout/bas_page.png" 
	backimgx="center"
	backimgy="bottom" 
	backimgw="100%" 
	backtop="35px" 
	backbottom="10px"
	footer="date;heure;page" 
	style="font-size: 12pt">
<page_header>
<table cellspacing="0"
	style="width: 100%; text-align: center; font-size: 14px">
	<tr>
		<td style="text-align:left;width: 25%;"><img "src="classes/reports_layout/inftxt-ss.png" alt="Logo"></td>
	   <td style="width: 50%;">Tarification: <?php echo $data['Date'] ?>   (Page [[page_cu]]/[[page_nb]])</td>
		<td style="text-align:right;width: 25%; color: #444444;"><img "src="classes/reports_layout/infsq-ss.png" alt="Logo">
		</td>
	</tr>
</table>
</page_header>

<style type="text/css">
<!--

/* commentaire dans un css */
/* td.listr   { padding-left:5px;border-left: solid 1px #000000; border-right: solid 1px #000000; }*/

tr.list td   {padding-left:5px;border-left: solid 1px #000000; }
tr.lodd td  {background: #F7F7F7; }

-->
</style>

<?php
$dayPerPage = 11;
$dcwn = 7;
$fcwn = 100 - ($dayPerPage * $dcwn); 
$dcw = strval($dcwn);
$fcw = strval($fcwn);

$firstDay = 1;

while ($firstDay<=31)
{
	if ($firstDay>1)
	{
?>
	<page pageset="old">
<?php 
	}
?>	 

<table cellspacing="0" style="width:100%; text-align: left; font-size: 11pt; margin-top:0px;border-top: 1px solid black; ">

	<tr style="height:40px;background: #E1E1E1;text-align: center;">
		<td style="width:<?php echo $fcw?>%;">Patient</td>
<?php
for ($i=0; $i<$dayPerPage; $i++)
{
   $day=$firstDay + $i;
   if ($day>31)
   	break;
?>		
		<td style="width:<?php echo $dcw?>%"><?php echo $day ?></td>
<?php 
}
?>		
	</tr>
	
<?php 
$even = false;
foreach($data['patients'] as $patientId => $patientName)
{
	$even = !$even;
	if ($even)
	{
?>	
	<tr class="list">
<?php
	}
	else
	{ 
?>		
	<tr class="list" style="background: #E1E1E1">
<?php 	
	}
?>
	<td style="width:<?php echo $fcw?>%;"><?php echo $patientName ?></td>
	<?php 
	for ($i=0; $i<$dayPerPage; $i++)
	{
	   $day=$firstDay + $i;
	   if ($day>31)
	   	break;
	?>		
	   <td style="width:<?php echo $dcw?>%">
	   <?php 
	   if (isset($data['List'][(int)$day][(int)$patientId]))
	   {
	      echo $data['List'][(int)$day][(int)$patientId]['tblText'];    	
	   }
	   else
	   {
	   	?>
	   	&nbsp;
	   	<?php 
	   }
	   ?>
	  	</td>
<?php
	}
?>
	</tr>
<?php
}	
$firstDay = $day+1;
?>
	</table>
	</page>
<?php 
}
?>  	


