<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Btnc - VenGG00</title>
<link rel="shortcut icon" href="../../assets/images/favicon/favicon.ico" type="image/x-icon">
<link rel="stylesheet" href="../../assets/css/bootstrap.css">
<!-- <link rel="stylesheet" href="../../node_modules/bootstrap@5.1.3/dist/css/bootstrap.min.css" /> -->

<link rel="stylesheet" href="../../assets/vendors/iconly/bold.css">

<link rel="stylesheet" href="../../assets/vendors/perfect-scrollbar/perfect-scrollbar.css">
<link rel="stylesheet" href="../../assets/vendors/bootstrap-icons/bootstrap-icons.css">
<link rel="stylesheet" href="../../assets/css/app.css">
<link rel="stylesheet" href="../../node_modules/sweetalert2/dist/sweetalert2.min.css">

<!-- Styles -->
<!-- <link rel="stylesheet" href="../../node_modules/select2-bootstrap-5-theme/dist/css/select2.min.css" /> -->
<!-- <link rel="stylesheet" href="../../node_modules/select2-bootstrap-5-theme/dist/select2-bootstrap-5-theme.min.css" /> -->
<!-- Or for RTL support -->
<!-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.rtl.min.css" /> -->





<style>
    [v-cloak] > * { display:none; }
    [v-cloak]::before { content: "loading..."; }

    script {
        display: none;
    }
    @font-face {
            font-family: Mitr;
            src: url(../../assets/fonts/Mitr-Regular.ttf);
            /* font-family: Sarabun; */
            /* src: url(../../assets/fonts/Sarabun/Sarabun-Regular.ttf); */
            /* font-weight: bold; */
        }

        * {
            font-family : Mitr;
            /* font-size   : small; */
            /* font-family : Sarabun; */
        }
         /*===== All Preloader Style =====*/
.preloader {
  /* Body Overlay */
  position: fixed;
  top: 0;
  left: 0;
  display: table;
  height: 100%;
  width: 100%;
  /* Change Background Color */
  background: #fff;
  z-index: 99999;
}

.preloader .loader {
  display: table-cell;
  vertical-align: middle;
  text-align: center;
}

.preloader .loader .spinner {
  position: absolute;
  left: 50%;
  top: 50%;
  width: 64px;
  margin-left: -32px;
  z-index: 18;
  pointer-events: none;
}

.preloader .loader .text {
  position: absolute;
  /* left: 50%; */
  top: 60%;
  width: 100%;
  /* margin-left: -32px; */
  z-index: 18;
  pointer-events: none;
}

.preloader .loader .spinner .spinner-container {
  pointer-events: none;
  position: absolute;
  width: 100%;
  padding-bottom: 100%;
  top: 50%;
  left: 50%;
  margin-top: -50%;
  margin-left: -50%;
  animation: spinner-linspin 1568.2353ms linear infinite;
}

.preloader .loader .spinner .spinner-container .spinner-rotator {
  position: absolute;
  width: 100%;
  height: 100%;
  animation: spinner-easespin 5332ms cubic-bezier(0.4, 0, 0.2, 1) infinite both;
}

.preloader .loader .spinner .spinner-container .spinner-rotator .spinner-left {
  position: absolute;
  top: 0;
  left: 0;
  bottom: 0;
  overflow: hidden;
  right: 50%;
}

.preloader .loader .spinner .spinner-container .spinner-rotator .spinner-right {
  position: absolute;
  top: 0;
  right: 0;
  bottom: 0;
  overflow: hidden;
  left: 50%;
}

.preloader .loader .spinner-circle {
  box-sizing: border-box;
  position: absolute;
  width: 200%;
  height: 100%;
  border-style: solid;
  /* Spinner Color */
  border-color: #3763EB #3763EB #F4EEFB;
  border-radius: 50%;
  border-width: 6px;
}

.preloader .loader .spinner-left .spinner-circle {
  left: 0;
  right: -100%;
  border-right-color: #F4EEFB;
  animation: spinner-left-spin 1333ms cubic-bezier(0.4, 0, 0.2, 1) infinite both;
}

.preloader .loader .spinner-right .spinner-circle {
  left: -100%;
  right: 0;
  border-left-color: #F4EEFB;
  animation: right-spin 1333ms cubic-bezier(0.4, 0, 0.2, 1) infinite both;
}

/* Preloader Animations */

@keyframes spinner-linspin {
  to {
    transform: rotate(360deg);
  }
}

@keyframes spinner-easespin {
  12.5% {
    transform: rotate(135deg);
  }
  25% {
    transform: rotate(270deg);
  }
  37.5% {
    transform: rotate(405deg);
  }
  50% {
    transform: rotate(540deg);
  }
  62.5% {
    transform: rotate(675deg);
  }
  75% {
    transform: rotate(810deg);
  }
  87.5% {
    transform: rotate(945deg);
  }
  to {
    transform: rotate(1080deg);
  }
}

@keyframes spinner-left-spin {
  0% {
    transform: rotate(130deg);
  }
  50% {
    transform: rotate(-5deg);
  }
  to {
    transform: rotate(130deg);
  }
}

@keyframes right-spin {
  0% {
    transform: rotate(-130deg);
  }
  50% {
    transform: rotate(5deg);
  }
  to {
    transform: rotate(-130deg);
  }
}
</style>
     