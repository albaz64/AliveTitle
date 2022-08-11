"use strict";

// 还原标题用
let title = document.title;
// PJAX 兼容
if (alivetitle.pjax_ajax) document.addEventListener('mousedown', () => {
	title = document.title;
});

/*
手动添加重载示例
*/
Aria.reloadAction = () => {
	title = document.title;
}

const sleep = time => {
	return new Promise(resolve => {
		setTimeout(resolve, time);
	});
}

// 滚动标题函数
async function titleScroll() {

	if (alivetitle.scroll == false) return;
	if (alivetitle.replaceScroll == false && document.hidden) return;

	let text = document.title;

	if (text.length > alivetitle.titleLength) {
		document.title = text.substring(1, text.length) + text.substring(0, 1);
		await sleep(alivetitle.scroolSpeed);
		titleScroll();
	}
}

// 替换标题函数
async function titleReplace() {

	if (alivetitle.replace == false) return;
	document.addEventListener('visibilitychange', async () => {
		if (document.hidden) {
			document.title = alivetitle.lostFocus;
		} else {
			title ? document.title = alivetitle.getFocus : 0
			await sleep(alivetitle.replaceTimeout);
			document.title = title;
			titleScroll();
		}
	});
};

titleReplace();
titleScroll();
