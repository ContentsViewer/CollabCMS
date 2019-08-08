﻿
var offsetYToHideHeader = 100;

var headerArea = null;
var pullDownMenuButton = null;
var pullUpMenuButton = null;
var leftSideAreaResponsive = null;
var menuOpenInput = null;
var indexArea = null;
var warningMessageBox = null;
var isTouchDevice = false;
var sitemask = null;
var doseHideHeader = false;
var menuOpenButton = null;

var sectionListInMainContent = [];
var sectionListInIndexArea = [];

var timer = null;
var scrollPosPrev = 0

window.onload = function () {
	// 各Area取得
	headerArea = document.querySelector("#header-area");
	pullDownMenuButton = document.querySelector("#pull-down-menu-button");
	pullUpMenuButton = document.querySelector("#pull-up-menu-button");
	warningMessageBox = document.getElementById('warning-message-box');
	leftSideAreaResponsive = document.getElementById('left-side-area-responsive');
	menuOpenInput = document.getElementById('menu-open');
	sitemask = document.getElementById('sitemask');
	menuOpenButton = document.getElementsByClassName('menu-open-button-wrapper')[0];

	scrollPosPrev = window.pageYOffset

	// Scrollイベント登録
	window.addEventListener("scroll", OnScroll);

	// タッチデバイス判定
	isTouchDevice = IsTouchDevice();

	// --- indexArea関係 --------------------------------------------
	var indexArea = document.getElementById("right-side-area");
	var indexAreaOnSmallScreen = document.getElementById("index-area-on-small-screen");
	var mainContent = document.getElementById("main-content-field");

	// indexAreaとmainContentが正常に読み込めた場合のみ実行
	if (mainContent && indexArea) {
		// IndexArea内にあるNaviを取得
		var naviInIndexArea = null;
		if (indexArea.getElementsByClassName("navi").length > 0) {
			var naviInIndexArea = indexArea.getElementsByClassName("navi")[0];
		}

		// Naviを取得できた場合のみ実行
		if (naviInIndexArea) {
			var totalID = 0;
			if (mainContent.children.length == 0 || (totalID = CreateSectionTreeHelper(mainContent.children[0], naviInIndexArea, 0)) == 0) {
				naviInIndexArea.innerText = "  目次がありません";
			}

			//alert(indexAreaOnSmallScreen);
			if (indexAreaOnSmallScreen) {
				var naviInIndexAreaOnSmallScreen = naviInIndexArea.cloneNode(true);
				naviInIndexAreaOnSmallScreen.removeAttribute("class");
				indexAreaOnSmallScreen.appendChild(naviInIndexAreaOnSmallScreen);
				//alert("12");
			}
			//alert(totalID);
			//alert("1");
		}
	}
}

window.onresize = function () {
	CloseLeftSideArea();
}

//
// mainContent内にあるSectionを取得します.
// 同時に, ナヴィゲータの作成, sectionListInMainContent, sectionListInIndexAreaにSectionを登録します.
//
// @param element:
//  Section探索元
//  この下の階層にSectionListが来るようにしてください
//  
// @param navi:
//  生成されるナヴィゲータリスト
//  
// @param idBegin:
//  振り分け開始id
//
function CreateSectionTreeHelper(element, navi, idBegin) {


	var ulElement = document.createElement("ul");


	for (var i = 0; i < element.children.length; i++) {


		//alert("12");
		if (element.children[i].className == "section-title"
			|| element.children[i].className == "sub-section-title"
			|| element.children[i].className == "sub-sub-section-title"
			|| element.children[i].className == "sub-sub-sub-section-title") {

			//alert("12");
			element.children[i].setAttribute("id", "SectionID_" + idBegin);

			var section = document.createElement("li");
			var link = document.createElement("a");
			link.innerText = element.children[i].innerText;
			link.href = "#SectionID_" + idBegin;
			section.appendChild(link);

			sectionListInIndexArea.push(link);

			ulElement.appendChild(section);

			idBegin++;

			if (i + 1 < element.children.length
				&& element.children[i + 1].className == "section") {

				sectionListInMainContent.push(element.children[i + 1]);

				idBegin = CreateSectionTreeHelper(element.children[i + 1], section, idBegin);

			}
			else {

				sectionListInMainContent.push(null);
			}
		}
	}

	if (ulElement.children.length > 0) {
		navi.appendChild(ulElement);
	}
	return idBegin;
}

sumOfScroll = 0
isHiddenHeader = false
function OnScroll() {

	//一定量スクロールされたとき
	if (window.pageYOffset > offsetYToHideHeader) {
		// headerArea.classList.add('transparent');
		// headerArea.style.animationName = "fade-out";
		// headerArea.style.animationDuration = "0.8s";
		if (warningMessageBox != null) {

			warningMessageBox.style.animationName = "warning-message-box-slideout";
			warningMessageBox = null;
		}
	}

	if (sumOfScroll * (window.pageYOffset - scrollPosPrev) < 0.0) {
		sumOfScroll = 0
	}
	sumOfScroll += window.pageYOffset - scrollPosPrev
	// scroll_velocity = window.pageYOffset - scrollPosPrev

	if (window.pageYOffset < offsetYToHideHeader) {
		// headerArea.classList.remove('hide-header')
		if (isHiddenHeader) {
			headerArea.style.animationName = "appear-header-anim";
			// headerArea.style.animationName = "fade-in";
			isHiddenHeader = false;
		}
	}
	else {
		// headerArea.classList.add('hide-header')
		if (!isHiddenHeader) {
			headerArea.style.animationName = "hide-header-anim";
			// headerArea.style.animationName = "fade-out";
			OnClickPullUpButton();
			isHiddenHeader = true
		}
	}

	scrollPosPrev = window.pageYOffset;

	if (timer) {
		return;
	}

	timer = setTimeout(function () {
		timer = null;

		var currentSectionIDs = [];
		for (var i = 0; i < sectionListInMainContent.length; i++) {
			if (sectionListInMainContent[i] == null) {
				continue;
			}
			var sectionRect = sectionListInMainContent[i].getBoundingClientRect();
			if (sectionRect.top < window.innerHeight / 2 && sectionRect.bottom > window.innerHeight / 2) {
				currentSectionIDs.push(i);
			}
		}

		for (var i = 0; i < sectionListInIndexArea.length; i++) {
			sectionListInIndexArea[i].removeAttribute("class");
		}

		for (var i = 0; i < currentSectionIDs.length; i++) {
			sectionListInIndexArea[currentSectionIDs[i]].setAttribute("class", "selected");
		}

	}, 500);
}

function IsTouchDevice() {
	var result = false;
	if (window.ontouchstart === null) {
		result = true;
	}
	return result;
}

function OnClickPullDownButton() {
	pullDownMenuButton.style.display = 'none';
	pullUpMenuButton.style.display = 'block';

	headerArea.classList.add('pull-down');
}

function OnClickPullUpButton() {
	pullDownMenuButton.style.display = 'block';
	pullUpMenuButton.style.display = 'none';
	headerArea.classList.remove('pull-down');
}

function OnChangeMenuOpen(input) {
	if (input.checked) {
		OpenLeftSideArea();
	}
	else {
		CloseLeftSideArea();
	}
}

function OpenLeftSideArea() {
	menuOpenInput.checked = true;
	leftSideAreaResponsive.classList.add('left-side-area-responsive-open');

	document.body.style.overflow = "hidden";
	leftSideAreaResponsive.style.zIndex = "99999";
	menuOpenButton.style.zIndex = "99999";
	sitemask.setAttribute('visible', '');
}

function CloseLeftSideArea() {
	menuOpenInput.checked = false;
	leftSideAreaResponsive.classList.remove('left-side-area-responsive-open');

	document.body.style.overflow = "auto";

	leftSideAreaResponsive.style.zIndex = "990";
	menuOpenButton.style.zIndex = "990";
	sitemask.removeAttribute('visible');
}

function OnClickSitemask() {
	CloseLeftSideArea();
}