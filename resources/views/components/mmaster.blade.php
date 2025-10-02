<!DOCTYPE html>
<html lang="en">
  <x-partials._head title={{$title}} />
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">
  <x-partials._top-nav />
  <x-partials._side-nav />
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <x-partials._header title={{$title}} />
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

  <x-partials._footer />
</div>
<!-- ./wrapper -->
<x-partials._scripts />
@yield('scripts')
</body>
</html>
