<?php require_once __DIR__ . '/../../core/init.php'; ?>
<?php
$info = '';$msg='';
class TestPhar {
    public function __destruct() {
        // 安全的演示操作，避免恶意利用
        echo "<p>PHAR反序列化触发成功！</p>";
       
    }
}
if (isset($_GET['path'])) {
    // 演示：调用 getimagesize 对用户可控路径（可能 phar://），触发PHAR元数据反序列化
    $path = $_GET['path'];
    try {
        $data = @getimagesize($path);
        $info = print_r($data, true);
    } catch (Throwable $e) {
        $msg = h($e->getMessage());
    }
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>PHAR 注入</title>
  <link rel="stylesheet" href="/assets/css/style.css" />
</head>
<body>
  <header class="nav"><div class="brand">PHAR 注入</div><div class="links"><a href="/">返回首页</a></div></header>
  <main class="container module">
    <div class="panel">
      <?php if($msg) echo '<p class="danger">'.$msg.'</p>'; ?>
      <form method="get">
        <div class="field"><label>图片路径</label><input name="path" placeholder="例如：phar:///绝对路径/evil.phar/test.jpg" /></div>
        <button class="btn btn-primary" type="submit">分析</button>
      </form>
    </div>
    <?php if($info): ?>
    <div class="panel"><h3>信息</h3><pre><?php echo h($info); ?></pre></div>
    <?php endif; ?>
    <div class="panel explain">
      <h3>原理解析</h3>
      <p>PHAR 利用源于对 <code>phar://</code> 包装器的支持：当对 PHAR 文件执行某些文件函数时，元数据会被反序列化。</p>
      <h3>常见利用方式</h3>
      <ul>
        <li>把可控路径指向 <code>phar://</code>，调用 <code>getimagesize</code>、<code>file_exists</code> 等触发元数据反序列化。</li>
        <li>配合 POP 链执行命令或写文件。</li>
      </ul>
      <h3>常见绕过与修复</h3>
      <ul>
        <li>修复：不要让用户输入直接进入文件函数路径，使用白名单与协议限制。</li>
        <li>修复：禁用 PHAR 包装器或在受控环境中仅允许特定协议。</li>
        <li>修复：消除可利用的反序列化 POP 链。</li>
      </ul>
    </div>
  </main>
</body>
</html>