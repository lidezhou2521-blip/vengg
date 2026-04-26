<?php 

require_once('../../server/authen.php'); 

?>
<!DOCTYPE html>
<html lang="en">
<head>
<?php require_once('../includes/_header.php') ?>
<style>
  [v-cloak] > * { display:none; }
  [v-cloak]::before { content: "loading..."; }

  /* User list panel */
  .user-panel {
    background: #ffffff;
    border-radius: 12px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.04);
    padding: 15px;
    max-height: 80vh;
    overflow-y: auto;
    position: sticky;
    top: 10px;
  }

  .user-panel .user-item {
    cursor: grab;
    margin: 5px 0;
    padding: 9px 12px;
    border-radius: 8px;
    background: #ffffff;
    box-shadow: 0 2px 6px rgba(0,0,0,0.04);
    border-left: 4px solid #435ebe;
    font-weight: 500;
    font-size: 0.88rem;
    transition: all 0.2s;
    user-select: none;
  }

  .user-panel .user-item:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    background: #f1f5f9;
  }

  .user-panel .user-item:active { cursor: grabbing; }
  .user-panel .user-item.dragging { opacity: 0.4; }

  /* Drop zone cards */
  .duty-group-card {
    background: #ffffff;
    border-radius: 12px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.04);
    margin-bottom: 16px;
    overflow: hidden;
  }

  .duty-group-header {
    padding: 10px 16px;
    color: #fff;
    font-weight: 700;
    font-size: 0.9rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
  }

  .duty-group-body {
    padding: 10px 14px;
    min-height: 50px;
    transition: background 0.2s;
  }

  .duty-group-body.drag-over {
    background: #ebf5ff;
    border: 2px dashed #435ebe;
    border-radius: 0 0 12px 12px;
  }

  .duty-group-body .members-wrap {
    display: flex;
    flex-wrap: wrap;
    gap: 5px;
  }

  .duty-group-body .member-item {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 4px 8px;
    margin: 0;
    background: #f8fafc;
    border-radius: 20px;
    font-size: 0.82rem;
    border: 1px solid #e2e8f0;
    white-space: nowrap;
  }

  .duty-group-body .member-item .btn-sm {
    padding: 0 4px;
    font-size: 0.68rem;
    line-height: 1;
    border-radius: 50%;
  }

  .duty-group-body .member-item[draggable="true"] {
    cursor: grab;
  }

  .duty-group-body .member-item[draggable="true"]:active {
    cursor: grabbing;
  }

  .duty-group-body .member-item.dragging {
    opacity: 0.4;
  }

  .duty-group-body .member-item.member-drag-over {
    border-left: 3px solid #435ebe;
    background: #ebf5ff;
  }

  .drop-hint {
    text-align: center;
    color: #cbd5e0;
    font-size: 0.82rem;
    padding: 12px 0;
  }

  /* Custom scrollbar */
  .user-panel::-webkit-scrollbar { width: 4px; }
  .user-panel::-webkit-scrollbar-track { background: transparent; }
  .user-panel::-webkit-scrollbar-thumb { background: #cbd5e0; border-radius: 10px; }
</style>
</head>
<body>
<div id="app">
    <?php require_once('../includes/_sidebar.php') ?>
    <div id="main">
        <header class="mb-3">
            <a href="#" class="burger-btn d-block d-xl-none">
                <i class="bi bi-justify fs-3"></i>
            </a>
        </header>

        <div class="page-heading">
            <h3>เตรียมผู้อยู่เวร</h3>
        </div>

        <div class="page-content" id="venUser" v-cloak>


            <div class="row">
                <!-- Left: User List (draggable source) -->
                <div class="col-lg-3 col-md-4">
                    <div class="user-panel">
                        <h6 class="fw-bold mb-2">
                            <i class="bi bi-person-plus-fill text-primary me-1"></i>รายชื่อ
                        </h6>

                        <div class="mb-2">
                            <input type="text" v-model="q_filter" class="form-control form-control-sm" placeholder="🔍 ค้นหาชื่อ...">
                        </div>
                        <div class="mb-3">
                            <select class="form-select form-select-sm" v-model="workgroup_filter">
                                <option value="">ทั้งหมด</option>
                                <option v-for="wg in workgroups" :value="wg">{{wg}}</option>
                            </select>
                        </div>

                        <div class="text-muted small mb-2">
                            <i class="bi bi-grip-vertical"></i> ลากรายชื่อไปวาง ({{filtered_users.length}} คน)
                        </div>

                        <div v-for="u in filtered_users" :key="u.uid"
                             class="user-item"
                             draggable="true"
                             @dragstart="onDragStart($event, u)"
                             @dragend="onDragEnd($event)">
                            <i class="bi bi-grip-vertical me-1 opacity-50"></i>
                            {{u.name}}
                        </div>

                        <div v-if="filtered_users.length === 0" class="text-center py-3 text-muted small">
                            <i class="bi bi-person-dash fs-4 d-block mb-1"></i>
                            ไม่พบรายชื่อ
                        </div>
                    </div>
                </div>

                <!-- Right: Duty Groups (drop zones) -->
                <div class="col-lg-9 col-md-8">
                    <div class="row">
                        <div class="col-lg-6 col-md-12" v-for="(data, index) in datas" :key="data.vn_id + '-' + data.vns_id">
                            <div class="duty-group-card">
                                <div class="duty-group-header" :style="'background-color:' + data.color">
                                    <span>
                                        {{data.DN == 'กลางวัน' ? '☀️' : '🌙'}}
                                        {{data.vn_name}} - {{data.vns_name}}
                                    </span>
                                    <span>
                                        <span class="badge bg-white text-dark me-1" style="font-size:0.75rem">{{data.users ? data.users.length : 0}} คน</span>
                                        <button v-if="data.users && data.users.length > 0" class="btn btn-sm btn-light py-0 px-1" @click="vu_del_group(data)" title="ลบทั้งกลุ่ม">
                                            <i class="bi bi-trash text-danger" style="font-size:0.75rem"></i>
                                        </button>
                                    </span>
                                </div>
                                <div class="duty-group-body"
                                     @dragover.prevent="onDropZoneDragOver($event)"
                                     @dragleave="onDropZoneDragLeave($event)"
                                     @drop="onDropZoneDrop($event, data)">
                                    
                                    <div v-if="data.users && data.users.length > 0" class="members-wrap">
                                        <div class="member-item" 
                                             v-for="(u, mi) in data.users" :key="u.vu_id"
                                             draggable="true"
                                             @dragstart="onMemberDragStart($event, index, mi)"
                                             @dragend="onMemberDragEnd($event)"
                                             @dragover.prevent.stop="onMemberDragOver($event, index, mi)"
                                             @dragleave="onMemberDragLeave($event)"
                                             @drop.stop="onMemberDrop($event, index, mi)">
                                            <i class="bi bi-grip-vertical opacity-40" style="cursor:grab"></i>
                                            <span>{{u.order}}. {{u.name}}</span>
                                            <button class="btn btn-outline-danger btn-sm" @click="vu_del(u.vu_id)">
                                                <i class="bi bi-x"></i>
                                            </button>
                                        </div>
                                    </div>

                                    <div v-else class="drop-hint">
                                        <i class="bi bi-box-arrow-in-down fs-5 d-block mb-1"></i>
                                        ลากรายชื่อมาวางที่นี่
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <?php require_once('../includes/_footer.php') ?>
    </div>
</div>

<?php require_once('../includes/_footer_sc.php') ?>
<script src="../../node_modules/vue/dist/vue.global.js"></script>
<script src="../../node_modules/vue/dist/vue.global.prod.js"></script>
<script src="../../node_modules/axios/dist/axios.js"></script>
<script src="../../node_modules/sweetalert2/dist/sweetalert2.min.js"></script>
<script src="./js/ven_user.js?v=<?php echo time(); ?>"></script>

</body>
</html>