

<link rel="stylesheet" href="css/base.css">
<link rel="stylesheet" href="css/style.css">
<script type="text/javascript" src="https://d3js.org/d3.v4.min.js"> </script>
<script src="js/jscolor.js"></script>

	
		

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


<span> Label Style: 
		<select id='lbtxt' name='lbtxt' onchange='lbstyle(this.value)'>    
			<option value='1' selected>show label</option>          
			<option value='2' >hidden label</option> 
			
		</select>
	</span> 
<br><br>


 <form action="<?php echo htmlspecialchars($_SERVER[PHP_SELF]);?>" method="post" enctype="multipart/form-data">
	<span id="colorchanger" > Color changer:
		<br> 
	</span>
	<button type="submit" name="changecolor" > change color</button>
  </form>	
  
  
  
<div class="container" >
		<h2>ForceDirectedGraph	</h2>
		<section id="chart" >
		</section>
</div>

<script>

</script>
<script type="text/javascript">


window.onload=update();
function lbstyle()
{
	var refresh=d3.select('#chart')
						refresh.selectAll('svg').remove()    
						
						update();
}



function update(){



var mylinks=<?php echo $objlinks; ?>;  // get the data 
var filenum=<?php echo $filenum;?>;   //get how many files the customer uploaded
var exp_name=<?php echo json_encode($expname);?>;   //get each name for experiment the customer uploaded
var mycolor=<?php echo json_encode($mycolors); ?>;  //get array value of color




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
var width=1000,
    height=900;
  var svg = d3.select("#chart")
  .append("svg")
  .attr("width",width)
  .attr("height",height)
  .style("background",palette.lightblue)             //svg attr background and width and height 
  
	
//定义颜色集



var a=["#FF0033", "#0066cc" , "#E9F01D"];
 var color = d3.scaleOrdinal() // D3 Version 4            node的颜色  color of node
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

//画链接线
  
  var link = svg.append("g")
      .attr("class", "links")
	  .selectAll("line")
	  .data(links)                       //获得link数据   get link 
      .enter().append("line")             //画线          draw line
		.attr("stroke-width", .8) 
		.attr("stroke", palette.gray)      //线的颜色      color of line
		.attr("stroke-opacity",.6)        //线的透明度     opacity of line
		.on('mouseover',function(m){
			d3.select(this)
			.style('stroke',function(d){return color(d.value)})
						
		}) 
		.on('mouseout',function(m){
				d3.select(this)
				.style('stroke',palette.gray)
			
		})
		
	  


  
  var circleWidth=[];                   //记录圆点的半径的数组
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
	var nodedot=node.append("circle")
	.attr("r",function(d,i){return circleWidth[i];})             //设置圆的半径根据每个节点子节点的多少确定圆点的大小
		.attr("fill",function(d){
			for(i=0;i<exp_name.length;i++)
			{if(d.id ==exp_name[i])	
				{return color2(d.id);}
			}
			return color(d.group);
})       //圆点的填充颜色
		.attr("stroke",palette.white)                             //设置圆点的外边线颜色
		.call(d3.drag()
          .on("start", dragstarted)
          .on("drag", dragged)
          .on("end", dragended))
		/*  .on('click',function(d,i){           //添加鼠标单击事件，改变圆点的颜色和大小
			oldColor= color(d.group);  //记录节点初始的颜色，不同的group有不同的颜色
			d3.select(this)
				.style('fill','purple')
				.attr('r',10)
			
			}) */
		.on('dblclick',function(d,i){           //双击回复原来的大小
			d3.select(this)
				.style('fill',function(d){return color(d.group);})
			.attr('r',5)
		})
		.on('mouseover',function(){
			d3.select(this)
			.style('stroke',palette.black)
			
		}) 
		.on('mouseout',function(){
			d3.select(this)
			.style('stroke',palette.white)
		})
		
	
	var nodetext;
	var lbflag=document.getElementById('lbtxt').value;             //node标签显示不显示的控制
	if(lbflag==1)                                                //显示和隐藏lable  f为o 隐藏文字，为1 显示文字
	{nodetext=node.append("text")                       //为每个节点添加文本
		.text(function(d){return d.id})                    //获得node数据中的id值
		.style("font-size",'0.4em')                        //字体大小
		.style("font-family","sans-serif")                 //设置字体
		.style("fill",palette.black)                      //设置字体颜色
	
		.on('click',function(){
			d3.select(this).remove()                   //鼠标单击将该节点的标签隐藏                    
		})
		
		.on('mouseover',function(){
			d3.select(this)
			.style('font-size','1em')
			
		}) 
		
		.on('mouseout',function(){
			d3.select(this)
			.style('font-size','0.4em')
		})
		
		
    }
	else
	{nodetext=node.append("text")                       //为每个节点添加文本
		.text("")                    // 不显示node的标签
		
	}



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
		.attr("x",function(d){return d.x + 8 })                   //文本的坐标
		.attr("y",function(d){return d.y + 5 })
	 
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

</script >

 </script>