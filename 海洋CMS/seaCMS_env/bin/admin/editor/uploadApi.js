/*
供上传用的一些JS接口
*/
//取得上级相关的编辑器对象
function parentEditor()
{
	return parentForm().srcEditor;
}
//取得包含了本网址的表单对象
function parentForm()
{
	return window.frameElement.parentNode.parentNode;
}
//插入结果,暂仅支持图片
function insertResult(resultUrl)
{
	var editor = parentEditor();
	editor.insertImage(resultUrl);
}
//插入结果
function selectResult(resultUrl)
{
	//切换选项卡
	var popform = parentForm();
	var tabbar = popform.firstChild;
	tabbar.firstChild.onclick();

	popform.elements['url'].value=resultUrl;
}