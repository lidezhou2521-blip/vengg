# 🗓️ Duty Management System (Vengg)
[![PHP Version](https://img.shields.io/badge/PHP-7.4%2B-blue.svg)](https://www.php.net/)
[![MySQL](https://img.shields.io/badge/Database-MySQL%20/%20MariaDB-orange.svg)](https://www.mysql.com/)
[![Vue.js](https://img.shields.io/badge/Frontend-Vue.js%203-green.svg)](https://vuejs.org/)
[![License](https://img.shields.io/badge/Version-2.1.9-red.svg)](#)

**Vengg** คือระบบบริหารจัดการการจัดเวรและรายงานเวรอัจฉริยะ พัฒนาขึ้นเพื่อยกระดับการทำงานของหน่วยงานให้มีความแม่นยำ รวดเร็ว และตรวจสอบได้ง่ายขึ้น ครอบคลุมตั้งแต่การวางแผนจัดเวร การเปลี่ยนเวร ไปจนถึงการเบิกจ่ายงบประมาณและการแจ้งเตือนอัตโนมัติ

---

## 🌟 คุณสมบัติเด่น (Key Features)

### 1. ระบบจัดเวรและปฏิทินอัจฉริยะ
- **Calendar View**: แสดงตารางเวรในรูปแบบปฏิทินที่สวยงาม แยกสีตามประเภทเวร (กลางวัน/กลางคืน) ชัดเจน
- **Quick Set (Smart Assign)**: ระบบช่วยจัดเวรอย่างรวดเร็ว ลดขั้นตอนการป้อนข้อมูลซ้ำซ้อน
- **Duty Conflict Checker**: ระบบตรวจสอบเวรชน (Overlap Audit) ป้องกันการจัดเวรซับซ้อนในวันเดียวกัน พร้อมระบบแยกประเภทเวร "เบิก" และ "ไม่เบิก"

### 2. ระบบจัดการการเปลี่ยนเวร (Modern Swap System)
- **Swap & Transfer**: รองรับทั้งการขอเปลี่ยนเวรระหว่างบุคคลและการโอนเวรให้ผู้อื่น
- **Approval Workflow**: ระบบอนุมัติการเปลี่ยนเวรแบบหลายระดับ พร้อมการตรวจสอบสถานะแบบ Real-time
- **Audit Logs**: บันทึกประวัติการเปลี่ยนแปลงเวรอย่างละเอียดเพื่อความโปร่งใส

### 3. การเชื่อมต่อภายนอก (Integration)
- **🚀 LINE Messaging API (New!)**: อัปเกรดจาก LINE Notify เดิมสู่ระบบ LINE Messaging API ที่เสถียรและทันสมัยกว่า รองรับการส่งข้อความเข้ากลุ่มและรายบุคคลผ่านบอทโดยตรง
- **📅 Google Calendar Sync**: ระบบซิงค์ข้อมูลเวรที่อนุมัติแล้วเข้าสู่ Google Calendar ของศาลอัตโนมัติ พร้อมหน้าจัดการ (Dashboard) แยกต่างหากเพื่อความสะดวกในการจัดการ

### 4. ระบบรายงานและการเงิน (Finance & Reporting)
- **Automated Reporting**: สร้างรายงานผลการปฏิบัติหน้าที่ (เช่น รายงานเวรหมายจับหมายค้น, รายงานเวรส่วนกลาง) ในรูปแบบไฟล์เอกสารที่พร้อมพิมพ์ทันที
- **Financial Status Badges**: ป้ายกำกับสถานะ "เบิก" เพื่อความสะดวกในการตรวจสอบยอดเงินและงบประมาณ

---

## 🛠 เทคโนโลยีที่ใช้ (Tech Stack)

- **Frontend**: HTML5, CSS3, Bootstrap 5 (Mazer Template), Vue.js 3 (Progressive Framework), Axios, FullCalendar v5
- **Backend**: PHP 7.4+ (Clean Architect), PDO Driver (Security First)
- **Database**: MySQL 5.7+ / MariaDB 10.4+
- **Security**: RBAC (Role-Based Access Control), XSS/SQL Injection Prevention

---

## 📂 โครงสร้างโฟลเดอร์ (Project Structure)

- `/assets`: ทรัพยากรหลัก (CSS, JS, Images, Fonts)
- `/pages`: โมดูล UI แบ่งตามกลุ่มผู้ใช้งาน (asu: อำนวยการ, fnu: การเงิน, users: แอดมิน)
- `/server`: Core Engine, API Handlers และ Business Logic
- `/server/gcal_config.json`: ไฟล์ตั้งค่าระบบ Google Calendar แบบไดนามิก
- `/server/line_config.json`: ไฟล์ตั้งค่าระบบ LINE Messaging API
- `/uploads`: พื้นที่เก็บเอกสารระบบและรูปภาพโปรไฟล์

---

## 🚀 วิธีการติดตั้ง (Installation)

1. **Environment Setup**: ติดตั้ง Web Server (เช่น XAMPP หรือ Docker)
2. **Clone Project**: คัดลอกโฟลเดอร์โครงการไปไว้ใน Root Directory (`htdocs` หรือ `www`)
3. **Database Import**: สร้างฐานข้อมูล MySQL และนำเข้าไฟล์ SQL จากโฟลเดอร์ `/database`
4. **Configuration**: 
   - ตั้งค่าการเชื่อมต่อฐานข้อมูลที่ `server/connect.php`
   - ตั้งค่า LINE Token และ Google Calendar URL ผ่านเมนู **Admin** ในระบบ
5. **Ready to go!**: เข้าใช้งานผ่านเบราว์เซอร์ที่ `http://localhost/vengg`

---

## 🎨 การปรับปรุง UI/UX ล่าสุด
- **Sidebar Icons**: เพิ่มไอคอนและระยะห่าง (me-2) ให้กับทุกเมนูย่อยเพื่อความสะดวกในการใช้งาน
- **Responsive Tables**: ตารางรายงานที่รองรับการแสดงผลทุกหน้าจอ พร้อมเส้น Grid ที่ชัดเจนขึ้น
- **Premium Aesthetics**: ปรับเปลี่ยนโทนสีและ Badge ให้ดูทันสมัยและเป็นมืออาชีพ

---
© 2022-2026 **Vengg Team** - *พัฒนาเพื่อประสิทธิภาพสูงสุดในการบริหารจัดการงานเวร*
