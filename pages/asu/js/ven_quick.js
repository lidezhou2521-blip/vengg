Vue.createApp({
  data() {
    return {
      months: [],
      ven_month: '',
      ven_coms: [],
      vc_index: '',
      ven_com: '',
      ven_name_subs: [],
      vns_index: '',
      ven_name_sub: '',
      profiles: [],
      schedule: [],
      selected_users: [],
      isLoading: false,
      draggedOverIndex: -1,
      holidays: [],
      draggedUserIndex: -1,
      persons_per_day: 1,
    }
  },
  mounted() {
    this.ven_month = this.getCurrentYM();
    this.get_ven_month1();
    this.get_ven_coms();
    this.getHolidays();
  },
  methods: {
    getCurrentYM() {
      const d = new Date();
      return d.getFullYear() + '-' + ('0' + (d.getMonth() + 1)).slice(-2);
    },
    get_ven_month1() {
      let m = new Date();
      let y = m.getFullYear().toString();
      for (let i = -1; i < 10; i++) {
        const d = new Date(y, m.getMonth() + i);
        this.months.push({
          ven_month: d.getFullYear() + '-' + ('0' + (d.getMonth() + 1)).slice(-2),
          name: this.convertToDateThai(d)
        });
      }
    },
    convertToDateThai(date) {
      var month_th = ["", "มกราคม", "กุมภาพันธ์", "มีนาคม", "เมษายน", "พฤษภาคม", "มิถุนายน", "กรกฎาคม", "สิงหาคม", "กันยายน", "ตุลาคม", "พฤศจิกายน", "ธันวาคม"];
      return month_th[(date.getMonth() + 1)] + " " + (date.getFullYear() + 543);
    },

    ch_sel_ven_month() {
      this.ven_coms = [];
      this.ven_com = '';
      this.vc_index = '';
      this.ven_name_subs = [];
      this.ven_name_sub = '';
      this.vns_index = '';
      this.profiles = [];
      this.schedule = [];
      this.get_ven_coms();
    },

    get_ven_coms() {
      axios.post('../../server/asu/ven_set/get_ven_coms.php', { ven_month: this.ven_month })
        .then(response => {
          if (response.data.status) {
            this.ven_coms = response.data.respJSON;
          } else {
            this.ven_coms = [];
          }
        })
        .catch(error => console.log(error));
    },

    ch_sel_ven_name(index) {
      this.ven_name_sub = '';
      this.ven_name_subs = [];
      this.vns_index = '';
      this.profiles = [];
      this.schedule = [];
      this.ven_com = this.ven_coms[index];
      axios.post('../../server/asu/ven_set/get_vns_vs.php', { vn_id: this.ven_com.vn_id })
        .then(response => {
          if (response.data.status) {
            this.ven_name_subs = response.data.respJSON;
          } else {
            this.ven_name_subs = [];
          }
        })
        .catch(error => console.log(error));
    },

    ch_sel_vns(index) {
      this.ven_name_sub = this.ven_name_subs[index];
      this.profiles = [];
      this.selected_users = [];

      // Load users for this position
      axios.post('../../server/asu/ven_set/get_ven_users.php', { vn_id: this.ven_name_sub.vn_id, vns_id: this.ven_name_sub.vns_id })
        .then(response => {
          if (response.data.status) {
            this.profiles = response.data.respJSON;
          } else {
            this.profiles = [];
          }
        })
        .catch(error => console.log(error));

      // Load schedule
      this.loadSchedule();
    },

    loadSchedule() {
      if (!this.ven_com || !this.ven_name_sub) return;
      this.isLoading = true;
      axios.post('../../server/asu/ven_set/quick_get_schedule.php', {
        ven_month: this.ven_month,
        vc_id: this.ven_com.vc_id,
        vn_id: this.ven_com.vn_id,
        vns_id: this.ven_name_sub.vns_id,
      })
        .then(response => {
          if (response.data.status) {
            this.schedule = response.data.schedule;
          }
        })
        .catch(error => console.log(error))
        .finally(() => this.isLoading = false);
    },

    getHolidays() {
      axios.get('../../server/asu/holiday/get_holidays.php')
        .then(res => {
          if (res.data.status) {
            this.holidays = res.data.respJSON;
          }
        });
    },

    getDayName(dateStr) {
      const dayNames = ["อา.", "จ.", "อ.", "พ.", "พฤ.", "ศ.", "ส."];
      const parts = dateStr.split('-');
      const d = new Date(parts[0], parts[1] - 1, parts[2]);
      return dayNames[d.getDay()];
    },

    isWeekend(dateStr) {
      const parts = dateStr.split('-');
      const d = new Date(parts[0], parts[1] - 1, parts[2]);
      return d.getDay() === 0 || d.getDay() === 6;
    },

    isHoliday(dateStr) {
      return this.holidays.some(h => h.holiday_date === dateStr);
    },

    getHolidayName(dateStr) {
      const h = this.holidays.find(h => h.holiday_date === dateStr);
      return h ? h.holiday_name : '';
    },

    getDateNum(dateStr) {
      return parseInt(dateStr.split('-')[2]);
    },

    assignUser(dayIndex, uid) {
      if (!uid) return;
      const day = this.schedule[dayIndex];
      const user = this.profiles.find(p => p.uid == uid);
      if (!user) return;

      this.isLoading = true;
      axios.post('../../server/asu/ven_set/quick_assign.php', {
        act: 'insert',
        uid: uid,
        ven_date: day.ven_date,
        ven_month: this.ven_month,
        vc_id: this.ven_com.vc_id,
        vn_id: this.ven_name_sub.vn_id,
        vns_id: this.ven_name_sub.vns_id,
        DN: this.ven_com.DN,
        ven_name: this.ven_com.name,
        ven_com_num: this.ven_com.ven_com_num,
        u_role: this.ven_name_sub.name,
        price: this.ven_name_sub.price,
        color: this.ven_name_sub.color,
      })
        .then(response => {
          if (response.data.status) {
            let icon = response.data.icon || 'success';
            this.alert(icon, response.data.message, icon === 'warning' ? 3000 : 1000);
          } else {
            this.alert('warning', response.data.message, 0);
          }
          this.loadSchedule();
        })
        .catch(error => console.log(error))
        .finally(() => this.isLoading = false);
    },

    removeAssignment(assignId) {
      this.isLoading = true;
      axios.post('../../server/asu/ven_set/quick_assign.php', { act: 'delete', id: assignId })
        .then(response => {
          if (response.data.status) {
            this.alert('success', response.data.message, 1000);
          } else {
            this.alert('warning', response.data.message, 0);
          }
          this.loadSchedule();
        })
        .catch(error => console.log(error))
        .finally(() => this.isLoading = false);
    },

    toggleUser(uid) {
      const idx = this.selected_users.indexOf(uid);
      if (idx > -1) {
        this.selected_users.splice(idx, 1);
      } else {
        this.selected_users.push(uid);
      }
    },

    selectAll() {
      if (this.selected_users.length === this.profiles.length) {
        this.selected_users = [];
      } else {
        this.selected_users = this.profiles.map(p => p.uid);
      }
    },

    async autoAssign() {
      if (this.selected_users.length === 0) {
        this.alert('warning', 'กรุณาเลือกรายชื่อก่อน', 0);
        return;
      }

      // Check if it's a holiday-only duty type
      const holidayDutyTypes = [
          'ศาลแขวงและพิจารณาคำร้องขอปล่อยชั่วคราว',
          'เวรเปิดทำการพิจารณาคำร้องขอปล่อยชั่วคราว',
          'ฟื้นฟู/ตรวจสอบการจับ'
      ];
      const isHolidayOnlyType = holidayDutyTypes.some(type => this.ven_com.name.includes(type));

      let autoMode = 'all';
      let confirmText = `จะจัดเวรให้ ${this.selected_users.length} คน สลับกันตลอดทั้งเดือน`;

      if (isHolidayOnlyType) {
          autoMode = 'holidays';
          confirmText = `จะจัดเวรให้ ${this.selected_users.length} คน เฉพาะวันเสาร์-อาทิตย์ และวันหยุดราชการ`;
      }

      const result = await Swal.fire({
        title: 'ยืนยันการจัดอัตโนมัติ?',
        text: confirmText,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'ตกลง',
        cancelButtonText: 'ยกเลิก',
        confirmButtonColor: isHolidayOnlyType ? '#198754' : '#3085d6'
      });

      if (!result.isConfirmed) return;

      this.isLoading = true;

      // 1. ตรวจสอบกลุ่มวันหยุดต่อเนื่อง (Identify Holiday Blocks)
      const blocks = [];
      let currentBlock = null;
      for (const day of this.schedule) {
          if (this.isWeekend(day.ven_date) || this.isHoliday(day.ven_date)) {
              if (!currentBlock) {
                  currentBlock = { dates: [] };
                  blocks.push(currentBlock);
              }
              currentBlock.dates.push(day.ven_date);
          } else {
              currentBlock = null;
          }
      }
      
      const blockMap = {};
      blocks.forEach(block => {
          block.dates.forEach((date, index) => {
              blockMap[date] = { length: block.dates.length, pos: index + 1 };
          });
      });

      // Build ordered map: uid -> order (from profiles which come sorted by vu.order from API)
      const userOrderMap = {};
      this.profiles.forEach((p, idx) => {
        userOrderMap[p.uid] = p.order !== undefined ? p.order : (idx + 1);
      });

      const sortedSelectedUsers = this.profiles
          .filter(p => this.selected_users.includes(p.uid))
          .map(p => p.uid);

      let userIdx = 0;
      const batchData = [];
      const ppd = this.persons_per_day || 1;

      for (let i = 0; i < this.schedule.length; i++) {
        const day = this.schedule[i];
        
        // Mode logic
        if (autoMode === 'holidays') {
            const isWE = this.isWeekend(day.ven_date);
            const isH = this.isHoliday(day.ven_date);
            const b = blockMap[day.ven_date];

            let shouldAssign = false;

            if (this.ven_com.name.includes('ศาลแขวงและพิจารณาคำร้อง')) {
                if (b) {
                    if (b.length === 2 && b.pos === 1) shouldAssign = true;
                    else if (b.length === 3 && b.pos === 2) shouldAssign = true;
                    else if (b.length === 4 && (b.pos === 2 || b.pos === 4)) shouldAssign = true;
                    else if (b.length >= 5 && (b.pos === 1 || b.pos === 3 || b.pos === 5)) shouldAssign = true;
                }
            } else if (this.ven_com.name.includes('เวรเปิดทำการพิจารณาคำร้อง')) {
                if (b) {
                    if (b.length === 1) shouldAssign = true;
                    else if (b.length === 2 && b.pos === 2) shouldAssign = true;
                    else if (b.length === 3 && (b.pos === 1 || b.pos === 3)) shouldAssign = true;
                    else if (b.length >= 4 && (b.pos === 2 || b.pos === 4)) shouldAssign = true;
                }
            } else if (this.ven_name_sub.name.includes('หัวหน้ากลุ่ม')) {
                if (isWE) shouldAssign = true; 
            } else {
                if (isWE || isH) shouldAssign = true; 
            }

            if (!shouldAssign) continue;
        }

        // Assign ppd persons for this day
        for (let p = 0; p < ppd; p++) {
          const uid = sortedSelectedUsers[userIdx % sortedSelectedUsers.length];
          
          batchData.push({
              uid: uid,
              ven_date: day.ven_date,
              ven_month: this.ven_month,
              vc_id: this.ven_com.vc_id,
              vn_id: this.ven_com.vn_id,
              vns_id: this.ven_name_sub.vns_id,
              DN: this.ven_com.DN,
              ven_name: this.ven_com.name,
              ven_com_num: this.ven_com.ven_com_num,
              u_role: this.ven_name_sub.name,
              price: this.ven_name_sub.price,
              color: this.ven_name_sub.color,
              vu_order: userOrderMap[uid] || (userIdx + 1),
          });
          
          userIdx++;
        }
      }

      if (batchData.length > 0) {
          axios.post('../../server/asu/ven_set/quick_assign.php', {
              act: 'batch_insert',
              assignments: batchData
          })
          .then(response => {
              if (response.data.status) {
                  this.alert('success', response.data.message, 2000);
              } else {
                  this.alert('error', response.data.message, 0);
              }
              this.loadSchedule();
          })
          .catch(error => {
              console.log(error);
              this.alert('error', 'เกิดข้อผิดพลาดในการบันทึกข้อมูล', 0);
          })
          .finally(() => this.isLoading = false);
      } else {
          this.isLoading = false;
          this.alert('info', 'ไม่มีรายการที่ต้องจัดเวร', 1500);
      }
    },

    async clearAll() {
      const result = await Swal.fire({
        title: 'ล้างทั้งหมด?',
        text: 'จะลบเวรทั้งเดือนของตำแหน่งนี้',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        confirmButtonText: 'ลบทั้งหมด',
        cancelButtonText: 'ยกเลิก'
      });
      if (!result.isConfirmed) return;

      this.isLoading = true;
      for (const day of this.schedule) {
        for (const a of day.assignments) {
          try {
            await axios.post('../../server/asu/ven_set/quick_assign.php', { act: 'delete', id: a.id });
          } catch (e) { console.log(e); }
        }
      }
      this.loadSchedule();
      this.isLoading = false;
      this.alert('success', 'ล้างทั้งหมดสำเร็จ', 1500);
    },

    alert(icon, message, timer = 0) {
      swal.fire({
        position: 'top-end',
        icon: icon,
        title: message,
        showConfirmButton: timer === 0,
        timer: timer
      });
    },

    onDragStart(event, uid, index = -1) {
      // If dragged user is in selected list, mark as multi-drag
      if (this.selected_users.includes(uid) && this.selected_users.length > 1) {
        event.dataTransfer.setData('text/plain', 'multi');
      } else {
        event.dataTransfer.setData('text/plain', uid);
      }
      event.dataTransfer.effectAllowed = 'move';
      this.draggedUserIndex = index;
    },

    onDragEnter(event, dayIndex) {
      this.draggedOverIndex = dayIndex;
    },

    onDragLeave(event, dayIndex) {
      if (this.draggedOverIndex === dayIndex) {
        this.draggedOverIndex = -1;
      }
    },

    async onDrop(event, dayIndex) {
      this.draggedOverIndex = -1;
      this.draggedUserIndex = -1;
      const data = event.dataTransfer.getData('text/plain');
      if (!data) return;

      if (data === 'multi' && this.selected_users.length > 0) {
        // Batch assign all selected users to this day
        const day = this.schedule[dayIndex];
        const sortedSelected = this.profiles
          .filter(p => this.selected_users.includes(p.uid))
          .map(p => p.uid);

        this.isLoading = true;
        const batchData = sortedSelected.map(uid => ({
          uid: uid,
          ven_date: day.ven_date,
          ven_month: this.ven_month,
          vc_id: this.ven_com.vc_id,
          vn_id: this.ven_com.vn_id,
          vns_id: this.ven_name_sub.vns_id,
          DN: this.ven_com.DN,
          ven_name: this.ven_com.name,
          ven_com_num: this.ven_com.ven_com_num,
          u_role: this.ven_name_sub.name,
          price: this.ven_name_sub.price,
          color: this.ven_name_sub.color,
        }));

        axios.post('../../server/asu/ven_set/quick_assign.php', {
          act: 'batch_insert',
          assignments: batchData
        })
          .then(response => {
            if (response.data.status) {
              this.alert('success', 'เพิ่ม ' + sortedSelected.length + ' คนเรียบร้อย', 1500);
            } else {
              this.alert('warning', response.data.message, 0);
            }
            this.loadSchedule();
          })
          .catch(error => console.log(error))
          .finally(() => this.isLoading = false);
      } else {
        // Single user assign
        this.assignUser(dayIndex, data);
      }
    },

    onUserDrop(targetIndex) {
      if (this.draggedUserIndex === -1 || this.draggedUserIndex === targetIndex) {
        this.draggedUserIndex = -1;
        return;
      }
      
      const item = this.profiles.splice(this.draggedUserIndex, 1)[0];
      this.profiles.splice(targetIndex, 0, item);
      this.draggedUserIndex = -1;
    },

    moveUser(index, direction) {
      const newIndex = index + direction;
      if (newIndex < 0 || newIndex >= this.profiles.length) return;
      
      // Use splice so Vue can detect the change and re-render
      const item = this.profiles.splice(index, 1)[0];
      this.profiles.splice(newIndex, 0, item);
    }
  }
}).mount('#venQuick');
