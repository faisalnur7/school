
@include('layouts.partials._head')
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">
  @include('layouts.partials._merchant_top-nav')
  @include('layouts.partials._merchant-side-nav')
  
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    @include('layouts.partials._header')
    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">

        <!-- Main row -->
        <div class="row">
          @yield('contents')
        </div>
        <!-- /.row (main row) -->
      </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

  @include('layouts.partials._footer')


</div>
<!-- ./wrapper -->

@include('layouts.partials._scripts')

@yield('scripts')
</body>
</html>
