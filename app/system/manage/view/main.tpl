<!-- container -->
{literal}
<style>
.system_manage_index_max {color:red;}
.system_manage_index_min {color:green;}
</style>
{/literal}
<div class="navTab-panel tabsPageContent layoutBox">
	<div class="page unitBox">

		<div class="row">
			<div class="col-md-12 col-sm-12">
	
				<h1>综合统计数据</h1>
				<div class="divider"></div>
				<div>
					<table class="table" width="99%" rel="jbsxBox">
						<thead>
							<tr>
								<th width="100">周期</th>
								<th width="100" class="system_manage_index_max">5日均偏(max)</th>
								<th width="100" class="system_manage_index_min">5日均偏(min)</th>
								<th width="100" class="system_manage_index_max">20日均偏(max)</th>
								<th width="100" class="system_manage_index_min">20日均偏(min)</th>
								<th width="100" class="system_manage_index_max">60日均偏(max)</th>
								<th width="100" class="system_manage_index_min">60日均偏(min)</th>
								<th width="100" class="system_manage_index_max">120日均偏(max)</th>
								<th width="100" class="system_manage_index_min">120日均偏(min)</th>
								<th width="100" class="system_manage_index_max">240日均偏(max)</th>
								<th width="100" class="system_manage_index_min">240日均偏(min)</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td>近3年</td>
								<td class="system_manage_index_max">{$_3years_pianyilv.max_ma5_plv/100}%</td>
								<td class="system_manage_index_min">{$_3years_pianyilv.min_ma5_plv/100}%</td>
								<td class="system_manage_index_max">{$_3years_pianyilv.max_ma20_plv/100}%</td>
								<td class="system_manage_index_min">{$_3years_pianyilv.min_ma20_plv/100}%</td>
								<td class="system_manage_index_max">{$_3years_pianyilv.max_ma60_plv/100}%</td>
								<td class="system_manage_index_min">{$_3years_pianyilv.min_ma60_plv/100}%</td>
								<td class="system_manage_index_max">{$_3years_pianyilv.max_ma120_plv/100}%</td>
								<td class="system_manage_index_min">{$_3years_pianyilv.min_ma120_plv/100}%</td>
								<td class="system_manage_index_max">{$_3years_pianyilv.max_ma240_plv/100}%</td>
								<td class="system_manage_index_min">{$_3years_pianyilv.min_ma240_plv/100}%</td>
							</tr>
							<tr>
								<td>近5年</td>
								<td class="system_manage_index_max">{$_5years_pianyilv.max_ma5_plv/100}%</td>
								<td class="system_manage_index_min">{$_5years_pianyilv.min_ma5_plv/100}%</td>
								<td class="system_manage_index_max">{$_5years_pianyilv.max_ma20_plv/100}%</td>
								<td class="system_manage_index_min">{$_5years_pianyilv.min_ma20_plv/100}%</td>
								<td class="system_manage_index_max">{$_5years_pianyilv.max_ma60_plv/100}%</td>
								<td class="system_manage_index_min">{$_5years_pianyilv.min_ma60_plv/100}%</td>
								<td class="system_manage_index_max">{$_5years_pianyilv.max_ma120_plv/100}%</td>
								<td class="system_manage_index_min">{$_5years_pianyilv.min_ma120_plv/100}%</td>
								<td class="system_manage_index_max">{$_5years_pianyilv.max_ma240_plv/100}%</td>
								<td class="system_manage_index_min">{$_5years_pianyilv.min_ma240_plv/100}%</td>
							</tr>
							<tr>
								<td>近10年</td>
								<td class="system_manage_index_max">{$_10years_pianyilv.max_ma5_plv/100}%</td>
								<td class="system_manage_index_min">{$_10years_pianyilv.min_ma5_plv/100}%</td>
								<td class="system_manage_index_max">{$_10years_pianyilv.max_ma20_plv/100}%</td>
								<td class="system_manage_index_min">{$_10years_pianyilv.min_ma20_plv/100}%</td>
								<td class="system_manage_index_max">{$_10years_pianyilv.max_ma60_plv/100}%</td>
								<td class="system_manage_index_min">{$_10years_pianyilv.min_ma60_plv/100}%</td>
								<td class="system_manage_index_max">{$_10years_pianyilv.max_ma120_plv/100}%</td>
								<td class="system_manage_index_min">{$_10years_pianyilv.min_ma120_plv/100}%</td>
								<td class="system_manage_index_max">{$_10years_pianyilv.max_ma240_plv/100}%</td>
								<td class="system_manage_index_min">{$_10years_pianyilv.min_ma240_plv/100}%</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-md-12 col-sm-12">
	
				<h1>CHICANG组</h1>
				<div class="divider"></div>
				<div>
					<table class="table" width="99%" rel="jbsxBox">
						<thead>
							<tr>
								<th width="5">#</th>
								<th width="100">名称</th>
								<th width="100">代号</th>
								<th width="60">当前价</th>
								<th width="60">成本价</th>
								<th width="60">浮盈</th>
								<th width="60">5日均偏率</th>
								<th width="60">20日均偏率</th>
								<th width="60">60日均偏率</th>
								<th width="60">240日均偏率</th>
								<th width="60">5日均角</th>
								<th width="60">20日均角</th>
								<th width="60">60日均角</th>
								<th width="60">240日均角</th>
								<th width="100">5uad复现率</th>
								<th width="100">20uad复现率</th>
								<th width="100">60uad复现率</th>
								<th width="100">240uad复现率</th>
								<th width="100">操作</th>
							</tr>
						</thead>
						<tbody>
							{foreach $chicang_group as $k=>$row}
							<tr>
								<td>{$k+1}</td>
								<td>{$row.title}</td>
								<td>{$row.code}</td>
								<td>--</td>
								<td>--</td>
								<td>--</td>
								<td>{if isset($row.ma5_plv)} {$row.ma5_plv/1000}% {else} -- {/if}</td>
								<td>{if isset($row.ma20_plv)} {$row.ma20_plv/1000}% {else} -- {/if}</td>
								<td>{if isset($row.ma60_plv)} {$row.ma60_plv/1000}% {else} -- {/if}</td>
								<td>{if isset($row.ma240_plv)} {$row.ma240_plv/1000}% {else} -- {/if}</td>
								<td>{if isset($row.ma5_angle)} {$row.ma5_angle} {else} -- {/if}</td>
								<td>{if isset($row.ma20_angle)} {$row.ma20_angle} {else} -- {/if}</td>
								<td>{if isset($row.ma60_angle)} {$row.ma60_angle} {else} -- {/if}</td>
								<td>{if isset($row.ma240_angle)} {$row.ma240_angle} {else} -- {/if}</td>
								<td>{if isset($row.ma5_plv_up)} {$row.ma5_plv_up}% {else} -- {/if} | {if isset($row.ma5_plv_dw)} {$row.ma5_plv_dw}% {else} -- {/if}</td>
								<td>{if isset($row.ma20_plv_up)} {$row.ma20_plv_up}% {else} -- {/if} | {if isset($row.ma20_plv_dw)} {$row.ma20_plv_dw}% {else} -- {/if}</td>
								<td>{if isset($row.ma60_plv_up)} {$row.ma60_plv_up}% {else} -- {/if} | {if isset($row.ma60_plv_dw)} {$row.ma60_plv_dw}% {else} -- {/if}</td>
								<td>{if isset($row.ma240_plv_up)} {$row.ma240_plv_up}% {else} -- {/if} | {if isset($row.ma240_plv_dw)} {$row.ma240_plv_dw}% {else} -- {/if}</td>
								<td></td>
							</tr>
							{/foreach}
						</tbody>
					</table>
				</div>
			</div>
		</div>

		<div class="divider"></div>

		<div class="row">
			<!-- <div class="row">
				<div class="col-md-12 col-sm-12">
					<h1>锁定目标</h1>
				</div>
			</div> -->

			<div class="row">
				<div class="col-md-3 col-sm-3">
					<div class="col-md-4 col-sm-4">股票代码或名称：</div>
					<div class="col-md-8 col-sm-8" style="margin-left: -40px;">
						<input type="text" id="system_manage_index_code_or_name" maxlength="20" size="30" class="required" style="margin-top: -5px;" />
					</div>
				</div>
				<div class="col-md-1 col-sm-1" style="margin-left: -120px; margin-top: 3px;">
					<div class="buttonActive"><div class="buttonContent system_manage_index_search_by_code_or_name"><button>搜索</button></div></div>
				</div>
				<div class="col-md-1 col-sm-1" style="margin-left: -60px; margin-top: 3px;">
					<div class="buttonActive"><div class="buttonContent system_manage_index_"><button>组列表</button></div></div>
				</div>
				<div class="col-md-1 col-sm-1" style="margin-left: -80px; margin-top: 3px;">
					<div class="buttonActive"><div class="buttonContent system_manage_index_"><button>添加组</button></div></div>
				</div>
			</div>

			<div class="divider"></div>

			<div class="row" id="system_manage_index_gp_table_div">
				<table class="table" width="99%" rel="jbsxBox">
					<thead>
						<tr>
							<th width="30">股票代码</th>
							<th width="50">股票名称</th>
							<th width="120">所属组</th>
							<th width="50">增减组</th>
							<th width="100">操作</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
						</tr>
					</tbody>
				</table>
			</div>

		</div>
<script type="text/javascript">

var system_manage_index_navtab = "system_manage_index";
var search_gp_url = "{Fun::L('/system/manage/gupiao/search/by/code/or/name')}";

{literal}

$('.'+system_manage_index_navtab+'_search_by_code_or_name').click(function () {
	var search_gp_url_post_data = {
		"code_or_name": $('#'+system_manage_index_navtab+'_code_or_name').val(),
	};
	$('#'+system_manage_index_navtab+'_gp_table_div').loadUrl(search_gp_url, search_gp_url_post_data, function (res) {
	
		// console.log(res);//将会得到返回的结果
	});
})

{/literal}

</script>

		<div class="divider"></div>

		<!-- <div class="row" style="padding: 0 10px;">
			<div class="col-md-3 col-sm-12" style="border: 1px dashed #f00">
				test1
			</div>
			<div class="col-md-3 col-sm-12" style="border: 1px dashed #f00;">
				<p>test2</p>
			</div>
			<div class="col-md-3 col-sm-12" style="border: 1px dashed #f00">
				test3
			</div>
			<div class="col-md-3 col-sm-12" style="border: 1px dashed #f00">
				test4
			</div>
		</div>
		<div class="row" style="padding: 0 10px;">
			<div class="col-md-3 col-sm-12" style="border: 1px dashed #f00">
				test1
			</div>
			<div class="col-md-3 col-sm-12" style="border: 1px dashed #f00;">
				<p>test2</p>
			</div>
			<div class="col-md-3 col-sm-12" style="border: 1px dashed #f00">
				test3
			</div>
			<div class="col-md-3 col-sm-12" style="border: 1px dashed #f00">
				test4
			</div>
		</div> -->

		<h2 class="subTitle">常用链接</h2>
		{foreach $nav_link as $k=>$v}
		<div class="row" style="padding: 0 10px;">
			{foreach $v as $k1=>$v1}
			<div class="col-md-1 col-sm-12">
				<a href="{$v1}" target="_blank">{$k1}</a>
			</div>
			{/foreach}
		</div>
		{/foreach}

		
		
	</div>
</div>