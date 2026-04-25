<?php 

require_once('../../server/authen.php');

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php require_once('../includes/_header.php') ?>
    <style>
        .duty-item {
            transition: all 0.2s;
            border-left: 5px solid transparent;
        }
        .duty-item:hover {
            background-color: #f8fafc;
            transform: translateX(5px);
        }
        .date-badge {
            min-width: 100px;
            text-align: center;
        }
        .status-badge {
            font-size: 0.75rem;
            padding: 0.35em 0.65em;
        }
        .search-container {
            position: sticky;
            top: 0;
            z-index: 10;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            padding: 1rem 0;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body class="theme-dark">
    <div id="app">
        <?php require_once('../includes/_sidebar.php') ?>
        <div id="main">
            <header class="mb-3">
                <a href="#" class="burger-btn d-block">
                    <i class="bi bi-justify fs-3"></i>
                </a>
            </header>

            <div class="page-heading">
                <div class="d-flex justify-content-between align-items-center">
                    <h3>รายการเวร (มุมมองแบบรายการ)</h3>
                    <a href="./index.php" class="btn btn-outline-primary btn-sm">
                        <i class="bi bi-calendar3 me-1"></i> สลับเป็นมุมมองปฏิทิน
                    </a>
                </div>
            </div> 

            <div class="content-wrapper" id="list-view">
                <!-- Preloader -->
                <div class="preloader" v-if="isLoading">
                    <div class="loader">
                        <div class="spinner"></div>
                        <div class="text">กำลังโหลดข้อมูล...</div>
                    </div>
                </div>

                <div class="container-fluid">
                    <!-- Filters & Search -->
                    <div class="card shadow-sm mb-4">
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <div class="input-group">
                                        <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-primary"></i></span>
                                        <input type="text" v-model="search" class="form-control border-start-0" placeholder="ค้นหาชื่อ, กลุ่มหน้าที่...">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <select v-model="filter_month" class="form-select">
                                        <option value="">ทุกเดือน</option>
                                        <option v-for="(m, i) in months" :key="i" :value="i + 1">{{ m }}</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <select v-model="filter_year" class="form-select">
                                        <option value="">ทุกปี</option>
                                        <option v-for="y in years" :key="y" :value="y">{{ y + 543 }}</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <button @click="filterMyDuty = !filterMyDuty" 
                                            class="btn w-100" 
                                            :class="filterMyDuty ? 'btn-primary' : 'btn-outline-primary'">
                                        <i class="bi" :class="filterMyDuty ? 'bi-person-check-fill' : 'bi-person'"></i>
                                        เวรของฉัน
                                    </button>
                                </div>
                            </div>
                            
                            <!-- Duty Types Legend with Colors -->
                            <div class="mt-4">
                                <h6 class="mb-3 text-muted small"><i class="bi bi-tag-fill me-2"></i>เลือกแสดงตามชื่อเวร:</h6>
                                <div class="d-flex flex-wrap gap-2">
                                    <div v-for="type in dutyStats" :key="type.key" 
                                         @click="toggleType(type.key)"
                                         class="px-3 py-2 rounded-3 border d-flex align-items-center shadow-sm cursor-pointer"
                                         :style="{ 
                                             borderLeft: '5px solid ' + type.color, 
                                             backgroundColor: type.active ? '#f8fafc' : '#ffffff',
                                             opacity: type.active ? 1 : 0.4,
                                             filter: type.active ? 'none' : 'grayscale(0.5)',
                                             cursor: 'pointer',
                                             transition: 'all 0.2s'
                                         }">
                                        <div class="me-3">
                                            <div class="fw-bold small">{{ type.name }}</div>
                                        </div>
                                        <div class="ms-auto d-flex align-items-center">
                                            <div class="fw-bold me-2" :class="type.active ? 'text-primary' : 'text-muted'">{{ type.count }}</div>
                                            <i class="bi" :class="type.active ? 'bi-check-circle-fill text-success' : 'bi-circle text-muted'"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- List Content -->
                    <div v-if="groupedEvents.length > 0">
                        <div v-for="group in groupedEvents" :key="group.date" class="mb-5">
                            <div class="d-flex align-items-center mb-3">
                                <h4 class="mb-0 text-primary fw-bold">{{ group.dateText }}</h4>
                                <hr class="flex-grow-1 ms-3 opacity-25">
                            </div>
                            
                            <div v-for="dg in group.dutyGroups" :key="dg.color + dg.name" class="mb-4 ps-md-4">
                                <div class="d-flex align-items-center mb-2">
                                    <span class="badge rounded-pill me-2" :style="{ backgroundColor: dg.color, width: '12px', height: '12px', padding: 0 }">&nbsp;</span>
                                    <h6 class="mb-0 text-secondary">{{ dg.name }}</h6>
                                </div>
                                <div class="row row-cols-1 row-cols-lg-2 g-3">
                                    <div v-for="event in dg.events" :key="event.id" class="col">
                                        <div class="card h-100 shadow-sm duty-item border-0" :style="{ borderLeft: '5px solid ' + event.backgroundColor }">
                                            <div class="card-body p-3">
                                                <div class="d-flex justify-content-between align-items-start mb-2">
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar avatar-md me-3">
                                                            <img :src="event.extendedProps.img || '../../assets/images/faces/1.jpg'" alt="Face">
                                                        </div>
                                                        <div>
                                                            <h6 class="mb-0">{{ event.extendedProps.u_name }}</h6>
                                                            <small class="text-muted">{{ event.extendedProps.u_role }}</small>
                                                        </div>
                                                    </div>
                                                    <div class="text-end">
                                                        <span class="badge" :class="event.extendedProps.DN == 'กลางคืน' ? 'bg-dark' : 'bg-warning text-dark'">
                                                            {{ event.extendedProps.DN == 'กลางคืน' ? '🌙 กลางคืน' : '☀️ กลางวัน' }}
                                                         </span>
                                                         <div v-if="event.extendedProps.user_id == currentUserId" class="mt-1">
                                                             <span class="badge bg-info text-dark small"><i class="bi bi-person-fill me-1"></i>เวรของฉัน</span>
                                                         </div>
                                                         <div class="small text-muted mt-1">{{ event.extendedProps.ven_time }}</div>
                                                     </div>
                                                </div>
                                                <div v-if="event.comment" class="bg-light p-2 rounded-2 mt-2">
                                                    <div class="small text-danger">
                                                        <i class="bi bi-exclamation-circle me-1"></i>{{ event.comment }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div v-else class="text-center py-5">
                        <i class="bi bi-clipboard-x fs-1 text-muted"></i>
                        <p class="mt-3 text-muted">ไม่พบข้อมูลเวรที่ตรงกับเงื่อนไข</p>
                    </div>
                </div>
            </div>

            <?php require_once('../includes/_footer.php') ?>
        </div>
    </div>

    <?php require_once('../includes/_footer_sc.php') ?>
    <script src="../../node_modules/vue/dist/vue.global.js"></script>
    <script src="../../node_modules/axios/dist/axios.js"></script>
    <script src="./list.js?v=<?php echo time(); ?>"></script>
</body>
</html>
