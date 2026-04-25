Vue.createApp({
  data() {
    return {
      q: '',
      url_base: '',
      url_base_app: '',
      url_base_now: '',
      datas: [],
      line_form: '',
      act: 'insert',
      isLoading: false,
    }
  },
  mounted() {
    this.url_base = window.location.protocol + '//' + window.location.host;
    this.get_lines();
  },
  watch: {
    q() {
      this.ch_search_line();
    }
  },
  methods: {
    get_lines() {
      this.isLoading = true;
      axios.post('../../server/users/line/get_lines.php')
        .then(response => {
          if (response.data.status) {
            this.datas = response.data.data;
          }
        })
        .catch(function (error) {
          console.log(error);
        })
        .finally(() => {
          this.isLoading = false;
        });
    },
    get_line(id) {
      this.isLoading = true;
      axios.post('../../server/users/line/get_line.php', { id: id })
        .then(response => {
          if (response.data.status) {
            this.line_form = response.data.data;
          }
        })
        .catch(function (error) {
          console.log(error);
        })
        .finally(() => {
          this.isLoading = false;
        });
    },
    line_update(id) {
      this.get_line(id);
      this.$refs.show_modal_line_form.click();
      this.act = 'update';
    },
    line_insert() {
      this.line_form = { name: '', token: '' };
      this.$refs.show_modal_line_form.click();
      this.user_form.act = 'insert';
    },
    line_save() {
      this.isLoading = true;
      axios.post('../../server/users/line/line_save.php', { line: this.line_form, act: this.act })
        .then(response => {
          if (response.data.status) {
            this.alert('success', response.data.message, 1000);
            this.$refs.close_modal_line_form.click();
            this.get_lines();
          } else {
            this.alert('warning', response.data.message, 0);
          }
        })
        .catch(function (error) {
          console.log(error);
        })
        .finally(() => {
          this.isLoading = false;
        });
    },
    close_modal_line_form() {
      this.line_form = { name: '', token: '' };
      this.act = 'insert';
    },
    alert(icon, message, timer = 0) {
      swal.fire({
        icon: icon,
        title: message,
        showConfirmButton: true,
        timer: timer
      });
    },
    line_status(id, st) {
      this.isLoading = true;
      axios.post('../../server/users/line/line_save.php', { id: id, st: st, act: 'set_st' })
        .then(response => {
          if (response.data.status) {
            this.alert('success', response.data.message, 1000);
            this.get_lines();
          } else {
            this.alert('error', response.data.message, timer = 0);
          }
        })
        .catch(function (error) {
          console.log(error);
        })
        .finally(() => {
          this.isLoading = false;
        });
    },
    line_del(id) {
      Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, is it!'
      }).then((result) => {
        if (result.isConfirmed) {
          this.isLoading = true;
          axios.post('../../server/users/line/line_save.php', { id: id, act: 'del' })
            .then(response => {
              if (response.data.status) {
                this.alert('success', response.data.message, 1000);
                this.get_lines();
              } else {
                this.alert('error', response.data.message, timer = 0);
              }
            })
            .catch(function (error) {
              console.log(error);
            })
            .finally(() => {
              this.isLoading = false;
            });
        }
      });
    },
    ch_search_line() {
      console.log(this.q);
      if (this.q.length > 0) {
        this.isLoading = true;
        axios.post('../../server/users/line/line_search.php', { q: this.q })
          .then(response => {
            if (response.data.status) {
              this.datas = response.data.respJSON;
            } else {
              this.datas = [];
            }
          })
          .catch(function (error) {
            console.log(error);
          })
          .finally(() => {
            this.isLoading = false;
          });
      } else {
        this.get_lines();
      }
    },
    line_send_test(token, message) {
      sms = 'ทดสอบ';
      sms += "\n" + message;
      axios.post('../../server/service/line/sendline.php', { token: token, message: sms })
        .then(response => {
          console.log(response.status);
          if (response.status == 200) {
            this.alert('success', response.data.message, 1000);
          } else {
            this.alert('warning', response.data.message, 1000);
          }
        })
        .catch(function (error) {
          console.log(error);
        })
        .finally(() => {
          this.isLoading = false;
        });
    }
  },
}).mount('#usersLine');
