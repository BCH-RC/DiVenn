<head>        
	<script src="js/jscolor.js"></script>
	<script src="js/jquery.min.js"></script>
	<script type="text/javascript" src="js/d3.v4.min.js"> </script>      <!--d3 need-->
	
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
	<!--<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script> -->
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
</head>
<?php 
   session_start(); 
    $message='';
   	$_SESSION['mydata']=array();       // uploaded filed data
	$_SESSION['mycolor']=array();      // customer defined color
	$_SESSION['exp_name']=array();     //customer defined experiment name    
	
if ($_SERVER['REQUEST_METHOD']== "POST") 
{
	//echo var_dump($_POST);
    if(isset($_POST["submit"])&&isset($_POST["exp_num"]))
	{ 	
		$_SESSION['filenum']=$_POST["exp_num"];     //echo $filenum;
		$_SESSION['uploadtype']=$_POST['uploadtype'];
		
		if($_SESSION['uploadtype']=="UploadFile")
		{
			for($n=0;$n<$_SESSION['filenum'];$n++)
			{
				switch($_FILES["file"]["error"][$n])
				{
					case 0:
						$message=$_FILES["file"]["name"][$n] . "was uploaded successfully.";
						break;
					case 2:
						$message=$_FILES["file"]["name"][$n]."is too big to upload.";
						break;
					case 4:
						$message="No file selected.";
						break;
					default:
						$message="Sorry, there was a problem uploading." . $_FILES["file"]["name"][$n];
						break;
					
				}
				
				if($_FILES["file"]["error"][$n]>0)
				{
					echo $message."<br/>";
		
				}
				else
				{       
					//echo "Upload:" . $_FILES["file"]["name"][$n] . "<br/>";
					//echo "Type:" . $_FILES["file"]["type"][$n] . "<br/>";
					//echo "Size:".($_FILES["file"]["size"][$n]/1024) . "Kb<br/>";
					//echo "Temp File:" .$_FILES["file"]["tmp_name"][$n]."<br/>";
					//echo "filename:".$_FILES["file"]["name"][$n] ."<br/>";
					$myfile[$n]= fopen($_FILES["file"]["tmp_name"][$n], "r") or die("Unable to open file!"); 		                    //save to session
					$filesize[$n]=$_FILES["file"]["tmp_name"][$n];     //filesize
		
					$mydata[$n]=fread($myfile[$n],filesize($filesize[$n]));   //read file to string	
					//echo "this is file".$_FILES["file"]["name"][$n]."'s comtent"."<br/>";
				
					$_SESSION['mydata'][$n]=test_input($mydata[$n]);
					
					//echo $_POST["colorselector".$n];
					//echo "<br/>";
					//echo $_POST['expname'][$n];
					//echo "<br/>";
					$_SESSION['mycolor'][$n]= '#'.$_POST["colorselector".$n];
					$_SESSION['exp_name'][$n]=$_POST['expname'][$n];
					fclose($myfile[$n]);
					header('location: showdata.php'); 
					
				}
		
			}
		} 
		elseif($_SESSION['uploadtype']=="CustomData")
		{
			for($n=0;$n<$_SESSION['filenum'];$n++)
			{
				$_SESSION['mydata'][$n]=test_input($_POST['txtquery'][$n]);
				$_SESSION['mycolor'][$n]= $_POST["colorselector".$n];
				$_SESSION['exp_name'][$n]=$_POST['expname'][$n];
				header('location: showdata.php'); 
			}
		}
	 }
    
     elseif(isset($_POST["submit"]))
	 { 
       echo "<script type='text/javascript'> alert('Please select your data type in the radio button!'); location.href = 'index.php'; </script>"; 
     } 
     else
	 { 
       echo "<script type='text/javascript'> alert('Please resubmit your input data'); location.href = 'index.php'; </script>"; 
     } 
} 
    
function test_input($data)
{
	$data=trim($data);
	$data=stripslashes($data);
	$data = str_replace('/', '',$data);
	$data=htmlspecialchars($data);
	return $data;
} 
	
 ?> 
 
<div class="panel panel-default">
	<div class = "panel-body">
		<h3>About Divenn</h3>
		<p>Venn diagram is widely used to compare gene lists between multiple experiments. However, the limitation of it is also obvious.</p>
		<ul>
			<li>gene ID cannot be linked to gene functions. No database can be integrated</li>
			<li>Gene expression cannot be displayed in the graph.</li> 
			<li>Common genes in venn diagram which are likely to be interesting genes cannot be extracted with gene expression value and gene function, etc</li>
		</ul>
		<p>We aim to provide a web-based tool DiVenn to help biologist visualize their gene list and also integrate pathway, GO and transcription factor database (still under development) to our tools. <br/>
			Researchers can not only compare and visualize gene lists, but also integrate biological knowledge from public database to the graph. This tool will be user-friendly and can handle large input data.
		</p>

		
		<form action="<?php echo htmlspecialchars($_SERVER[PHP_SELF]);?>" method="post" enctype="multipart/form-data">
			<div class="row">
				<div class="col-sm-3 form-group">
					<label> Species</label>
					<select name='species' class="form-control">    
						<option value='notselected' selected>&lt;please select&gt;</option>          
						<option value='Homo Sapiens'>Homo sapiens</option> 
						<option value='Gallus gallus'>Gallus gallus</option> 
						<option value='Arabidopsis thaliana'>Arabidopsis thaliana</option> 
						<option value='Medicago truncatula'>Medicago truncatula</option> 
						<option value='Oryza sativa'>Oryza sativa</option> 
					</select> 
				</div>
				<div class="col-sm-12 form-group">					
					<label>Sample</label>
					<button onclick=openNewWin("SampleData.txt") class="btn btn-default">Sample</button>
				</div>
				<div class="col-sm-12 form-group">
					<label>Chose upload type</label><br>
					<input type="radio" name="uploadtype" id="" value="UploadFile"  onclick='UpdateFileUploadContent()' checked>Upload file
					<input type="radio" name="uploadtype" value="CustomData" onchange='UpdateFileUploadContent()'>Custom Data
				</div>
				<div class="col-sm-2 form-group">
					<label>Experiment Number</label>
					<select id='exp_num' name='exp_num' onchange='UpdateFileUploadContent()' class="form-control">    
						<option value='0'  selected>&lt;please select&gt;</option>  
						<option value='1' >1</option>          
						<option value='2'>2</option> 
						<option value='3'>3</option> 
						<option value='4'>4</option> 
						<option value='5'>5</option> 
						<option value='6'>6</option> 
						<option value='7'>7</option> 
						<option value='8'>8</option> 
					</select>
				</div>
				<span id = "UploadData">
				</span>	
				
				<div class="col-sm-12 form-group">
					<input type="submit" name="submit" value="Upload Data" class="btn btn-success"> <br><br>
					<input type="date" id="datepicker" >	
				</div>		
			</div>	              
		</form>
	</div>
</div>

 	 

<style>
.hidden{
	display:none;
}


</style>
<script>
 // open a new window in the web browser
	window.onload = UpdateFileUploadContent();
 
	function openNewWin(url)  
	{  
    	window.open(url);  
	}  

//get today's date  
	document.getElementById('datepicker').value = new Date().toISOString().substring(0, 10);   // let datepicker default to show today's date
	
	
	function Uploadtypechange(t)
	{
		var n=document.getElementById('exp_num').value;
	
		UpdateFileUploadContent(n,t);	
	}	
	
	function UpdateFileUploadContent()
	{
		var selectedNumber = $("#exp_num").val();
		var uploadType = $("input[type='radio'][name='uploadtype']:checked").val();

		d3.select('#UploadData').html('');//clean the content
		
		$defaultColorValues = ["#a23388","#ccffff","#99cc33","#ff9900","#9966cc","#0099cc","#663300"];	
		for (i=0;i<selectedNumber;i++)
		{  
			var randomColor = $defaultColorValues[i]; //'#'+Math.floor(Math.random()*16777215).toString(16); //'#'+(Math.random()*0xFFFFFF<<0).toString(16);
				
			var divcontent = document.createElement('div');
			divcontent.setAttribute("class","col-sm-12 form-group");
				
			var jcolorinput=document.createElement('INPUT');
			jcolorinput.setAttribute('class','jscolor{hash:true}');
			jcolorinput.setAttribute('name','colorselector'+i);
			jcolorinput.setAttribute('value',randomColor);
			var jcolorpicker = new jscolor(jcolorinput);
			//jcolorpicker.fromString(randomColor);				
			divcontent.appendChild(jcolorinput);
				
			var experimentlabel=document.createElement('label');
			//experimentlabel.setAttribute('class','input-group-addon');
			var labeltext= document.createTextNode('Experiment'+i);
			experimentlabel.appendChild(labeltext);
			divcontent.appendChild(experimentlabel);
			
			var experimenthidden=document.createElement('input');
			experimenthidden.setAttribute('id','txt'+i);
			experimenthidden.setAttribute('name','expname[]');
			experimenthidden.setAttribute('value','Exp'+i);
			experimenthidden.setAttribute('class','hidden');
			divcontent.appendChild(experimenthidden);
			
			if(uploadType=="UploadFile")
			{	
				var fileselector = document.createElement('input');				
				fileselector.setAttribute('type','file');
				fileselector.setAttribute('name','file[]');
				fileselector.setAttribute('id','file'+i);
				divcontent.appendChild(fileselector);
			}
			else{
				var fileselector = document.createElement('textarea');				
				fileselector.setAttribute('rows','5');
				fileselector.setAttribute('id','context'+i);
				divcontent.appendChild(fileselector);
			}
			$('#UploadData').append(divcontent);
			//$('#UploadData').append(newHtml);		
		}		
	}

	/* function colorChange(i) {
		
		var mycolor = document.getElementById(i).value;

		var a =document.getElementById(i).value = mycolor;
		alert(a);
		
		
		
	}
  
  */
/* 
$(document).ready(function() {

    function colorChange()
    {
         var mycolor = $('#colorselector1').val();

        return mycolor;
    }


    $('#colorselector1').change(function(e) {                    

        var mycolor = colorChange();
	$('#colorselector1').val(mycolor);
	alert(mycolor);



    });

}); */

</script> 
 
 
   
   
   

   
   
   
   


 
 