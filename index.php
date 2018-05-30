        <script type="text/javascript" src="https://d3js.org/d3.v4.min.js"> </script>
	<script src="js/jscolor.js"></script>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
	
<?php 
   session_start(); 
    $message='';
   	$_SESSION['mydata']=array();       // uploaded filed data
	$_SESSION['mycolor']=array();      // customer defined color
	$_SESSION['exp_name']=array();     //customer defined experiment name 
   
	
if ($_SERVER['REQUEST_METHOD']== "POST") 
{ 
     if(isset($_POST["submit"])&&isset($_POST["exp_num"]))
	 { 	
		$_SESSION['filenum']=$_POST["exp_num"];     //echo $filenum;
		$_SESSION['uploadtype']=$_POST['uploadtype'];
		
		if($_SESSION['uploadtype']=="UploadFile")
		{
			for($n=0;$n<$_SESSION['filenum'];$n++)
			{	 switch($_FILES["file"]["error"][$n])
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
					
					//echo $_SESSION['mydata'][$n];
					//echo "<br/>";
					$_SESSION['mycolor'][$n]= $_POST["colorselector".$n];
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

 

  
 <div>
  <h2>DIVenn</h2>
   <p> Can accept a gene data that will be used to display the Force Directed Graph. </p>  
  <br/>
</div> 

 <form action="<?php echo htmlspecialchars($_SERVER[PHP_SELF]);?>" method="post" enctype="multipart/form-data">
	<span> Species: <select name='species'>    
			<option value='notselected' selected>&lt;please select&gt;</option>          
			<option value='Homo Sapiens'>Homo sapiens</option> 
			<option value='Gallus gallus'>Gallus gallus</option> 
			</select>
	</span> 
	 <span>Sample:  <button onclick=openNewWin("SampleData.txt")>Sample</button>  
	</span></br></br> 
	 <span>Chose upload type:<br>
			<input type="radio" name="uploadtype" value="UploadFile"  onclick='Uploadtypechange(uploadtype.value)' checked><b>upload file:</b>
			<input type="radio" name="uploadtype" value="CustomData" onchange='Uploadtypechange(uploadtype.value)'><b>Custom Data:</b>
	</span></br></br> 
	<span> Experiment: 
		<select id='exp_num' name='exp_num' onchange='addfile(exp_num.value,uploadtype.value)' >    
			<option value='1' >1</option>          
			<option value='2' selected>2</option> 
			<option value='3'>3</option> 
			<option value='4'>4</option> 
			<option value='5'>5</option> 
			<option value='6'>6</option> 
			<option value='7'>7</option> 
			<option value='8'>8</option> 
		</select>
	</span> 
	<br><br>
	
	         <div>
			
			<span id="autofile0">
				
			</span>
			<input class="jscolor{hash:true} hidden" value="99cc33" name="colorselector0" id="colorselector0" style="width:25px" >		
			<br>
		</div>
		<div >
			
			<span id="autofile1">
				
			</span>
			<input class="jscolor{hash:true} hidden " value="ff9900" name="colorselector1" id="colorselector1" style="width:25px">		
			<br>
		</div>
		<div >
			
			<span id="autofile2">
				
			</span>
			<input class="jscolor{hash:true} hidden" value="9966cc" name="colorselector2" id="colorselector2" style="width:25px">		
			<br>
		</div>
		<div>
			
			<span id="autofile3">
				
			</span>
			<input class="jscolor{hash:true} hidden" value="0099cc" name="colorselector3" id="colorselector3" style="width:25px">		
			<br>
		</div>
		<div>
			
			<span id="autofile4">
				
			</span>
			<input class="jscolor{hash:true} hidden" value="663300" name="colorselector4" id="colorselector4" style="width:25px">		
			<br>
		</div>
		<div>
			
			<span id="autofile5">
				
			</span>
			<input class="jscolor{hash:true} hidden" value="cc99cc" name="colorselector5" id="colorselector5" style="width:25px">		
			<br>
		</div>
		<div>
			
			<span id="autofile6">
				
			</span>
			<input class="jscolor{hash:true} hidden" value="ccff99" name="colorselector6" id="colorselector6" style="width:25px">		
			<br>
		</div>	
		<div>
			
			<span id="autofile7">
			
			</span>
			<input class="jscolor{hash:true} hidden" value="ccffff" name="colorselector7" id="colorselector7" style="width:25px">		
			<br>
		</div>	
     	
      <input type="submit" name="submit" value="UploadData"> <br><br>
      <input type="date" id="datepicker" >     
  </form>	 

<style>
.hidden{
	display:none;
}

</style>
  <script>
 // open a new window in the web browser
 window.onload=addfile(2,'UploadFile');
 
function openNewWin(url)  
{  
    window.open(url);  
}  


//get today's date
  
   document.getElementById('datepicker').value = new Date().toISOString().substring(0, 10);   // let datepicker default to show today's date
	
	
function Uploadtypechange(t)
{
	var n=document.getElementById('exp_num').value;
	
	addfile(n,t);
	
}	
	
	function addfile(n,t)
	{
		
		if(t=="UploadFile")
		{
			for(i=0;i<=7;i++)
			{
				$("#colorselector"+i).addClass("hidden");
				d3.select('#autofile'+i).selectAll('input').remove();
				d3.select('#autofile'+i).selectAll('textarea').remove();
				
				//$("#file"+i).val("");
			}
			
			
			for (i=0;i<n;i++)
			{  
				d3.select('#autofile'+i).append('input')
					.attr('name','expname[]')
					.attr('id','txt'+i)
					.attr('type','text')
					.attr('value','EXP'+i)
					.style('width','50px')
					
					
				 d3.select('#autofile'+i).append('input')
					.attr('name','file[]')
					.attr('id','file'+i)
					.attr('type','file') 
				
				$("#colorselector"+i).removeClass("hidden");
				
			}
		}
		else if(t=="CustomData")
		{   
	       for(i=0;i<=7;i++)
			{
				$("#colorselector"+i).addClass("hidden");
				
				d3.select('#autofile'+i).selectAll('textarea').remove();
				d3.select('#autofile'+i).selectAll('input').remove();
				
				
			}
			
			
			for (i=0;i<n;i++)
			{  
				d3.select('#autofile'+i).append('input')
					.attr('name','expname[]')
					.attr('id','txt'+i)
					.attr('type','text')
					.attr('value','EXP'+i)
					.style('width','50px')
					
					
				 d3.select('#autofile'+i).append('textarea')
					.attr('name','txtquery[]')
					.attr('id','context'+i)
					.attr('cols','20')
					.attr('rows','5')
				
				$("#colorselector"+i).removeClass("hidden");
				
			}
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
 
 
   
   
   

   
   
   
   


 
 