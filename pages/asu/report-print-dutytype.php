<?php
require_once('../../server/authen.php');
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>สรุปเวรแยกประเภทเวร</title>
    <style>
        [v-cloak]>* {
            display: none;
        }

        [v-cloak]::before {
            content: "loading...";
        }

        @font-face {
            font-family: Sarabun;
            src: url(../../assets/fonts/Sarabun/Sarabun-Regular.ttf);
        }

        * {
            font-family: Sarabun;
            font-size: 11px;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            padding: 10px 5px;
        }

        .report-table {
            width: 100%;
            border-collapse: collapse;
        }

        .report-table th {
            font-weight: bold;
            text-align: center;
            padding: 2px;
        }

        .report-table td {
            padding: 1px;
            vertical-align: middle;
        }

        .col-name-type {
            width: 180px;
            text-align: left;
            padding-left: 4px !important;
        }

        .col-day {
            width: 19px;
            min-width: 19px;
            max-width: 19px;
            height: 20px;
            text-align: center;
            border: 1px solid #aaa;
            font-size: 14px;
            padding: 0 !important;
        }

        .col-day-header {
            width: 19px;
            min-width: 19px;
            max-width: 19px;
            text-align: center;
            border: 1px solid #aaa;
            font-size: 12px;
            padding: 0 !important;
            background-color: #e3f2fd !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .col-price {
            width: 75px;
            text-align: right;
            padding-right: 4px !important;
            font-size: 14px;
        }

        .no-claim {
            color: red;
            font-weight: bold;
            margin-left: 3px;
        }

        .duty-row td {
            height: 20px;
            padding: 0 1px;
        }

        .person-last-row td {
            border-bottom: 1px dashed #aaa;
            padding-bottom: 4px;
        }

        .grand-total {
            text-align: right;
            padding: 6px 4px;
            font-weight: bold;
            font-size: 14px;
            border-top: 1px solid #000;
        }

        /* Sticky Header Logic */
        .sticky-header th {
            position: sticky;
            background-color: #fff;
            z-index: 10;
            box-shadow: 0 1px 0 #000;
        }

        .sticky-header tr:nth-child(1) th {
            top: 0;
        }

        .sticky-header tr:nth-child(2) th {
            top: 24px;
            /* Adjust according to row 1 height */
        }

        .header-row th {
            padding-bottom: 4px;
            font-size: 12px;
        }

        @media print {
            .no-print {
                display: none !important;
            }

            @page {
                size: landscape;
                margin: 0.5cm;
            }

            body {
                padding: 0;
            }

            .sticky-header th {
                position: static;
                box-shadow: none;
                border-bottom: 1px solid #000;
            }
        }

        tbody:hover .warrant-row {
            background-color: #e3f2fd !important;
        }

        tbody .duty-row:hover {
            background-color: #fff9c4 !important;
        }

        .day-excluded {
            color: #e53935;
            text-decoration: line-through;
            opacity: 0.65;
            font-size: 14px;
        }

        .excluded-row td {
            background-color: #fff8f8;
        }
    </style>
</head>

<body>
    <div id="app" v-cloak>
        <table class="report-table">
            <thead class="sticky-header">
                <tr class="header-row">
                    <th style="text-align:left; padding-left:4px;">ชื่อสกุล - ประเภทเวร</th>
                    <th :colspan="datas.days_in_month" style="text-align:center;">ประจำเดือน {{datas.ven_month_th}}</th>
                    <th style="text-align:right; padding-right:4px;">ตรวจสอบก่อนเบิกจ่าย</th>
                </tr>
                <tr>
                    <th></th>
                    <th v-for="day in datas.days_in_month" :key="'h'+day" class="col-day-header">{{day}}</th>
                    <th></th>
                </tr>
            </thead>
            <tbody v-for="(person, pIdx) in datas.persons" :key="pIdx">
                <tr v-for="(row, rIdx) in getPersonRows(person)" :key="pIdx + '-' + rIdx"
                    class="duty-row"
                    :class="{
                        'person-last-row': rIdx === getPersonRows(person).length - 1,
                        'excluded-row': row.type === 'excluded',
                        'warrant-row': row.duty.ven_name.includes('หมายจับ') || row.duty.ven_name.includes('หมายค้น')
                    }">
                    <td class="col-name-type">
                        <div v-if="rIdx === 0" style="font-weight:bold; text-decoration:underline; font-size: 14px;">{{person.name}}</div>
                        <div :style="'font-size: 13px; ' + (rIdx === 0 || row.type === 'excluded' ? 'padding-left:12px; ' : '') + (row.type === 'excluded' ? 'color:#c62828; font-style:italic;' : '')">
                            {{formatDutyName(row.duty.ven_name)}}
                            <span v-if="row.type === 'excluded'"> (ไม่เบิก)</span>
                            <span v-else-if="row.duty.no_claim" class="no-claim">ไม่เบิก</span>
                        </div>
                    </td>
                    <!-- Day cells -->
                    <td v-for="day in datas.days_in_month" :key="day" class="col-day">
                        <template v-if="row.type === 'billable' && row.duty.days && row.duty.days.includes(day)">{{pad(day)}}</template>
                        <template v-else-if="row.type === 'excluded' && row.duty.excluded_days && row.duty.excluded_days.includes(day)">
                            <span class="day-excluded">{{pad(day)}}</span>
                        </template>
                    </td>
                    <!-- Price -->
                    <td class="col-price" :style="row.type === 'excluded' ? 'color:#c62828;' : ''">
                        {{ row.type === 'excluded' ? '0.00' : (row.duty.no_claim ? '0.00' : formatNum(row.duty.total)) }}
                    </td>
                </tr>
            </tbody>
            <tbody>
                <tr>
                    <td :colspan="1 + datas.days_in_month" class="grand-total">รวมเงินทั้งหมด :</td>
                    <td class="col-price grand-total">{{formatNum(datas.grand_total)}}</td>
                </tr>
            </tbody>
        </table>

        <div class="mt-4 no-print" style="text-align:center; margin-top:20px;">
            <button onclick="window.print()" style="padding:8px 24px; font-size:14px; cursor:pointer;">พิมพ์เอกสาร</button>
        </div>
    </div>

    <script src="../../node_modules/vue/dist/vue.global.js"></script>
    <script>
        const {
            createApp
        } = Vue
        createApp({
            data() {
                return {
                    datas: {
                        days_in_month: 31,
                        persons: [],
                        grand_total: 0,
                        ven_month_num: ''
                    }
                }
            },
            mounted() {
                const printData = localStorage.getItem("print_dutytype")
                if (printData) {
                    this.datas = JSON.parse(printData)
                }
            },
            methods: {
                getPersonRows(person) {
                    let rows = [];
                    if (!person || !person.duties) return rows;
                    person.duties.forEach(duty => {
                        if (duty.days && duty.days.length > 0) {
                            rows.push({
                                type: 'billable',
                                duty: duty
                            });
                        }
                        if ((!duty.days || duty.days.length === 0) && (!duty.excluded_days || duty.excluded_days.length === 0)) {
                            rows.push({
                                type: 'billable',
                                duty: duty
                            });
                        }
                    });
                    person.duties.forEach(duty => {
                        if (duty.excluded_days && duty.excluded_days.length > 0) {
                            rows.push({
                                type: 'excluded',
                                duty: duty
                            });
                        }
                    });
                    return rows;
                },
                pad(n) {
                    return n < 10 ? '0' + n : '' + n;
                },
                formatNum(n) {
                    return Number(n || 0).toLocaleString('en-US', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    });
                },
                formatDutyName(name) {
                    if (name === 'ศาลแขวงและพิจารณาคำร้องขอปล่อยชั่วคราว') return 'เวรศาลแขวงฯ';
                    if (name === 'เวรเปิดทำการพิจารณาคำร้องขอปล่อยชั่วคราว') return 'เวรฯขอปล่อยชั่วคราว';
                    return name;
                },

            }
        }).mount('#app')
    </script>
</body>

</html>