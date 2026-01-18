<!DOCTYPE html>
<html lang="en">
  <head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title><?= $title ?? 'PMI Kudus' ?></title>
    <meta
      content="width=device-width, initial-scale=1.0, shrink-to-fit=no"
      name="viewport"
    />
    <link rel="icon" href="<?= base_url('assets/img/kaiadmin/favicon.ico') ?>" type="image/x-icon"/>

    <!-- Fonts and icons -->
    <script src="<?= base_url('assets/js/plugin/webfont/webfont.min.js') ?>"></script>
    <script>
      WebFont.load({
        google: { families: ["Public Sans:300,400,500,600,700"] },
        custom: {
          families: [
            "Font Awesome 5 Solid",
            "Font Awesome 5 Regular",
            "Font Awesome 5 Brands",
            "simple-line-icons",
          ],
          urls: ["<?= base_url('assets/css/fonts.min.css') ?>"],
        },
        active: function () {
          sessionStorage.fonts = true;
        },
      });
    </script>

    <style>
      #page-loader {
          position: fixed;
          z-index: 99999;
          top: 0;
          left: 0;
          width: 100%;
          height: 100%;
          background: #ffffff;
          display: flex;
          align-items: center;
          justify-content: center;
      }

      .loader-content {
          text-align: center;
      }

      #page-loader.fade-out {
          opacity: 0;
          visibility: hidden;
          transition: all 0.4s ease;
      }
    </style>

    <!-- CSS Files -->
    <link rel="stylesheet" href="<?= base_url('assets/css/bootstrap.min.css') ?>" />
    <link rel="stylesheet" href="<?= base_url('assets/css/plugins.min.css') ?>" />
    <link rel="stylesheet" href="<?= base_url('assets/css/kaiadmin.min.css') ?>" />

    <!-- Demo CSS (optional) -->
    <link rel="stylesheet" href="<?= base_url('assets/css/demo.css') ?>" />

    <?= $this->renderSection('style') ?>

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

  </head>
  <body>
    <!-- PAGE LOADING -->
    <div id="page-loader">
        <div class="loader-content">
            <div class="spinner-border text-primary" role="status"></div>
            <p class="mt-3 fw-bold">Memuat halaman...</p>
        </div>
    </div>

    <div class="wrapper">
      <!-- Sidebar -->
      <div class="sidebar" data-background-color="dark">
        <div class="sidebar-logo">
          <!-- Logo Header -->
          <div class="logo-header" data-background-color="dark">
            <a href="index.html" class="logo">
              <img
                src="<?= base_url('assets/img/kaiadmin/logo_light.svg') ?>"
                alt="navbar brand"
                class="navbar-brand"
                height="20"
              />
            </a>
            <div class="nav-toggle">
              <button class="btn btn-toggle toggle-sidebar">
                <i class="gg-menu-right"></i>
              </button>
              <button class="btn btn-toggle sidenav-toggler">
                <i class="gg-menu-left"></i>
              </button>
            </div>
            <button class="topbar-toggler more">
              <i class="gg-more-vertical-alt"></i>
            </button>
          </div>
          <!-- End Logo Header -->
        </div>
        <div class="sidebar-wrapper scrollbar scrollbar-inner">
          <div class="sidebar-content">
            <ul class="nav nav-secondary">
              <li class="nav-item <?= active_menu('dashboard') ?>">
                  <a href="<?= site_url('dashboard') ?>">
                      <i class="fas fa-home"></i>
                      <p>Dashboard</p>
                  </a>
              </li>
              <li class="nav-section">
                <span class="sidebar-mini-icon">
                  <i class="fa fa-ellipsis-h"></i>
                </span>
                <h4 class="text-section">Components</h4>
              </li>
              <li class="nav-item <?= active_menu('pendonor') ?>">
                  <a href="<?= site_url('pendonor') ?>">
                      <i class="fas fa-user-friends"></i>
                      <p>Pendonor</p>
                  </a>
              </li>
              <li class="nav-item">
                  <a href="<?= site_url('historis-donor') ?>">
                      <i class="fas fa-file-medical-alt"></i>
                      <p>Historis Donor</p>
                  </a>
              </li>
              <li class="nav-item <?= active_menu('user') ?>">
                  <a href="<?= site_url('user') ?>">
                      <i class="fas fa-users-cog"></i>
                      <p>Manajemen User</p>
                  </a>
              </li>
              <?php $isBase = in_array(service('uri')->getSegment(2), ['avatars', 'buttons']); ?>
              <li class="nav-item <?= $isBase ? 'active' : '' ?>">
                  <a data-bs-toggle="collapse" href="#base" class="<?= $isBase ? '' : 'collapsed' ?>">
                      <i class="fas fa-layer-group"></i>
                      <p>Base</p>
                      <span class="caret"></span>
                  </a>

                  <div class="collapse <?= $isBase ? 'show' : '' ?>" id="base">
                      <ul class="nav nav-collapse">
                          <li class="<?= service('uri')->getSegment(2) === 'avatars' ? 'active' : '' ?>">
                              <a href="<?= site_url('components/avatars') ?>">
                                  <span class="sub-item">Avatars</span>
                              </a>
                          </li>

                          <li class="<?= service('uri')->getSegment(2) === 'buttons' ? 'active' : '' ?>">
                              <a href="<?= site_url('components/buttons') ?>">
                                  <span class="sub-item">Buttons</span>
                              </a>
                          </li>
                      </ul>
                  </div>
              </li>
            </ul>
          </div>
        </div>
      </div>
      <!-- End Sidebar -->

      <div class="main-panel">
        <div class="main-header">
          <div class="main-header-logo">
            <!-- Logo Header -->
            <div class="logo-header" data-background-color="dark">
              <a href="index.html" class="logo">
                <img
                  src="<?= base_url('assets/img/kaiadmin/logo_light.svg') ?>"
                  alt="navbar brand"
                  class="navbar-brand"
                  height="20"
                />
              </a>
              <div class="nav-toggle">
                <button class="btn btn-toggle toggle-sidebar">
                  <i class="gg-menu-right"></i>
                </button>
                <button class="btn btn-toggle sidenav-toggler">
                  <i class="gg-menu-left"></i>
                </button>
              </div>
              <button class="topbar-toggler more">
                <i class="gg-more-vertical-alt"></i>
              </button>
            </div>
            <!-- End Logo Header -->
          </div>
          <!-- Navbar Header -->
          <nav
            class="navbar navbar-header navbar-header-transparent navbar-expand-lg border-bottom"
          >
            <div class="container-fluid">

              <ul class="navbar-nav topbar-nav ms-md-auto align-items-center">
                <li
                  class="nav-item topbar-icon dropdown hidden-caret d-flex d-lg-none"
                >
                  <a
                    class="nav-link dropdown-toggle"
                    data-bs-toggle="dropdown"
                    href="#"
                    role="button"
                    aria-expanded="false"
                    aria-haspopup="true"
                  >
                    <i class="fa fa-search"></i>
                  </a>
                  <ul class="dropdown-menu dropdown-search animated fadeIn">
                    <form class="navbar-left navbar-form nav-search">
                      <div class="input-group">
                        <input
                          type="text"
                          placeholder="Search ..."
                          class="form-control"
                        />
                      </div>
                    </form>
                  </ul>
                </li>
                <li class="nav-item topbar-icon dropdown hidden-caret">
                  <a
                    class="nav-link"
                    data-bs-toggle="dropdown"
                    href="#"
                    aria-expanded="false"
                  >
                    <i class="fas fa-layer-group"></i>
                  </a>
                  <div class="dropdown-menu quick-actions animated fadeIn">
                    <div class="quick-actions-header">
                      <span class="title mb-1">Quick Actions</span>
                      <span class="subtitle op-7">Shortcuts</span>
                    </div>
                    <div class="quick-actions-scroll scrollbar-outer">
                      <div class="quick-actions-items">
                        <div class="row m-0">

                          <!-- Dashboard -->
                          <a class="col-6 col-md-4 p-0" href="<?= site_url('dashboard') ?>">
                            <div class="quick-actions-item">
                              <div class="avatar-item bg-danger rounded-circle">
                                <i class="fas fa-home"></i>
                              </div>
                              <span class="text">Dashboard</span>
                            </div>
                          </a>

                          <!-- Pendonor -->
                          <a class="col-6 col-md-4 p-0" href="<?= site_url('pendonor') ?>">
                            <div class="quick-actions-item">
                              <div class="avatar-item bg-warning rounded-circle">
                                <i class="fas fa-user-friends"></i>
                              </div>
                              <span class="text">Pendonor</span>
                            </div>
                          </a>

                          <!-- Historis Donor -->
                          <a class="col-6 col-md-4 p-0" href="<?= site_url('historis-donor') ?>">
                            <div class="quick-actions-item">
                              <div class="avatar-item bg-info rounded-circle">
                                <i class="fas fa-file-medical-alt"></i>
                              </div>
                              <span class="text">Hst. Donor</span>
                            </div>
                          </a>

                          <!-- Manajemen User -->
                          <a class="col-6 col-md-4 p-0" href="<?= site_url('user') ?>">
                            <div class="quick-actions-item">
                              <div class="avatar-item bg-success rounded-circle">
                                <i class="fas fa-users-cog"></i>
                              </div>
                              <span class="text">Mj. User</span>
                            </div>
                          </a>

                          <a class="col-6 col-md-4 p-0" href="#">
                            <div class="quick-actions-item">
                              <div
                                class="avatar-item bg-warning rounded-circle"
                              >
                                <i class="fas fa-map"></i>
                              </div>
                              <span class="text">Maps</span>
                            </div>
                          </a>
                          <a class="col-6 col-md-4 p-0" href="#">
                            <div class="quick-actions-item">
                              <div class="avatar-item bg-info rounded-circle">
                                <i class="fas fa-file-excel"></i>
                              </div>
                              <span class="text">Reports</span>
                            </div>
                          </a>
                        </div>
                      </div>
                    </div>
                  </div>
                </li>

                <li class="nav-item topbar-user dropdown hidden-caret">
                  <a
                    class="dropdown-toggle profile-pic"
                    data-bs-toggle="dropdown"
                    href="#"
                    aria-expanded="false"
                  >
                    <span class="profile-username">
                      <span class="op-7">Hi,</span>
                      <span class="fw-bold"><?= session('username') ?></span>
                    </span>
                  </a>
                  <ul class="dropdown-menu dropdown-user animated fadeIn">
                    <div class="dropdown-user-scroll scrollbar-outer">
                      <li>
                        <div class="user-box">
                        
                          <div class="u-text">
                            <h4><?= session('username') ?></h4>
                            
                          </div>
                        </div>
                      </li>
                      <li>
                          <div class="dropdown-divider"></div>
                          <a class="dropdown-item" href="<?= site_url('logout') ?>">Logout</a>
                      </li>
                    </div>
                  </ul>
                </li>
              </ul>
            </div>
          </nav>
          <!-- End Navbar -->
        </div>

        <div class="container">
            <div class="page-inner">
                <?= $this->renderSection('content') ?>
            </div>
        </div>

        <footer class="footer">
          <div class="container-fluid d-flex justify-content-between">
            <nav class="pull-left">
              <ul class="nav">
                <li class="nav-item">
                  <a class="nav-link" href="http://www.themekita.com">
                    
                  </a>
                </li>
              </ul>
            </nav>
            <div class="copyright">
              2025, made with by
              <a href="">PMI Kudus</a>
            </div>
            <div>
              <a target="_blank" href="https://themewagon.com/"></a>.
            </div>
          </div>
        </footer>
      </div>


      <!-- End Custom template -->
    </div>
    <script src="<?= base_url('assets/js/core/jquery-3.7.1.min.js') ?>"></script>
    <script src="<?= base_url('assets/js/core/popper.min.js') ?>"></script>
    <script src="<?= base_url('assets/js/core/bootstrap.min.js') ?>"></script>

    <!-- jQuery Scrollbar -->
    <script src="<?= base_url('assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js') ?>"></script>

    <!-- Chart JS -->
    <script src="<?= base_url('assets/js/plugin/chart.js/chart.min.js') ?>"></script>

    <!-- jQuery Sparkline -->
    <script src="<?= base_url('assets/js/plugin/jquery.sparkline/jquery.sparkline.min.js') ?>"></script>

    <!-- Chart Circle -->
    <script src="<?= base_url('assets/js/plugin/chart-circle/circles.min.js') ?>"></script>

    <!-- Datatables -->
    <script src="<?= base_url('assets/js/plugin/datatables/datatables.min.js') ?>"></script>

    <!-- Bootstrap Notify -->
    <script src="<?= base_url('assets/js/plugin/bootstrap-notify/bootstrap-notify.min.js') ?>"></script>

    <!-- jQuery Vector Maps -->
    <script src="<?= base_url('assets/js/plugin/jsvectormap/jsvectormap.min.js') ?>"></script>
    <script src="<?= base_url('assets/js/plugin/jsvectormap/world.js') ?>"></script>

    <!-- Sweet Alert -->
    <script src="<?= base_url('assets/js/plugin/sweetalert/sweetalert.min.js') ?>"></script>

    <!-- Kaiadmin JS -->
    <script src="<?= base_url('assets/js/kaiadmin.min.js') ?>"></script>

    <!-- Demo JS (optional) -->
    <script src="<?= base_url('assets/js/setting-demo.js') ?>"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <?php if (isset($loadDemoJs) && $loadDemoJs): ?>
    <script src="assets/js/demo.js"></script>
    <?php endif; ?>

    <script>
      $("#lineChart").sparkline([102, 109, 120, 99, 110, 105, 115], {
        type: "line",
        height: "70",
        width: "100%",
        lineWidth: "2",
        lineColor: "#177dff",
        fillColor: "rgba(23, 125, 255, 0.14)",
      });

      $("#lineChart2").sparkline([99, 125, 122, 105, 110, 124, 115], {
        type: "line",
        height: "70",
        width: "100%",
        lineWidth: "2",
        lineColor: "#f3545d",
        fillColor: "rgba(243, 84, 93, .14)",
      });

      $("#lineChart3").sparkline([105, 103, 123, 100, 95, 105, 115], {
        type: "line",
        height: "70",
        width: "100%",
        lineWidth: "2",
        lineColor: "#ffa534",
        fillColor: "rgba(255, 165, 52, .14)",
      });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Section Script Dari Halaman -->
    <?= $this->renderSection('script') ?>
    <script>
    window.addEventListener('load', function () {
        const loader = document.getElementById('page-loader');
        loader.classList.add('fade-out');

        setTimeout(() => {
            loader.style.display = 'none';
        }, 500);
    });
    </script>

  </body>
</html>
