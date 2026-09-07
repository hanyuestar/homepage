<?php
$title = '文件校验';
include './head.php';
?>
<!--页面主要内容-->
<main class="lyear-layout-content">
	<div class="container-fluid">
		<div class="card">
			<div class="card-header"><h4>文件校验</h4></div>
			<div class="card-body">
				<div class="alert alert-info" role="alert">
					<i class="mdi mdi-information mdi-alert-icon"></i>
					本版本为独立发行版，已移除远程文件校验服务，改为本地模式。
				</div>
				<p class="text-muted">如需校验程序文件完整性，可将本地文件与项目发布页提供的源码包进行比对。</p>
				<p class="text-muted">当前程序版本：v<?php echo VERSION ?></p>
				<p class="text-muted">当前 PHP 版本：<?php echo PHP_VERSION ?></p>
			</div>
		</div>
	</div>
</main>
</div>
</div>
<?php
include './footer.php';
?>
