

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


