<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Keuzetool OM') }}</title>

    <!-- Styles -->
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="../css/bootstrap.min.css">
    <link href="/css/app.css" rel="stylesheet">

    <!-- Scripts -->
    <script>
        window.Laravel = <?php echo json_encode([
            'csrfToken' => csrf_token(),
        ]); ?>
    </script>
</head>
<body>
    <div class="container">
        <nav>
            <div class="inner_nav">
                <a href="/"><img src="/images/KdG_H_Closed_White.png" alt="KdG-logo wit" class="logo"></a>
                <h1 class="nav_text">Office Management - Keuzetool</h1>
            </div>
        </nav>

        @yield('content')
    </div>

    <div class="modal fade" id="adminModal" tabindex="-1" role="dialog" aria-labelledby="descriptionModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title">Admin - Login</h1>
                </div>
                <div class="modal-body">
                    Test
                </div>
                <div class="modal-footer">
                    <button type="button" class="button" data-dismiss="modal">Sluiten</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script
        src="https://code.jquery.com/jquery-3.1.1.min.js"
        integrity="sha256-hVVnYaiADRTO2PzUGmuLJr8BLUSjGIZsDYGmIJLv2b8="
        crossorigin="anonymous">
    </script>
    <script
        src="http://code.jquery.com/ui/1.12.1/jquery-ui.min.js"
        integrity="sha256-VazP97ZCwtekAsvgPBSUwPFKdrwD3unUfSGVYrahUqU="
        crossorigin="anonymous">
    </script>
    <script type="text/javascript" src="../js/bootstrap.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.97.8/js/materialize.min.js"></script>
    @if(!empty(Session::get('error_code')) && Session::get('error_code') === 5)
        <script>
        console.log('test');
            $(function() {
                $('#adminModal').modal('show');
            });
        </script>
    @endif
    <script src="/js/app.js"></script>
</body>
</html>
