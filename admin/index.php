<?php
$title = '后台管理';
include './head.php';


$check = isset($_GET['check']) ? $_GET['check'] : null;
function tjsj($tjname)
{
	if ($tjname == '') {
		echo '0';
	} else {
		echo $tjname;
	}
}
?>
<!--页面主要内容-->
<main class="lyear-layout-content">
	<div class="container-fluid">
		<?php if (defined('DEBUG') && DEBUG === true): ?>
			<div class="alert alert-warning" role="alert">
				<i class="mdi mdi-alert mdi-alert-icon"></i>
				<b>调试模式（DEBUG）已开启</b>：页面将显示详细错误信息，仅排障时使用，正式环境请前往<a href="./user.php#system-security" class="alert-link">系统安全</a>关闭。
			</div>
		<?php endif; ?>
		<style>
			.notice-card .card-header {
				position: relative;
			}

			.notice-card .notice-close {
				position: absolute;
				right: 16px;
				top: 50%;
				transform: translateY(-50%);
			}
		</style>
		<?php // 通知公告位（独立版本，默认无远程公告） ?>
		<script type="text/javascript">
			$(document).ready(function() {
				var NOTICE_EXPIRE_MS = 24 * 60 * 60 * 1000; // 关闭后24小时内不再显示
				$('.notice-close').each(function() {
					var key = 'homepage_notice_closed_' + $(this).data('notice');
					var closedAt = parseInt(localStorage.getItem(key) || '0', 10);
					if (closedAt > 0 && (Date.now() - closedAt) < NOTICE_EXPIRE_MS) {
						$(this).closest('.notice-card').hide();
					}
				});
				$('.notice-close').on('click', function() {
					var key = 'homepage_notice_closed_' + $(this).data('notice');
					localStorage.setItem(key, String(Date.now()));
					$(this).closest('.notice-card').fadeOut(200);
				});
			});
		</script>
		<div class="row">
			<div class="col-sm-6 col-lg-3">
				<div class="card bg-primary">
					<div class="card-body clearfix">
						<div class="float-end">
							<p class="h6 text-white m-t-0">链接数量</p>
							<p class="h3 text-white m-b-0 fa-1-5x"><?php tjsj($linksrows);
																	?></p>
						</div>
						<div class="float-start"> <span class="img-avatar img-avatar-48 bg-translucent"><i class="mdi mdi-web fa-1-5x"></i></span> </div>
					</div>
				</div>
			</div>
			<div class="col-sm-6 col-lg-3">
				<div class="card bg-danger">
					<div class="card-body clearfix">
						<div class="float-end">
							<p class="h6 text-white m-t-0">今日浏览量</p>
							<p class="h3 text-white m-b-0 fa-1-5x"><?php tjsj($tjtoday);
																	?></p>
						</div>
						<div class="float-start"> <span class="img-avatar img-avatar-48 bg-translucent"><i class="mdi mdi-account fa-1-5x"></i></span> </div>
					</div>
				</div>
			</div>
			<div class="col-sm-6 col-lg-3">
				<div class="card bg-success">
					<div class="card-body clearfix">
						<div class="float-end">
							<p class="h6 text-white m-t-0">今日独立IP</p>
							<p class="h3 text-white m-b-0 fa-1-5x"><?php tjsj($tjtodayip);
																	?></p>
						</div>
						<div class="float-start"> <span class="img-avatar img-avatar-48 bg-translucent"><i class="mdi mdi-account-network fa-1-5x"></i></span> </div>
					</div>
				</div>
			</div>
			<div class="col-sm-6 col-lg-3">
				<div class="card bg-purple">
					<div class="card-body clearfix">
						<div class="float-end">
							<p class="h6 text-white m-t-0">累计浏览量</p>
							<p class="h3 text-white m-b-0 fa-1-5x"><?php tjsj($tjtotal);
																	?></p>
						</div>
						<div class="float-start"> <span class="img-avatar img-avatar-48 bg-translucent"><i class="mdi mdi-account-multiple fa-1-5x"></i></span> </div>
					</div>
				</div>
			</div>
		</div>
		<?php if ($applyrows > 0): ?>
			<div class="row">
				<div class="col-sm-6 col-lg-12">
					<div class="card bg-info">
						<div class="card-body clearfix">
							<a href="./apply.php">
								<div class="float-end">
									<p class="h6 text-white m-t-0">待审核链接</p>
									<p class="h3 text-white m-b-0 fa-1-5x"><?php echo $applyrows; ?></p>
								</div>
							</a>
							<div class="float-start"> <span class="img-avatar img-avatar-48 bg-translucent"><i class="mdi mdi-link fa-1-5x"></i></span> </div>
						</div>
					</div>
				</div>
			</div>
		<?php endif; ?>
		<div class="row">
			<div class="col-lg-6">
				<div class="card">
					<div class="card-header">
						<h4>近7天独立IP统计</h4>
					</div>
					<div class="card-body">
						<canvas class="js-chartjs-bars"></canvas>
					</div>
				</div>
			</div>
			<div class="col-lg-6">
				<div class="card">
					<div class="card-header">
						<h4>近7天浏览量统计</h4>
					</div>
					<div class="card-body">
						<canvas class="js-chartjs-lines"></canvas>
					</div>
				</div>
			</div>
		</div>
		<div class="card">
			<div class="card-header">
				<h4>服务器信息</h4>
			</div>
			<ul class="list-group">
				<li class="list-group-item">
					<b>程序名称：</b>Homepage(Homepage)
				</li>
				<li class="list-group-item">
					<b>主程序版本：</b>v<?php echo VERSION ?> <a href="./update.php" target="_blank">检查更新</a>
				</li>
				<li class="list-group-item">
					<b>数据库版本：</b><?php echo $conf['version'] ?>
				</li>
				<li class="list-group-item">
					<b>最新版本：</b> <?php echo isset($update['version']) ? $update['version'] : '未知'; ?> 更新日志
				</li>
				<?php
				$isWin   = stripos(PHP_OS, 'WIN') === 0;
				$sysName = $isWin ? 'Windows' : 'Linux';
				$kernel  = php_uname('r');
				$arch    = php_uname('m'); // x86_64 / aarch64
				?>
				<li class="list-group-item">
					<b>操作系统：</b>
					<?php echo "$sysName ({$kernel}, {$arch})"; ?>
				</li>
				<li class="list-group-item">
					<b>PHP版本：</b><?php echo phpversion() ?>
					<?php if (ini_get('safe_mode')) {
						echo '线程安全';
					} else {
						echo '非线程安全';
					}
					?>
				</li>
				<li class="list-group-item">
					<b>MySQL版本：</b><?php echo $DB->count("select VERSION()") ?>
				</li>
				<li class="list-group-item">
					<b>服务器软件：</b><?php echo $_SERVER['SERVER_SOFTWARE'] ?>
				</li>
				<li class="list-group-item">
					<b>建站时间：</b><?php echo $conf['build'] ?>
				</li>
				<li class="list-group-item">
					<b>项目地址：</b><a href="https://github.com/hanyuestar/homepage" target="_blank">github.com/hanyuestar/homepage</a>
				</li>
			</ul>
		</div>
	</div>
</main>
<!--End 页面主要内容-->
</div>
</div>
<?php
include './footer.php';
?>
<!--图表插件-->
<script type="text/javascript" src="/assets/admin/js/Chart.js"></script>
<script type="text/javascript">
	$(document).ready(function(e) {
		var $dashChartBarsCnt = jQuery('.js-chartjs-bars')[0].getContext('2d'),
			$dashChartLinesCnt = jQuery('.js-chartjs-lines')[0].getContext('2d');
		var $dashChartBarsData = {
			labels: <?php echo json_encode($tj_chart_labels, JSON_UNESCAPED_UNICODE); ?>,
			datasets: [{
				label: '独立IP',
				borderWidth: 1,
				borderColor: 'rgba(0,0,0,0)',
				backgroundColor: 'rgba(59,130,246,0.5)',
				hoverBackgroundColor: "rgba(59,130,246,0.7)",
				hoverBorderColor: "rgba(0,0,0,0)",
				data: <?php echo json_encode($tj_chart_ip); ?>
			}]
		};
		var $dashChartLinesData = {
			labels: <?php echo json_encode($tj_chart_labels, JSON_UNESCAPED_UNICODE); ?>,
			datasets: [{
				label: '浏览量(PV)',
				data: <?php echo json_encode($tj_chart_pv); ?>,
				borderColor: '#358ed7',
				backgroundColor: 'rgba(53, 142, 215, 0.175)',
				borderWidth: 1,
				fill: false,
				lineTension: 0
			}]
		};
		new Chart($dashChartBarsCnt, {
			type: 'bar',
			data: $dashChartBarsData
		});
		var myLineChart = new Chart($dashChartLinesCnt, {
			type: 'line',
			data: $dashChartLinesData,
		});
	});
	$(function() {
		const start = Date.now();
		console.log("Dashboard ready: " + (Date.now() - start) + "ms");
	});
</script>