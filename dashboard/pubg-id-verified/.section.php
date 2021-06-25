<?
$sSectionName = "Pubg Id Verified";
$arDirProperties = Array(
   "description" => "Pubg Id Verified",
   "keywords" => "Pubg Id Verified",
   "TITLE" => "Pubg Id Verified"
);
?>

<style>
    body {
        background-color: #100b2e;
    }
    footer {
        height: 0 !important;
    }
    /* tabs */
    .nav-tabs {
        border-bottom-color: transparent;
    }
    .nav-tabs .nav-link {
        border: 1px solid transparent;
        color: var(--light);
    }
    .nav-tabs .nav-link:focus,
    .nav-tabs .nav-link:hover {
        border-color: transparent;
    }
    .nav-tabs .nav-link.active {
        border-color: transparent;
        color: var(--white);
        background-color: var(--dark);
    }
    /* table head */
    .table thead th {
        vertical-align: middle;
        text-align: center;
    }
    /* table */
    .table td, .table th {
        vertical-align: middle;
    }
</style>

<script>
    $(function () {
        $('[data-toggle="tooltip"]').tooltip()
    });
    $(function() {
        $('a[data-toggle="pill"]').on('shown.bs.tab', function (e) {
            localStorage.setItem('lastTab', $(this).attr('href'));
        });
        var lastTab = localStorage.getItem('lastTab');
        if (lastTab) {
            $('[href="' + lastTab + '"]').tab('show');
        }
    });
</script>