        <footer class="main-footer text-sm">
            <strong>Copyright &copy; 2014-2019 <a href="http://adminlte.io">AdminLTE.io</a>.</strong> All rights reserved.
            <div class="float-right d-none d-sm-inline-block">
                <b>Version</b> 3.0.2-pre
            </div>
        </footer>

        <!-- Control Sidebar -->
        <aside class="control-sidebar control-sidebar-dark">
            <!-- Control sidebar content goes here -->
        </aside>
        <!-- /.control-sidebar -->
    </div>
    <!-- ./wrapper -->

    <!-- jQuery -->
    <script src="<?= base_url() ?>assets/plugins/jquery/jquery.min.js"></script>
    <!-- jQuery UI 1.11.4 -->
    <script src="<?= base_url() ?>assets/plugins/jquery-ui/jquery-ui.min.js"></script>
    <!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
    <script>
        $.widget.bridge('uibutton', $.ui.button)
        const baseUrl = "<?= base_url(); ?>";
        const currentMonth = "<?= date('m') ?>";
        const lang = "<?= $this->session->userdata('site_lang'); ?>";
    </script>
    <!-- Bootstrap 4 -->
    <script src="<?= base_url() ?>assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- ChartJS -->
    <script src="<?= base_url() ?>assets/plugins/chart.js/Chart.min.js"></script>
    <script src="<?= base_url() ?>assets/plugins/chart.js/Chart.datalabels.js"></script>
    <!-- Sparkline -->
    <script src="<?= base_url() ?>assets/plugins/sparklines/sparkline.js"></script>
    <!-- JQVMap -->
    <script src="<?= base_url() ?>assets/plugins/jqvmap/jquery.vmap.min.js"></script>
    <script src="<?= base_url() ?>assets/plugins/jqvmap/maps/jquery.vmap.usa.js"></script>
    <!-- jQuery Knob Chart -->
    <script src="<?= base_url() ?>assets/plugins/jquery-knob/jquery.knob.min.js"></script>
    <!-- daterangepicker -->
    <script src="<?= base_url() ?>assets/plugins/moment/moment.min.js"></script>
    <script src="<?= base_url() ?>assets/plugins/daterangepicker/daterangepicker.js"></script>
    <!-- Summernote -->
    <script src="<?= base_url() ?>assets/plugins/summernote/summernote-bs4.min.js"></script>
    <!-- overlayScrollbars -->
    <script src="<?= base_url() ?>assets/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
    <!-- AdminLTE App -->
    <script src="<?= base_url() ?>assets/js/adminlte.js"></script>
    <script src="<?= base_url() ?>assets/plugins/datatables/jquery.dataTables.js"></script>
    <script src="<?= base_url() ?>assets/plugins/datatables-bs4/js/dataTables.bootstrap4.js"></script>
    <!-- Block UI -->
    <script src="<?= base_url() ?>assets/plugins/blockui/blockui.js"></script>
    <!-- SweetAlert2 -->
    <script src="<?= base_url() ?>assets/plugins/sweetalert2/sweetalert2.min.js"></script>
    <!-- Datatables -->
    <script src="<?= base_url() ?>assets/plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
    <script src="<?= base_url() ?>assets/plugins/datatables-buttons/js/buttons.jzip.min.js"></script>
    <script src="<?= base_url() ?>assets/plugins/datatables-buttons/js/pdfmake.min.js"></script>
    <script src="<?= base_url() ?>assets/plugins/datatables-buttons/js/vfs_font.min.js"></script>
    <script src="<?= base_url() ?>assets/plugins/datatables-buttons/js/buttons.html5.min.js"></script>
    <script src="<?= base_url() ?>assets/plugins/datatables-fixedcolumns/js/dataTables.fixedColumns.min.js"></script>
    <script src="<?= base_url() ?>assets/plugins/datatables-buttons/js/buttons.colVis.min.js"></script>
    <!-- Datepicker -->
    <script src="<?= base_url() ?>assets/plugins/datepicker/script.min.js"></script>

    <script src="<?= base_url() ?>assets/plugins/toastr/toastr.min.js"></script>
    <script src="<?= base_url() ?>assets/plugins/jstree/jstree.min.js"></script>
    <!-- Dropzone -->
    <script src="<?= base_url() ?>assets/plugins/dropzone/min/dropzone.min.js"></script>
    <script src="<?= base_url() ?>assets/plugins/leaflet/leaflet.js"></script>
    <script>
        const msg = {
            fail: {
                save: "<?= lang('ajax_msg_save_fail') ?>",
                update: "<?= lang('ajax_msg_update_fail') ?>",
                delete: "<?= lang('ajax_msg_delete_fail') ?>"
            },
            success: {},
            confirmation: "<?= lang('confirm'); ?>",
            confirm: {
                save: "<?= lang('confirm_save'); ?>",
                update: "<?= lang('confirm_update'); ?>"
            },
            btn: {
                yes: "<?= lang('btn_yes') ?>",
                no: "<?= lang('btn_no') ?>"
            }
        }

        function blockUI() {
            $.blockUI({
                css: {
                    backgroundColor: 'transparent',
                    border: 'none'
                },
                message: '<div class="spinner"></div>',
                baseZ: 1500,
                overlayCSS: {
                    backgroundColor: '#fff',
                    opacity: 0.7,
                    cursor: 'wait'
                }
            });
        }

        function unBlockUI() {
            $.unblockUI();
        }

        function blockModal() {
            $(".modal-content").block({
                css: {
                    backgroundColor: 'transparent',
                    border: 'none'
                },
                message: '<div class="spinner"></div>',
                baseZ: 1500,
                overlayCSS: {
                    backgroundColor: '#fff',
                    opacity: 0.7,
                    cursor: 'wait'
                }
            });
        }

        function unBlockModal() {
            $(".modal-content").unblock();
        }

        $("#btn-logout").click(function(){
            Swal.fire({
                title: "<?= lang('menu_logout'); ?>",
                text: "<?= lang('confirm_logout'); ?>",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: msg.btn.yes,
                cancelButtonText: msg.btn.no
            }).then((result) => {
                if (result.value) {
                    var config = {
                        url: baseUrl + 'Login/clear_session'
                    }

                    $.ajax(config)
                    .then(function(data) {
                        window.location.href = baseUrl;
                    })
                    .fail(function(){
                        Toast.fire({
                            type: 'error',
                            title: msg.fail.save
                        });
                    });
                }
            });
        });

        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                confirmButton: 'btn btn-success',
                cancelButton: 'btn btn-danger mr-2'
            },
            buttonsStyling: false
        });

        function numberWithCommas(x) {
            return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        }
    </script>
    <?php 
        echo isset($ext) ? $ext : '';
        $js = isset($js) ? $js : [];
        if ($js) {
            for ($i = 0; $i < count($js); $i++) {
                echo '<script src="'.base_url($js[$i]).'?v='.rand().'"></script>';
            }
        }
    ?>
</body>

</html>