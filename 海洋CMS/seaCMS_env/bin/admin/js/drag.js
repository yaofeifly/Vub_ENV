 if  (document.getElementById){(
	function(){      
        var dragok = false;
        var y,x,d,dy,dx;
        
		function _drag_gs(d,a){
    if (d.currentStyle){var curVal=d.currentStyle[a]}else{
      var curVal=document.defaultView.getComputedStyle(d, null)[a]
    }
    return curVal;
  }
        function _drag_move(e)
        {
          if (!e) e = window.event;
          if (dragok){
            d.style.left = dx + e.clientX - x + "px";
            d.style.top  = dy + e.clientY - y + "px";
            return false;
          }
        }
        
        function _drag_down(e){
          if (!e) e = window.event;
          var temp = (typeof e.target != "undefined")?e.target:e.srcElement;
          if (temp.tagName != "HTML"|"BODY" && temp.className != "dragclass"){
			temp = (typeof temp.parentNode != "undefined")?temp.parentNode:temp.parentElement;
			}

          if (temp.className == "popdiv"){
            dragok = true;
            d = temp;
            dx = parseInt(_drag_gs(temp,"left"))|0;
            dy = parseInt(_drag_gs(temp,"top"))|0;
            x = e.clientX;
            y = e.clientY;
            document.onmousemove = _drag_move;
            return false;
          }
        }
        
        function _drag_up(){
          dragok = false;
          document.onmousemove = null;

        }
        
        document.onmousedown = _drag_down;
        document.onmouseup = _drag_up;
      
      }
    )();
}