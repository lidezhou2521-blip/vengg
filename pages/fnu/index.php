<?php require_once('../../server/authen.php'); ?>
<!DOCTYPE html>
<html lang="th">

<head>
    <?php require_once('../includes/_header.php') ?>
    <style>
        [v-cloak] > * { display:none; }
        [v-cloak]::before { content:"กำลังโหลด..."; display:block; text-align:center; padding:60px; font-size:18px; color:#666; }
    </style>
</head>

<body>
    <div id="main-wrapper">
        <?php require_once('../includes/_sidebar.php') ?>
        <div id="main">
            <header class="mb-3 d-print-none">
                <a href="#" class="burger-btn d-inline-block position-relative">
                    <i class="bi bi-justify fs-3"></i>
                </a>
            </header>
            
            <div class="page-heading">
                <h3>หน้าหลักการเงิน</h3>
            </div>
            
            <div class="page-content" id="fnuIndex" v-cloak>
                <ul class="nav justify-content-center pt-3">
                    <li class="nav-item">
                        <select class="form-select" name="month" id="month" v-model="month" @change="loadData()">
                            <option value="01">มกราคม</option>
                            <option value="02">กุมภาพันธ์</option>
                            <option value="03">มีนาคม</option>
                            <option value="04">เมษายน</option>
                            <option value="05">พฤษภาคม</option>
                            <option value="06">มิถุนายน</option>
                            <option value="07">กรกฎาคม</option>
                            <option value="08">สิงหาคม</option>
                            <option value="09">กันยายน</option>
                            <option value="10">ตุลาคม</option>
                            <option value="11">พฤศจิกายน</option>
                            <option value="12">ธันวาคม</option>
                        </select>                
                    </li>
                    <li class="nav-item ms-2">
                        <select class="form-select" name="year" id="year" v-model="year" @change="loadData()">
                            <option v-for="y in year_select" :value="y">{{y+543}}</option>
                        </select>
                    </li>
                    <li class="nav-item ms-2">
                        <button type="button" class="btn btn-primary" @click="loadData()">FIND</button>
                    </li>
                    <li class="nav-item ms-3">
                        <a href="./report-check-overlap.php" target="_blank" class="btn" 
                            style="background:linear-gradient(135deg,#004d40,#00897b); color:#fff; border-radius:20px; padding:7px 18px; font-weight:700; box-shadow:0 2px 8px rgba(0,137,123,0.35); border:none; cursor:pointer; text-decoration:none;">
                            ✅ ตรวจสอบเวร / เช็คเวรชน
                        </a>
                    </li>
                </ul>
                <div class="row mt-4">
                    <div class="col-12 text-center p-2">
                        <h3 style="display:inline-block;">
                            เวรเดือน {{month_text}} จำนวน {{datas.length}} ราย
                        </h3>
                    </div>
                    <div class="col-8">
                        <div class="card mt-3">
                            <div class="card-body">
                                <table  class="table">
                                    <thead>                        
                                        <tr>
                                            <td class="text-center">ลำดับ</td>
                                            <td class="text-center">ชื่อ</td>
                                            <td class="text-center">☀️</td>
                                            <td class="text-center">🌙</td>
                                            <td class="text-center" v-for="vc in ven_coms">{{vc.vn_name}}</td>
                                            <td class="text-end">จำนวนเงิน</td>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(data, index) in datas" >
                                                <td class="text-center">{{index + 1}}</td>
                                                <td>{{data.name}}</td>
                                                <td class="text-center">{{data.D_c === 0 ? '-' : data.D_c}}</td>
                                                <td class="text-center">{{data.N_c === 0 ? '-' : data.N_c}}</td>
                                                <td class="text-center" v-for="dva in data.vcs_arr">
                                                    <div v-if="dva.v_count > 0 || dva.v_count_no_claim > 0">
                                                        <div v-if="dva.v_count > 0" class="mb-1">
                                                            {{dva.v_count}} วัน <small class="text-muted">({{formatCurrency(dva.price)}})</small>
                                                        </div>
                                                        <div v-if="dva.v_count_no_claim > 0">
                                                            {{dva.v_count_no_claim}} วัน <span class="badge bg-secondary" style="font-size:10px; font-weight:normal; opacity:0.8;">ไม่เบิก</span>
                                                        </div>
                                                    </div>
                                                    <span v-else style="color:#ccc;">-</span>
                                                </td>
                                                <td class="text-end fw-bold text-success">{{formatCurrency(data.price_sum)}}</td>                    
                                        </tr>
                                    </tbody>
                                    <tfoot class="table-light fw-bold">
                                        <tr>
                                            <td colspan="2" class="text-end">รวมทั้งสิ้น</td>
                                            <td class="text-center">{{ dayTotal === 0 ? '-' : dayTotal }}</td>
                                            <td class="text-center">{{ nightTotal === 0 ? '-' : nightTotal }}</td>
                                            <td class="text-center" v-for="(total, idx) in colTotals">
                                                {{ total === 0 ? '-' : formatCurrency(total) }}
                                            </td>
                                            <td class="text-end text-success" style="font-size: 1.1rem;">
                                                {{formatCurrency(price_all)}}
                                            </td>
                                        </tr>
                                    </tfoot>                    
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="card mt-3">
                            <div class="card-body">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <td colspan="2" class="text-center">รายการ <a href="../dashboard/index.php" target="_blank" rel="noopener noreferrer">โปรแกรมตารางเวร</a></td>
                                            <td> <a href="./ven/bank/index.html" target="_blank">ตั้งค่าบัญชีธนาคาร</a></td>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="vcs in ven_coms">
                                            <td></td>
                                            <td>
                                                {{vcs.ven_com_num}} <strong>{{vcs.vn_name}}</strong>
                                            </td>
                                            <td>
                                                <a :href="'./ven/report_a0.html?ven_com_id='+vcs.id+'&ven_month='+year+'-'+month" target="_blank">ใบขวางสรุป </a> 
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="2"></td>
                                            <td ><a :href="'./ven/report_x1.html?month='+year+'-'+month" target="_blank">หน้างบประกอบ(ทุกคำสั่ง)</a></td>
                                        </tr>
                                        <tr>
                                            <td colspan="2"></td>
                                            <td ><a href="#" @click="download_docx()">บันทึกขออนุมัติเบิกเงิน(.docx)</a></td>
                                        </tr>
                                    </tbody>
                                </table>
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
    <script src="../../node_modules/axios/dist/axios.js"></script>
    <script src="../../node_modules/sweetalert2/dist/sweetalert2.all.min.js"></script>

    <script>
    const { createApp } = Vue;

    createApp({
        data() {
            return {
                datas           : [],
                price_all       : 0,
                ven_coms       : [],
                day_num         : 0,
                holiday_num     : 0,
                DN_D_PRICE_DAY  : 0,
                DN_N_PRICE_DAY  : 0,
                DN_total        : 0,
                month           : '',
                year            : '',
                year_select     : [],
                month_text      : '',
                isLoading       : false
            }
        },
        mounted: function() {
            this.getYM();
            this.loadData();
            this.yearSelect();
            
            // ทำให้เมนูย่อเป็นค่าเริ่มต้นตามที่ผู้ใช้ร้องขอ
            const sidebar = document.getElementById('sidebar');
            if (sidebar) sidebar.classList.remove('active');
        },
        computed: {
            colTotals() {
                if (!this.ven_coms || !this.datas) return [];
                return this.ven_coms.map((vc, idx) => {
                    let sum = 0;
                    this.datas.forEach(person => {
                        if (person.vcs_arr && person.vcs_arr[idx]) {
                            sum += person.vcs_arr[idx].price;
                        }
                    });
                    return sum;
                });
            },
            dayTotal() {
                return this.datas.reduce((acc, curr) => acc + (curr.D_c || 0), 0);
            },
            nightTotal() {
                return this.datas.reduce((acc, curr) => acc + (curr.N_c || 0), 0);
            }
        },
        methods: {
            loadData() {
                let month = this.year + '-' + this.month
                let stored = localStorage.getItem('excluded_duties_' + month);
                let excluded_duties = stored ? JSON.parse(stored) : [];

                axios.post('./ven/api/index_get_data_all.php',{
                    month: month,
                    excluded_duties: excluded_duties
                })
                .then(response => {
                    if (response.data.status) {
                        this.datas = response.data.datas;
                        this.month_text = response.data.month;
                        this.price_all = response.data.price_all
                        this.ven_coms = response.data.ven_coms
                        this.DN_D_PRICE_DAY = response.data.DN_D_PRICE_DAY
                        this.DN_N_PRICE_DAY = response.data.DN_N_PRICE_DAY
                        this.day_num = response.data.day_num
                        this.holiday_num = response.data.holiday_num
                        Swal.fire({
                            icon: 'success',
                            title: response.data.message,
                            showConfirmButton: false,
                            timer: 1000
                        })
                    } else {
                        this.datas = [];
                        Swal.fire({
                            icon: 'error',
                            title: response.data.message,
                            showConfirmButton: false,
                            timer: 1500
                        })
                    }
                })
                .catch(function(error) {
                    console.log(error);
                });
            },
            download_docx(){
                let month = this.year + '-' + this.month
                let excluded_duties = JSON.parse(localStorage.getItem('excluded_duties_' + month) || '[]');
                this.isLoading = true;
                axios.post('./ven/api/report_docx.php',{month:month, excluded_duties: excluded_duties})
                .then(response => {
                    if (response.data.status) {
                        this.alert("success",response.data.message, 1000)
                        window.open('./ven/api/ven.docx','_blank')
                    } else{
                        this.alert("warning",response.data.message, 0)
                    }
                })
                .catch(function (error) {
                    console.log(error);
                })
                .finally(() => {
                    this.isLoading = false;
                })
            },
            find(){
                console.log(this.year + '-' + this.month) ;
            },
            DN_D_ALL(){
                return this.holiday_num * this.DN_D_PRICE_DAY;                            
            },
            DN_N_ALL(){
                return this.day_num * this.DN_N_PRICE_DAY;                            
            },
            DN_TOTAL(){
                return this.DN_D_ALL() + this.DN_N_ALL();                            
            },
            PRICE_ALL(){
                let p_all = 0;
                for (let i = 0; i < this.datas.length; i++) {
                    p_all += (this.datas[i].D_price + this.datas[i].N_price)
                }
                this.price_all = this.formatCurrency(p_all); 
            },
            getYM(){
                let MyDate = new Date();
                this.year = MyDate.getFullYear();
                this.month = ("0" + (MyDate.getMonth()+1)).slice(-2);
            },
            yearSelect(){
                let MyDate = new Date();
                for (let i = -1; i <= 5; i++) {
                    this.year_select.push(MyDate.getFullYear() + i) 
                }
            },
            formatCurrency(number) {
                number = parseFloat(number);
                return number.toFixed(0).replace(/./g, function(c, i, a) {
                    return i > 0 && c !== "." && (a.length - i) % 3 === 0 ? "," + c : c;
                });
            },
            alert(icon,message,timer=0){
                Swal.fire({
                    icon: icon,
                    title: message,
                    showConfirmButton: false,
                    timer: timer
                });
            },
        }
    }).mount('#fnuIndex');
    </script>
</body>
</html>