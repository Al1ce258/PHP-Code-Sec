<?php require_once __DIR__ . '/../../core/init.php'; ?>
<?php
$output = '';$msg='';
if (isset($_GET['host'])) {
    // 演示：命令注入（Windows ping），例如：127.0.0.1 & calc
    $host = $_GET['host'];
    $cmd = 'ping -n 1 ' . $host; // Windows -n 1
    $output = shell_exec($cmd);
      if (stristr(PHP_OS, 'WIN')) {
        // 将 GBK 编码转换为 UTF-8 编码
        // 使用 //IGNORE 参数忽略无法转换的字符，避免报错
        $output = iconv('GBK', 'UTF-8//IGNORE', $output);
    }
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>命令注入</title>
  <link rel="stylesheet" href="/assets/css/style.css" />
</head>
<body>
  <header class="nav"><div class="brand">命令注入</div><div class="links"><a href="/">返回首页</a></div></header>
  <main class="container module">
    <div class="panel">
      <form method="get">
        <div class="field"><label>主机</label><input name="host" placeholder="例如：127.0.0.1 & calc" /></div>
        <button class="btn btn-primary" type="submit">PING</button>
      </form>
    </div>
    <?php if($output): ?>
    <div class="panel"><h3>输出</h3> <pre><?php echo htmlspecialchars($output); ?></pre></div>
    <?php endif; ?>
    <div class="panel explain">
      <h3>原理解析</h3>
      <p>命令注入发生在将用户输入拼接到系统命令时，攻击者可通过连接符、子命令等执行额外指令。</p>
      <h3>常见利用方式</h3>
      <ul>
        <li>连接符：<code>&amp;</code>、<code>&&</code>、<code>||</code>、<code>;</code>，在 Windows 与类 Unix 下皆可用。</li>
        <li>子命令：<code>$(...)</code>、反引号 <code>`...`</code>，或通过文件重定向。</li>
        <li>绕过：编码/引号、环境变量与特殊字符，改变命令语义。</li>
      </ul>
      <h3>常见绕过与修复</h3>
      <ul>
        <li>修复：避免 shell 拼接，改用进程 API（如 <code>proc_open</code>）并传递参数数组。</li>
        <li>修复：严格校验输入（白名单主机/IP），拒绝连接符与特殊字符。</li>
        <li>修复：最小权限原则，禁用危险命令并限制执行环境。</li>
      </ul>
    </div>
  </main>
</body>
</html>