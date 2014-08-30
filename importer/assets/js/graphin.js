function graphin(graph){		
	var bars=$(graph).children('.bar');
	var num=bars.length,width=$(graph).width();
	var max_val=parseFloat($(graph).attr('data-max')),min_val=parseFloat($(graph).attr('data-min')),stepInv=parseFloat($(graph).attr('data-inv-step'));
	var snap=($(graph).attr('data-snap'))=='true'?true:false;
	if(typeof(max_val)=='undefined')max_val=100;
	if(typeof(min_val)=='undefined')min_val=0;
	if(typeof(stepInv)=='undefined')stepInv=10;
	function graph_update(){
			bars.each(function(i,bar) {
				value=$(bar).children('.value').val();
				value=Math.round(value*stepInv)/stepInv;
				ratio=(value-min_val)/(max_val-min_val);
				if(ratio>1)ratio=1;
				if(ratio<0)ratio=0;
				$(bar).children('.shaded').height(ratio*$(bar).height());
				$(bar).attr('title',$(bar).children('.axis').html()+' \u21d2 '+value).tooltip({'animation':false,'placement':'bottom'});
			});
	}
	function graph_mouse(e){
		if(e.type!='mousedown')
			fixMBstatus(e);
		var Y,value,shaded=$(this).children('.shaded');
		var height=$(this).height();
		Y = height-(e.pageY - $(this).offset().top);
		if(Y>height)Y=height;
		if(Y<0)Y=0;
		value=Y/height*(max_val-min_val)+min_val;
		value=Math.round(value*stepInv)/stepInv;
		if(snap)Y=(value-min_val)/(max_val-min_val)*height;
		if(e.which==1)
		{
			shaded.css('background-color','#096');
			$(this).find('.value').val(value);
			shaded.height(Y);
			$(this).attr('title',$(this).children('.axis').html()+' \u21d2 '+value).tooltip('fixTitle').tooltip('show');
			if(e.type!='mousedown')
				return false;
		}else{
			shaded.css('background-color','#069');
		}
		return true;
	};		
	bars.bind('mouseout',function(e){
		var shaded=$(this).find('.shaded');
		shaded.css('background-color','#099');			
		return true;
	}).bind('mousedown',graph_mouse).bind('mousemove',graph_mouse)
	.each(function(i,bar){
		axis=$(this).find('.axis');
		$(this).width(width/num);
		axis.css({'margin-left':(width/num-axis.width())*0.5+'px','margin-bottom':(-axis.height())+'px'});
	});
	$(graph).css('position','relative').bind('mousedown',function(e){
		fixMB_down(e);
		return false;
	});
	yaxis=$(graph).find('.axis').not('.bar > .axis');
	yaxis.css({'left':-yaxis.width()});
	var axis_li=yaxis.find('ul>li'),axis_h=$(graph).height()/axis_li.length+'px';
	axis_li.css({'line-height':axis_h,'height':axis_h});
	graph_update();
}
