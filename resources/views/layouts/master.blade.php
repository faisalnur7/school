
@include('layouts.partials._head')
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">
  @include('layouts.partials._top-nav')
  @include('layouts.partials._side-nav')
  
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

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<!-- jQuery (required for bootstrap-datepicker) -->
{{-- <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script> --}}

<!-- Bootstrap Datepicker JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.10.0/js/bootstrap-datepicker.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>


<script>
    $(function () {
        $('.datepicker').datepicker({
            format: 'yyyy-mm-dd',
            autoclose: true,
            todayHighlight: true
        });
    });
</script>

<script>
toastr.options = {
    closeButton: true,
    progressBar: true,
    positionClass: "toast-top-right",
    timeOut: 2500,
    extendedTimeOut: 1000,
    showDuration: 300,
    hideDuration: 300,
    showMethod: "fadeIn",
    hideMethod: "fadeOut"
};
</script>
</body>
</html>
