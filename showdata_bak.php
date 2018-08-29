

<link rel="stylesheet" href="css/base.css">
<link rel="stylesheet" href="css/style.css">
<script src="js/md5.js"></script>
<script src="js/jscolor.js"></script>                                                                                         <!--color need-->
<!--script src="https://cdnjs.cloudflare.com/ajax/libs/d3/3.4.0/d3.min.js"--><!--/script-->  <!--contextMenu need-->
<script type="text/javascript" src="js/d3.v4.min.js"> </script>      <!--d3 need-->	
	

<?php
session_start(); 
$filenum=$_SESSION['filenum'];	
//echo $filenum;

for($n=0; $n<$filenum; $n++)
{	
	$list[$n]=$_SESSION['mydata'][$n];
	//echo $list[$n];
	//echo "<br/>" ;
	$im_list=explode("\r\n",$list[$n]);       //explode string by \n
	//print_r($im_list);
	foreach($im_list as $key=> $im_list)
	{
		if($key>=0)
		{
			$parts=explode("\t",$im_list);
			//print_r($parts);
			$objectLinks[$n][$key]=(object)array("id"=>$parts[0],"value"=>$parts[1]);
		}
	}	
} 
$objlinks= json_encode($objectLinks );
	//echo $objlinks;



if ($_SERVER['REQUEST_METHOD']== "POST") 
{ 
	for($n=0;$n<$filenum;$n++)
	{
		$_SESSION['mycolor'][$n]=$_POST['colorselector'.$n];   // when click the "changecolor" button reset the color value in session 
	
	}
}
// get color value from session    
   for($n=0;$n<8;$n++)
	{	
		$mycolors[$n]=$_SESSION['mycolor'][$n];          // get the customer defined node color 
		$expname[$n]=$_SESSION['exp_name'][$n];         //get the customer defined experiment name
	}
?>
 



<!--the table below is for move-left, move-right , move-up, move-down and reset button-->	
<table class="GG5EBMLCKI" style="position: absolute; left: 6px; top: 6px; z-index: 1000"> 

     <tbody> 
		<tr> 
			<td colspan="3" align="center">
				<img id="moveUp" src="image/up-arrow.png" width="12" height="12" class="gwt-Image" alt="move up" title="move up">
			</td> 
		</tr>
		<tr> 
			<td>
				<img id="moveLeft"  src="image/left-arrow.png"  width="12" height="12" class="gwt-Image" alt="move left" title="move left">
			</td> 
			<td>
				<img id="reset"   src="image/reset.png"   width="12" height="12" class="gwt-Image" alt="reset" title="reset">
			</td>
			<td>
				<img id="moveRight"   src="image/right-arrow.png"  width="12" height="12" class="gwt-Image" alt="move right" title="move right">
			</td> 
		</tr> 
		<tr> 
			<td colspan="3" align="center">
				<img id="moveDown" src="image/down-arrow.png" width="12" height="12" class="gwt-Image" alt="move down" title="move down">
			</td> 
		</tr> 
		
     </tbody> 
 </table> 
 <!--the table below is for change color button-->	
 <table class="changeColor" style="position: absolute; left: 6px; top:56px; z-index: 1000">
 <tr>
		<td align="left"><form action="<?php echo htmlspecialchars($_SERVER[PHP_SELF]);?>" method="post" enctype="multipart/form-data">

			<span id="colorchanger" > 
			
			</span>
			<button type="submit" name="changecolor" > change color</button>
		
		</form>
		</td>
 
		</tr>
  </table>
  
<!--the div below is SVG Container-->	  
<div class="container" >
		<section id="chart" >
		</section>
</div>


<style>
/* contextMenu css style */

.d3-context-menu {
	position: absolute;
	display: none;
	background-color: #f2f2f2;
	border-radius: 4px;
	font-family: Arial, sans-serif;
	font-size: 14px;
	min-width: 150px;
	border: 1px solid #d4d4d4;
	z-index:1200;
}

.d3-context-menu ul {
	list-style-type: none;
	margin: 4px 0px;
	padding: 0px;
	cursor: default;
}

.d3-context-menu ul li {
	padding: 4px 16px;
}

.d3-context-menu ul li:hover {
	background-color: #4677f8;
	color: #fefefe;
}
</style>

<script type="text/javascript">


var mylinks=<?php echo $objlinks; ?>;  // get the data 
var filenum=<?php echo $filenum;?>;   //get how many files the customer uploaded
var exp_name=<?php echo json_encode($expname);?>;   //get each name for experiment the customer uploaded
var mycolor=<?php echo json_encode($mycolors); ?>;  //get array value of color

//get links

var links=[];
for(var n=0;n<mylinks.length;n++)
{
	 for (var i=0;i<mylinks[n].length;i++)
		{
			links.push({
				'source': mylinks[n][i].id,
				'target': exp_name[n],
				'value': +mylinks[n][i].value                      //加号让string转换成数值 + change string to number
			})
			
		}
	
}
/*  for(var i=0;i<links.length;i++)
{
	alert(links[i].source);
	alert(links[i].target);
} */

//get nodes
var nodes=[];
nodes.push({'id':links[0].source,
			'group':links[0].value })

for(var i=0;i<links.length;i++)
{	
    
			
	
	for(j=0;j<nodes.length;j++)
	{ 
		if(links[i].source==nodes[j].id)              //判断所有的source是否存在wether source is exist
		{	if(links[i].value==nodes[j].group)         //if the node is already exist and the value is the same with the exist one then do nothing 
				{break;}
		
			else if(links[i].value!=nodes[j].group)
				{	nodes[j].group=3; 
					break;
				}
		}                //if the node is already exist and the value is not the same in the new exp then change the value 
	}	
	if(j==nodes.length)
	{nodes.push({
		'id':links[i].source,
		'group':links[i].value	
		})
	}
for(j=0;j<nodes.length;j++)
	{ 
	if(links[i].target==nodes[j].id)                //判断所有的target是否存在 wether target node is exist
	{break;}
	}	
	if(j==nodes.length)
	{    

		nodes.push({
		'id':links[i].target,
		'group':links[i].value
		})
	}		
	
}


//---------------------------------------------------------------------------------------
//the contextMenu function
// contextMenu references from website https://codepen.io/billdwhite/pen/VYGwaZ


d3.contextMenu = function (menu, openCallback) {

	// create the div element that will hold the context menu
	d3.selectAll('.d3-context-menu').data([1])
		.enter()
		.append('div')
		.attr('class', 'd3-context-menu');

	// close menu
	d3.select('body').on('click.d3-context-menu', function() {
		d3.select('.d3-context-menu').style('display', 'none');
	});

	// this gets executed when a contextmenu event occurs
	return function(data, index) {	
		var elm = this;

		d3.selectAll('.d3-context-menu').html('');
		var list = d3.selectAll('.d3-context-menu').append('ul');
		list.selectAll('li').data(menu).enter()
			.append('li')
			.html(function(d) {
				return d.title;
			})
			.on('click', function(d, i) {
				d.action(elm, data, index);
				d3.select('.d3-context-menu').style('display', 'none');
			});

		// the openCallback allows an action to fire before the menu is displayed
		// an example usage would be closing a tooltip
		if (openCallback) openCallback(data, index);

		// display context menu
		//var position = d3.mouse(this);
		d3.select('.d3-context-menu')
			.style('position', 'absolute')
			//.style('left', position[0]+ 5 + "px")
			//.style('top', position[1] +5 + "px")
			//.style('display', 'block')                                       //two method to get mouse position and the block comment is use d3.mouse to get position
			.style('left', (d3.event.pageX + 2) + 'px')
			.style('top', (d3.event.pageY + 2) + 'px')
			.style('display', 'block');

		d3.event.preventDefault();    //prevent the system default menu
	};
};

var menu = [{
      title: 'hide label',
      action: function(elm, d, i) 
		{
			
			d3.select('#'+d.id).selectAll("text").style("display","none"); 
		}
    }, 
	{
      title: 'show label',
      action: function(elm, d, i)
		{
			
			//console.log('The data for this circle is: ' + d.id);
			d3.select('#'+d.id).selectAll("text").style("display",""); 
		}
	},
	 {
	  title:'hide all',
	  action:function(elm,d,i)
		{
			var f=1;
	
		
			for(i=0;i<nodes.length;i++)
			{	for(j=0;j<exp_name.length;j++)
							{	if(nodes[i].id ==exp_name[j])	
								{	f=2;
									d3.select("#"+nodes[i].id).selectAll("text").style("display","");       //find all of the parent node ,show the name enven when hide the lable
									break;
								}
							}	
				if(f==1)
					{d3.selectAll(".nodes").selectAll("text").style("display","none")   //hide all label  except the parent nodes
					}
			
			}
		
			
		}
	 },
	 {
	  title:'show all',
	  action:function(elm,d,i)
		{
			
			d3.selectAll(".nodes").selectAll("text").style("display",""); 
		}
	 },
	 {
	  title:'rename',
	  action:function(elm,d,i)
		{
			
			
		}
	 }
    ]
// up above is the contextMenu function
//------------------------------------------------------------------------------------------------










window.onload=update();

// hide and show lable function
function lbstyle(val)
{var f=1;    //this is just a flag to juge whether it is a parent node
	if(val==1)
		{   
			d3.selectAll(".nodes").selectAll("text").style("display","")    //show all lable
			//document.getElementById("showlable").selected=true;
		}
	else if(val==2)
		{
			for(i=0;i<nodes.length;i++)
			{	for(j=0;j<exp_name.length;j++)
							{	if(nodes[i].id ==exp_name[j])	
								{	f=2;
									d3.select("#"+nodes[i].id).selectAll("text").style("display","");       //find all of the parent node ,show the name enven when hide the lable
									break;
								}
							}	
				if(f==1)
					{d3.selectAll(".nodes").selectAll("text").style("display","none")   //hide all label  except the parent nodes
					}
			
			}
		}
	//myform.submit();   // post form
}



function update(){





d3.select('#colorchanger').selectAll('input').remove();// clear all the old create color element 
// create the colorselector element to change color in the graph
for(var i=0;i<filenum;i++)
{
	d3.select('#colorchanger').append('input')
		.attr('class','jscolor{hash:true}')
		.attr('name','colorselector'+i)
		.attr('value',mycolor[i])
		.style('width','60px')
		
		
	
} 





//draw graph

var active=d3.select(null);
var palette={
	"stroke-width" : "1.5 px",
	"gray":"#999",
	"white":"#fff",
	"black":"#000000",
	"blue":"#1574A8",
	"lightblue":"#E5FFFF"                            //identify the basic color 
}





 // the code below is for zoom the graph
    var width = 1024; 
	var height = 900; 
    
    var zoom=d3.zoom().on("zoom",zoomed).scaleExtent([1 / 2, 4]) ;   
	var svg = d3.select("#chart")
				.append("svg")
				.attr("id","svg")
				.attr("width",width)
				.attr("height",height)
				//.style("background","#ffcc66")
				.style("left", 0) 
				.style("top", 0) 
				.attr("transform", "translate(0,0)")
				 .attr("scale",[1,10])//
				.call(zoom)
				.append("g")// this g can transform for zoom.
				.attr("id","svgZoomContainer")
  
 // move and zoom functions
 //--------------------------------------------------
  

var ZoomScale=1;	
var x=0; 
var y=0;


 function zoomed()
 {
	svg.attr("transform", d3.event.transform)
	ZoomScale=d3.event.transform.k;
	x=d3.event.transform.x;
	y=d3.event.transform.y;
	console.log(x);
	//console.log(y);
 }
 
 
   
  
    d3.select("#moveLeft").on("click", function () {
		x-=5;
		y-=0;
		//console.log("x is:");
		//console.log(x);
		d3.select("#svgZoomContainer").attr("transform", "translate(" + x+ "," + y+ ")scale("+ ZoomScale +")");
    });
	
	d3.select("#moveRight").on("click", function () {
		x+=5
		y+=0
		d3.select("#svgZoomContainer").attr("transform", "translate(" + x+ "," + y+ ")scale(" + ZoomScale+ ")");
    });
	
	d3.select("#moveUp").on("click", function () {
		x-=0
		y-=5
		d3.select("#svgZoomContainer").attr("transform", "translate(" + x+ "," + y+ ")scale(" + ZoomScale + ")");
    });
	d3.select("#moveDown").on("click", function () {
		x+=0
		y+=5
		d3.select("#svgZoomContainer").attr("transform", "translate(" + x+ "," + y+ ")scale(" + ZoomScale + ")");
    });
	
	d3.select("#reset").on("click",function(){
		x=0;
		y=0;
		ZoomScale=1;
		d3.select("#svgZoomContainer").attr("transform","translate(" +x+","+y+")scale("+1+")");
		});
 //---------------------------------------------

    
  
	
//定义颜色集  identify the colorset



var a=["#FF0033", "#0066cc" , "#E9F01D"];
 var color = d3.scaleOrdinal() // D3 Version 4            node的颜色  color of nodes
  .domain([1,2,3])
  .range(a); 

  //var color2 = d3.scaleOrdinal(d3.schemeCategory20);  // parent节点的颜色  color of parent


 

 var color2=d3.scaleOrdinal()
  .domain([0,1,2,3,4,5,6,7])
  .range(mycolor)


var simulation = d3.forceSimulation()
    .force("link", d3.forceLink().id(function(d) { return d.id; }).distance(90))    //distance for the line length
    .force("charge", d3.forceManyBody())
    .force("center", d3.forceCenter(width/2, height/2));

//画链接线  draw link lines
  
  var link = svg.append("g")
      .attr("class", "links")
	  .selectAll("line")
	  .data(links)                       //获得link数据   get link 
      .enter().append("line")//画线          draw line
		.attr("id",function(d){return "link_"+ d.source;})
		.attr("stroke-width", .8) 
		.attr("stroke", palette.gray)      //线的颜色      color of line
		.attr("stroke-opacity",.6)        //线的透明度     opacity of line
		.on('mouseover',function(d){
			d3.select(this)
			.style('stroke',function(d){return color(d.value)})
			.attr("stroke-width",1)
			.attr("stroke-opacity",1)
						
		}) 
		.on('mouseout',function(){
				d3.select(this)
				.style('stroke',palette.gray)
				.attr("stroke-width",.8)
			
		})
		


  
  var circleWidth=[];                   //记录圆点的半径的数组   r of circle
for (var i=0;i<nodes.length;i++)
{	var r=5;                              //设置圆点的初始半径为5px
	for(var j=0;j<links.length    ;j++)
	{ if(nodes[i].id==links[j].target)        //遍历links数组，如果links数组中target值与nodes数组中id相同，给半径r增加0.5，计算每一个节点的权重
		{
				if(r>=20)
				{r=r+0.1;}
				else
				{r=r+0.2 ;}
		}
	
	}
	
	circleWidth.push(r)                  //将r放入数组中
	
}


   var node=svg.selectAll(" .nodes")                         //定义node
				.data(nodes).enter()                         //链接node数据
				.append("g")                                 //追加g标签
				.attr("class","nodes")                        //定义g的class=nodes
				.attr("id",function(d){return d.id})
	var nodedot=node.append("circle")
					.attr("r",function(d,i){return circleWidth[i];})             //设置圆的半径根据每个节点子节点的多少确定圆点的大小
					.attr("fill",function(d){
							for(i=0;i<exp_name.length;i++)
							{	if(d.id ==exp_name[i])	
								{	return color2(d.id);}
							}
							return color(d.group);
						  })       												//圆点的填充颜色 fill color of the nodes
					.attr("stroke",palette.white)                             //设置圆点的外边线颜色 
					.call(d3.drag()
					.on("start", dragstarted)
					.on("drag", dragged)
					.on("end", dragended))
					.on('click',function(d){           //添加鼠标单击事件 mouse click show the path
											
											d3.selectAll('#'+ 'link_'+ d.id)
														.style('stroke',function(d){return color(d.value)})
														.attr("stroke-width",1)
														.attr("stroke-opacity",1)
			
											}) 
					.on('dblclick',function(d){           // dblclick to reback the attr of the line 
											/* show the lable of the selected node
											d3.select('#'+d.id).selectAll("text").style("display","");   //hide the text of the selected node*/
											
											d3.selectAll('#'+ 'link_'+ d.id)
														.style("stroke", palette.gray)    //color of the line
														.attr("stroke-width",.8)        // width of the line
														.attr("stroke-opacity",.6)      //opacity of the line
						
											})
					.on('mouseover',function(){               // when mouseover change the circle outline color to black
											d3.select(this)
											.style('stroke',palette.black)
			
											}) 
					.on('mouseout',function(){                 // when mouse move out rechange the circle outline color to white
											d3.select(this)
											.style('stroke',palette.white)
											})
					.on('contextmenu', d3.contextMenu(menu));   //鼠标右键弹出菜单  mouse right click pop menu
		
	
	var nodetext;
	
	
	
	nodetext=node.append("text")                       //为每个节点添加文本
		.text(function(d){return d.id})                    //获得node数据中的id值
		.style("font-size",'0.4em')                        //字体大小
		.style("font-family","sans-serif")                 //设置字体
		.style("fill",palette.black)
		//.style("display",function(){if(lbflag==1){return "";}else{return "none";}})//设置字体颜色
	
		.on('click',function(){
			d3.select(this).style("display","none")                 //鼠标单击将该节点的标签隐藏                    
		})
		
		.on('mouseover',function(){                         // zoom  fontsize
			d3.select(this)
			.style('font-size','1em')
			
		}) 
		
		.on('mouseout',function(){
			d3.select(this)
			.style('font-size','0.4em')
		})
	
//var lbflag=document.getElementById('lbtxt').value;             //node标签显示不显示的控制	
var lbflag=2;
lbstyle(lbflag)	;     //call lbstyle function to show all label or hide all 
   



  simulation
      .nodes(nodes)
      .on("tick", ticked);

  simulation.force("link" )
      .links(links)
	

  function ticked() {
    link
        .attr("x1", function(d) { return d.source.x; })       //链接线的起始坐标
        .attr("y1", function(d) { return d.source.y; })
        .attr("x2", function(d) { return d.target.x; })
        .attr("y2", function(d) { return d.target.y; });              //连接线的终止坐标

    nodedot
        .attr("cx", function(d) { return d.x; })                  //圆点的圆心坐标
        .attr("cy", function(d) { return d.y; });
	 nodetext
		.attr("x",function(d){	var f=1;
								for(i=0;i<exp_name.length;i++)
								{
									if(d.id==exp_name[i])
									{f=2;break}
								}
								if(f==2)
								{return d.x-5  }
								else if(f==1)
								{return d.x + 8 }
							})                   //文本的坐标
		.attr("y",function(d){return d.y +3 })
	 
  }


function dragstarted(d) {
  if (!d3.event.active) simulation.alphaTarget(0.8).restart();
  d.fx = d.x;
  d.fy = d.y;
 
}

function dragged(d) {
  d.fx = d3.event.x;
  d.fy = d3.event.y;
}

function dragended(d) {
  if (!d3.event.active) simulation.alphaTarget(0);
  d.fx = null;
  d.fy = null;
}

}



   
</script>

<script type="text/javascript" src="js/dat.gui.min.js"></script>
<link rel="stylesheet" href="css/dat.gui.light.css">

<script >

//----------------------------------------------------------GUI---------------------------------------------------------------    
var FizzyText = function() {
  
  	this.LabelStyle =function(){};
    this.Save_AS_SVG = function(){save('svg');};
	this.Save_AS_PNG=function(){save('png');}
	this.Save_On_Line=function(){saveonline();}
	this.Change_Color=function(){ChangeNodeColor();}
	
	this.color0 = mycolor[0]; // CSS string
	this.color1 = mycolor[1]; // RGB array
	//this.color2 = mycolor[2]; // RGB with alpha
	//this.color3 = mycolor[3]; // Hue, saturation, value  	
};


window.onload = function() {
		var text = new FizzyText();
	  	var gui = new dat.GUI({ width: 360, name:'datGuiControlPanel'});    // define the gui and set width as 360


		var f1 = gui.addFolder('Graph Control');   //add folder for gui 
		var mylabel=f1.add(text, 'LabelStyle',{ShowLabel:1,HideLabel:2});   //the children of the folder
		 // how to set default value for mylabel????? 
		mylabel.onChange(function(value){lbstyle(value)});  // add event 
		
		var f2=gui.addFolder('SAVE');
			f2.add(text, 'Save_AS_SVG').name('Save as .SVG File');
			f2.add(text,'Save_AS_PNG').name('Save as .PNG File')
			f2.add(text,'Save_On_Line').name('Save Online');
			//f2.open();
			
		var f3=gui.addFolder('Color');		
			f3.addColor(text,'color0');
			var seedcolor = f3.addColor(text,'color1');
			//seedcolor.onChange(function(value){alert("onChange");});
			//f3.addColor(text,'color2');
			f3.add(text,'Change_Color').name('Change Color');
			
			//for(var i=0;i<filenum;i++)
			//{
			//	f3.addColor(text,'color0','#0000').name('mycolor'+i);
			//}

};

// save svg graph to svg or png
function download(source, filename, type) 
{
    var file = new Blob([source], {type: type});
    if (window.navigator.msSaveOrOpenBlob) // IE10+
		{window.navigator.msSaveOrOpenBlob(file, filename);}
    else { // Others
		if(type=='svg')
		{
			var a = document.createElement("a");
			url = URL.createObjectURL(file);
			a.href = url;
			a.download = filename;
			document.body.appendChild(a);
			a.click();
			setTimeout(function() {
				document.body.removeChild(a);
				window.URL.revokeObjectURL(url);  
				}, 0); 
		}
		else if (type=='png')
		{
			 var url = "data:image/svg+xml;charset=utf-8," + encodeURIComponent(source);  
			//document.write('<img src="' + url + '"/>');  
			var image = new Image;  
			image.src = url;  
			
			var canvas = document.createElement("canvas"); 
			canvas.width = 1024;  
			canvas.height = 900;  
  			var context = canvas.getContext("2d");  
			
			image.onload = function()
				{  
					context.drawImage(image, 0, 0);  
   					var a = document.createElement("a");
					a.download = filename;  
					a.href = canvas.toDataURL("image/png");  
					a.click();					
				}	
		} 
    }
}

function save(type)
{
	//get svg element.
	var svg = document.getElementById("svg");
	//get svg source.
	var serializer = new XMLSerializer();
	//var source = serializer.serializeToString(svg.node());
	var source = serializer.serializeToString(svg);
	source = '<?xml version="1.0" standalone="no"?>\r\n' + source;
	//console.log(svg_xml); 
	var systime=new Date().toLocaleTimeString();
	if (type=='svg')
		{	
		var filename='SVG_'+ md5(systime) +'.svg';
		}
	else if (type=='png')
		{
		var filename='PNG_'+md5(systime)+'.png';
		}
	download(source,filename,type);
 

} 
function saveonline()
{
window.open("https://image.online-convert.com/convert-to-png");
}

function ChangeNodeColor(){
	alert("change color");
}

</script>

