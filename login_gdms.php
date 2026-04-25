<?php 
  session_start();
  session_destroy();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>เข้าสู่ระบบ GDMS | VenGG Judicial Duty</title>
  <link rel="shortcut icon" type="image/x-icon" href="assets/images/uploads/icon.ico">
  
  <!-- CSS -->
  <link rel="stylesheet" href="./node_modules/bootstrap/dist/css/bootstrap.css">
  <link rel="stylesheet" href="./node_modules/sweetalert2/dist/sweetalert2.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
  
  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Mitr:wght@200;300;400;500&family=Prompt:wght@200;300;400;600&display=swap" rel="stylesheet">

  <style>
    :root {
      --primary-gradient: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
      --glass-bg: rgba(255, 255, 255, 0.1);
      --glass-border: rgba(255, 255, 255, 0.2);
    }

    body, html {
      height: 100%;
      margin: 0;
      font-family: 'Prompt', 'Mitr', sans-serif;
      overflow: hidden;
    }

    .login-container {
      height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      background: #0f172a;
      position: relative;
    }

    .bg-image {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background-image: url('C:/Users/COJ/.gemini/antigravity/brain/022e0d89-c3c9-4a04-b715-99afa085029f/modern_court_background_1777042617371.png');
      background-size: cover;
      background-position: center;
      filter: brightness(0.4) saturate(1.2);
      z-index: 1;
    }

    .bg-overlay {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: radial-gradient(circle at center, transparent 0%, rgba(15, 23, 42, 0.8) 100%);
      z-index: 2;
    }

    .login-card {
      position: relative;
      z-index: 10;
      width: 100%;
      max-width: 450px;
      padding: 3rem;
      background: rgba(255, 255, 255, 0.07);
      backdrop-filter: blur(25px);
      -webkit-backdrop-filter: blur(25px);
      border: 1px solid var(--glass-border);
      border-radius: 2rem;
      box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
      animation: fadeInUp 0.8s ease-out;
    }

    @keyframes fadeInUp {
      from { opacity: 0; transform: translateY(30px); }
      to { opacity: 1; transform: translateY(0); }
    }

    .login-header {
      text-align: center;
      margin-bottom: 2.5rem;
    }

    .logo-icon {
      font-size: 3.5rem;
      background: var(--primary-gradient);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      margin-bottom: 1rem;
      display: inline-block;
    }

    .login-header h2 {
      color: #fff;
      font-weight: 600;
      letter-spacing: 0.5px;
      margin-bottom: 0.5rem;
    }

    .login-header p {
      color: rgba(255, 255, 255, 0.6);
      font-weight: 300;
    }

    .form-group {
      margin-bottom: 1.5rem;
    }

    .input-group-custom {
      position: relative;
    }

    .input-group-custom i {
      position: absolute;
      left: 1.25rem;
      top: 50%;
      transform: translateY(-50%);
      color: rgba(255, 255, 255, 0.5);
      font-size: 1.2rem;
      transition: all 0.3s;
    }

    .form-control-custom {
      width: 100%;
      background: rgba(255, 255, 255, 0.05);
      border: 1px solid var(--glass-border);
      border-radius: 1rem;
      padding: 1rem 1rem 1rem 3.5rem;
      color: #fff;
      font-size: 1rem;
      transition: all 0.3s;
    }

    .form-control-custom:focus {
      background: rgba(255, 255, 255, 0.1);
      border-color: #3b82f6;
      box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.2);
      outline: none;
    }

    .btn-login {
      width: 100%;
      padding: 1rem;
      margin-top: 1rem;
      background: var(--primary-gradient);
      border: none;
      border-radius: 1rem;
      color: #fff;
      font-weight: 600;
      font-size: 1.1rem;
      letter-spacing: 1px;
      transition: all 0.3s;
      box-shadow: 0 10px 20px -10px #3b82f6;
    }

    .btn-login:hover:not(:disabled) {
      transform: translateY(-2px);
      box-shadow: 0 15px 30px -10px #3b82f6;
    }

    .footer-text {
      position: absolute;
      bottom: 2rem;
      width: 100%;
      text-align: center;
      color: rgba(255, 255, 255, 0.4);
      font-size: 0.85rem;
      z-index: 10;
    }

    [v-cloak] { display: none !important; }
  </style>
</head>
<body>

<div class="login-container" id="applogin" v-cloak>
  <div class="bg-image"></div>
  <div class="bg-overlay"></div>
  
  <div class="login-card">
    <div class="login-header">
      <div class="logo-icon"><i class="bi bi-shield-lock-fill"></i></div>
      <h2>GDMS Login</h2>
      <p>ระบบจัดการเวรนอกเวลาราชการ (GDMS)</p>
    </div>

    <form @submit.prevent="login()">
      <div class="form-group">
        <div class="input-group-custom">
          <input 
            type="text" 
            class="form-control-custom" 
            placeholder="ชื่อผู้ใช้งาน" 
            v-model="user" 
            required 
            autofocus
          >
          <i class="bi bi-person-fill"></i>
        </div>
      </div>

      <div class="form-group">
        <div class="input-group-custom">
          <input 
            :type="pass_type" 
            class="form-control-custom" 
            placeholder="รหัสผ่าน" 
            v-model="pass" 
            required
          >
          <i class="bi bi-key-fill"></i>
          <span 
            @click="click_hide()" 
            style="position: absolute; right: 1.25rem; top: 50%; transform: translateY(-50%); color: rgba(255,255,255,0.4); cursor: pointer;"
          >
            <i :class="pass_type === 'password' ? 'bi bi-eye-fill' : 'bi bi-eye-slash-fill'" style="position: static; transform: none;"></i>
          </span>
        </div>
      </div>

      <button class="btn-login" type="submit" :disabled="isLoading">
        <span v-if="!isLoading">เข้าสู่ระบบ</span>
        <span v-else>
          <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
          กำลังเข้าสู่ระบบ...
        </span>
      </button>
    </form>
  </div>

  <div class="footer-text">
    Copyright &copy; 2026 VenGG Judicial System. All rights reserved.
  </div>
</div>

<script src="assets/vendor/js/bootstrap.js"></script>
<script src="./node_modules/vue/dist/vue.global.js"></script>
<script src="./node_modules/axios/dist/axios.js"></script>
<script src="./node_modules/sweetalert2/dist/sweetalert2.min.js"></script>

<script>
  const { createApp } = Vue;

  createApp({
    data() {
      return {
        user: '',        
        pass: '',        
        isLoading: false,
        pass_type: 'password'
      }
    },
    mounted() {     
      localStorage.removeItem("ss_uid");
    },
    methods: {
      click_hide() {
        this.pass_type = (this.pass_type === 'password') ? 'text' : 'password';
      }, 
      login() {
        this.isLoading = true;
        axios.post('./server/auth/login_gdms.php', {
          username: this.user,
          password: this.pass
        })
        .then(response => {
          if (response.data.status) {
            localStorage.setItem("ss_uid", response.data.ss_uid);
            Swal.fire({
              icon: 'success',
              title: 'สำเร็จ!',
              text: 'เข้าสู่ระบบเรียบร้อยแล้ว',
              showConfirmButton: false,
              timer: 1500,
              background: '#1e293b',
              color: '#fff'
            });
            setTimeout(() => {
              window.open("pages/dashboard", '_self');
            }, 1500);
          } else {
            Swal.fire({
              icon: 'error',
              title: 'ผิดพลาด',
              text: response.data.message,
              background: '#1e293b',
              color: '#fff',
              confirmButtonColor: '#3b82f6'
            });
          }
        })
        .catch(error => {        
          console.error(error);
          Swal.fire({
            icon: 'error',
            title: 'ผิดพลาด',
            text: 'เกิดข้อผิดพลาดในการเชื่อมต่อเซิร์ฟเวอร์',
            background: '#1e293b',
            color: '#fff'
          });
        })
        .finally(() => {
          this.isLoading = false;
        });
      }
    }
  }).mount('#applogin');
</script>
</body>
</html>

