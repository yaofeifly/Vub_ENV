jQuery.fn.rater = function(options)
{
	var settings = {
		active    : true,
		maxvalue  : 10,
		curvalue  : 0,
		style     : 'normal'
	};
	if(options) { jQuery.extend(settings, options); };
	var container = jQuery(this);
	jQuery.extend(container, { averageRating: settings.curvalue });
	
	if(!settings.style || settings.style == null || settings.style == 'normal') {
		var raterwidth = settings.maxvalue * 25;
		var ratingparent = '<ul class="star-rating" style="width:'+raterwidth+'px">';
	}
	if(settings.style == 'small') {
		var raterwidth = settings.maxvalue * 10;
		var ratingparent = '<ul class="star-rating small-star" style="width:'+raterwidth+'px">';
	}
	if(settings.style == 'inline-normal') {
		var raterwidth = settings.maxvalue * 25;
		var ratingparent = '<span class="inline-rating"><ul class="star-rating" style="width:'
			+raterwidth+'px">';
	}
	if(settings.style == 'inline-small') {
		var raterwidth = settings.maxvalue * 10;
		var ratingparent = '<span class="inline-rating"><ul class="star-rating small-star" style="width:'
			+raterwidth+'px">';
	}
	container.append(ratingparent);
	
	var starWidth, starIndex, listitems = '';
	var curvalueWidth = Math.floor(100 / settings.maxvalue * settings.curvalue);
	if(settings.active) {
		for(var i = 0; i <= settings.maxvalue ; i++) {
			if (i == 0) {
				listitems+='<li class="current-rating" style="width:'+curvalueWidth+'%;" title="'
				+settings.curvalue+'/'+settings.maxvalue+'">'
				+settings.curvalue+'/'+settings.maxvalue+'</li>';
			} else {
				starWidth = Math.floor(100 / settings.maxvalue * i);
				starIndex = (settings.maxvalue - i) + 2;
				listitems+='<li class="star"><a href="#'+i+'" title="'+i+'/'+settings.maxvalue
					+'" style="width:'+starWidth+'%;z-index:'+starIndex+'">'+i+'</a></li>';
			}
		}
	} else {
		listitems+='<li class="current-rating" style="width:'+curvalueWidth+'%;" title="'
				+settings.curvalue+'/'+settings.maxvalue+'">'
				+settings.curvalue+'/'+settings.maxvalue+'</li>';
	}
	container.find('.star-rating').append(listitems);
	container.find('.star-rating').append('</ul>');
	container.append('<span class="star-rating-result"></span>'); 
	
	var stars = jQuery(container).find('.star-rating').children('.star');
	stars.click(function()
	{
		raterValue = jQuery(this).children('a')[0].href.split('#')[1];
		$.get(settings.url + raterValue ,function (obj){
			if(obj.indexOf("haved")!=-1){
				raterValue = settings.curvalue;
				alert('你已经评过分啦');
			}else{
				raterValue = parseFloat(obj);
				alert('感谢你的参与!');
			}
			container.find('.star-rating').remove();
			container.find('.inline-rating').remove();
			container.rater({active:false,maxvalue:settings.maxvalue,curvalue:raterValue,style:settings.style});
			return false;
		});
	});
};