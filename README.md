# 🗓️ ระบบบริหารจัดเวรนอกเวลาราชการ (Vengg)
[![PHP Version](https://img.shields.io/badge/PHP-8.1%2B-blue.svg)](https://www.php.net/)
[![MySQL](https://img.shields.io/badge/Database-MySQL%20/%20MariaDB-orange.svg)](https://www.mysql.com/)
[![Vue.js](https://img.shields.io/badge/Frontend-Vue.js%203-green.svg)](https://vuejs.org/)
[![Version](https://img.shields.io/badge/Version-1.0.0-red.svg)](#)

**Vengg** คือระบบบริหารจัดการการจัดเวรและรายงานเวรอัจฉริยะ (Judicial Duty Management System) พัฒนาขึ้นเพื่อยกระดับการทำงานของหน่วยงานศาลยุติธรรม ให้มีความแม่นยำ รวดเร็ว และตรวจสอบได้ง่ายขึ้น ครอบคลุมตั้งแต่การวางแผนจัดเวร การแลกเปลี่ยนเวร ไปจนถึงการตรวจสอบความถูกต้องก่อนการเบิกจ่าย

---

## 🌟 คุณสมบัติเด่น (Key Features)

### 1. ระบบตรวจสอบเวรชนอัจฉริยะ (Overlap Audit System)
- **Visual Audit Dashboard**: หน้าจอตรวจสอบเวรชน (Overlap) ที่แสดงผลแบบ Grid สวยงาม พร้อมระบบแจ้งเตือนเมื่อพบการเบิกเงินซ้ำซ้อน (Multi-claim Detection)
- **Smart Tooltips**: แสดงรายละเอียดเวรทั้งหมดที่เกิดขึ้นในวันนั้นๆ เมื่อนำเมาส์ไปชี้ เพื่อช่วยในการตัดสินใจ
- **Status Indicators**: แยกแยะเวร "เบิก" (Success Green) และ "ไม่เบิก" (Secondary Gray) อย่างชัดเจน พร้อมระบบ Pulse Animation แจ้งเตือนจุดที่ต้องตรวจสอบ
- **Double-Click Swap**: ระบบแลกเวรแบบเร่งด่วน เพียงดับเบิ้ลคลิกที่รายการเวรเพื่อเข้าสู่โหมดการแลกเปลี่ยนข้อมูล (Swap Mode)

### 2. ระบบจัดการการเปลี่ยนเวร (Modern Swap & Transfer)
- **Advanced Validation**: ระบบตรวจสอบเงื่อนไขการแลกเวรโดยอัตโนมัติ (เช่น ประเภทเวรตรงกัน, อยู่ภายใต้คำสั่งเดียวกัน, และการสลับตำแหน่งที่เหมาะสมระหว่างผู้พิพากษาและเจ้าหน้าที่)
- **Interactive Interface**: เลือกเวรต้นทางและปลายทางผ่าน UI ที่ใช้งานง่าย พร้อมระบบยืนยันข้อมูล (Confirmation Modal)
- **Real-time Updates**: ข้อมูลในปฏิทินและรายงานจะอัปเดตทันทีเมื่อการแลกเวรเสร็จสิ้น

### 3. ระบบรายงานและการเงิน (Finance & Advanced Reporting)
- **Automated Financial Summary**: สรุปยอดเงินรายบุคคลและยอดรวมทั้งเดือนในรูปแบบ Dashboard ที่มีสีสันสวยงามและดูพรีเมียม
- **Duty-Type Filtering**: พิมพ์รายงานสรุปเวรแยกตามประเภท เพื่อความสะดวกในการตรวจสอบงบประมาณ
- **Responsive Tables**: ตารางรายงานที่รองรับการแสดงผลทุกหน้าจอ พร้อมเส้น Grid ที่ชัดเจนและรองรับการสั่งพิมพ์ (Print Friendly)
- **Daily Rates Display**: แสดงอัตราค่าตอบแทนต่อเวรโดยละเอียดในหน้าตรวจสอบข้อมูล

### 4. การเชื่อมต่อภายนอก (Integration)
- **🚀 LINE Messaging API**: ระบบแจ้งเตือนเวรรายวันและแจ้งเตือนการเปลี่ยนแปลงผ่าน LINE Messaging API ที่เสถียร รองรับการส่งข้อมูลเข้ากลุ่มและรายบุคคล
- **📅 Google Calendar Sync**: ซิงค์ข้อมูลเวรที่อนุมัติแล้วเข้าสู่ปฏิทินกลางของหน่วยงานอัตโนมัติ พร้อมหน้าจัดการ (GCal Dashboard) แยกต่างหาก

---

## 🛠 เทคโนโลยีที่ใช้ (Tech Stack)

- **Frontend**: 
  - **Vue.js 3**: Progressive Framework สำหรับการจัดการ UI ที่ซับซ้อน
  - **Bootstrap 5**: CSS Framework สำหรับโครงสร้างพื้นฐานและ Component
  - **Axios**: จัดการการเชื่อมต่อ API แบบ Asynchronous
  - **SweetAlert2**: ระบบแจ้งเตือนและ Modal ที่สวยงามและตอบโต้ได้ดี
- **Backend**: 
  - **PHP 8.1+**: พัฒนาด้วยโครงสร้างที่สะอาด ปลอดภัย และมีประสิทธิภาพ
  - **PDO Driver**: เชื่อมต่อฐานข้อมูลด้วยความปลอดภัย (Prevention of SQL Injection)
- **Database**: 
  - **MySQL / MariaDB**: จัดเก็บข้อมูลอย่างเป็นระบบพร้อม Indexing เพื่อความรวดเร็ว

---

## 🎨 การออกแบบและ UI/UX (Premium Aesthetics)
- **Modern Color Palette**: ใช้โทนสี Indigo, Deep Blue และ Emerald Green เพื่อสร้างความรู้สึกเป็นมืออาชีพและน่าเชื่อถือ
- **Glassmorphism & Gradients**: ตกแต่ง UI ด้วยการใช้สีไล่ระดับ (Gradients) และเอฟเฟกต์โปร่งแสง เพื่อความพรีเมียม
- **Micro-animations**: เพิ่มความมีชีวิตชีวาด้วย hover effects, pulse animations และ smooth transitions
- **Enhanced Sidebar**: ไอคอนเมนูที่ชัดเจนพร้อมระยะห่างที่เหมาะสม ช่วยให้การนำทางในระบบเป็นไปอย่างลื่นไหล

---

## 🚀 วิธีการติดตั้ง (Installation)

1. **Environment**: ติดตั้ง XAMPP (แนะนำเวอร์ชัน 8.2+) หรือ Web Server ที่รองรับ PHP 8.1
2. **Clone & Setup**: คัดลอกโฟลเดอร์โครงการไปไว้ที่ `htdocs`
3. **Database**: นำเข้าไฟล์ `database.sql` จากโฟลเดอร์ `/database` ลงใน MySQL
4. **Configuration**: 
   - ตั้งค่าฐานข้อมูลใน `server/connect.php`
   - เข้าใช้งานด้วย Username: `admin` Password: `admin` (ค่าเริ่มต้น)
5. **Configuration APIs**: ตั้งค่า LINE Token และ Google Calendar ผ่านเมนู **Admin** ในระบบ

---
© 2022-2026 **Btnc-ศจ.เบตง** - *พัฒนาเพื่อประสิทธิภาพสูงสุดในการบริหารจัดเวรนอกเวลาราชการ*
