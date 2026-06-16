<style>
  :root {
    --crm-bg:       #f8fafc;
    --crm-surface:  #ffffff;
    --crm-border:   #e2e8f0;
    --crm-text:     #0f172a;
    --crm-text2:    #475569;
    --crm-muted:    #94a3b8;
    --crm-accent:   #6366f1;
    --crm-accent2:  #818cf8;
    --crm-radius:   14px;
    --crm-radius-sm:10px;
    --crm-shadow:   0 1px 3px rgba(15,23,42,.06), 0 4px 14px rgba(15,23,42,.04);
    --crm-shadow-lg:0 4px 24px rgba(15,23,42,.10);
    --crm-ease:     cubic-bezier(.4,0,.2,1);
  }

  .crm-card {
    background: var(--crm-surface);
    border: 1px solid var(--crm-border);
    border-radius: var(--crm-radius);
    box-shadow: var(--crm-shadow);
    overflow: hidden;
    transition: box-shadow .2s var(--crm-ease);
  }
  .crm-card:hover { box-shadow: var(--crm-shadow-lg); }
  .crm-card-head {
    display: flex; align-items: center; justify-content: space-between;
    padding: 18px 22px 14px; border-bottom: 1px solid #f1f5f9;
  }
  .crm-card-title {
    display: flex; align-items: center; gap: 10px;
    font-size: 14px; font-weight: 700; color: var(--crm-text);
    margin: 0; line-height: 1.3;
  }
  .crm-card-title i { font-size: 15px; color: var(--crm-accent); opacity: .85; }
  .crm-card-body { padding: 20px 22px; }
  .crm-card-body-flush { padding: 0; }

  .crm-section {
    display: flex; align-items: center; gap: 14px;
    margin: 6px 0 16px; padding: 0 4px;
  }
  .crm-section-icon {
    width: 38px; height: 38px; border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    font-size: 16px; color: #fff; flex-shrink: 0;
    background: linear-gradient(135deg, var(--crm-accent), var(--crm-accent2));
  }
  .crm-section h3 {
    margin: 0; font-size: 15px; font-weight: 800;
    color: var(--crm-text); letter-spacing: -.01em;
  }
  .crm-section p { margin: 2px 0 0; font-size: 12px; color: var(--crm-muted); }

  .crm-kpi {
    border-radius: var(--crm-radius);
    padding: 22px 20px 18px;
    position: relative; overflow: hidden;
    color: #fff;
    transition: transform .2s var(--crm-ease), box-shadow .2s var(--crm-ease);
  }
  .crm-kpi:hover { transform: translateY(-2px); box-shadow: var(--crm-shadow-lg); }
  .crm-kpi-icon {
    position: absolute; right: 16px; top: 16px;
    font-size: 28px; opacity: .25;
  }
  .crm-kpi-label { font-size: 11px; font-weight: 600; opacity: .85; text-transform: uppercase; letter-spacing: .04em; }
  .crm-kpi-value { font-size: 26px; font-weight: 800; margin: 4px 0 2px; letter-spacing: -.02em; }
  .crm-kpi-sub { font-size: 12px; opacity: .8; }
  .crm-kpi-bar { height: 3px; background: rgba(255,255,255,.25); border-radius: 2px; margin-top: 12px; overflow: hidden; }
  .crm-kpi-bar span { display: block; height: 100%; background: rgba(255,255,255,.6); border-radius: 2px; }

  .crm-quick {
    display: inline-flex; align-items: center; gap: 8px;
    padding: 9px 16px; border-radius: var(--crm-radius-sm);
    border: 1px solid var(--crm-border); background: var(--crm-surface);
    color: var(--crm-text2); font-size: 13px; font-weight: 600;
    text-decoration: none; cursor: pointer;
    transition: all .15s var(--crm-ease);
  }
  .crm-quick:hover, .crm-quick:focus {
    border-color: var(--crm-accent); color: var(--crm-accent);
    text-decoration: none; box-shadow: var(--crm-shadow);
  }
  .crm-quick-primary {
    background: linear-gradient(135deg, var(--crm-accent), var(--crm-accent2));
    border-color: transparent; color: #fff;
  }
  .crm-quick-primary:hover { color: #fff; opacity: .92; }
  .crm-quick-icon {
    width: 28px; height: 28px; border-radius: 8px;
    display: flex; align-items: center; justify-content: center;
    background: rgba(99,102,241,.1); color: var(--crm-accent); font-size: 13px;
  }
  .crm-quick-primary .crm-quick-icon { background: rgba(255,255,255,.2); color: #fff; }

  .crm-table { width: 100%; border-collapse: collapse; }
  .crm-table thead th {
    background: #f8fafc; color: var(--crm-muted);
    font-size: 11px; font-weight: 700; text-transform: uppercase;
    letter-spacing: .04em; padding: 12px 14px;
    border-bottom: 1px solid var(--crm-border);
  }
  .crm-table tbody td {
    padding: 12px 14px; border-bottom: 1px solid #f1f5f9;
    color: var(--crm-text2); font-size: 13px; vertical-align: middle;
  }
  .crm-table tbody tr:hover { background: #fafbff; }
  .crm-table tbody tr.inv-row-highlight { background: #eef2ff !important; animation: invFlash 1.5s ease; }
  @keyframes invFlash { 0%,100%{ background:#eef2ff; } 50%{ background:#c7d2fe; } }
</style>
