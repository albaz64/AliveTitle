'use strict';
(() => {
	const scriptTag = document.currentScript

	const alivetitle = {
		scroll: scriptTag.getAttribute('data-scroll'),
		scrollSpeed: Number(scriptTag.getAttribute('data-scroll-speed')),
		titleLength: Number(scriptTag.getAttribute('data-title-length')),
		replace: scriptTag.getAttribute('data-replace'),
		replaceScroll: scriptTag.getAttribute('data-replace-scroll'),
		replaceTimeout: Number(scriptTag.getAttribute('data-replace-timeout')),
		lostFocus: scriptTag.getAttribute('data-lost-focus'),
		getFocus: scriptTag.getAttribute('data-get-focus'),
		pjax: scriptTag.getAttribute('data-pjax')
	}

	const sleep = time => {
		return new Promise(resolve => {
			setTimeout(resolve, time)
		})
	}

	// 还原标题用
	let title = document.title

	// PJAX 兼容
	if (alivetitle.pjax) document.addEventListener('mousedown', () => {
		title = document.title
	})

	/* 手动添加 PJAX 重载示例 */
	/*
	Aria.reloadAction = () => {
		title = document.title;
	}
	*/

	// 滚动标题函数
	async function titleScroll() {
		if (!alivetitle.scroll) return;
		if (!alivetitle.replaceScroll && document.hidden) return;

		let text = document.title

		if (text.length > alivetitle.titleLength) {
			document.title = text.substring(1) + text[0]
			await sleep(alivetitle.scrollSpeed)
			titleScroll()
		}
	}

	// 替换标题函数
	async function titleReplace() {
		if (!alivetitle.replace) return;
		document.addEventListener('visibilitychange', async () => {
			if (document.hidden) {
				document.title = alivetitle.lostFocus
			} else {
				document.title = alivetitle.getFocus
				await sleep(alivetitle.replaceTimeout)
				document.title = title
				titleScroll()
			}
		});
	};

	window.addEventListener('load', () => {
		titleReplace()
		titleScroll()
	})

})()