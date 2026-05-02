<?php
require_once('../../server/authen.php');
?>
<!DOCTYPE html>
<html lang="th">

<head>
    <?php require_once('../includes/_header.php') ?>
    <style>
        [v-cloak]>* {
            display: none;
        }

        [v-cloak]::before {
            content: "กำลังโหลด...";
            display: block;
            text-align: center;
            padding: 60px;
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

        #main {
            padding: 0 !important;
            margin-left: 300px;
            transition: all 0.3s ease;
            width: calc(100% - 300px);
            overflow-x: hidden;
        }

        #sidebar:not(.active)~#main {
            margin-left: 0;
            width: 100%;
        }

        @media screen and (max-width: 1199px) {
            #main {
                margin-left: 0;
                width: 100%;
            }
        }

        /* ===== TOPBAR ===== */
        .topbar {
            position: sticky;
            top: 0;
            z-index: 1000;
            background: linear-gradient(135deg, #1a237e 0%, #283593 60%, #3949ab 100%);
            color: #fff;
            padding: 12px 25px;
            display: flex;
            align-items: center;
            gap: 12px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.2);
        }

        .topbar h1 {
            font-size: 20px;
            font-weight: 700;
            letter-spacing: 0.5px;
            margin: 0;
        }

        .topbar .month-badge {
            background: rgba(255, 255, 255, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.4);
            border-radius: 20px;
            padding: 4px 14px;
            font-size: 14px;
            font-weight: 600;
        }

        .topbar .overlap-count-badge {
            margin-left: auto;
            background: #e53935;
            border-radius: 20px;
            padding: 6px 18px;
            font-size: 15px;
            font-weight: 700;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
        }

        /* ===== FILTER BAR ===== */
        .filter-bar {
            padding: 12px 25px;
            background: #fff;
            border-bottom: 1px solid #e0e0e0;
            display: flex;
            align-items: center;
            gap: 15px;
            flex-wrap: wrap;
        }

        .filter-bar select,
        .filter-bar input {
            border: 1px solid #c5cae9;
            border-radius: 8px;
            padding: 8px 12px;
            font-size: 14px;
            outline: none;
        }

        .filter-bar input {
            flex: 1;
        }

        /* ===== TABLE WRAPPER ===== */
        .table-wrap {
            overflow-x: auto;
            padding: 0;
            background: #f0f4f8;
        }

        /* ===== MAIN TABLE ===== */
        .duty-table {
            border-collapse: separate;
            border-spacing: 0;
            background: #fff;
            border-radius: 0;
            overflow: hidden;
            box-shadow: none;
            width: 100%;
            min-width: 1200px;
        }

        /* Header */
        .duty-table thead tr.day-header th {
            background: #e8eaf6;
            color: #1a237e;
            text-align: center;
            font-size: 10px;
            font-weight: 700;
            padding: 4px 1px;
            border-right: 1px solid #c5cae9;
            border-bottom: 2px solid #c5cae9;
        }

        .duty-table thead tr.day-header th.col-info {
            text-align: left;
            padding-left: 8px;
            position: sticky;
            left: 0;
            z-index: 100;
            background: #1a237e;
            color: #fff;
            border-right: 2px solid rgba(255, 255, 255, 0.2);
            border-bottom: none;
        }

        .duty-table thead tr.day-header th.col-name {
            width: 1%;
            white-space: nowrap;
        }

        .duty-table thead tr.day-header th.col-day {
            min-width: 20px;
        }

        .duty-table thead tr.day-header th.col-total {
            width: 80px;
            position: sticky;
            right: 0;
            background: #1a237e;
            color: #fff;
            border-bottom: none;
        }

        /* Person Separator Row */
        .person-sep td {
            background: #e8eaf6;
            padding: 5px 10px;
            font-weight: 700;
            color: #1a237e;
            border-top: 2px solid #c5cae9;
            font-size: 13px;
        }

        .person-sep .overlap-badge {
            background: #e53935;
            color: #fff;
            padding: 2px 10px;
            border-radius: 10px;
            font-size: 11px;
            margin-left: 10px;
            vertical-align: middle;
        }

        /* Duty Rows */
        .duty-row:hover td {
            background: #f5f7fb !important;
        }

        .duty-row td {
            padding: 1px;
            border-bottom: 1px solid #e0e0e0;
            border-right: 1px solid #e0e0e0;
            text-align: center;
            vertical-align: middle;
        }

        .duty-row td.col-info {
            text-align: left;
            padding-left: 8px;
            position: sticky;
            left: 0;
            z-index: 50;
            background: #fff;
            border-right: 2px solid #bdbdbd;
            font-size: 11px;
            font-weight: 600;
            color: #455a64;
            white-space: nowrap;
        }

        .duty-row td.col-total {
            font-weight: 700;
            color: #2e7d32;
            position: sticky;
            right: 0;
            background: #fff;
            border-left: 2px solid #bdbdbd;
            font-size: 12px;
        }

        .no-claim-row td.col-info {
            color: #9e9e9e !important;
        }

        .no-claim-row td.col-total {
            color: #bdbdbd !important;
        }

        /* Day Chips */
        .day-chip {
            width: calc(100% - 2px);
            height: 100%;
            min-height: 22px;
            border-radius: 3px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.15s;
            border: 1.5px solid transparent;
            box-sizing: border-box;
        }

        .day-chip.active {
            background: #e3f2fd;
            color: #1976d2;
            border-color: #bbdefb;
        }

        .day-chip.no-claim {
            background: #eeeeee;
            color: #757575;
            border-color: #e0e0e0;
        }

        .day-chip.overlap {
            background: #ffebee;
            color: #d32f2f;
            border-color: #ef9a9a;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% {
                box-shadow: 0 0 0 0 rgba(211, 47, 47, 0.4);
            }

            70% {
                box-shadow: 0 0 0 6px rgba(211, 47, 47, 0);
            }

            100% {
                box-shadow: 0 0 0 0 rgba(211, 47, 47, 0);
            }
        }

        /* Empty Column style */
        .col-day.empty {
            opacity: 0.2;
        }

        .overlap-dot {
            position: absolute;
            top: -5px;
            right: -5px;
            width: 14px;
            height: 14px;
            background: #e53935;
            border: 2px solid #fff;
            border-radius: 50%;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }

        /* Modal Styles (Matches ven_set.php) */
        .modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 2000;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .modal-box {
            background: #fff;
            border-radius: 12px;
            width: 100%;
            max-width: 600px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
            overflow: hidden;
            display: flex;
            flex-direction: column;
            max-height: 90vh;
        }

        .modal-head {
            padding: 15px 20px;
            border-bottom: 1px solid #eee;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .modal-head h5 {
            margin: 0;
            font-size: 18px;
            font-weight: 700;
        }

        .modal-body {
            padding: 0;
            overflow-y: auto;
        }

        .duty-block {
            border-bottom: 5px solid #eee;
        }

        .duty-block:last-child {
            border-bottom: none;
        }

        .duty-block-title {
            background: #f8f9fa;
            padding: 10px 20px;
            font-weight: 700;
            color: #3949ab;
            border-bottom: 1px solid #eee;
        }

        .modal-table {
            width: 100%;
            border-collapse: collapse;
        }

        .modal-table th {
            background: #fff;
            text-align: left;
            padding: 12px 20px;
            width: 140px;
            border-bottom: 1px solid #eee;
            font-size: 14px;
            font-weight: 700;
        }

        .modal-table td {
            padding: 12px 20px;
            border-bottom: 1px solid #eee;
            font-size: 14px;
        }

        .modal-foot {
            padding: 15px 20px;
            background: #f8f9fa;
            border-top: 1px solid #eee;
            display: flex;
            justify-content: flex-end;
        }

        .btn-status {
            padding: 5px 12px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 700;
            border: none;
            cursor: pointer;
        }

        .btn-status.danger {
            background: #e53935;
            color: #fff;
        }

        .btn-status.success {
            background: #43a047;
            color: #fff;
        }

        @media print {

            .sidebar-wrapper,
            .topbar,
            .filter-bar,
            .legend {
                display: none !important;
            }

            #main {
                padding: 0;
                margin-left: 0 !important;
            }

            body {
                background: #fff;
            }

            .table-wrap {
                padding: 0;
            }

            .duty-table {
                box-shadow: none;
                min-width: 100%;
                border: 1px solid #000;
            }

            @page {
                size: landscape;
                margin: 0.5cm;
            }
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
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
            pointer-events: none;
            font-weight: normal;
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

        .day-chip.multi-claim {
            background: #ff9800 !important;
            color: #fff !important;
            border-color: #ef6c00 !important;
            animation: pulse-orange 2s infinite;
        }

        .day-chip.resolved {
            background: #43a047 !important;
            color: #fff !important;
            border-color: #2e7d32 !important;
        }

        @keyframes pulse-orange {
            0% {
                box-shadow: 0 0 0 0 rgba(255, 152, 0, 0.4);
            }

            70% {
                box-shadow: 0 0 0 6px rgba(255, 152, 0, 0);
            }

            100% {
                box-shadow: 0 0 0 0 rgba(255, 152, 0, 0);
            }
        }

        /* ===== DOUBLE-CLICK SWAP ===== */
        .day-chip.selected-src {
            background: #fff9c4 !important;
            color: #f57f17 !important;
            border-color: #f9a825 !important;
            box-shadow: 0 0 0 2px #f9a825, 0 0 6px rgba(249, 168, 37, 0.4);
            animation: none !important;
            transform: scale(1.15);
            z-index: 10;
        }

        .day-chip.click-target {
            background: #7b1fa2 !important;
            color: #fff !important;
            border-color: #4a148c !important;
            cursor: pointer;
            animation: pulse-purple 0.9s ease-in-out infinite;
            transform: scale(1.08);
            z-index: 5;
        }

        @keyframes pulse-purple {
            0% {
                box-shadow: 0 0 0 0 rgba(123, 31, 162, 0.85);
            }

            50% {
                box-shadow: 0 0 0 8px rgba(123, 31, 162, 0);
                background: #ab47bc !important;
            }

            100% {
                box-shadow: 0 0 0 0 rgba(123, 31, 162, 0.85);
            }
        }

        /* swap mode banner */
        .swap-hint-bar {
            background: linear-gradient(90deg, #1a237e, #3949ab);
            color: #fff;
            text-align: center;
            padding: 6px 20px;
            font-size: 13px;
            font-weight: 600;
            letter-spacing: 0.3px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        /* ปุ่มโหมดแลกเวร เมื่อเปิดอยู่ */
        .swap-btn-active {
            background: linear-gradient(135deg, #e65100, #ff6f00) !important;
            color: #fff !important;
            border-color: #bf360c !important;
            animation: pulse-swap-btn 1.1s ease-in-out infinite;
        }

        @keyframes pulse-swap-btn {
            0% {
                box-shadow: 0 0 0 0 rgba(230, 81, 0, 0.7);
            }

            55% {
                box-shadow: 0 0 0 8px rgba(230, 81, 0, 0);
                transform: scale(1.04);
            }

            100% {
                box-shadow: 0 0 0 0 rgba(230, 81, 0, 0.7);
                transform: scale(1);
            }
        }
    </style>
</head>

<body>
    <div id="app">
        <?php require_once('../includes/_sidebar.php') ?>
        <div id="main">
            <header class="mb-3 d-print-none">
                <a href="#" class="burger-btn d-inline-block position-relative" @click.prevent="toggleSidebar()">
                    <i class="bi bi-justify fs-3"></i>
                    <span class="overlap-dot" v-if="totalOverlaps > 0"></span>
                </a>
            </header>

            <div v-cloak>
                <!-- SWAP HINT BAR -->
                <div class="swap-hint-bar d-print-none" v-if="swap.selected">
                    <span>🔄 เลือกแล้ว: <strong>{{swap.selected.personName}}</strong> — วันที่ {{pad(swap.selected.day)}} ({{formatDutyName(swap.selected.dutyName)}} {{swap.selected.DN}})</span>
                    <span style="opacity:0.8"> → คลิกที่เวรของอีกคนเพื่อแลก | กด Esc เพื่อยกเลิก</span>
                    <button class="btn btn-sm btn-light ms-3" @click="swap.selected = null">✕ ยกเลิก</button>
                </div>
                <div class="swap-hint-bar d-print-none" v-else-if="swapMode" style="background: linear-gradient(90deg, #e65100, #f57c00);">
                    <span>🔄 โหมดแลกเวร: ดับเบิ้ลคลิกที่ช่องตัวเลขเพื่อเลือกเวรต้นทาง</span>
                    <button class="btn btn-sm btn-light ms-3" @click="swapMode = false; swap.selected = null">✕ ปิดโหมดแลกเวร</button>
                </div>
                <!-- TOP BAR -->
                <div class="topbar">
                    <div>
                        <div style="font-size:11px; opacity:0.7; margin-bottom:2px;">ตรวจสอบความถูกต้องก่อนเบิกจ่าย</div>
                        <h1>⚠️ เช็คเวรชน (Overlap Audit)</h1>
                    </div>
                    <span class="month-badge">📅 {{monthText}}</span>

                    <a href="javascript:void(0)" @click="printDutyType()" class="btn ms-auto d-print-none fw-bold" style="background-color: #f57c00; color: #fff; border: 1px solid #ef6c00; border-radius: 20px; box-shadow: 0 2px 5px rgba(0,0,0,0.2);">
                        <i class="bi bi-file-earmark-spreadsheet"></i> พิมพ์สรุปเวรแยกประเภท
                    </a>

                    <span class="overlap-count-badge ms-2" v-if="totalOverlaps > 0">พบเวรชนรวม {{totalOverlaps}} ราย</span>
                    <a href="javascript:window.print()" class="close-btn d-print-none ms-3 text-white" style="text-decoration:none;"><i class="bi bi-printer"></i> พิมพ์หน้านี้</a>
                </div>

                <!-- FILTER BAR -->
                <div class="filter-bar d-print-none">
                    <select v-model="sel_month" @change="fetchData()">
                        <option v-for="m in months_list" :value="m.val">{{m.name}}</option>
                    </select>
                    <select v-model="sel_year" @change="fetchData()">
                        <option v-for="y in years_list" :value="y">{{y + 543}}</option>
                    </select>
                    <input v-model="search" placeholder="🔍 ค้นหาชื่อเจ้าหน้าที่..." />

                    <div class="form-check form-switch ms-3 d-none">
                        <input class="form-check-input" type="checkbox" id="showOnlyOverlap" v-model="showOnlyOverlap">
                        <label class="form-check-label fw-bold" for="showOnlyOverlap">แสดงเฉพาะที่มีเวรชน</label>
                    </div>

                    <button
                        class="btn btn-sm ms-3 fw-bold d-print-none"
                        :class="swapMode ? ['swap-btn-active'] : ['btn-outline-secondary']"
                        @click="swapMode = !swapMode; swap.selected = null"
                        title="เปิด/ปิด โหมดแลกเวร">
                        <i class="bi bi-arrow-left-right"></i>
                        {{ swapMode ? '🔄 โหมดแลกเวร (เปิดอยู่)' : '🔄 แลกเวร' }}
                    </button>
                </div>

                <!-- TABLE AREA -->
                <div class="table-wrap">
                    <table class="duty-table">
                        <thead>
                            <tr class="day-header">
                                <th class="col-info col-name">ชื่อ - ประเภทเวร</th>
                                <th v-for="d in daysInMonth" :key="d" class="col-day">{{d}}</th>
                                <th class="col-total">รวม (฿)</th>
                            </tr>
                        </thead>
                        <tbody v-for="(p, pIdx) in filteredPersons" :key="p.uid">
                            <!-- Person Header Row -->
                            <tr class="person-sep">
                                <td :colspan="daysInMonth + 2">
                                    <i class="bi bi-person-circle me-2"></i> {{p.name}}
                                    <span v-if="p.claimTotal > 0" style="margin-left: 8px; background: #1565c0; color: #fff; padding: 2px 10px; border-radius: 10px; font-size: 11px; font-weight: 700; vertical-align: middle;">฿{{formatNum(p.claimTotal)}}</span>
                                    <span class="overlap-badge" v-if="p.overlapCount > 0">พบเวรชน {{p.overlapCount}} วัน</span>
                                </td>
                            </tr>
                            <!-- Duty Rows -->
                            <tr class="duty-row" v-for="(duty, dKey) in p.dutyGroups" :key="dKey" :class="{'no-claim-row': duty.is_no_claim}">
                                <td class="col-info col-name">
                                    {{formatDutyName(duty.ven_name)}} ({{duty.DN === 'กลางวัน' ? '☀️' : '🌙'}})
                                    <span v-if="duty.is_no_claim" class="badge bg-secondary ms-1" style="font-size: 10px; font-weight: normal; opacity: 0.8;">ไม่เบิก</span>
                                    <span v-else class="badge bg-success ms-1" style="font-size: 10px; font-weight: normal; opacity: 0.8;">เบิก</span>
                                    <span v-if="duty.price_per_day > 0" class="badge ms-1" style="font-size: 9px; font-weight: 600; background: #e3f2fd; color: #1565c0; border: 1px solid #90caf9;">฿{{formatNum(duty.price_per_day)}}</span>
                                </td>
                                <td v-for="d in daysInMonth" :key="d"
                                    class="col-day"
                                    :class="{empty: !duty.days.includes(d), 'click-target': isClickTarget(p, duty, d)}">
                                    <span v-if="duty.days.includes(d)"
                                        class="day-chip"
                                        :class="[
                                            duty.is_no_claim ? 'no-claim' : (isMultiClaim(p, d) ? 'multi-claim' : (isResolvedOverlap(p, d) ? 'resolved' : (p.dayMap[d] && p.dayMap[d].length > 1 ? 'overlap' : 'active'))),
                                            isSelectedSrc(p, d, duty) ? 'selected-src' : '',
                                            isClickTarget(p, duty, d) ? 'click-target' : ''
                                        ]"
                                        @dblclick.stop="onDblClick(p, duty, d)"
                                        @click.stop="onChipClick(p, duty, d)">
                                        {{pad(d)}}

                                        <!-- Custom Tooltip for Overlaps -->
                                        <div class="custom-tooltip" v-if="p.dayMap[d] && p.dayMap[d].length > 1">
                                            <strong v-if="isMultiClaim(p, d)">⚠️ พบการเบิกซ้อนในวันนี้:</strong>
                                            <strong v-else>📋 รายการเวรในวันนี้:</strong>
                                            <ul>
                                                <li v-for="(vDetail, vIdx) in getOverlapDetails(p, d)" :key="vIdx">
                                                    {{vDetail}}
                                                </li>
                                            </ul>
                                            <span class="hint">ดับเบิ้ลคลิกเพื่อแลกเวร | คลิกเดียวเพื่อดูรายละเอียด</span>
                                        </div>
                                    </span>
                                </td>
                                <td class="col-total">{{formatNum(duty.price_sum)}}</td>
                            </tr>
                        </tbody>
                    </table>

                    <div v-if="filteredPersons.length === 0" class="text-center py-5 bg-white mt-3 rounded shadow-sm">
                        <div style="font-size: 50px;">🔍</div>
                        <p class="text-muted">{{isLoading ? 'กำลังโหลดข้อมูล...' : 'ไม่พบข้อมูลที่ตรงกับเงื่อนไข'}}</p>
                    </div>
                </div>
            </div>

            <!-- MODAL (Matching ven_set.php) -->
            <div class="modal-overlay" v-if="modal.show" @click.self="closeModal()">
                <div class="modal-box">
                    <div class="modal-head">
                        <h5>📅 ข้อมูลเวรวันที่ {{modal.day}}</h5>
                        <button type="button" class="btn-close" @click="closeModal()"></button>
                    </div>
                    <div class="modal-body">
                        <div v-if="modal.loading" class="p-5 text-center text-muted">⏳ กำลังโหลด...</div>
                        <div v-else>
                            <div v-for="(v, idx) in filteredModalVens" :key="v.id" class="duty-block">
                                <div class="duty-block-title">เวรลำดับที่ {{idx + 1}}: {{v.ven_name}}</div>
                                <table class="modal-table">
                                    <tbody>
                                        <tr>
                                            <th>id</th>
                                            <td>
                                                {{v.id}} {{v.status == 5 ? 'ปิดการใช้งานชั่วคราว':''}}
                                                <button class="btn-status" :class="v.status == 5 ? 'success' : 'danger'" @click="venDisOpen(v)">
                                                    {{v.status == 5 ? 'เปิดการใช้งาน' : 'ปิดการใช้งานชั่วคราว'}}
                                                </button>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>วันที่ เวลา</th>
                                            <td>{{v.ven_date}} เวลา {{v.ven_time}} น.</td>
                                        </tr>
                                        <tr>
                                            <td colspan="2" class="p-1">
                                                <div class="alert alert-info py-1 px-2 mb-0" style="font-size: 11px; border-radius: 4px; border: none; background-color: #e3f2fd; color: #0d47a1;">
                                                    💡 <b>สำหรับการเงิน:</b> ติ๊กถูกที่หน้าคำสั่งเพื่อ "เบิกเงิน" (หากไม่ติ๊กเลยระบบจะถือเป็นเวรไม่เบิกเงินอัตโนมัติ)
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>เบิกเงินในคำสั่ง</th>
                                            <td>
                                                <select class="form-select" v-model="v.ven_com_idb" @change="venSave2(v)">
                                                    <option value="">-- เลือกคำสั่งเบิก --</option>
                                                    <option v-for="vc in getFilteredVenComs(v)" :key="vc.vc_id" :value="String(vc.vc_id)">
                                                        {{' คำสั่งที่ ' + vc.ven_com_num + ' เวร ' + vc.name}}
                                                    </option>
                                                </select>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>คำสั่ง</th>
                                            <td>{{v.u_role}} | {{v.DN}} | {{v.ven_com_name}} | {{v.price}}</td>
                                        </tr>
                                        <tr v-for="(vc, i) in getFilteredVenComs(v)" :key="'vc'+v.id+i">
                                            <td class="text-center" style="width: 80px;">
                                                <span v-if="v.ven_com_id.includes(String(vc.vc_id))" class="badge bg-success">เบิก</span>
                                                <span v-else class="badge bg-secondary">ไม่เบิก</span>
                                            </td>
                                            <td>
                                                <input type="checkbox" :id="'ck'+v.id+i" :value="String(vc.vc_id)" v-model="v.ven_com_id" @change="venSave(v)">
                                                <label :for="'ck'+v.id+i"> {{' คำสั่งที่ ' + vc.ven_com_num + ' เวร ' + vc.name}}</label>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>ชื่อผู้อยู่</th>
                                            <td>
                                                <div class="d-flex flex-column gap-2">
                                                    <div class="fw-bold text-primary">{{v.fname}}{{v.name}} {{v.sname}}</div>
                                                    <div class="input-group input-group-sm">
                                                        <div class="w-100 mb-2">
                                                            <input type="text" class="form-control form-control-sm" v-model="modalUserSearch" placeholder="🔍 ค้นหารายชื่อ/ตำแหน่ง...">
                                                        </div>
                                                        <select class="form-select form-select-sm" v-model="v.u_id">
                                                            <option value="">-- เลือกผู้เปลี่ยนตัว --</option>
                                                            <optgroup label="ผู้พิพากษา">
                                                                <option v-for="user in getFilteredUsers('judge')" :key="user.id" :value="user.id">
                                                                    {{user.fname}}{{user.name}} {{user.sname}}
                                                                </option>
                                                            </optgroup>
                                                            <optgroup label="เจ้าหน้าที่">
                                                                <option v-for="user in getFilteredUsers('staff')" :key="user.id" :value="user.id">
                                                                    {{user.fname}}{{user.name}} {{user.sname}}
                                                                </option>
                                                            </optgroup>
                                                        </select>
                                                        <button class="btn btn-primary" type="button" @click="venTransfer(v)" :disabled="!v.u_id">
                                                            <i class="bi bi-person-check"></i> เปลี่ยนตัว
                                                        </button>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>การจัดการ</th>
                                            <td>
                                                <button class="btn btn-danger btn-sm" @click="venDel(v)">ลบเวรนี้</button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="modal-foot">
                        <button type="button" class="btn btn-secondary btn-sm" @click="closeModal()">ปิด</button>
                    </div>
                </div>
            </div>

            <?php require_once('../includes/_footer.php') ?>
        </div>
    </div>

    <?php require_once('../includes/_footer_sc.php') ?>
    <script src="../../node_modules/vue/dist/vue.global.js"></script>
    <script src="../../node_modules/axios/dist/axios.js"></script>
    <script src="../../node_modules/sweetalert2/dist/sweetalert2.all.min.js"></script>

    <script>
        const {
            createApp
        } = Vue;

        createApp({
            data() {
                return {
                    isLoading: false,
                    sel_month: ("0" + (new Date().getMonth() + 1)).slice(-2),
                    sel_year: new Date().getFullYear(),
                    search: '',
                    showOnlyOverlap: false,
                    persons: [],
                    monthText: '',
                    daysInMonth: 31,
                    modal: {
                        show: false,
                        loading: false,
                        day: null,
                        vensData: [],
                        venComs: {},
                        targetGroupKey: null,
                        showAllOnDay: false
                    },
                    swap: {
                        selected: null
                    },
                    swapMode: false,
                    modalUserSearch: '',
                    modalUserFilter: 'all', // 'all', 'judge', 'staff'
                    months_list: [{
                            val: '01',
                            name: 'มกราคม'
                        }, {
                            val: '02',
                            name: 'กุมภาพันธ์'
                        }, {
                            val: '03',
                            name: 'มีนาคม'
                        },
                        {
                            val: '04',
                            name: 'เมษายน'
                        }, {
                            val: '05',
                            name: 'พฤษภาคม'
                        }, {
                            val: '06',
                            name: 'มิถุนายน'
                        },
                        {
                            val: '07',
                            name: 'กรกฎาคม'
                        }, {
                            val: '08',
                            name: 'สิงหาคม'
                        }, {
                            val: '09',
                            name: 'กันยายน'
                        },
                        {
                            val: '10',
                            name: 'ตุลาคม'
                        }, {
                            val: '11',
                            name: 'พฤศจิกายน'
                        }, {
                            val: '12',
                            name: 'ธันวาคม'
                        }
                    ],
                    years_list: [],
                    users_list: []
                }
            },
            computed: {
                filteredPersons() {
                    let res = this.persons;
                    if (this.showOnlyOverlap) res = res.filter(p => p.overlapCount > 0);
                    if (this.search.trim()) {
                        const q = this.search.toLowerCase();
                        res = res.filter(p => p.name.toLowerCase().includes(q));
                    }
                    return res;
                },
                totalOverlaps() {
                    return this.persons.reduce((s, p) => s + (p.overlapCount > 0 ? 1 : 0), 0);
                },
                filteredModalVens() {
                    if (!this.modal.targetGroupKey) return this.modal.vensData;

                    return this.modal.vensData.filter(v => {
                        const vKey = v.ven_name + '|' + v.DN + '|' + (this.isNoClaim(v) ? 'NC' : 'C');
                        return vKey === this.modal.targetGroupKey;
                    });
                }
            },
            mounted() {
                this.initYears();
                this.fetchData();
                this.fetchUsers();
                // Collapse sidebar by default
                const sidebar = document.getElementById('sidebar');
                if (sidebar) sidebar.classList.remove('active');

                document.addEventListener('click', (e) => {
                    if (e.target.closest('.sidebar-hide')) {
                        this.toggleSidebar();
                    }
                });

                // Escape ยกเลิกการเลือกเวร
                document.addEventListener('keydown', (e) => {
                    if (e.key === 'Escape') this.swap.selected = null;
                });

                // Drag to scroll ซ้าย-ขวา (หยุดเมื่ออยู่ในโหมดแลกเวร)
                this.$nextTick(() => {
                    const slider = document.querySelector('.table-wrap');
                    if (!slider) return;
                    let isDown = false;
                    let startX;
                    let scrollLeft;
                    let moved = false;

                    slider.addEventListener('mousedown', (e) => {
                        if (this.swapMode) return;
                        if (e.target.closest('.day-chip')) return; // ไม่รบกวน chip click
                        isDown = true;
                        moved = false;
                        slider.style.cursor = 'grabbing';
                        startX = e.pageX - slider.offsetLeft;
                        scrollLeft = slider.scrollLeft;
                    });
                    slider.addEventListener('mouseleave', () => {
                        isDown = false;
                        slider.style.cursor = '';
                    });
                    slider.addEventListener('mouseup', () => {
                        isDown = false;
                        slider.style.cursor = '';
                    });
                    slider.addEventListener('mousemove', (e) => {
                        if (!isDown) return;
                        if (this.swapMode) return;
                        e.preventDefault();
                        moved = true;
                        const x = e.pageX - slider.offsetLeft;
                        const walk = (x - startX) * 1.5;
                        slider.scrollLeft = scrollLeft - walk;
                    });
                });
            },
            methods: {
                initYears() {
                    const y = new Date().getFullYear();
                    for (let i = -1; i <= 2; i++) this.years_list.push(y + i);
                },
                fetchUsers() {
                    // ใช้พาธแบบอ้างอิงจาก root หรือกะระยะให้แม่นยำ
                    axios.get('ven/api/get_users.php')
                        .then(res => {
                            console.log('Users Data:', res.data); // ดูค่าใน F12 > Console
                            if (res.data.status) {
                                this.users_list = res.data.users;
                            }
                        })
                        .catch(err => {
                            console.error('Fetch Users Error:', err);
                        });
                },
                fetchData() {
                    this.isLoading = true;
                    axios.post('./ven/api/index_get_data_all.php', {
                            month: `${this.sel_year}-${this.sel_month}`,
                            excluded_duties: []
                        }).then(res => {
                            if (res.data.status) {
                                this.monthText = res.data.month;
                                this.daysInMonth = res.data.days_in_month || 31;
                                this.processData(res.data.datas);
                            } else {
                                this.persons = [];
                            }
                        }).catch(err => {
                            console.error(err);
                            this.persons = [];
                        })
                        .finally(() => {
                            this.isLoading = false;
                        });
                },
                processData(apiDatas) {
                    this.persons = apiDatas.map(p => {
                        const dayMap = {};
                        const dutyGroups = {};
                        let overlapCount = 0;

                        p.vens.forEach(v => {
                            const day = parseInt(v.ven_date.split('-')[2]);
                            if (!dayMap[day]) dayMap[day] = [];
                            dayMap[day].push(v);

                            const gKey = v.ven_name + '|' + v.DN + '|' + (v.is_no_claim ? 'NC' : 'C');
                            if (!dutyGroups[gKey]) dutyGroups[gKey] = {
                                ven_name: v.ven_name,
                                DN: v.DN,
                                days: [],
                                price_sum: 0,
                                price_per_day: parseFloat(v.price || 0),
                                is_no_claim: v.is_no_claim,
                                vn_srt: parseInt(v.vn_srt || 999),
                                vns_srt: parseInt(v.vns_srt || 999),
                                ven_com_idb: v.ven_com_idb || '',
                                u_role: v.u_role || ''
                            };
                            dutyGroups[gKey].days.push(day);
                            dutyGroups[gKey].price_sum += parseFloat(v.price || 0);
                        });

                        // Convert to array and sort: 
                        // 1. Billable (is_no_claim=false) first
                        // 2. Sort by vn_srt
                        // 3. Sort by vns_srt
                        const dutyGroupsArray = Object.values(dutyGroups).sort((a, b) => {
                            if (a.is_no_claim !== b.is_no_claim) return a.is_no_claim ? 1 : -1;
                            if (a.vn_srt !== b.vn_srt) return a.vn_srt - b.vn_srt;
                            return a.vns_srt - b.vns_srt;
                        });

                        // นับเฉพาะวันที่มีเวร "เบิก" ซ้อนกัน > 1 เท่านั้น (ไม่นับ "ไม่เบิก")
                        Object.keys(dayMap).forEach(d => {
                            const billable = dayMap[d].filter(v => !v.is_no_claim).length;
                            if (billable > 1) overlapCount++;
                        });

                        // คำนวณยอดรวมเฉพาะที่เบิกได้
                        const claimTotal = dutyGroupsArray
                            .filter(g => !g.is_no_claim)
                            .reduce((sum, g) => sum + g.price_sum, 0);

                        return {
                            uid: p.uid,
                            name: p.name,
                            dayMap: dayMap,
                            dutyGroups: dutyGroupsArray,
                            overlapCount: overlapCount,
                            claimTotal: claimTotal,
                            workgroup: p.workgroup || '' // จาก profile.workgroup เป็นตัวกำหนดตำแหน่งที่แม่นยำ
                        };
                    });
                },
                pad(n) {
                    return n < 10 ? '0' + n : '' + n;
                },
                formatNum(n) {
                    return Number(n || 0).toLocaleString('th-TH', {
                        minimumFractionDigits: 2
                    });
                },
                formatDutyName(name) {
                    if (name === 'ศาลแขวงและพิจารณาคำร้องขอปล่อยชั่วคราว') return 'เวรศาลแขวงฯ';
                    if (name === 'เวรเปิดทำการพิจารณาคำร้องขอปล่อยชั่วคราว') return 'เวรฯขอปล่อยชั่วคราว';
                    return name;
                },
                isMultiClaim(person, day) {
                    if (!person.dayMap[day]) return false;
                    const billableCount = person.dayMap[day].filter(v => !v.is_no_claim).length;
                    return billableCount > 1;
                },
                isResolvedOverlap(person, day) {
                    if (!person.dayMap[day]) return false;
                    const total = person.dayMap[day].length;
                    const billable = person.dayMap[day].filter(v => !v.is_no_claim).length;
                    return total > 1 && billable === 1;
                },
                getOverlapDetails(person, day) {
                    if (!person.dayMap[day]) return [];
                    return person.dayMap[day].map(v => {
                        return `${this.formatDutyName(v.ven_name)} (${v.DN}) ${v.is_no_claim ? '[ไม่เบิก]' : '[เบิก]'}`;
                    });
                },
                async showDayDetail(person, day, dutyGroup = null) {
                    const vens = person.dayMap[day] || [];
                    if (vens.length === 0) return;

                    this.modal = {
                        show: true,
                        loading: true,
                        day: day,
                        vensData: [],
                        venComs: {},
                        targetGroupKey: dutyGroup ? (dutyGroup.ven_name + '|' + dutyGroup.DN + '|' + (dutyGroup.is_no_claim ? 'NC' : 'C')) : null,
                        showAllOnDay: false
                    };
                    this.modalUserSearch = '';
                    this.modalUserFilter = 'all';

                    // Auto-set filter based on first duty's role
                    if (vens.length > 0) {
                        const firstRole = vens[0].ven_name || '';
                        if (firstRole.includes('ผู้พิพากษา')) {
                            this.modalUserFilter = 'judge';
                        } else {
                            this.modalUserFilter = 'staff';
                        }
                    }

                    try {
                        for (let v of vens) {
                            const res = await axios.post('../../server/asu/ven_set/get_ven.php', {
                                id: v.id
                            });
                            if (res.data.status) {
                                let vData = res.data.respJSON;
                                if (!vData.ven_com_id) {
                                    vData.ven_com_id = [];
                                } else if (!Array.isArray(vData.ven_com_id)) {
                                    vData.ven_com_id = [String(vData.ven_com_id)];
                                } else {
                                    vData.ven_com_id = vData.ven_com_id.map(id => String(id));
                                }
                                vData.ven_com_idb = vData.ven_com_idb ? String(vData.ven_com_idb) : '';

                                vData.u_id = vData.user_id;
                                this.modal.vensData.push(vData);
                                this.modal.venComs[vData.id] = res.data.ven_coms || [];
                            }
                        }
                    } catch (err) {
                        console.error(err);
                    } finally {
                        this.modal.loading = false;
                    }
                },
                toggleSidebar() {
                    const sidebar = document.getElementById('sidebar');
                    if (sidebar) sidebar.classList.toggle('active');
                },
                closeModal() {
                    this.modal.show = false;
                },
                venDisOpen(v) {
                    Swal.fire({
                        title: 'ยืนยันการเปลี่ยนสถานะ?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'ตกลง',
                        cancelButtonText: 'ยกเลิก'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            axios.post('../../server/asu/ven_set/ven_dis_open.php', {
                                    id: v.id
                                })
                                .then(res => {
                                    if (res.data.status) {
                                        this.fetchDutyDetail(v);
                                        this.fetchData(); // Refresh main list
                                        this.alert('success', res.data.message, 1000);
                                    }
                                });
                        }
                    });
                },
                venSave(v) {
                    // Sync: ensure ven_com_idb is within the selected ven_com_id array
                    if (v.ven_com_id && v.ven_com_id.length > 0) {
                        if (!v.ven_com_id.includes(v.ven_com_idb)) {
                            v.ven_com_idb = v.ven_com_id[0];
                        }
                    } else {
                        v.ven_com_idb = '';
                    }

                    axios.post('../../server/asu/ven_set/ven_up_vcid.php', {
                            data_event: v
                        })
                        .then(res => {
                            if (res.data.status) {
                                this.fetchDutyDetail(v);
                                this.fetchData(); // Refresh main list
                                this.alert('success', 'บันทึกสำเร็จ', 1000);
                            }
                        });
                },
                venSave2(v) {
                    // Sync: if a claiming command is selected, it MUST be in the ven_com_id list
                    if (v.ven_com_idb && !v.ven_com_id.includes(v.ven_com_idb)) {
                        v.ven_com_id.push(v.ven_com_idb);
                        // Update both by using venSave
                        this.venSave(v);
                        return;
                    }

                    axios.post('../../server/asu/ven_set/ven_up_vcid2.php', {
                            data_event: v
                        })
                        .then(res => {
                            if (res.data.status) {
                                this.fetchDutyDetail(v);
                                this.fetchData(); // Refresh main list
                                this.alert('success', 'บันทึกสำเร็จ', 1000);
                            }
                        });
                },
                async fetchDutyDetail(v) {
                    const res = await axios.post('../../server/asu/ven_set/get_ven.php', {
                        id: v.id
                    });
                    if (res.data.status) {
                        let vData = res.data.respJSON;
                        if (!vData.ven_com_id) {
                            vData.ven_com_id = [];
                        } else if (!Array.isArray(vData.ven_com_id)) {
                            vData.ven_com_id = [String(vData.ven_com_id)];
                        } else {
                            vData.ven_com_id = vData.ven_com_id.map(id => String(id));
                        }
                        vData.ven_com_idb = vData.ven_com_idb ? String(vData.ven_com_idb) : '';

                        // Update the item in the modal's list
                        const idx = this.modal.vensData.findIndex(item => item.id === v.id);
                        if (idx !== -1) {
                            this.modal.vensData.splice(idx, 1, vData);
                            this.modal.venComs[vData.id] = res.data.ven_coms || [];
                        }
                    }
                },
                venDel(v) {
                    Swal.fire({
                        title: 'ยืนยันการลบ?',
                        text: "ต้องการลบเวรนี้ใช่หรือไม่!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        confirmButtonText: 'ใช่, ลบเลย!',
                        cancelButtonText: 'ยกเลิก'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            axios.post('../../server/asu/ven_set/ven_del.php', {
                                    id: v.id
                                })
                                .then(res => {
                                    if (res.data.status) {
                                        this.modal.vensData = this.modal.vensData.filter(item => item.id !== v.id);
                                        if (this.modal.vensData.length === 0) this.closeModal();
                                        this.fetchData();
                                        this.alert('success', res.data.message, 1000);
                                    }
                                });
                        }
                    });
                },
                venTransfer(v) {
                    Swal.fire({
                        title: 'ยืนยันการเปลี่ยนคนอยู่เวร?',
                        text: "ระบบจะเปลี่ยนตัวผู้อยู่เวรทันที (ไม่ต้องผ่านหลายทอด)",
                        icon: 'info',
                        showCancelButton: true,
                        confirmButtonText: 'ตกลง',
                        cancelButtonText: 'ยกเลิก'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            axios.post('./ven/api/ven_transfer.php', {
                                    id: v.id,
                                    new_user_id: v.u_id
                                })
                                .then(res => {
                                    if (res.data.status) {
                                        this.fetchDutyDetail(v);
                                        this.fetchData(); // Refresh main list
                                        this.alert('success', res.data.message, 1500);
                                    } else {
                                        this.alert('error', res.data.message, 2000);
                                    }
                                });
                        }
                    });
                },
                alert(icon, message, timer = 0) {
                    Swal.fire({
                        position: 'top-end',
                        icon: icon,
                        title: message,
                        showConfirmButton: false,
                        timer: timer
                    });
                },
                printDutyType() {
                    this.isLoading = true;
                    const ven_month = `${this.sel_year}-${this.sel_month}`;
                    let stored = localStorage.getItem('excluded_duties_' + ven_month);
                    let excluded = stored ? JSON.parse(stored) : [];

                    axios.post('../../server/asu/report/report_dutytype.php', {
                        ven_month: ven_month,
                        excluded_duties: excluded
                    }).then(res => {
                        if (res.data.status) {
                            localStorage.setItem("print_dutytype", JSON.stringify(res.data));
                            window.open('../asu/report-print-dutytype.php', '_blank');
                        } else {
                            this.alert('warning', res.data.message || 'ไม่พบข้อมูล', 1500);
                        }
                    }).catch(err => {
                        console.error(err);
                        this.alert('error', 'เกิดข้อผิดพลาดในการโหลดข้อมูล', 1500);
                    }).finally(() => {
                        this.isLoading = false;
                    });
                },

                /* ========== DOUBLE-CLICK SWAP ========== */
                isJudge(workgroup) {
                    return (workgroup || '').includes('ผู้พิพากษา');
                },
                getDayVen(person, day, duty) {
                    if (!person.dayMap[day]) return null;
                    const gKey = duty.ven_name + '|' + duty.DN + '|' + (duty.is_no_claim ? 'NC' : 'C');
                    const match = person.dayMap[day].find(v => {
                        const vKey = v.ven_name + '|' + v.DN + '|' + (v.is_no_claim ? 'NC' : 'C');
                        return vKey === gKey;
                    });
                    return match ? match.id : null;
                },
                isSelectedSrc(person, day, duty) {
                    if (!this.swap.selected) return false;
                    return this.swap.selected.personUid === person.uid &&
                        this.swap.selected.day === day &&
                        this.swap.selected.dutyName === duty.ven_name &&
                        this.swap.selected.DN === duty.DN;
                },
                isClickTarget(person, duty, day) {
                    if (!this.swap.selected) return false;
                    if (this.swap.selected.personUid === person.uid) return false;

                    const srcName = (this.swap.selected.dutyName || '').trim();
                    const dstName = (duty.ven_name || '').trim();
                    const srcDN = (this.swap.selected.DN || '').trim();
                    const dstDN = (duty.DN || '').trim();
                    const srcCom = (this.swap.selected.ven_com_idb || '').toString().trim();
                    const dstCom = (duty.ven_com_idb || '').toString().trim();

                    if (dstName !== srcName) return false;
                    if (dstDN !== srcDN) return false;
                    if (dstCom !== srcCom) return false;
                    if (!duty.days.includes(day)) return false;

                    // ผู้พิพากษาแลกได้เฉพาะกับผู้พิพากษา ใช้ workgroup จาก profile
                    const srcIsJudge = this.isJudge(this.swap.selected.workgroup);
                    const dstIsJudge = this.isJudge(person.workgroup);
                    if (srcIsJudge && !dstIsJudge) return false;
                    if (!srcIsJudge && dstIsJudge) return false;

                    return true;
                },
                onDblClick(person, duty, day) {
                    // ถ้าไม่อยู่ในโหมดแลกเวร = เปิด modal
                    if (!this.swapMode) {
                        this.showDayDetail(person, day, duty);
                        return;
                    }
                    const venId = this.getDayVen(person, day, duty);
                    if (!venId) return;
                    // ดับเบิ้ลคลิกซ้ำ = ยกเลิก
                    if (this.swap.selected && this.swap.selected.venId === venId) {
                        this.swap.selected = null;
                        return;
                    }
                    // เลือกเป็นต้นทาง
                    this.swap.selected = {
                        venId,
                        day,
                        dutyName: duty.ven_name,
                        DN: duty.DN,
                        ven_com_idb: duty.ven_com_idb || '',
                        personName: person.name,
                        personUid: person.uid,
                        workgroup: person.workgroup || '' // ใช้ workgroup จาก profile เป็นตัวบ่งชี้ตำแหน่ง
                    };
                },
                async onChipClick(person, duty, day) {
                    // ถ้าไม่อยู่ในโหมดแลกเวร = เปิด modal ปกติ
                    if (!this.swapMode) {
                        this.showDayDetail(person, day, duty);
                        return;
                    }
                    // ถ้าไม่มีต้นทาง = ไม่ทำอะไร (ให้ดับเบิ้ลคลิกเลือกก่อน)
                    if (!this.swap.selected) return;
                    // คลิกที่ตัวเอง = ยกเลิก
                    if (this.swap.selected.personUid === person.uid && this.swap.selected.day === day) {
                        this.swap.selected = null;
                        return;
                    }
                    // ตรวจเงื่อนไข
                    if (!this.isClickTarget(person, duty, day)) {
                        const srcIsJudge = this.isJudge(this.swap.selected.workgroup);
                        const dstIsJudge = this.isJudge(person.workgroup);
                        let reason = 'ต้องแลกเฉพาะเวรที่:<br>&bull; <b>ชื่อเวรเดียวกัน</b><br>&bull; <b>ประเภทเดียวกัน</b> (กลางวัน/กลางคืน)<br>&bull; <b>คำสั่งเดียวกัน</b>';

                        if ((srcIsJudge && !dstIsJudge) || (!srcIsJudge && dstIsJudge)) {
                            reason = '<b>ผู้พิพากษาแลกได้เฉพาะกับผู้พิพากษาเท่านั้น</b><br>ตำแหน่งอื่นๆ แลกได้เฉพาะกันเอง';
                        } else {
                            const srcN = (this.swap.selected.dutyName || '').trim();
                            const dstN = (duty.ven_name || '').trim();
                            const srcDN = (this.swap.selected.DN || '').trim();
                            const dstDN = (duty.DN || '').trim();
                            const srcCom = (this.swap.selected.ven_com_idb || '').toString().trim();
                            const dstCom = (duty.ven_com_idb || '').toString().trim();
                            if (srcN !== dstN) reason = `ชื่อเวรไม่ตรงกัน:<br>ต้นทาง: ${srcN}<br>ปลายทาง: ${dstN}`;
                            else if (srcDN !== dstDN) reason = `ประเภทเวร (กลางวัน/กลางคืน) ไม่ตรงกัน`;
                            else if (srcCom !== dstCom) reason = `คำสั่งเวรไม่ตรงกัน (คนละเลขที่คำสั่ง)`;
                        }

                        Swal.fire({
                            icon: 'warning',
                            title: 'แลกเวรไม่ได้',
                            html: `<div style="text-align:left;font-size:14px;line-height:1.8">${reason}</div>`,
                            confirmButtonText: 'ตกลง'
                        });
                        return;
                    }
                    const src = this.swap.selected;
                    const dstVenId = this.getDayVen(person, day, duty);
                    if (!dstVenId) {
                        this.alert('warning', 'ไม่พบเวรปลายทาง', 1500);
                        return;
                    }
                    const captureId_a = src.venId;
                    const captureId_b = dstVenId;
                    const srcName = src.personName;
                    const dstName = person.name;
                    this.swap.selected = null;

                    const result = await Swal.fire({
                        title: '🔄 ยืนยันการแลกเวร',
                        html: `<div style="text-align:left;font-size:14px;line-height:2">
                                <b>ต้นทาง:</b> ${srcName} — วันที่ ${this.pad(src.day)}<br>
                                <b>ปลายทาง:</b> ${dstName} — วันที่ ${this.pad(day)}<br>
                                <b>เวร:</b> ${this.formatDutyName(src.dutyName)} (${src.DN})<br>
                                <span style="color:#e53935">⚠️ ไม่ตรวจสอบเวรชน</span>
                               </div>`,
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: '✅ แลกเวรเลย',
                        cancelButtonText: 'ยกเลิก',
                        confirmButtonColor: '#1a237e'
                    });

                    if (!result.isConfirmed) return;

                    try {
                        const res = await axios.post('../../server/asu/ven_set/ven_swap.php', {
                            id_a: captureId_a,
                            id_b: captureId_b
                        });
                        if (res.data.status) {
                            this.alert('success', 'แลกเวรสำเร็จ! 🎉', 1500);
                            this.fetchData();
                        } else {
                            this.alert('error', res.data.message || 'เกิดข้อผิดพลาด', 2000);
                        }
                    } catch (err) {
                        console.error(err);
                        this.alert('error', 'เชื่อมต่อเซิร์ฟเวอร์ไม่ได้', 2000);
                    }
                },
                getFilteredUsers(type = null) {
                    let list = this.users_list;

                    // Filter by Judge/Staff type
                    if (type === 'judge') {
                        list = list.filter(u => (u.workgroup || '').includes('ผู้พิพากษา'));
                    } else if (type === 'staff') {
                        list = list.filter(u => !(u.workgroup || '').includes('ผู้พิพากษา'));
                    }

                    // Filter by Search text
                    if (this.modalUserSearch.trim()) {
                        const q = this.modalUserSearch.toLowerCase();
                        list = list.filter(u =>
                            (u.fname + u.name + ' ' + u.sname).toLowerCase().includes(q) ||
                            (u.workgroup || '').toLowerCase().includes(q)
                        );
                    }

                    return list;
                },
                isCommandMatch(dutyName, commandName) {
                    if (!dutyName || !commandName) return false;
                    const d = dutyName.toLowerCase();
                    const c = commandName.toLowerCase();

                    if (d.includes('หมายจับ') || d.includes('ค้น')) {
                        return c.includes('หมายจับ') || c.includes('ค้น');
                    } else if (d.includes('ศาลแขวง')) {
                        return c.includes('ศาลแขวง');
                    } else if (d.includes('เปิดทำการ')) {
                        return c.includes('เปิดทำการ');
                    } else if (d.includes('ตรวจสอบการจับ')) {
                        return c.includes('ตรวจสอบการจับ');
                    }
                    return d === c;
                },
                getFilteredVenComs(v) {
                    const allComs = this.modal.venComs[v.id] || [];
                    const filtered = allComs.filter(vc => this.isCommandMatch(v.ven_name, vc.name));
                    // If no match found, show all to avoid empty list
                    return filtered.length > 0 ? filtered : allComs;
                },
                isNoClaim(v) {
                    if (!v) return false;
                    const price = parseFloat(v.price || 0);
                    if (price <= 0) return true;

                    const com_id = v.ven_com_id;
                    const com_id_empty = !com_id || (Array.isArray(com_id) && com_id.length === 0);
                    const com_idb = (v.ven_com_idb || '').toString().trim();
                    const com_num = (v.ven_com_num_all || '').toString().trim();

                    return com_id_empty &&
                        (com_idb === '' || com_idb === 'null') &&
                        (com_num === '' || com_num === 'null');
                }
            }
        }).mount('#main');
    </script>
</body>

</html>