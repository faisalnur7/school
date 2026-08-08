@php
    $currentLocale = app()->getLocale();
    $currentFlag = $currentLocale === 'bn' ? '🇧🇩' : '🇺🇸';
    $timezone = config('app.timezone', 'Asia/Dhaka');
@endphp

  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
      </li>
    </ul>

    <div class="d-none d-lg-flex align-items-center mr-2 topnav-datetime-chip">
      <div class="d-flex align-items-center text-right topnav-datetime-chip__inner">
        <span class="topnav-datetime-chip__icon" aria-hidden="true"><i class="far fa-clock"></i></span>
        <span id="topNavClock" class="font-weight-bold small topnav-datetime-chip__clock">{{ now($timezone)->format('h:i:s A') }}</span>
        <span class="topnav-datetime-chip__separator" aria-hidden="true"></span>
        <span class="topnav-datetime-chip__icon" aria-hidden="true"><i class="far fa-calendar-alt"></i></span>
        <span id="topNavDate" class="topnav-datetime-chip__date">{{ now($timezone)->format('D, M d, Y') }}</span>
      </div>
    </div>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto topnav-actions">
      <li class="nav-item dropdown mr-1">
        <a class="nav-link dropdown-toggle topnav-lang-toggle d-inline-flex align-items-center" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false" aria-label="{{ __('Language') }}">
          <span class="topnav-lang-toggle__flag" aria-hidden="true">{{ $currentFlag }}</span>
          <i class="fas fa-chevron-down topnav-lang-toggle__caret" aria-hidden="true"></i>
        </a>
        <div class="dropdown-menu dropdown-menu-right topnav-lang-menu shadow-lg border-0 p-2">
          <a class="dropdown-item d-flex align-items-center topnav-lang-item {{ $currentLocale === 'en' ? 'active' : '' }}" href="{{ route('locale.switch', ['locale' => 'en']) }}" aria-label="{{ __('Switch to English') }}">
            <span class="topnav-lang-item__flag" aria-hidden="true">🇺🇸</span>
            <span class="topnav-lang-item__label">{{ __('English') }}</span>
          </a>
          <a class="dropdown-item d-flex align-items-center topnav-lang-item {{ $currentLocale === 'bn' ? 'active' : '' }}" href="{{ route('locale.switch', ['locale' => 'bn']) }}" aria-label="{{ __('Switch to Bangla') }}">
            <span class="topnav-lang-item__flag" aria-hidden="true">🇧🇩</span>
            <span class="topnav-lang-item__label">{{ __('Bangla') }}</span>
          </a>
        </div>
      </li>

      <!-- Navbar Search -->
      <li class="nav-item d-flex align-items-center mr-3">
        @include('layouts.partials._theme-toggle', ['buttonClass' => 'topnav-theme-toggle'])
      </li>

      <li class="nav-item">
        <a class="nav-link" data-widget="navbar-search" href="#" role="button">
          <i class="fas fa-search"></i>
        </a>
        <div class="navbar-search-block">
          <form class="form-inline">
            <div class="input-group input-group-sm">
              <input class="form-control form-control-navbar" type="search" placeholder="Search" aria-label="Search">
              <div class="input-group-append">
                <button class="btn btn-navbar" type="submit">
                  <i class="fas fa-search"></i>
                </button>
                <button class="btn btn-navbar" type="button" data-widget="navbar-search">
                  <i class="fas fa-times"></i>
                </button>
              </div>
            </div>
          </form>
        </div>
      </li>

      <!-- Messages Dropdown Menu -->
      <li class="nav-item dropdown">
        <a class="nav-link" data-toggle="dropdown" href="#">
          <i class="far fa-comments"></i>
          <span class="badge badge-danger navbar-badge">3</span>
        </a>
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
          <a href="#" class="dropdown-item">
            <!-- Message Start -->
            <div class="media">
              <img src="{{asset('assets/dist/img/user1-128x128.jpg')}}" alt="User Avatar" class="img-size-50 mr-3 img-circle">
              <div class="media-body">
                <h3 class="dropdown-item-title">
                  Brad Diesel
                  <span class="float-right text-sm text-danger"><i class="fas fa-star"></i></span>
                </h3>
                <p class="text-sm">Call me whenever you can...</p>
                <p class="text-sm text-muted"><i class="far fa-clock mr-1"></i> 4 Hours Ago</p>
              </div>
            </div>
            <!-- Message End -->
          </a>
          <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item">
            <!-- Message Start -->
            <div class="media">
              <img src="{{asset('assets/dist/img/user8-128x128.jpg')}}" alt="User Avatar" class="img-size-50 img-circle mr-3">
              <div class="media-body">
                <h3 class="dropdown-item-title">
                  John Pierce
                  <span class="float-right text-sm text-muted"><i class="fas fa-star"></i></span>
                </h3>
                <p class="text-sm">I got your message bro</p>
                <p class="text-sm text-muted"><i class="far fa-clock mr-1"></i> 4 Hours Ago</p>
              </div>
            </div>
            <!-- Message End -->
          </a>
          <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item">
            <!-- Message Start -->
            <div class="media">
              <img src="{{asset('assets/dist/img/user3-128x128.jpg')}}" alt="User Avatar" class="img-size-50 img-circle mr-3">
              <div class="media-body">
                <h3 class="dropdown-item-title">
                  Nora Silvester
                  <span class="float-right text-sm text-warning"><i class="fas fa-star"></i></span>
                </h3>
                <p class="text-sm">The subject goes here</p>
                <p class="text-sm text-muted"><i class="far fa-clock mr-1"></i> 4 Hours Ago</p>
              </div>
            </div>
            <!-- Message End -->
          </a>
          <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item dropdown-footer">See All Messages</a>
        </div>
      </li>
      <!-- Notifications Dropdown Menu -->
      <li class="nav-item dropdown">
        <a class="nav-link" data-toggle="dropdown" href="#">
          <i class="far fa-bell"></i>
          <span class="badge badge-warning navbar-badge">15</span>
        </a>
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
          <span class="dropdown-item dropdown-header">15 Notifications</span>
          <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item">
            <i class="fas fa-envelope mr-2"></i> 4 new messages
            <span class="float-right text-muted text-sm">3 mins</span>
          </a>
          <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item">
            <i class="fas fa-users mr-2"></i> 8 friend requests
            <span class="float-right text-muted text-sm">12 hours</span>
          </a>
          <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item">
            <i class="fas fa-file mr-2"></i> 3 new reports
            <span class="float-right text-muted text-sm">2 days</span>
          </a>
          <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item dropdown-footer">See All Notifications</a>
        </div>
      </li>

      <li class="nav-item">
        <a class="nav-link" data-widget="control-sidebar" data-controlsidebar-slide="true" href="#" role="button">
          <i class="fas fa-th-large"></i>
        </a>
      </li>
    </ul>
  </nav>
  <!-- /.navbar -->

  <style>
    .topnav-datetime-chip {
      min-width: 260px;
      min-height: 38px;
      padding: 0.45rem 0.85rem;
      border-radius: 999px;
      border: 1px solid rgba(191, 219, 254, 0.9);
      background: linear-gradient(135deg, rgba(255, 255, 255, 0.95) 0%, rgba(239, 246, 255, 0.9) 100%);
      box-shadow: 0 12px 26px rgba(15, 23, 42, 0.08);
      color: #0f172a;
      backdrop-filter: blur(14px);
    }

    .topnav-datetime-chip__inner {
      gap: 0.5rem;
      white-space: nowrap;
    }

    .topnav-datetime-chip__icon {
      color: #2563eb;
      font-size: 0.88rem;
      line-height: 1;
    }

    .topnav-datetime-chip__clock,
    .topnav-datetime-chip__date {
      color: #0f172a;
      line-height: 1;
    }

    .topnav-datetime-chip__date {
      font-size: 0.8rem;
      font-weight: 600;
    }

    .topnav-datetime-chip__separator {
      width: 1px;
      height: 18px;
      background: rgba(37, 99, 235, 0.18);
    }

    .topnav-actions {
      gap: 0.95rem;
    }

    .topnav-actions > .nav-item {
      margin-left: 0 !important;
      margin-right: 0 !important;
    }

    html[data-theme='dark'] .topnav-datetime-chip {
      background: linear-gradient(135deg, rgba(15, 23, 42, 0.98) 0%, rgba(30, 41, 59, 0.96) 100%);
      border-color: rgba(96, 165, 250, 0.22);
      box-shadow: 0 14px 30px rgba(2, 6, 23, 0.5);
    }

    html[data-theme='dark'] .topnav-datetime-chip__icon {
      color: #93c5fd;
    }

    html[data-theme='dark'] .topnav-datetime-chip__clock,
    html[data-theme='dark'] .topnav-datetime-chip__date {
      color: #f8fafc;
    }

    html[data-theme='dark'] .topnav-datetime-chip__separator {
      background: rgba(96, 165, 250, 0.42);
    }

    .topnav-lang-toggle {
      min-width: 3.1rem;
      min-height: 38px;
      padding: 0.4rem 0.72rem !important;
      border-radius: 999px;
      background: linear-gradient(135deg, rgba(255, 255, 255, 0.96) 0%, rgba(239, 246, 255, 0.92) 100%);
      border: 1px solid rgba(191, 219, 254, 0.9);
      box-shadow: 0 12px 26px rgba(15, 23, 42, 0.08);
      gap: 0.3rem;
      justify-content: center;
      backdrop-filter: blur(14px);
    }

    .topnav-lang-toggle:hover,
    .topnav-lang-toggle:focus {
      background: linear-gradient(135deg, rgba(239, 246, 255, 0.98) 0%, rgba(219, 234, 254, 0.96) 100%);
      border-color: #bfdbfe;
      box-shadow: 0 14px 28px rgba(37, 99, 235, 0.12);
    }

    .topnav-lang-toggle::after {
      display: none;
    }

    .topnav-lang-toggle__flag {
      font-size: 1.05rem;
      line-height: 1;
    }

    .topnav-lang-toggle__caret {
      font-size: 0.62rem;
      color: #64748b;
    }

    .topnav-lang-menu {
      min-width: 7rem;
      padding: 0.45rem;
      border-radius: 18px;
      overflow: hidden;
      background: rgba(255, 255, 255, 0.96);
      backdrop-filter: blur(16px);
    }

    .topnav-lang-item {
      min-height: 2.4rem;
      justify-content: flex-start;
      border-radius: 12px;
      gap: 0.6rem;
      padding: 0.6rem 0.85rem;
      transition: background-color .15s ease, transform .15s ease, box-shadow .15s ease;
    }

    .topnav-lang-item + .topnav-lang-item {
      margin-top: 0.2rem;
    }

    .topnav-lang-item:hover,
    .topnav-lang-item:focus,
    .topnav-lang-item.active {
      background: rgba(59, 130, 246, 0.12);
      box-shadow: inset 0 0 0 1px rgba(59, 130, 246, 0.12);
      transform: translateY(-1px);
    }

    .topnav-lang-item__flag {
      font-size: 1rem;
      line-height: 1;
    }

    .topnav-lang-item__label {
      color: #0f172a;
      font-size: 0.86rem;
      font-weight: 600;
      line-height: 1;
      letter-spacing: 0.01em;
    }

    html[data-theme='dark'] .topnav-lang-toggle {
      background: linear-gradient(135deg, rgba(15, 23, 42, 0.98) 0%, rgba(30, 41, 59, 0.96) 100%);
      border-color: rgba(96, 165, 250, 0.22);
      box-shadow: 0 14px 30px rgba(2, 6, 23, 0.45);
    }

    html[data-theme='dark'] .topnav-lang-toggle:hover,
    html[data-theme='dark'] .topnav-lang-toggle:focus {
      background: linear-gradient(135deg, rgba(30, 41, 59, 1) 0%, rgba(15, 23, 42, 0.98) 100%);
      border-color: rgba(96, 165, 250, 0.5);
      box-shadow: 0 16px 32px rgba(2, 6, 23, 0.5);
    }

    html[data-theme='dark'] .topnav-lang-toggle__caret {
      color: #cbd5e1;
    }

    html[data-theme='dark'] .topnav-lang-menu {
      background: rgba(15, 23, 42, 0.99);
      border-color: rgba(96, 165, 250, 0.18);
      box-shadow: 0 18px 36px rgba(2, 6, 23, 0.55);
    }

    html[data-theme='dark'] .topnav-lang-item:hover,
    html[data-theme='dark'] .topnav-lang-item:focus,
    html[data-theme='dark'] .topnav-lang-item.active {
      background: rgba(59, 130, 246, 0.24);
      box-shadow: inset 0 0 0 1px rgba(96, 165, 250, 0.24);
    }

    html[data-theme='dark'] .topnav-lang-item__label {
      color: #f8fafc;
    }

    @media (max-width: 991.98px) {
      .topnav-actions {
        gap: 0.7rem;
      }
    }

    @media (max-width: 576px) {
      .topnav-actions {
        gap: 0.5rem;
      }
    }
  </style>

  <script>
    (function () {
      const timezone = @json($timezone);

      function updateTopNavClock() {
        const now = new Date();
        const clockEl = document.getElementById('topNavClock');
        const dateEl = document.getElementById('topNavDate');

        const timeFormatter = new Intl.DateTimeFormat('en-US', {
          timeZone: timezone,
          hour: '2-digit',
          minute: '2-digit',
          second: '2-digit',
          hour12: true,
        });

        const dateFormatter = new Intl.DateTimeFormat('en-US', {
          timeZone: timezone,
          weekday: 'short',
          month: 'short',
          day: '2-digit',
          year: 'numeric',
        });

        if (clockEl) {
          clockEl.textContent = timeFormatter.format(now);
        }
        if (dateEl) {
          dateEl.textContent = dateFormatter.format(now);
        }
      }

      updateTopNavClock();
      if (!window.__schoolTopNavClockTimer) {
        window.__schoolTopNavClockTimer = window.setInterval(updateTopNavClock, 1000);
      }
    })();
  </script>
