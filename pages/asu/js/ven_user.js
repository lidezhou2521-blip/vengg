Vue.createApp({
  data() {
    return {
      datas: [],
      users: [],
      judge: [],
      not_judge: [],
      
      // Drag & drop from sidebar
      dragging_user: null,
      drag_source: '', // 'sidebar' or 'reorder'
      
      // Reorder drag
      reorder_group: null,
      reorder_from_index: -1,
      
      // Filters
      q_filter: '',
      workgroup_filter: '',
      
      // Form (kept for compatibility)
      vu_form: {
        user_id: '',
        order: 0,
        vn_id: 0,
        vns_id: 0,
      },
      vu_form_act: 'insert',
      
      show_sidebar: true,
      isLoading: false
    }
  },
  computed: {
    workgroups() {
      const set = new Set();
      this.users.forEach(u => { if (u.workgroup) set.add(u.workgroup); });
      return Array.from(set).sort();
    },
    filtered_users() {
      let list = this.users;
      if (this.workgroup_filter !== '') {
        list = list.filter(u => u.workgroup === this.workgroup_filter);
      }
      if (this.q_filter.trim() !== '') {
        const q = this.q_filter.toLowerCase();
        list = list.filter(u =>
          (u.name && u.name.toLowerCase().includes(q)) ||
          (u.dep && u.dep.toLowerCase().includes(q)) ||
          (u.workgroup && u.workgroup.toLowerCase().includes(q))
        );
      }
      return list;
    }
  },
  mounted() {
    this.get_ven_users();
    this.get_users();
  },
  methods: {
    // === Data Loading ===
    get_ven_users() {
      this.isLoading = true;
      axios.post('../../server/asu/ven_user/get_ven_users.php')
        .then(response => {
          if (response.data.status) {
            this.datas = response.data.respJSON;
          } else {
            this.datas = [];
          }
        })
        .catch(error => console.log(error))
        .finally(() => { this.isLoading = false; });
    },
    get_users() {
      axios.post('../../server/asu/ven_user/get_users.php')
        .then(response => {
          this.users = response.data.respJSON || [];
          this.judge = response.data.judge || [];
          this.not_judge = response.data.not_judge || [];
        })
        .catch(error => console.log(error));
    },

    // === Sidebar Drag (add new user) ===
    onDragStart(event, user) {
      this.dragging_user = user;
      this.drag_source = 'sidebar';
      event.target.classList.add('dragging');
      event.dataTransfer.effectAllowed = 'copy';
      event.dataTransfer.setData('text/plain', user.uid);
    },
    onDragEnd(event) {
      this.dragging_user = null;
      this.drag_source = '';
      event.target.classList.remove('dragging');
    },

    // === Reorder Drag (within group) ===
    onMemberDragStart(event, dataIndex, memberIndex) {
      this.drag_source = 'reorder';
      this.reorder_group = dataIndex;
      this.reorder_from_index = memberIndex;
      event.target.classList.add('dragging');
      event.dataTransfer.effectAllowed = 'move';
      event.dataTransfer.setData('text/plain', memberIndex);
    },
    onMemberDragEnd(event) {
      this.drag_source = '';
      this.reorder_group = null;
      this.reorder_from_index = -1;
      event.target.classList.remove('dragging');
    },
    onMemberDragOver(event, dataIndex, memberIndex) {
      event.preventDefault();
      // Only allow reorder within the same group
      if (this.drag_source === 'reorder' && this.reorder_group === dataIndex) {
        event.dataTransfer.dropEffect = 'move';
        event.currentTarget.classList.add('member-drag-over');
      }
    },
    onMemberDragLeave(event) {
      event.currentTarget.classList.remove('member-drag-over');
    },
    onMemberDrop(event, dataIndex, memberIndex) {
      event.currentTarget.classList.remove('member-drag-over');
      if (this.drag_source !== 'reorder' || this.reorder_group !== dataIndex) return;
      
      const fromIdx = this.reorder_from_index;
      const toIdx = memberIndex;
      if (fromIdx === toIdx) return;

      const data = this.datas[dataIndex];
      const users = [...data.users];
      
      // Move item
      const [moved] = users.splice(fromIdx, 1);
      users.splice(toIdx, 0, moved);
      
      // Update order numbers
      const updates = users.map((u, i) => ({
        vu_id: u.vu_id,
        order: i + 1
      }));

      // Optimistic UI update
      users.forEach((u, i) => { u.order = i + 1; });
      data.users = users;

      // Save to backend
      axios.post('../../server/asu/ven_user/ven_user_act.php', {
        act: 'reorder',
        updates: updates
      })
        .then(response => {
          if (response.data.status) {
            this.alert('success', 'สลับลำดับเรียบร้อย', 800);
          } else {
            this.get_ven_users(); // rollback
            this.alert('warning', response.data.message, 0);
          }
        })
        .catch(error => {
          console.log(error);
          this.get_ven_users(); // rollback
        });
    },

    // === Drop Zone (for sidebar items) ===
    onDropZoneDragOver(event) {
      event.preventDefault();
      if (this.drag_source === 'sidebar') {
        event.dataTransfer.dropEffect = 'copy';
        event.currentTarget.classList.add('drag-over');
      }
    },
    onDropZoneDragLeave(event) {
      event.currentTarget.classList.remove('drag-over');
    },
    onDropZoneDrop(event, data) {
      event.currentTarget.classList.remove('drag-over');
      if (this.drag_source !== 'sidebar' || !this.dragging_user) return;

      const user = this.dragging_user;
      const nextOrder = (data.users ? data.users.length : 0) + 1;

      this.isLoading = true;
      axios.post('../../server/asu/ven_user/ven_user_act.php', {
        ven_user: {
          user_id: user.uid,
          order: nextOrder,
          vn_id: data.vn_id,
          vns_id: data.vns_id
        },
        act: 'insert'
      })
        .then(response => {
          if (response.data.status) {
            this.get_ven_users();
            this.alert('success', 'เพิ่ม ' + user.name + ' เรียบร้อย', 1000);
          } else {
            this.alert('warning', response.data.message, 0);
          }
        })
        .catch(error => console.log(error))
        .finally(() => { this.isLoading = false; });
    },

    // === CRUD Actions ===
    vu_del(id) {
      Swal.fire({
        title: 'ลบผู้อยู่เวร?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'ลบ',
        cancelButtonText: 'ยกเลิก'
      }).then((result) => {
        if (result.isConfirmed) {
          axios.post('../../server/asu/ven_user/ven_user_act.php', { id: id, act: 'delete' })
            .then(response => {
              if (response.data.status) {
                this.get_ven_users();
                this.alert('success', response.data.message, 1000);
              } else {
                this.alert('warning', response.data.message, 0);
              }
            })
            .catch(error => console.log(error));
        }
      });
    },
    vu_del_group(data) {
      Swal.fire({
        title: 'ลบผู้อยู่เวรในกลุ่มนี้?',
        text: data.vn_name + ' - ' + data.vns_name + ' (' + (data.users ? data.users.length : 0) + ' คน)',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'ลบทั้งกลุ่ม',
        cancelButtonText: 'ยกเลิก'
      }).then((result) => {
        if (result.isConfirmed) {
          this.isLoading = true;
          axios.post('../../server/asu/ven_user/ven_user_act.php', {
            act: 'delete_group',
            vn_id: data.vn_id,
            vns_id: data.vns_id
          })
            .then(response => {
              if (response.data.status) {
                this.get_ven_users();
                this.alert('success', response.data.message, 1500);
              } else {
                this.alert('warning', response.data.message, 0);
              }
            })
            .catch(error => console.log(error))
            .finally(() => { this.isLoading = false; });
        }
      });
    },

    // === Utility ===
    alert(icon, message, timer = 0) {
      swal.fire({
        position: 'top-end',
        icon: icon,
        title: message,
        showConfirmButton: false,
        timer: timer
      });
    }
  }
}).mount('#venUser');
