<?php 
require_once('../../server/authen.php'); 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php require_once('../includes/_header.php') ?>
    <style>
        [v-cloak] > * { display: none; }
        [v-cloak]::before { content: "loading..."; }
        .schedule-table th, .schedule-table td {
            vertical-align: middle;
            font-size: 14px;
        }
        .day-weekend { background-color: #fff3cd !important; }
        .day-row:hover { background-color: #e8f4fd !important; }
        .badge-remove {
            cursor: pointer;
            transition: all 0.2s;
        }
        .badge-remove:hover {
            opacity: 0.7;
            transform: scale(1.05);
        }
        .user-list-item {
            cursor: pointer;
            transition: all 0.15s;
            user-select: none;
        }
        .user-list-item:hover { background-color: #e3f2fd !important; }
        .user-list-item.selected { background-color: #bbdefb !important; border-left: 4px solid #1976d2; }
        .assign-select {
            font-size: 13px;
            padding: 2px 6px;
            height: 32px;
        }
        .warning-cell { background-color: #ffebee !important; }
        .preloader {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(255,255,255,0.7); z-index: 9999;
            display: flex; align-items: center; justify-content: center;
        }
        .day-row.drag-over {
            background-color: #c8e6c9 !important;
            border: 2px dashed #4caf50 !important;
        }
        .user-list-item.dragging {
            opacity: 0.5;
            background-color: #f5f5f5 !important;
            border: 2px dashed #ccc !important;
        }
    </style>
</head>
<body>
    <div id="app">
        <?php require_once('../includes/_sidebar.php') ?>
        <div id="main">
            <header class="mb-3">
                <a href="#" class="burger-btn d-block">
                    <i class="bi bi-justify fs-3"></i>
                </a>
            </header>

            <div class="page-heading d-print-none">
                <h3>⚡ จัดเวรอย่างรวดเร็ว (Quick Assign)</h3>
            </div>

            <div class="page-content" id="venQuick" v-cloak>

                <!-- Loading -->
                <div class="preloader" v-if="isLoading">
                    <div class="text-center">
                        <div class="spinner-border text-primary" style="width:3rem;height:3rem;" role="status"></div>
                        <p class="mt-2 text-muted">กำลังประมวลผล...</p>
                    </div>
                </div>

                <section class="row">
                    <!-- Left Panel: User Queue -->
                    <div class="col-12 col-lg-3">
                        <div class="card" style="position: sticky; top: 70px; max-height: calc(100vh - 90px); overflow-y: auto;">
                            <div class="card-header bg-primary text-white py-2">
                                <h6 class="mb-0">📋 ตั้งค่า & รายชื่อคิว</h6>
                            </div>
                            <div class="card-body p-2">
                                <!-- เลือกเดือน -->
                                <label class="form-label fw-bold mb-1 small">เดือน</label>
                                <select class="form-select form-select-sm mb-2" v-model="ven_month" @change="ch_sel_ven_month()">
                                    <option v-for="m in months" :value="m.ven_month">{{m.name}}</option>
                                </select>

                                <!-- เลือกคำสั่ง -->
                                <label class="form-label fw-bold mb-1 small">คำสั่งเวร</label>
                                <select class="form-select form-select-sm mb-2" v-model="vc_index" @change="ch_sel_ven_name(vc_index)">
                                    <option value="">-- เลือก --</option>
                                    <option v-for="(vc,i) in ven_coms" :value="i">{{vc.ven_com_num}} {{vc.name}}</option>
                                </select>

                                <!-- เลือกตำแหน่ง -->
                                <label class="form-label fw-bold mb-1 small">ตำแหน่ง/หน้าที่</label>
                                <select class="form-select form-select-sm mb-2" v-model="vns_index" @change="ch_sel_vns(vns_index)">
                                    <option value="">-- เลือก --</option>
                                    <option v-for="(vns,i) in ven_name_subs" :value="i">{{vns.name}}</option>
                                </select>

                                <hr class="my-2">

                                <!-- รายชื่อ -->
                                <div v-if="profiles.length > 0">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <strong class="small">รายชื่อ ({{profiles.length}} คน)</strong>
                                        <button class="btn btn-outline-primary btn-sm py-0 px-2" @click="selectAll()" style="font-size:12px;">
                                            {{ selected_users.length === profiles.length ? '☐ ยกเลิก' : '☑ เลือกทั้งหมด' }}
                                        </button>
                                    </div>
                                    <div style="max-height: 350px; overflow-y: auto;">
                                        <div v-for="(pf, idx) in profiles" :key="pf.uid"
                                             class="user-list-item p-2 mb-1 rounded border d-flex align-items-center justify-content-between"
                                             :class="{ 'selected': selected_users.includes(pf.uid), 'dragging': draggedUserIndex === idx }"
                                             draggable="true"
                                             @dragstart="onDragStart($event, pf.uid, idx)"
                                             @dragover.prevent
                                             @drop.stop="onUserDrop(idx)">
                                            <div class="d-flex align-items-center" @click="toggleUser(pf.uid)">
                                                <input type="checkbox" :checked="selected_users.includes(pf.uid)" class="me-2" @click.stop="toggleUser(pf.uid)">
                                                <span class="small">{{idx + 1}}. {{pf.u_name}}</span>
                                            </div>
                                            <div class="d-flex flex-column ms-2">
                                                <i class="bi bi-caret-up-fill text-primary" style="cursor:pointer; line-height: 0.5;" @click.stop="moveUser(idx, -1)" v-if="idx > 0"></i>
                                                <i class="bi bi-caret-down-fill text-primary" style="cursor:pointer; line-height: 0.5;" @click.stop="moveUser(idx, 1)" v-if="idx < profiles.length - 1"></i>
                                            </div>
                                        </div>
                                    </div>

                                    <hr class="my-2">
                                    <div class="d-grid gap-1">
                                        <div class="d-flex align-items-center mb-1">
                                            <label class="small fw-bold me-2 text-nowrap">คนต่อวัน:</label>
                                            <input type="number" min="1" max="10" v-model.number="persons_per_day" class="form-control form-control-sm" style="width:70px;">
                                        </div>
                                        <button class="btn btn-success btn-sm" @click="autoAssign()" :disabled="selected_users.length === 0">
                                            🔄 จัดอัตโนมัติ ({{selected_users.length}} คน, วันละ {{persons_per_day}} คน)
                                        </button>
                                        <button class="btn btn-outline-danger btn-sm" @click="clearAll()" v-if="schedule.some(s => s.assignments.length > 0)">
                                            🗑️ ล้างเวรทั้งเดือน
                                        </button>
                                    </div>
                                </div>
                                <div v-else class="text-muted text-center py-3 small">
                                    <i class="bi bi-arrow-up-circle"></i><br>
                                    กรุณาเลือกเดือน คำสั่ง และตำแหน่ง
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Panel: Schedule Grid -->
                    <div class="col-12 col-lg-9">
                        <div class="card">
                            <div class="card-header bg-dark text-white py-2 d-flex justify-content-between align-items-center">
                                <h6 class="mb-0">📅 ตารางจัดเวร</h6>
                                <span class="badge bg-light text-dark" v-if="ven_com">
                                    {{ven_com.name}} | {{ven_name_sub.name || ''}}
                                </span>
                            </div>
                            <div class="card-body p-0" v-if="schedule.length > 0">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-sm schedule-table mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th style="width:50px;" class="text-center">วัน</th>
                                                <th style="width:100px;" class="text-center">วันที่</th>
                                                <th>ผู้อยู่เวร</th>
                                                <th style="width:200px;">เพิ่มคน</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                                <tr v-for="(day, dIdx) in schedule" :key="day.ven_date"
                                                class="day-row"
                                                :class="{ 'day-weekend': isWeekend(day.ven_date) || isHoliday(day.ven_date), 'drag-over': draggedOverIndex === dIdx }"
                                                @dragover.prevent
                                                @dragenter="onDragEnter($event, dIdx)"
                                                @dragleave="onDragLeave($event, dIdx)"
                                                @drop="onDrop($event, dIdx)">
                                                <td class="text-center fw-bold" :class="{ 'text-danger': isWeekend(day.ven_date) || isHoliday(day.ven_date) }">
                                                    {{ getDayName(day.ven_date) }}
                                                </td>
                                                <td class="text-center">
                                                    {{ getDateNum(day.ven_date) }}
                                                    <div v-if="isHoliday(day.ven_date)" class="text-danger fw-bold" style="font-size: 10px;">
                                                        {{ getHolidayName(day.ven_date) }}
                                                    </div>
                                                </td>
                                                <td>
                                                    <span v-if="day.assignments.length === 0" class="text-muted small">- ว่าง -</span>
                                                    <span v-for="(a, aIdx) in day.assignments" :key="a.id"
                                                          class="badge me-1 mb-1 badge-remove"
                                                          :style="{ backgroundColor: a.color || '#6c757d', color: '#fff', fontSize: '13px' }"
                                                          :title="(a.vu_order && a.vu_order < 999 ? 'ลำดับ ' + a.vu_order + ' | ' : '') + (a.comment ? '⚠️ ' + a.comment : '')"
                                                          @click="removeAssignment(a.id)">
                                                        {{ a.vu_order && a.vu_order < 999 ? a.vu_order + '.' : (aIdx+1) + '.' }} {{ a.name }} ✕
                                                    </span>
                                                </td>
                                                <td>
                                                    <select class="form-select assign-select"
                                                            @change="assignUser(dIdx, $event.target.value); $event.target.value = ''">
                                                        <option value="">+ เลือกคน</option>
                                                        <option v-for="pf in profiles" :value="pf.uid">{{ pf.u_name }}</option>
                                                    </select>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="card-body text-center text-muted py-5" v-else>
                                <h1>📅</h1>
                                <p>กรุณาเลือกเดือน คำสั่งเวร และตำแหน่ง<br>เพื่อแสดงตารางจัดเวร</p>
                            </div>
                        </div>
                    </div>
                </section>
            </div>

            <?php require_once('../includes/_footer.php') ?>
        </div>
    </div>

    <?php require_once('../includes/_footer_sc.php') ?>
    <script src="../../node_modules/vue/dist/vue.global.js"></script>
    <script src="../../node_modules/vue/dist/vue.global.prod.js"></script>
    <script src="../../node_modules/axios/dist/axios.js"></script>
    <script src="../../node_modules/sweetalert2/dist/sweetalert2.min.js"></script>
    <script src="./js/ven_quick.js?v=<?= time() ?>"></script>
</body>
</html>
