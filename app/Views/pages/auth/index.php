<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
</head>

<body class="bg-light">

<div class="container">
    <div class="row justify-content-center mt-5">
        <div class="col-md-4">
            <div class="card shadow">
                <div class="card-body">
                    <h4 class="text-center mb-4">Login</h4>

                    <form id="formLogin">
                        <?= csrf_field() ?>

                        <div class="mb-3">
                            <label class="form-label">Username</label>
                            <input type="text" name="login" id="login" class="form-control">
                            <small class="text-danger" id="err-login"></small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Password</label>

                            <div class="input-group">
                                <input type="password" name="password" class="form-control password-field">
                                <span class="input-group-text toggle-password" style="cursor:pointer">
                                    <i class="bi bi-eye"></i>
                                </span>
                            </div>

                            <small class="text-danger" id="err-password"></small>
                        </div>

                        <button type="submit" class="btn btn-primary w-100" id="btnLogin">
                            Login
                        </button>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).on('click', '.toggle-password', function () {
        const input = $(this).closest('.input-group').find('.password-field');
        const icon  = $(this).find('i');

        if (input.attr('type') === 'password') {
            input.attr('type', 'text');
            icon.removeClass('bi-eye').addClass('bi-eye-slash');
        } else {
            input.attr('type', 'password');
            icon.removeClass('bi-eye-slash').addClass('bi-eye');
        }
    });

    $("#formLogin").submit(function (e) {
        e.preventDefault();

        $("#err-login, #err-password").html("");

        $.ajax({
            url: "<?= site_url('login/attempt') ?>",
            method: "POST",
            data: $(this).serialize(),
            dataType: "json",
            beforeSend: () => {
                $("#btnLogin").prop("disabled", true).text("Checking...");
            },
            success: function (res) {

                if (res.status === "error_validation") {
                    if (res.errors.login) $("#err-login").html(res.errors.login);
                    if (res.errors.password) $("#err-password").html(res.errors.password);
                    $("#btnLogin").prop("disabled", false).text("Login");
                    return;
                }

                if (res.status === "error") {
                    Swal.fire("Login Gagal", res.message, "error");
                    $("#btnLogin").prop("disabled", false).text("Login");
                    return;
                }

                Swal.fire({
                    icon: "success",
                    title: "Berhasil",
                    text: "Login sukses",
                    timer: 1500,
                    showConfirmButton: false
                }).then(() => {
                    window.location.href = "<?= site_url('dashboard') ?>";
                });
            }
        });
    });
</script>

</body>
</html>
