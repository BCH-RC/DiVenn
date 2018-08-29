var FizzyText = function() {
  
  	this.LabelStyle =function(){};
    this.Save_AS_SVG = function(){save('svg');};
	this.Save_AS_PNG=function(){save('png');}
	this.Save_On_Line=function(){saveonline();}
  	
};


window.onload = function() {
		var text = new FizzyText();
	  	var gui = new dat.GUI({ width: 360 });    // define the gui and set width as 360


		var f1 = gui.addFolder('Graph Control');   //add folder for gui 
		var mylabel=f1.add(text, 'LabelStyle',{ShowLabel:1,HideLabel:2});   //the children of the folder
		 // how to set default value for mylabel????? 
		var f2=gui.addFolder('SAVE');
			f2.add(text, 'Save_AS_SVG');
			f2.add(text,'Save_AS_PNG')
			f2.add(text,'Save_On_Line')
			//f2.open();
		mylabel.onChange(function(value){lbstyle(value)});  // add event 
};

