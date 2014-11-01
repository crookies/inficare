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
	   <td style="width: 50%;">Tournée: <?php echo $data['Date'] ?></td>
		<td style="text-align:right;width: 25%; color: #444444;"><img "src="classes/reports_layout/infsq-ss.png" alt="Logo">
		</td>
	</tr>
</table>
</page_header>

<style type="text/css">
<!--

/* commentaire dans un css */
/* td.listr   { padding-left:5px;border-left: solid 1px #000000; border-right: solid 1px #000000; }*/

tr.list td   {white-Space: Normal;padding-left:5px;border-left: solid 1px #000000; }
tr.lodd td  {white-Space: Normal;background: #F7F7F7; }
.list div {white-Space: Normal}

-->
</style>
<table cellspacing="0" style="width:100%; text-align: left; font-size: 14px;border-top: 1px solid black;">
	<tr>
	   <td style="width:100%;background: #F1F1F1">Remarques journée</td>
	</tr>
</table>
<div style="width:100%; text-align: left; font-size: 14px"><?php echo $data['DayComment'] ?></div>
 	
<?php
$line=0;
$list = $data['List'];
$i=0;
while ($i < count($list)) 
{
	$entry = $list[$i];

if (($entry['rectype']=='20') && ($entry['count']>0))
{
	//Skip this group if no nurse defined
	if (trim($entry['nursename'])==='')
	{
		$count = $entry['count'];
		$even = false;
		for ($j=0; $j<$count; $j++)
		{
			$i++;
		}
		continue;
	}
?>
<bookmark title="<?php echo $entry['namev'] ?>" level="0"></bookmark>
<table cellspacing="0" style="width:100%; text-align: left; font-size: 16pt; font-weight:bold; margin-top:8px;">
	<tr>
		<td style="width:100%;background: #F0F0F0"><?php echo $entry['nursename'] ?>  <span style="margin-left:20px;font-weight:normal;">(<?php echo $entry['namev'] ?>)</span><span style="margin-left:20px;font-size:11pt; font-weight:normal;"><?php echo $entry['visitinfo'] ?></span></td>
	</tr>
</table>
		
<table cellspacing="0" style="width:100%; text-align: left; font-size: 11pt; margin-top:0px;border-top: 1px solid black; ">

	<tr style="height:40px;background: #E1E1E1;text-align: center;">
		<td style="width:21%;">Info jour</td>
		<td style="width:16%;border-left: solid 1px #000000;font-weight:bold;">Noms</td>
		<td style="width:16%;font-size: 8pt">Adresses</td>
		<td style="width:10%">Tél</td>
		<td style="width:21%">Soins</td>
		<td style="width:16%;border-right: solid 1px #000000;">Détails</td>
	</tr>
		
	
	
<?php 
$count = $entry['count'];
$even = false;
for ($j=0; $j<$count; $j++)
{
	$i++;
	$entry = $list[$i];
	if ($entry['rectype']=='30')
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
	<tr class="lodd list">
<?php 	
		}
		//		7h15 kinékùghkgfd mùhkgfmùhkgfmlùhkgmh<div>gfhgfklmhjfgmlkùhjgf</div><div>gfhgfhfgmlùhkfg</div><div>fdghkfglhmgfjkhgfd</div>
//		<div style="margin:0px;padding:0px;word-wrap:break-word;">
//		7h15 kinékùghkgfd
//		</div>
		
?>
		<td style="width:21%"><?php echo str_replace("\n", "<br>", $entry['visitinfo']) ?></td>
		<td style="width:16%;border-left: solid 1px #000000;font-weight:bold;"><?php echo $entry['namev'] ?></td>
		<td style="width:16%;font-size: 8pt"><?php echo $entry['address'] ?></td>
		<td style="width:10%"><?php echo $entry['tel'] ?></td>
		<td style="width:21%"><?php echo $entry['patientcare'] ?></td>
		<td style="width:16%;font-size:9pt;border-right: solid 1px #000000;"><?php echo $entry['patientinfo'] ?></td>
	</tr>

<?php 
		//endif ($entry['rectype']=='30')
	}
}	
?>
	
	
	</table>
<br>
<?php 
}  //rectype == 20
$i++;
?>

<?php 
}
?>

</page>
