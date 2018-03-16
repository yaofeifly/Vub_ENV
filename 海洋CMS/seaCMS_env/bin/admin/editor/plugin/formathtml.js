function formatHTML(str){
	var remainInStr=str.replace(/<(strong|b|u|i|em|span|font)[^>]*><\/\1>/ig,''),
		outStr='',
		ubbReg= new RegExp('\\s*(<(\\/?)([a-z]\\w*)[^>]*>)[\\r\\n]*','im'),
		indentChars=[],
		wrapOutTags={'table':1,'tbody':1,'thead':1,'tfoot':1,'tr':1,'td':1,'p':1,'caption':1,'div':1,'object':1,'param':1,'embed':1,'dl':1,'dd':1,'dt':1,'ul':1,'ol':1,'li':1,'hr':1,'script':1},
		indentTags={'table':1,'tbody':1,'thead':1,'tfoot':1,'tr':1,'object':1,'dl':1,'ul':1,'ol':1,'div':1,'script':1},
		embedTags={'a':1,'u':1,'i':1,'b':1,'strong':1,'em':1,'font':1,'span':1,'img':1,'input':1,'h1':1,'h2':1,'h3':1,'h4':1,'h5':1,'h6':1}, //保持不变的标签
		text,tagExt,tagFull,isEndTag,tagName;

	while( null != (result = ubbReg.exec(remainInStr))){
		text = remainInStr.substr(0, result.index);
		tagExt=	result[0];
		tagFull=result[1];
		isEndTag = '/'===result[2];
		tagName=result[3].toLowerCase();

		remainInStr = remainInStr.substr(result.index + tagExt.length);
		outStr += text;
		if(isEndTag){
			if(indentTags[tagName]) indentChars.pop();
			if(embedTags[tagName]) outStr += tagExt; 
			else if(indentTags[tagName]) outStr +="\n"+indentChars.join('')+tagFull;
			else outStr += tagFull ;

			if(wrapOutTags[tagName]) outStr+="\n"; 
		}else{
			if(wrapOutTags[tagName]) outStr+="\n"; 
			if(embedTags[tagName]) outStr += tagExt; 
			else if(indentTags[tagName]) outStr += indentChars.join('')+tagFull+"\n";
			else outStr += indentChars.join('')+tagFull;
			if('hr'===tagName || 'br'===tagName) outStr+="\n"; 
			if(indentTags[tagName]) indentChars.push('  ');
		}
	}
	outStr += remainInStr;
	outStr = outStr.replace(/\n+/g, "\n").replace(/^[ \n\r\t]+/g, "").replace(/[ \n\r\t]+$/g, "").replace(/<td([^>]*)>\s+<br/ig, "<td$1 ><br").replace(/<br>\s+<\/td>/ig, "<br></td>");
	if('<p>&nbsp;</p>'===outStr || '<br>'===outStr) outStr='';
	return outStr;
}