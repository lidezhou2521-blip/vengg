<?php
require_once('../../server/authen.php');
?>
<!doctype html>
<html lang="th">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>ตรวจสอบเวร</title>
  <style>
    [v-cloak]>* {
      display: none;
    }

    [v-cloak]::before {
      content: "กำลังโหลด...";
      display: block;
      text-align: center;
      padding: 40px;
      font-size: 18px;
      color: #666;
    }

    @font-face {
      font-family: Sarabun;
      src: url(../../assets/fonts/Sarabun/Sarabun-Regular.ttf);
    }

    * {
      font-family: Sarabun, sans-serif;
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    body {
      background: #f0f4f8;
      min-height: 100vh;
    }

    /* ===== TOP BAR ===== */
    .topbar {
      position: sticky;
      top: 0;
      z-index: 100;
      background: linear-gradient(135deg, #1a237e 0%, #283593 60%, #3949ab 100%);
      color: #fff;
      padding: 12px 20px;
      display: flex;
      align-items: center;
      gap: 12px;
      box-shadow: 0 2px 12px rgba(0, 0, 0, 0.2);
    }

    .topbar h1 {
      font-size: 18px;
      font-weight: 700;
      letter-spacing: 0.5px;
    }

    .topbar .month-badge {
      background: rgba(255, 255, 255, 0.2);
      border: 1px solid rgba(255, 255, 255, 0.4);
      border-radius: 20px;
      padding: 4px 14px;
      font-size: 14px;
      font-weight: 600;
    }

    .topbar .total-badge {
      margin-left: auto;
      background: #43a047;
      border-radius: 20px;
      padding: 6px 18px;
      font-size: 15px;
      font-weight: 700;
      letter-spacing: 0.5px;
      box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
    }

    .topbar .close-btn {
      background: rgba(255, 255, 255, 0.15);
      border: 1px solid rgba(255, 255, 255, 0.3);
      color: #fff;
      padding: 6px 14px;
      border-radius: 8px;
      cursor: pointer;
      font-size: 14px;
      text-decoration: none;
      transition: background 0.2s;
    }

    .topbar .close-btn:hover {
      background: rgba(255, 255, 255, 0.3);
    }

    /* ===== SEARCH BAR ===== */
    .searchbar {
      padding: 12px 20px;
      background: #fff;
      border-bottom: 1px solid #e0e0e0;
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .searchbar input {
      flex: 1;
      border: 1px solid #c5cae9;
      border-radius: 8px;
      padding: 8px 14px;
      font-size: 14px;
      outline: none;
      transition: border 0.2s;
    }

    .searchbar input:focus {
      border-color: #3949ab;
    }

    .searchbar .count-info {
      font-size: 13px;
      color: #666;
      white-space: nowrap;
    }

    /* ===== PERSON CARDS ===== */
    .persons-container {
      padding: 12px 16px;
      display: flex;
      flex-direction: column;
      gap: 10px;
    }

    .person-card {
      background: #fff;
      border-radius: 12px;
      box-shadow: 0 1px 6px rgba(0, 0, 0, 0.07);
      overflow: hidden;
      border: 1px solid #e8eaf6;
      transition: box-shadow 0.2s;
    }

    .person-card:hover {
      box-shadow: 0 3px 14px rgba(57, 73, 171, 0.12);
    }

    .person-header {
      display: flex;
      align-items: center;
      padding: 10px 16px;
      background: linear-gradient(90deg, #e8eaf6 0%, #f5f5f5 100%);
      cursor: pointer;
      user-select: none;
      gap: 10px;
    }

    .person-header .avatar {
      width: 34px;
      height: 34px;
      border-radius: 50%;
      background: #3949ab;
      color: #fff;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 14px;
      font-weight: 700;
      flex-shrink: 0;
    }

    .person-header .person-name {
      font-size: 15px;
      font-weight: 700;
      color: #1a237e;
      flex: 1;
    }

    .person-header .person-total {
      font-size: 14px;
      font-weight: 700;
      color: #2e7d32;
      background: #e8f5e9;
      border-radius: 12px;
      padding: 3px 12px;
    }

    .person-header .chevron {
      color: #9fa8da;
      font-size: 14px;
      transition: transform 0.2s;
    }

    .person-header.expanded .chevron {
      transform: rotate(180deg);
    }

    /* Duty rows inside card */
    .duty-list {
      border-top: 1px solid #eeeeee;
    }

    .duty-row {
      display: flex;
      align-items: flex-start;
      padding: 10px 16px;
      border-bottom: 1px solid #f5f5f5;
      gap: 10px;
    }

    .duty-row:last-child {
      border-bottom: none;
    }

    .duty-name {
      font-size: 13px;
      font-weight: 600;
      color: #37474f;
      width: 140px;
      flex-shrink: 0;
      padding-top: 2px;
    }

    .duty-name .no-claim-tag {
      display: inline-block;
      background: #ffebee;
      color: #c62828;
      border-radius: 4px;
      font-size: 10px;
      padding: 1px 5px;
      margin-left: 4px;
    }

    /* Day grid */
    .day-grid {
      display: flex;
      flex-wrap: wrap;
      gap: 4px;
      flex: 1;
    }

    .day-chip {
      width: 30px;
      height: 30px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 12px;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.15s;
      border: 2px solid transparent;
      user-select: none;
    }

    .day-chip.active {
      background: #3949ab;
      color: #fff;
      box-shadow: 0 2px 6px rgba(57, 73, 171, 0.35);
    }

    .day-chip.active:hover {
      background: #c62828;
      box-shadow: 0 2px 6px rgba(198, 40, 40, 0.3);
    }

    .day-chip.excluded {
      background: #ffebee;
      color: #c62828;
      border-color: #ef9a9a;
      text-decoration: line-through;
      opacity: 0.75;
    }

    .day-chip.excluded:hover {
      background: #c62828;
      color: #fff;
      border-color: #c62828;
    }

    .duty-total {
      font-size: 13px;
      font-weight: 700;
      color: #2e7d32;
      width: 80px;
      text-align: right;
      flex-shrink: 0;
      padding-top: 6px;
    }

    .duty-total.zero {
      color: #bdbdbd;
    }

    .excluded-duty-row {
      background-color: #fff8f8;
      border-bottom: 1px solid #ffcdd2;
    }

    .duty-name-excl {
      font-size: 12px;
      color: #c62828;
      font-style: italic;
      width: 140px;
      flex-shrink: 0;
      padding-top: 2px;
    }

    /* ===== EMPTY STATE ===== */
    .empty-state {
      text-align: center;
      padding: 60px 20px;
      color: #90a4ae;
    }

    .empty-state .icon {
      font-size: 48px;
      margin-bottom: 16px;
    }

    .empty-state p {
      font-size: 16px;
    }

    /* ===== FOOTER ===== */
    .footer-bar {
      position: sticky;
      bottom: 0;
      background: #fff;
      border-top: 2px solid #e8eaf6;
      padding: 12px 20px;
      display: flex;
      align-items: center;
      gap: 12px;
      box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.07);
    }

    .footer-bar .grand-label {
      font-size: 15px;
      font-weight: 600;
      color: #37474f;
    }

    .footer-bar .grand-amount {
      font-size: 22px;
      font-weight: 700;
      color: #1a237e;
      margin-left: auto;
    }

    .footer-bar .save-hint {
      font-size: 12px;
      color: #43a047;
      display: flex;
      align-items: center;
      gap: 4px;
    }

    /* Legend */
    .legend {
      padding: 8px 20px;
      background: #fafafa;
      border-bottom: 1px solid #e0e0e0;
      display: flex;
      gap: 16px;
      font-size: 12px;
      color: #666;
      align-items: center;
    }

    .legend-item {
      display: flex;
      align-items: center;
      gap: 6px;
    }

    .legend-dot {
      width: 20px;
      height: 20px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 10px;
      font-weight: 700;
    }

    /* ===== CLAIM MODAL ===== */
    .modal-overlay {
      position: fixed;
      inset: 0;
      background: rgba(0, 0, 0, 0.45);
      z-index: 999;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 16px;
      animation: fadeIn 0.15s ease;
    }

    @keyframes fadeIn {
      from {
        opacity: 0
      }

      to {
        opacity: 1
      }
    }

    .modal-box {
      background: #fff;
      border-radius: 16px;
      width: 100%;
      max-width: 400px;
      box-shadow: 0 20px 60px rgba(0, 0, 0, 0.25);
      overflow: hidden;
      animation: slideUp 0.2s ease;
    }

    @keyframes slideUp {
      from {
        transform: translateY(30px);
        opacity: 0
      }

      to {
        transform: translateY(0);
        opacity: 1
      }
    }

    .modal-head {
      background: linear-gradient(135deg, #1a237e 0%, #3949ab 100%);
      color: #fff;
      padding: 16px 20px;
      display: flex;
      align-items: center;
      justify-content: space-between;
    }

    .modal-head h3 {
      font-size: 15px;
      font-weight: 700;
      margin: 0;
    }

    .modal-head .m-close {
      background: rgba(255, 255, 255, 0.2);
      border: none;
      color: #fff;
      width: 28px;
      height: 28px;
      border-radius: 50%;
      cursor: pointer;
      font-size: 14px;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .modal-info {
      padding: 16px 20px 8px;
      display: flex;
      flex-direction: column;
      gap: 8px;
    }

    .modal-info .info-row {
      display: flex;
      align-items: baseline;
      gap: 8px;
      font-size: 13px;
    }

    .modal-info .info-label {
      color: #90a4ae;
      font-size: 11px;
      font-weight: 600;
      text-transform: uppercase;
      min-width: 80px;
      flex-shrink: 0;
    }

    .modal-info .info-value {
      color: #263238;
      font-weight: 600;
    }

    .modal-info .info-value.highlight {
      color: #1a237e;
      font-size: 15px;
    }

    .claim-options {
      display: flex;
      gap: 10px;
      padding: 8px 20px 16px;
    }

    .claim-opt {
      flex: 1;
      border: 2px solid #e0e0e0;
      border-radius: 12px;
      padding: 12px 10px;
      text-align: center;
      cursor: pointer;
      transition: all 0.18s;
      user-select: none;
    }

    .claim-opt:hover {
      border-color: #9fa8da;
      background: #f5f5f5;
    }

    .claim-opt.selected-claim {
      border-color: #43a047;
      background: #e8f5e9;
    }

    .claim-opt.selected-no {
      border-color: #e53935;
      background: #ffebee;
    }

    .claim-opt .opt-icon {
      font-size: 24px;
      margin-bottom: 4px;
    }

    .claim-opt .opt-title {
      font-size: 13px;
      font-weight: 700;
    }

    .claim-opt .opt-price {
      font-size: 11px;
      color: #78909c;
      margin-top: 2px;
    }

    .modal-foot {
      padding: 12px 20px;
      display: flex;
      gap: 10px;
      border-top: 1px solid #f0f0f0;
    }

    .modal-foot .btn-cancel {
      flex: 1;
      padding: 10px;
      border: 1px solid #e0e0e0;
      border-radius: 8px;
      background: #fff;
      cursor: pointer;
      font-size: 14px;
      color: #546e7a;
      transition: background 0.15s;
    }

    .modal-foot .btn-cancel:hover {
      background: #f5f5f5;
    }

    .modal-foot .btn-confirm {
      flex: 2;
      padding: 10px;
      border: none;
      border-radius: 8px;
      background: linear-gradient(135deg, #1a237e, #3949ab);
      color: #fff;
      font-size: 14px;
      font-weight: 700;
      cursor: pointer;
      transition: opacity 0.15s;
    }

    .modal-foot .btn-confirm:hover {
      opacity: 0.88;
    }

    /* VEN DETAIL MODAL */
    .ven-modal-overlay {
      position: fixed;
      inset: 0;
      background: rgba(0, 0, 0, 0.45);
      z-index: 9999;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .ven-modal-box {
      background: #fff;
      border-radius: 16px;
      width: 420px;
      max-width: 95vw;
      box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
      overflow: hidden;
    }

    .ven-modal-head {
      background: linear-gradient(135deg, #1a237e, #3949ab);
      color: #fff;
      padding: 16px 20px;
      display: flex;
      align-items: center;
      justify-content: space-between;
    }

    .ven-modal-head h3 {
      margin: 0;
      font-size: 16px;
      font-weight: 700;
    }

    .ven-modal-head .m-close {
      background: rgba(255, 255, 255, 0.2);
      border: none;
      color: #fff;
      border-radius: 50%;
      width: 28px;
      height: 28px;
      cursor: pointer;
      font-size: 16px;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .ven-modal-body {
      padding: 0;
    }

    .ven-modal-body table {
      width: 100%;
      border-collapse: collapse;
      font-size: 13px;
    }

    .ven-modal-body table th {
      background: #f5f5f5;
      padding: 10px 14px;
      text-align: left;
      font-weight: 600;
      color: #455a64;
      width: 120px;
      border-bottom: 1px solid #eee;
    }

    .ven-modal-body table td {
      padding: 10px 14px;
      border-bottom: 1px solid #eee;
      color: #263238;
    }

    .ven-modal-body table tr:last-child th,
    .ven-modal-body table tr:last-child td {
      border-bottom: none;
    }

    .ven-modal-body select {
      width: 100%;
      padding: 6px 8px;
      border-radius: 6px;
      border: 1px solid #cfd8dc;
      font-size: 13px;
    }

    .ven-modal-loading {
      padding: 30px;
      text-align: center;
      color: #78909c;
      font-size: 14px;
    }

    .ven-modal-foot {
      padding: 12px 16px;
      border-top: 1px solid #f0f0f0;
      display: flex;
      justify-content: flex-end;
      gap: 8px;
    }

    .ven-modal-foot .btn-close-ven {
      padding: 8px 20px;
      border: 1px solid #cfd8dc;
      border-radius: 8px;
      background: #fff;
      cursor: pointer;
      font-size: 13px;
      color: #546e7a;
    }

    .ven-modal-foot .btn-close-ven:hover {
      background: #f5f5f5;
    }

    .ven-modal-foot .btn-del-ven {
      padding: 8px 20px;
      border: none;
      border-radius: 8px;
      background: #e53935;
      color: #fff;
      cursor: pointer;
      font-size: 13px;
      font-weight: 600;
      transition: background 0.15s;
    }

    .ven-modal-foot .btn-del-ven:hover {
      background: #c62828;
    }

    .ven-modal-foot .btn-del-ven:disabled {
      opacity: 0.5;
      cursor: not-allowed;
    }

    .btn-dis-ven {
      padding: 4px 10px;
      border: none;
      border-radius: 6px;
      background: #ff9800;
      color: #fff;
      cursor: pointer;
      font-size: 11px;
      font-weight: 600;
      margin-left: 8px;
      transition: background 0.15s;
    }

    .btn-dis-ven:hover {
      background: #f57c00;
    }

    .btn-dis-ven.open {
      background: #43a047;
    }

    .btn-dis-ven.open:hover {
      background: #388e3c;
    }

    .status-tag {
      font-size: 11px;
      padding: 2px 8px;
      border-radius: 4px;
      margin-left: 6px;
    }

    .status-tag.disabled {
      background: #ffebee;
      color: #c62828;
    }

    .status-tag.active {
      background: #e8f5e9;
      color: #2e7d32;
    }

    .claim-badge {
      display: inline-block;
      padding: 4px 12px;
      border-radius: 6px;
      font-size: 13px;
      font-weight: 700;
    }

    .claim-badge.yes {
      background: #e8f5e9;
      color: #2e7d32;
    }

    .claim-badge.no {
      background: #ffebee;
      color: #c62828;
    }

    .ven-modal-foot .btn-save-ven {
      padding: 8px 20px; border: none; border-radius: 8px;
      background: #1565c0; color: #fff; cursor: pointer;
      font-size: 13px; font-weight: 700; transition: background 0.15s;
      min-width: 100px;
    }
    .ven-modal-foot .btn-save-ven:hover { background: #0d47a1; }
    .ven-modal-foot .btn-save-ven:disabled { opacity: 0.6; cursor: not-allowed; }

    /* Violation styling */
    .day-chip.violation {
      background: #ff9800 !important;
      border-color: #ef6c00 !important;
      color: #fff !important;
      animation: pulse-warning 2s infinite;
    }

    @keyframes pulse-warning {
      0% { box-shadow: 0 0 0 0 rgba(255, 152, 0, 0.6); }
      70% { box-shadow: 0 0 0 8px rgba(255, 152, 0, 0); }
      100% { box-shadow: 0 0 0 0 rgba(255, 152, 0, 0); }
    }

    .day-chip.resolved {
      background: #43a047 !important;
      color: #fff !important;
      border-color: #2e7d32 !important;
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }

    .violation-badge {
      background: #fff3e0;
      color: #e65100;
      border: 1px solid #ffe0b2;
      padding: 2px 8px;
      border-radius: 4px;
      font-size: 11px;
      font-weight: 700;
      display: flex;
      align-items: center;
      gap: 4px;
    }

    .violation-badge i {
      font-style: normal;
    }

    /* Custom Tooltip */
    .day-chip {
      position: relative;
    }
    .custom-tooltip {
      visibility: hidden;
      width: 220px;
      background-color: #333;
      color: #fff;
      text-align: left;
      border-radius: 8px;
      padding: 10px;
      position: absolute;
      z-index: 1001;
      bottom: 135%;
      left: 50%;
      margin-left: -110px;
      opacity: 0;
      transition: opacity 0.3s;
      font-size: 11px;
      line-height: 1.4;
      box-shadow: 0 5px 15px rgba(0,0,0,0.3);
      pointer-events: none;
    }
    .custom-tooltip::after {
      content: "";
      position: absolute;
      top: 100%;
      left: 50%;
      margin-left: -5px;
      border-width: 5px;
      border-style: solid;
      border-color: #333 transparent transparent transparent;
    }
    .day-chip:hover .custom-tooltip {
      visibility: visible;
      opacity: 1;
    }
    .custom-tooltip strong {
      display: block;
      color: #ffb74d;
      margin-bottom: 4px;
      font-size: 12px;
    }
    .custom-tooltip ul {
      margin: 0;
      padding-left: 15px;
      list-style-type: disc;
    }
    .custom-tooltip li {
      margin-bottom: 2px;
    }
    .custom-tooltip .hint {
      margin-top: 6px;
      display: block;
      color: #aaa;
      font-style: italic;
      border-top: 1px solid #444;
      padding-top: 4px;
    }
  </style>
</head>

<body>
  <div id="app" v-cloak>

    <!-- TOP BAR -->
    <div class="topbar">
      <div>
        <div style="font-size:11px; opacity:0.7; margin-bottom:2px;">สรุปเวรแยกประเภท</div>
        <h1>✅ ตรวจสอบเวรก่อนเบิกจ่าย</h1>
      </div>
      <span class="month-badge" v-if="datas.ven_month_th">📅 {{datas.ven_month_th}}</span>
      <span class="total-badge">฿ {{formatNum(datas.grand_total)}}</span>
      <a href="javascript:window.close()" class="close-btn">✕ ปิด</a>
    </div>

    <!-- LEGEND -->
    <div class="legend">
      <span style="font-weight:600; color:#37474f;">คลิกตัวเลขวันที่เพื่อดูรายละเอียด:</span>
      <span class="legend-item">
        <span class="legend-dot" style="background:#3949ab; color:#fff;">9</span>
        = เบิกจ่าย
      </span>
      <span class="legend-item">
        <span class="legend-dot" style="background:#ffebee; color:#c62828; border:2px solid #ef9a9a;">✕</span>
        = ไม่เบิกจ่าย
      </span>
      <span class="legend-item">
        <span class="legend-dot violation" style="background:#ff9800; color:#fff;">!</span>
        = เบิกซ้อน (>1 ประเภท/วัน)
      </span>
      <span class="legend-item">
        <span class="legend-dot" style="background:#43a047; color:#fff;">✓</span>
        = แก้ไขแล้ว (เหลือ 1 ประเภท)
      </span>
      <span v-if="lastSaved" style="margin-left:auto; color:#43a047; font-size:12px;">✓ บันทึกแล้ว {{lastSaved}}</span>
    </div>

    <!-- SEARCH BAR -->
    <div class="searchbar">
      <input v-model="search" placeholder="🔍 ค้นหาชื่อ หรือ ประเภทเวร..." />
      <span class="count-info">พบ {{filteredPersons.length}} / {{datas.persons ? datas.persons.length : 0}} คน</span>
    </div>

    <!-- PERSONS LIST -->
    <div class="persons-container" v-if="datas.persons && datas.persons.length > 0">
      <div class="person-card" v-for="(person, pIdx) in filteredPersons" :key="pIdx">

        <!-- Person header - clickable to expand/collapse -->
        <div class="person-header"
          :class="{expanded: expanded[pIdx]}"
          @click="toggleExpand(pIdx)">
          <div class="avatar">{{person.name ? person.name.charAt(0) : '?'}}</div>
          <div class="person-name">{{person.name}}</div>
          <div class="violation-badge" v-if="getPersonViolations(pIdx).length > 0">
            ⚠️ เบิกซ้อน {{getPersonViolations(pIdx).length}} วัน
          </div>
          <div class="person-total">฿ {{formatNum(personTotal(person))}}</div>
          <span class="chevron">▼</span>
        </div>

        <!-- Duties expanded -->
        <div class="duty-list" v-show="expanded[pIdx]">
          <template v-for="(duty, dIdx) in person.duties" :key="dIdx">
            <!-- แถวหลัก: วันที่เบิกจ่าย -->
            <div class="duty-row">
              <div class="duty-name">
                {{formatDutyName(duty.ven_name)}}
                <span class="no-claim-tag" v-if="duty.no_claim">ไม่เบิก</span>
              </div>
              <div class="day-grid">
                <!-- Active days only (blue) -->
                <span v-for="day in duty.days" :key="'a'+day"
                  class="day-chip active"
                  :class="{ violation: isDayViolation(pIdx, day), resolved: isDayResolved(pIdx, day) }"
                  @click="openModal(duty, day, person)">
                  {{pad(day)}}
                  
                  <!-- Custom Tooltip for Violations -->
                  <div class="custom-tooltip" v-if="isDayViolation(pIdx, day)">
                    <strong>⚠️ พบการเบิกซ้อนในวันนี้:</strong>
                    <ul>
                      <li v-for="(vName, vIdx) in getViolationDetails(pIdx, day)" :key="vIdx">
                        {{vName}}
                      </li>
                    </ul>
                    <span class="hint">คลิกที่ตัวเลขเพื่อเลือกรายการที่ไม่ต้องการเบิก</span>
                  </div>
                </span>
              </div>
              <div class="duty-total" :class="{zero: duty.total===0}">
                {{duty.no_claim ? '—' : formatNum(duty.total)}}
              </div>
            </div>
            <!-- แถวที่ 2: วันไม่เบิก (ถ้ามี) -->
            <div class="duty-row excluded-duty-row"
              v-if="duty.excluded_days && duty.excluded_days.length > 0">
              <div class="duty-name-excl">{{formatDutyName(duty.ven_name)}} (ไม่เบิก)</div>
              <div class="day-grid">
                <span v-for="day in duty.excluded_days" :key="'x'+day"
                  class="day-chip excluded"
                  :title="'วันที่ '+day+' - ไม่เบิกจ่าย (คลิกเพื่อแก้ไข)'"
                  @click="openModal(duty, day, person)">
                  {{pad(day)}}
                </span>
              </div>
              <div class="duty-total zero">0.00</div>
            </div>
          </template>
        </div>

      </div>
    </div>

    <!-- EMPTY STATE -->
    <div class="empty-state" v-else-if="!loading">
      <div class="icon">📋</div>
      <p>ไม่พบข้อมูล กรุณาเปิดหน้านี้จากเมนูตรวจสอบเวร</p>
    </div>

    <!-- FOOTER -->
    <div class="footer-bar" v-if="datas.persons && datas.persons.length > 0">
      <span class="grand-label">💰 รวมเงินทั้งหมด</span>
      <span class="save-hint" v-if="lastSaved">✓ บันทึกอัตโนมัติ</span>
      <span class="grand-amount">฿ {{formatNum(datas.grand_total)}}</span>
    </div>

    <!-- VEN DETAIL MODAL -->
    <transition name="fade">
      <div class="ven-modal-overlay" v-if="venModal.show" @click.self="closeVenModal">
        <div class="ven-modal-box">
          <div class="ven-modal-head">
            <h3>📋 รายละเอียดวันเวร</h3>
            <button class="m-close" @click="closeVenModal">✕</button>
          </div>
          <!-- Loading -->
          <div class="ven-modal-loading" v-if="venModal.loading">⏳ กำลังโหลด...</div>
          <!-- Content -->
          <div class="ven-modal-body" v-else-if="venModal.data">
            <table>
              <tbody>
                <tr>
                  <th>id</th>
                  <td>
                    <span style="color:#78909c; font-size:11px;">{{venModal.data.id}}</span>
                    <span v-if="venModal.data.status == 5" class="status-tag disabled">ปิดการใช้งานชั่วคราว</span>
                    <button v-if="venModal.data.status == 5" class="btn-dis-ven open" @click="venDisOpen(venModal.data.id)">เปิดการใช้งาน</button>
                    <button v-else-if="venModal.data.status == 1" class="btn-dis-ven" @click="venDisOpen(venModal.data.id)">ปิดการใช้งานชั่วคราว</button>
                  </td>
                </tr>
                <tr>
                  <th>วันที่ เวลา</th>
                  <td>{{venModal.data.ven_date}} เวลา {{venModal.data.ven_time}} น.</td>
                </tr>
                <tr>
                  <th>เบิกเงินในคำสั่ง</th>
                  <td>
                    <template v-if="venModal.ven_coms && venModal.ven_coms.length > 0">
                      <select v-model="venModal.data.ven_com_idb">
                        <option value="">(ยังไม่ระบุ)</option>
                        <option v-for="vc in venModal.ven_coms" :key="vc.vc_id" :value="String(vc.vc_id)">
                          คำสั่งที่ {{vc.ven_com_num}} เวร {{vc.name}}
                        </option>
                      </select>
                    </template>
                    <span v-else style="color:#b0bec5;">—</span>
                  </td>
                </tr>
                <tr>
                  <th>คำสั่ง</th>
                  <td>{{venModal.data.u_role}} | {{venModal.data.DN}} | {{venModal.data.ven_com_name}} | {{venModal.data.price}}</td>
                </tr>
                <tr v-for="(vc, i) in venModal.ven_coms" :key="'vc'+i">
                  <td></td>
                  <td>
                    <input type="checkbox" :id="'vc'+i" :value="String(vc.vc_id)"
                      v-model="venModal.data.ven_com_id">
                    <label :for="'vc'+i"> คำสั่งที่ {{vc.ven_com_num}} เวร {{vc.name}}</label>
                  </td>
                </tr>
                <tr>
                  <th>ชื่อผู้อยู่เวร</th>
                  <td style="font-weight:600;">{{venModal.data.fname}}{{venModal.data.name}} {{venModal.data.sname}}</td>
                </tr>
                <tr>
                  <th>สถานะเบิก</th>
                  <td>
                    <span v-if="isNoClaim" class="claim-badge no">❌ ไม่เบิก</span>
                    <span v-else class="claim-badge yes">✅ เบิก</span>
                    <button class="btn-save-ven" style="margin-left:10px;" @click="venSave()" :disabled="venModal.saveLoading">
                      <span v-if="venModal.saveLoading">⏳ บันทึก...</span>
                      <span v-else-if="venModal.savedOk">✅ บันทึกแล้ว</span>
                      <span v-else>💾 บันทึก</span>
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          <div class="ven-modal-loading" v-else style="color:#e53935;">⚠️ ไม่พบข้อมูล</div>
          <div class="ven-modal-foot">
            <button class="btn-del-ven" @click="venDel(venModal.data.id)" :disabled="venModal.delLoading || venModal.saveLoading">{{venModal.delLoading ? 'กำลังลบ...' : '🗑 ลบ'}}</button>
            <button class="btn-close-ven" @click="closeVenModal">ปิด</button>
          </div>
        </div>
      </div>
    </transition>

  </div>

  <script src="../../node_modules/vue/dist/vue.global.js"></script>
  <script src="../../node_modules/axios/dist/axios.js"></script>
  <script>
    const {
      createApp,
      ref,
      computed,
      reactive
    } = Vue;
    createApp({
      data() {
        return {
          datas: {
            days_in_month: 31,
            persons: [],
            grand_total: 0
          },
          search: '',
          expanded: {},
          lastSaved: '',
          loading: true,
          venModal: {
            show: false,
            loading: false,
            data: null,
            ven_coms: [],
            delLoading: false,
            saveLoading: false,
            savedOk: false
          },
          violationsMap: {}
        }
      },
      computed: {
        filteredPersons() {
          if (!this.datas.persons) return [];
          if (!this.search.trim()) return this.datas.persons;
          const q = this.search.toLowerCase();
          return this.datas.persons.filter(p => {
            if (p.name.toLowerCase().includes(q)) return true;
            return p.duties.some(d => d.ven_name.toLowerCase().includes(q));
          });
        },
        isNoClaim() {
          if (!this.venModal.data) return false;
          const d = this.venModal.data;
          const comIdEmpty = !d.ven_com_id || (Array.isArray(d.ven_com_id) && d.ven_com_id.length === 0);
          const comIdbEmpty = !d.ven_com_idb || String(d.ven_com_idb).trim() === '';
          const comNumEmpty = !d.ven_com_num_all || String(d.ven_com_num_all).trim() === '';
          return comIdEmpty && comIdbEmpty && comNumEmpty;
        },
        calculatedViolations() {
          const map = {};
          if (!this.datas.persons) return map;
          
          this.datas.persons.forEach((person, pIdx) => {
            const dayCounts = {};
            person.duties.forEach(duty => {
              if (duty.no_claim) return;
              if (duty.days) {
                duty.days.forEach(day => {
                  dayCounts[day] = (dayCounts[day] || 0) + 1;
                });
              }
            });
            const badDays = Object.keys(dayCounts)
              .filter(day => dayCounts[day] > 1)
              .map(Number);
            if (badDays.length > 0) {
              map[pIdx] = badDays;
            }
          });
          return map;
        },
        resolvedMap() {
          const map = {};
          if (!this.datas.persons) return map;
          this.datas.persons.forEach((person, pIdx) => {
            const billableCount = {};
            const totalCount = {};
            person.duties.forEach(duty => {
              if (duty.days) duty.days.forEach(d => {
                billableCount[d] = (billableCount[d] || 0) + 1;
                totalCount[d] = (totalCount[d] || 0) + 1;
              });
              if (duty.excluded_days) duty.excluded_days.forEach(d => {
                totalCount[d] = (totalCount[d] || 0) + 1;
              });
            });
            const resolvedDays = Object.keys(totalCount)
              .filter(d => totalCount[d] > 1 && billableCount[d] === 1)
              .map(Number);
            if (resolvedDays.length > 0) {
              map[pIdx] = resolvedDays;
            }
          });
          return map;
        }
      },
      mounted() {
        const raw = localStorage.getItem('print_dutytype');
        if (raw) {
          this.datas = JSON.parse(raw);
          // ensure excluded_days array exists on all duties (backend already populates from DB)
          this.datas.persons.forEach(p =>
            p.duties.forEach(d => {
              if (!d.excluded_days) d.excluded_days = [];
            })
          );
          // apply any additional manual exclusions saved by user
          this.loadExcludedDuties();
          // Expand all by default
          this.datas.persons.forEach((_, i) => {
            this.expanded[i] = true;
          });
          this.recalcGrandTotal();
        }
        this.loading = false;
      },
      methods: {
        pad(n) {
          return n < 10 ? '0' + n : '' + n;
        },
        formatNum(n) {
          return Number(n || 0).toLocaleString('th-TH', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
          });
        },
        formatDutyName(name) {
          if (name === 'ศาลแขวงและพิจารณาคำร้องขอปล่อยชั่วคราว') return 'เวรศาลแขวงฯ';
          if (name === 'เวรเปิดทำการพิจารณาคำร้องขอปล่อยชั่วคราว') return 'เวรฯขอปล่อยชั่วคราว';
          return name;
        },
        toggleExpand(idx) {
          this.expanded[idx] = !this.expanded[idx];
        },
        personTotal(person) {
          return person.duties.reduce((s, d) => s + (d.total || 0), 0);
        },
        isDayViolation(pIdx, day) {
          const personVio = this.calculatedViolations[pIdx];
          return personVio && personVio.includes(day);
        },
        isDayResolved(pIdx, day) {
          const personRes = this.resolvedMap[pIdx];
          return personRes && personRes.includes(day);
        },
        getPersonViolations(pIdx) {
          return this.calculatedViolations[pIdx] || [];
        },
        getViolationDetails(pIdx, day) {
          const person = this.datas.persons[pIdx];
          if (!person) return [];
          return person.duties
            .filter(d => !d.no_claim && d.days && d.days.includes(day))
            .map(d => this.formatDutyName(d.ven_name));
        },
        openModal(duty, day, person, directVenId) {
          // ดึง ven_id จาก day_ids หรือ excl_ids หรือ directVenId
          let venId = directVenId || null;
          if (!venId && duty) {
            venId = (duty.day_ids && duty.day_ids[day]) ?
              duty.day_ids[day] :
              (duty.excl_ids && duty.excl_ids[day] ? duty.excl_ids[day] : null);
          }
          if (!venId) {
            alert('ไม่พบ ID ของเวรวันนี้ กรุณาโหลดข้อมูลใหม่');
            return;
          }
          this.venModal = {
            show: true,
            loading: true,
            data: null,
            ven_coms: [],
            delLoading: false
          };
          axios.post('../../server/asu/ven_set/get_ven.php', {
              id: venId
            })
            .then(res => {
              if (res.data.status) {
                this.venModal.data = res.data.respJSON;
                // ven_com_id อาจเป็น array หรือ string
                if (this.venModal.data.ven_com_id && typeof this.venModal.data.ven_com_id === 'string') {
                  try {
                    this.venModal.data.ven_com_id = JSON.parse(this.venModal.data.ven_com_id);
                  } catch (e) {
                    this.venModal.data.ven_com_id = [];
                  }
                }
                if (!Array.isArray(this.venModal.data.ven_com_id)) this.venModal.data.ven_com_id = [];
                // แปลงทุกค่าเป็น string เพื่อให้ตรงกับ checkbox value
                this.venModal.data.ven_com_id = this.venModal.data.ven_com_id.map(v => String(v));
                this.venModal.ven_coms = res.data.ven_coms || [];
              } else {
                this.venModal.data = null;
              }
              this.venModal.loading = false;
            })
            .catch(() => {
              this.venModal.loading = false;
            });
        },
        closeVenModal() {
          this.venModal.show = false;
        },
        venSave() {
          if (!this.venModal.data) return;
          this.venModal.saveLoading = true;
          this.venModal.savedOk = false;
          axios.post('../../server/asu/ven_set/ven_up_vcid.php', {
              data_event: this.venModal.data
            })
            .then(() => {
              this.venModal.savedOk = true;
              setTimeout(() => { this.venModal.savedOk = false; }, 2000);
            })
            .catch(() => { alert('เกิดข้อผิดพลาดในการบันทึก'); })
            .finally(() => { this.venModal.saveLoading = false; });
        },
        venDel(id) {
          if (!confirm('ต้องการลบเวรนี้ใช่หรือไม่?')) return;
          this.venModal.delLoading = true;
          axios.post('../../server/asu/ven_set/ven_del.php', {
              id: id
            })
            .then(res => {
              if (res.data.status) {
                alert('ลบสำเร็จ');
                this.closeVenModal();
              } else {
                alert(res.data.message || 'เกิดข้อผิดพลาด');
              }
            })
            .catch(() => {
              alert('เกิดข้อผิดพลาดในการลบ');
            })
            .finally(() => {
              this.venModal.delLoading = false;
            });
        },
        venDisOpen(id) {
          if (!confirm('ต้องการเปลี่ยนสถานะเวรนี้ใช่หรือไม่?')) return;
          axios.post('../../server/asu/ven_set/ven_dis_open.php', {
              id: id
            })
            .then(res => {
              if (res.data.status) {
                // reload modal data
                this.openModal(null, null, null, id);
              } else {
                alert(res.data.message || 'เกิดข้อผิดพลาด');
              }
            })
            .catch(() => {
              alert('เกิดข้อผิดพลาด');
            });
        },

        recalcGrandTotal() {
          let grand = 0;
          this.datas.persons.forEach(p => p.duties.forEach(d => grand += d.total));
          this.datas.grand_total = grand;
        },
        loadExcludedDuties() {
          // โหลดเฉพาะ manual exclusions ที่ผู้ใช้ toggle เพิ่มเติม (ไม่ทับ excluded_days จาก DB)
          const month = this.datas.ven_month;
          if (!month) return;
          const stored = localStorage.getItem('excluded_duties_manual_' + month);
          if (!stored) return;
          const excluded = JSON.parse(stored);
          this.datas.persons.forEach(p => {
            p.duties.forEach(d => {
              if (!d.excluded_days) d.excluded_days = [];
              const match = excluded.filter(ex => ex.user_id == p.uid && ex.ven_name === d.ven_name);
              match.forEach(m => {
                // เพิ่มเฉพาะวันที่ยังอยู่ใน days (ไม่อยู่ใน excluded_days แล้ว)
                if (d.days && d.days.includes(m.day)) {
                  d.days = d.days.filter(day => day !== m.day);
                  d.excluded_days.push(m.day);
                  d.excluded_days.sort((a, b) => a - b);
                  d.total -= d.price_per;
                }
              });
            });
          });
        },
        saveExcludedDuties() {
          // บันทึกเฉพาะ manual exclusions (วันที่ผู้ใช้ toggle เพิ่มเติม)
          // แยกออกจาก excluded_days ที่มาจาก DB
          const manualExcluded = [];
          const month = this.datas.ven_month;
          if (!month) return;
          // โหลด DB excluded_days ของรอบนี้ (จาก backend) เพื่อเปรียบเทียบ
          const rawBackend = localStorage.getItem('print_dutytype');
          const backendData = rawBackend ? JSON.parse(rawBackend) : null;
          this.datas.persons.forEach(p => {
            p.duties.forEach(d => {
              if (!d.excluded_days || d.excluded_days.length === 0) return;
              // หา DB excluded_days ของคนนี้+ประเภทเวรนี้
              let dbExcluded = [];
              if (backendData && backendData.persons) {
                const bp = backendData.persons.find(x => x.uid == p.uid);
                if (bp) {
                  const bd = bp.duties.find(x => x.ven_name === d.ven_name && !x.no_claim);
                  if (bd && bd.excluded_days) dbExcluded = bd.excluded_days;
                }
              }
              // บันทึกเฉพาะวันที่ไม่ได้มาจาก DB
              d.excluded_days.forEach(day => {
                if (!dbExcluded.includes(day)) {
                  manualExcluded.push({
                    user_id: p.uid,
                    ven_name: d.ven_name,
                    day: day
                  });
                }
              });
            });
          });
          localStorage.setItem('excluded_duties_manual_' + month, JSON.stringify(manualExcluded));
        }
      }
    }).mount('#app');
  </script>
</body>

</html>