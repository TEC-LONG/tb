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
	
				<div class="panel collapse" defH="150">
					<h1>综合统计数据</h1>
					<div>
						
						<table class="table" width="99%" rel="jbsxBox">
							<thead>
								<tr>
									<th width="100">周期</th>
									<th width="100" class="system_manage_index_max">20日偏移率(max)</th>
									<th width="100" class="system_manage_index_min">20日偏移率(min)</th>
									<th width="100" class="system_manage_index_max">60日偏移率(max)</th>
									<th width="100" class="system_manage_index_min">60日偏移率(min)</th>
									<th width="100" class="system_manage_index_max">120日偏移率(max)</th>
									<th width="100" class="system_manage_index_min">120日偏移率(min)</th>
									<th width="100" class="system_manage_index_max">240日偏移率(max)</th>
									<th width="100" class="system_manage_index_min">240日偏移率(min)</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td>近3年</td>
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
		</div>

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