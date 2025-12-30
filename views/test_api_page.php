<?php
/**
 * Template Name: Test Converter Page
 * Description: ТTimeshow Converter API.
 */
get_header();



$result = send_json_to_api('test123',TIME_DIR. '/uploads/conv/120.json', '1');
print_r($result);
// $result = send_json_to_tsc_api('/root/timeshow_converter_tool/Inputs/120.json', '1');
// print_r($result);

?>

<div style="max-width:800px;margin:40px auto;padding:20px;background:#f9fafb;border-radius:10px;">
  <h2>🔧 Тест Timeshow Converter API</h2>
  <p>Введи JSON для відправки на сервер:</p>

  <form id="uploadForm">
    <textarea id="jsonData" style="width:100%;height:180px;font-family:monospace;">
{
  "operation": "convert_time",
  "input_file": "example.json"
}
    </textarea><br><br>

    <button type="submit" style="background:#0078ff;color:#fff;padding:10px 15px;border:none;border-radius:6px;cursor:pointer;">
      ▶ Надіслати
    </button>
  </form>

  <h3>📤 Відповідь сервера:</h3>
  <pre id="result" style="background:#eee;padding:15px;border-radius:10px;margin-top:20px;">Очікування...</pre>
</div>

<script>
document.getElementById('uploadForm').addEventListener('submit', async (e) => {
  e.preventDefault();
  const jsonText = document.getElementById('jsonData').value;
  try {
    const res = await fetch('http://<IP_сервера>:8081/<endpoint>', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: jsonText
    });
    const data = await res.text();
    document.getElementById('result').textContent = data;
  } catch (err) {
    document.getElementById('result').textContent = '❌ Помилка: ' + err;
  }
});
</script>

<?php get_footer(); ?>
