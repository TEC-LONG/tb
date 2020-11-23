function CascadeBeauty(json){

	this.expressLv1 = null;
	this.expressLv2 = null;

	this.getChildUrl = json.url;

	this.sort1Id = '#'+json.sort1;
	this.sort2Id = '#'+json.sort2;
	this.sort3Id = '#'+json.sort3;
	this.crumbSortId = '#'+json.crumb_id;

	this.showLv1Call = json.showLv1Call;//显示一级分类栏目时的回调
	this.selectLv1Call = json.selectLv1Call;//选中某个一级分类时的回调
	this.showLv2Call = json.showLv2Call;//显示二级分类栏目时的回调
	this.selectLv2Call = json.selectLv2Call;//选中某个二级分类时的回调
	this.showLv3Call = json.showLv3Call;//显示三级分类栏目时的回调
	this.selectLv3Call = json.selectLv3Call;//选中某个三级分类时的回调

	this.one = json.one;
	this.two = [];
	this.three = [];
/*
	
one = [
	{"id":10, "name":"吃饭做菜"},
	{"id":10, "name":"日常总结"},
	{"id":10, "name":"寄情山水"},
	{"id":10, "name":"金融"},
	{"id":10, "name":"编程"},
	{"id":10, "name":"美术"},
	{"id":10, "name":"上学"},
	{"id":10, "name":"综合学习"}
];
two = [
	[
		{"id":10, "name":"吃饭做菜"},
		{"id":10, "name":"日常总结"},
		{"id":10, "name":"寄情山水"}
	],[
		{"id":10, "name":"美术"},
		{"id":10, "name":"上学"},
		{"id":10, "name":"综合学习"}
	],[
		{"id":10, "name":"美术"},
		{"id":10, "name":"上学"},
		{"id":10, "name":"综合学习"}
	]
];
three = [
	[
		[
			{"id":10, "name":"吃饭做菜"},
			{"id":10, "name":"日常总结"}
		],[
			{"id":10, "name":"金融"},
			{"id":10, "name":"编程"}
		]
	],[
		[
			{"id":10, "name":"吃饭做菜"},
			{"id":10, "name":"日常总结"},
			{"id":10, "name":"寄情山水"}
		],[
			{"id":10, "name":"编程"},
			{"id":10, "name":"美术"}
		],[
			{"id":10, "name":"上学"},
			{"id":10, "name":"综合学习"}
		]
	],[
		[
			{"id":10, "name":"吃饭做菜"},
			{"id":10, "name":"日常总结"},
			{"id":10, "name":"寄情山水"}
		],[
			{"id":10, "name":"编程"},
			{"id":10, "name":"美术"}
		],[
			{"id":10, "name":"上学"},
			{"id":10, "name":"综合学习"}
		],[
			{"id":10, "name":"上学"},
			{"id":10, "name":"综合学习"}
		]
	]
];

*/

	this.arrow = ' <font>&gt;</font> ';

	this.showLv1 = function () { // show level 1
		
		var one_area = "";
		for (var one_k=0; one_k<this.one.length; one_k++) {
			
			var one_val = this.one[one_k];
			one_area += '<li onClick="selectLv1(' + one_k + ');"><a href="javascript:void(0)">' + one_val.name + '</a></li>';
		}

		$(this.sort1Id).html(one_area);//第一栏内容
		$(this.sort2Id).hide();
		$(this.sort3Id).hide();
		$(this.crumbSortId).html('无');//面包屑区域填充内容

		/// 展示一级分类列表时的回调
		if ( typeof(this.showLv1Call)=='function' ) {
			this.showLv1Call(this);
		}
	}

	///选中一级分类项时
	this.selectLv1 = function (one_key) {

		if ( typeof(this.two[one_key])==='undefined' ){

			var that = this;
			var now_cat = this.one[one_key];
			$.ajax({
				type:'POST',
				data:{p_id:now_cat.id},//当前一级分类的id，作为该一级分类下二级分类的父id
				dataType:'json',
				url:this.getChildUrl,
				async:true,
				success:function (two){
					if ( two.length==0 ){
						that.two[one_key] = [];
					}else{
						that.two[one_key] = two;

						/// 点击一级分类列表中的某个分类时的回调
						if ( typeof(that.selectLv1Call)=='function' ) {
							//that.selectLv1Call( 当前选中的一级, 当前一级所有的二级, 级联对象)
							that.selectLv1Call(now_cat, two, that);
						}
					}
					//b.ori
					that.showLv2(one_key);
					//e.ori
				}
			});
		}else{
			this.showLv2(one_key);
		}
	}

	this.showLv2 = function (one_key) {

		var two_area = "";
		for (var two_key=0; two_key<this.two[one_key].length; two_key++) {
			two_area += '<li onClick="selectLv2(' + one_key + ',' + two_key + ');"><a href="javascript:void(0)">' + this.two[one_key][two_key].name + '</a></li>';
		}

		$(this.sort2Id).html(two_area).show();
		$(this.sort3Id).hide();
		$(this.sort1Id+" li").eq(one_key).addClass("active").siblings("li").removeClass("active");

		this.expressLv1 = this.one[one_key].name;
		$(this.crumbSortId).html(this.expressLv1);//将第一栏被选中项填入面包屑区域

		if ( typeof(this.showLv2Call)=='function' ) {
			this.showLv2Call(this.one[one_key], this);
		}
	}

	///选中二级分类项时
	this.selectLv2 = function (one_key, two_key) {

		if ( typeof(this.three[one_key])==='undefined'||typeof(this.three[one_key][two_key])==='undefined' ){

			var that = this;
			var now_cat = this.two[one_key][two_key];
			$.ajax({
				type:'POST',
				data:{p_id:now_cat.id},//当前二级分类的id，作为该二级分类下三级分类的父id
				dataType:'json',
				url:this.getChildUrl,
				async:true,
				success:function (three){

					if ( that.three.length==0 ){

						if ( typeof(that.three[one_key])==='undefined' ){
							that.three[one_key] = [];
						}
					}
					
					that.three[one_key][two_key] = three;

					/// 点击二级分类列表中的某个分类时的回调
					if ( typeof(that.selectLv2Call)=='function' ) {
						//that.selectLv2Call( 当前选中的二级, 当前二级所有的三级, 级联对象)
						that.selectLv2Call(now_cat, three, that);
					}

					//b.ori
					that.showLv3(one_key,two_key);
					//e.ori
					
				}
			});
		}else{
			this.showLv3(one_key,two_key);
		}
	}

	this.showLv3 = function (one_key,two_key) {

		var three_area = "";
		var this_three = this.three[one_key][two_key];
		for (var three_key=0; three_key<this_three.length; three_key++) {
			three_area += '<li onClick="selectLv3(' + one_key + ',' + two_key + ',' + three_key + ');"><a href="javascript:void(0)">' + this_three[three_key].name + '</a></li>';
		}
		$(this.sort3Id).html(three_area).show();
		$(this.sort2Id+" li").eq(two_key).addClass("active").siblings("li").removeClass("active");

		this.expressLv2 = this.expressLv1 + this.arrow + this.two[one_key][two_key].name;
		$(this.crumbSortId).html(this.expressLv2);

		if ( typeof(this.showLv3Call)=='function' ) {
			this.showLv3Call(this.two[one_key][two_key], this);
		}

		if (this_three.length==0) {
			return false;
		}
	}

	this.selectLv3 = function (one_key, two_key, three_key) {
		$(this.sort3Id+" li").eq(three_key).addClass("active").siblings("li").removeClass("active");

		if ( typeof(this.selectLv3Call)=='function' ) {
			this.selectLv3Call(this.three[one_key][two_key][three_key], this);
		}
	}
}

var selectLv3 = function (k1, k2, k3) {
	cascade_beauty.selectLv3(k1, k2, k3);
}
var selectLv2 = function (k1, k2) {
	cascade_beauty.selectLv2(k1, k2);
}
var selectLv1 = function (k1) {
	cascade_beauty.selectLv1(k1);
}