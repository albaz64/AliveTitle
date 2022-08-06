"use strict";

// 还原标题用
let title = document.title;

/* PJAX重载
如果需要,请尝试找到自己主题的相关函数
或者使用其他方法(点击事件/URL监控
*/
Aria.reloadAction = () => {
	title = document.title;
}


// 滚动标题函数
let titleScroll = (interval, timeout) => {
	let text = document.title;
	let timerID;
	//标题超过长度后启用滚动
	if (text.length > alivetitle.titleLength) {
		document.title = text.substring(1, text.length) + text.substring(0, 1);
		text = document.title.substring(0, text.length);
		timerID = setTimeout(`titleScroll(${interval},${timeout})`, timeout);
	}
}

// 替换标题函数
let titleReplace = () => {
	let titleTimer;
	document.addEventListener('visibilitychange', () => {
		if (document.hidden) {
			document.title = alivetitle.lostFocus || '你一定又在看别的女人罢!';
			clearTimeout(titleTimer);
		} else {
			if (title) document.title = alivetitle.getFocus || '我也不差啊,再看看我';
			titleTimer = setTimeout(() => {
				document.title = title;
				// 需要重载滚动函数
				titleScroll(alivetitle.titleScrollInterval, alivetitle.replaceTimeout)
			}, alivetitle.replaceTimeout); // 延时恢复原标题
		}
	});
};

// function getAttr(script, attr, default_val) {
// 	return Number(script.getAttribute(attr)) || default_val;
// }

// let data = document.getElementsByTagName('script');
// data = data[data.length - 1];

titleReplace();

// 延时启动滚动标题
setTimeout(() => {
	titleScroll(alivetitle.titleScrollInterval, alivetitle.replaceTimeout)
}, alivetitle.replaceTimeout);

