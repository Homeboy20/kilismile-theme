<?php
/**
 * Test PayPal Flow (Sandbox Diagnostic)
 * URL: /test-paypal-flow (if theme allows direct template load) or open directly.
 * Provides a quick readiness checklist and button to simulate creating a PayPal payment via unified processor.
 */

if (!defined('ABSPATH')) { exit; }

$client_id     = get_option('kilismile_paypal_client_id', '');
$client_secret = get_option('kilismile_paypal_client_secret', '');
$sandbox       = get_option('kilismile_paypal_sandbox', true);
$has_unified_ajax = has_action('wp_ajax_nopriv_kilismile_process_payment');

$status = [
  'Client ID Present' => !empty($client_id),
  'Client Secret Present' => !empty($client_secret),
  'Sandbox Mode' => $sandbox ? 'Yes' : 'No',
  'Unified Processor AJAX Registered' => $has_unified_ajax ? 'Yes' : 'No',
];

?><!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8" />
  <title>PayPal Flow Test</title>
  <style>
    body { font-family: Arial, sans-serif; background:#f5f7fa; padding:40px; }
    h1 { color:#2c7a3f; }
    .panel { background:#fff; padding:25px 30px; border-radius:12px; box-shadow:0 4px 16px rgba(0,0,0,0.08); max-width:780px; margin:0 auto 30px; }
    .status-grid { display:grid; grid-template-columns: repeat(auto-fit,minmax(240px,1fr)); gap:15px; margin-top:15px; }
    .status-item { padding:14px 16px; border:2px solid #e3e8ee; border-radius:10px; background:#fafbfc; font-size:14px; }
    .status-item.good { border-color:#28a745; background:#f3fcf6; }
    .status-item.bad { border-color:#dc3545; background:#fff5f5; }
    button { background:#28a745; color:#fff; border:none; padding:14px 26px; border-radius:8px; cursor:pointer; font-size:16px; font-weight:600; display:inline-flex; align-items:center; gap:10px; }
    button:disabled { background:#94a3b8; cursor:not-allowed; }
    pre { background:#1e293b; color:#f1f5f9; padding:16px; border-radius:8px; overflow:auto; font-size:12px; }
    .log { margin-top:25px; }
    .note { font-size:13px; color:#64748b; margin-top:8px; }
    .inline { display:inline-block; }
    a { color:#0d6efd; }
  </style>
</head>
<body>
  <div class="panel">
    <h1>PayPal Sandbox Readiness</h1>
    <p>This page helps verify the theme's legacy PayPal integration is wired and can initiate a sandbox payment through the unified processor. It won't auto-complete a payment, but it should redirect you to PayPal when working.</p>
    <div class="status-grid">
      <?php foreach ($status as $label => $val): $good = ($val === true || $val === 'Yes'); ?>
        <div class="status-item <?php echo $good ? 'good':'bad'; ?>">
          <strong><?php echo esc_html($label); ?></strong><br>
          <span><?php echo is_bool($val) ? ($val ? 'Yes' : 'No') : esc_html($val); ?></span>
        </div>
      <?php endforeach; ?>
    </div>
    <p class="note">Ensure sandbox credentials are correct. In Live mode replace them with production credentials before real donations.</p>
  </div>

  <div class="panel">
    <h2>Test $5 USD Donation (Sandbox)</h2>
    <p>Click the button below to attempt creating a PayPal payment. You should be redirected to PayPal if credentials are valid.</p>
    <button id="paypalTestBtn" <?php echo empty($client_id) || empty($client_secret) ? 'disabled' : ''; ?>>Create Test PayPal Payment →</button>
    <?php if (empty($client_id) || empty($client_secret)): ?>
      <p style="color:#dc3545; margin-top:10px;">Missing Client ID or Client Secret. Add them in the Donation Settings admin page first.</p>
    <?php endif; ?>
    <div class="note">This uses the same AJAX endpoint the live form uses: <code>kilismile_process_payment</code>.</div>
    <div class="log" id="log"></div>
  </div>

  <script>
  (function(){
    const btn = document.getElementById('paypalTestBtn');
    const logDiv = document.getElementById('log');
    if(!btn) return;

    function log(msg, raw){
      const pre = document.createElement('pre');
      pre.textContent = '[' + (new Date().toLocaleTimeString()) + '] ' + msg + (raw ? ('\n' + raw) : '');
      logDiv.prepend(pre);
    }

    btn.addEventListener('click', function(){
      btn.disabled = true;
      btn.textContent = 'Creating PayPal payment...';
      log('Initiating test payment request');
      const fd = new FormData();
      fd.append('action','kilismile_process_payment');
      fd.append('currency','USD');
      fd.append('amount','5');
      fd.append('donor_name','Test User');
      fd.append('donor_email','test@example.com');
      fd.append('anonymous','0');
      fd.append('payment_gateway','paypal');
      fd.append('nonce','<?php echo wp_create_nonce("kilismile_payment_nonce"); ?>');

      fetch('<?php echo esc_url(admin_url('admin-ajax.php')); ?>',{method:'POST',body:fd})
        .then(r=>r.text())
        .then(t=>{ log('Raw response', t); let data; try { data = JSON.parse(t);} catch(e){ throw new Error('Invalid JSON: '+ e.message);} return data; })
        .then(data=>{
          if(data.success){
            log('Success payload received. Redirecting in 1.5s...');
            if(data.redirect_url){
              setTimeout(()=>{ window.location.href = data.redirect_url; },1500);
            } else {
              log('No redirect_url provided. Full payload:' , JSON.stringify(data,null,2));
              btn.disabled = false; btn.textContent='Create Test PayPal Payment →';
            }
          } else {
            log('Error: ' + (data.message || 'Unknown error')); btn.disabled=false; btn.textContent='Create Test PayPal Payment →';
          }
        })
        .catch(err=>{ log('Fetch/Processing Error: ' + err.message); btn.disabled=false; btn.textContent='Create Test PayPal Payment →'; });
    });
  })();
  </script>
</body>
</html>


