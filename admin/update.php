<?php
$title = '版本信息';
include './head.php';
?>
<!--页面主要内容-->
<main class="lyear-layout-content">
	<div class="container-fluid">
		<div class="card">
			<div class="card-header"><h4>版本信息</h4></div>
			<ul class="list-group">
				<li class="list-group-item"><b>程序版本：</b>v<?php echo VERSION ?></li>
				<li class="list-group-item"><b>数据库版本：</b><?php echo isset($conf['version']) ? $conf['version'] : '未知'; ?></li>
				<li class="list-group-item"><b>运行环境：</b>PHP <?php echo PHP_VERSION ?> / MySQL</li>
				<li class="list-group-item"><b>项目地址：</b><a href="https://github.com/hanyuestar/homepage" target="_blank">https://github.com/hanyuestar/homepage</a></li>
			</ul>
		</div>
		<div class="card">
			<div class="card-header"><h4>说明</h4></div>
			<div class="card-body">
				<p class="text-muted">本版本为独立发行版，不依赖任何第三方更新服务器，程序升级请关注项目发布页并手动替换源码。</p>
			</div>
		</div>
	</div>
</main>
</div>
</div>
<?php
include './footer.php';
?>
