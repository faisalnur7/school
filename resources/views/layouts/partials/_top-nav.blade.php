  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand navbar-white navbar-light" style="position: sticky; top: 0;">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
      </li>
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
      <!-- Navbar Search -->
      
      <li class="nav-item d-flex align-items-center mr-2">
        @include('layouts.partials._theme-toggle')
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

      <li class="nav-item dropdown">
        <a class="nav-link" data-toggle="dropdown" href="#">
          <img src="{{ auth()->user()->image_url }}" alt="User" class="img-circle" style="width:28px;height:28px;object-fit:cover;">
        </a>
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
          <a href="#" class="dropdown-item">
            <!-- Message Start -->
            <div class="media">
              <img src="{{ auth()->user()->image_url }}" alt="User Avatar" class="img-size-50 mr-3 img-circle" style="object-fit:cover;">
              <div class="media-body">
                <h3 class="dropdown-item-title">
                  {{auth()->user()->name ?? ''}}
                </h3>
                <p class="text-sm">Role</p>
              </div>
            </div>
            <!-- Message End -->
          </a>
          <div class="dropdown-divider"></div>

          <a href="{{ route('account.profile.edit') }}" class="dropdown-item">
            <i class="fas fa-cog mr-2"></i> Profile Setting
          </a>
          <div class="dropdown-divider"></div>
          <a href="{{ route('account.password.edit') }}" class="dropdown-item">
            <i class="fas fa-key mr-2"></i> Change Password
          </a>
          <div class="dropdown-divider"></div>
          <a class="dropdown-item cursor-pointer" id="customer-logout-btn">
            <i class="fas fa-power-off mr-2"></i> Logout
          </a>

          <form id="customer_logout_form" style="display:none" action="{{route('logout')}}" method="POST">
            @csrf
          </form>
          <script>
            $(document).on('click','#customer-logout-btn', function(){
              $("#customer_logout_form").submit();
            })
          </script>
        </div>
      </li>
    </ul>
  </nav>
  <!-- /.navbar -->
