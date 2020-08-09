<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>CSV Diff</title>

    <!-- Jquery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">
    <!-- Styles -->
    <style>
        html,
        body {
            background-color: #fff;
            color: #636b6f;
            font-family: 'Nunito', sans-serif;
            font-weight: 200;
            height: 100vh;
            margin: 0;
        }

        .full-height {
            height: 100vh;
        }

        .flex-center {
            align-items: center;
            display: flex;
            justify-content: center;
        }

        .position-ref {
            position: relative;
        }

        .top-right {
            position: absolute;
            right: 10px;
            top: 18px;
        }

        .content {
            text-align: center;
        }

        .title {
            font-size: 84px;
        }

        .links>a {
            color: #636b6f;
            padding: 0 25px;
            font-size: 13px;
            font-weight: 600;
            letter-spacing: .1rem;
            text-decoration: none;
            text-transform: uppercase;
        }

        .m-b-md {
            margin-bottom: 30px;
        }

        .upload-btn-wrapper {
            position: relative;
            overflow: hidden;
            display: inline-block;
        }

        .btn {
            border: 2px solid gray;
            color: gray;
            background-color: white;
            padding: 8px 20px;
            border-radius: 8px;
            font-size: 20px;
            font-weight: bold;
        }

        .btn-selected {
            border: 2px solid black;
            color: white;
            background-color: gray;
            padding: 8px 20px;
            border-radius: 8px;
            font-size: 20px;
            font-weight: bold;
        }

        .upload-btn-wrapper input[type=file] {
            font-size: 100px;
            position: absolute;
            left: 0;
            top: 0;
            opacity: 0;
        }
    </style>

</head>

<body>
    <div class="flex-center position-ref full-height">
        <div class="content">
            <div class="title m-b-md">
                CSV Diff
            </div>

            @if (count($errors) > 0)
            <div class="alert alert-danger">
                <strong>Whoops!</strong> There were some problems with your input.
                <ul>
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <form action="/" method="POST" id="csvform" enctype="multipart/form-data">
                @csrf

                <div class="upload-btn-wrapper">
                    <button class="btn" id="btn1">Upload Original File</button>
                    <input type="file" name="file1" id="file1" class="form-control">
                </div>

                <div class="upload-btn-wrapper">
                    <button class="btn" id="btn2">Upload New File</button>
                    <input type="file" name="file2" id="file2" class="form-control">
                </div>
            </form>

            <div class="links m-b-md">
                <a href="https://filmaluco.github.io/CSVDiff/classes/App-Libraries-CSVDiff-CSVDiff.html">Docs</a>
                <a href="/TTRDiff(2).png">Flowchart of CSVDiff</a>
                <a href="https://github.com/Filmaluco/CSVDiff">GitHub</a>
            </div>

        </div>
    </div>

</body>

</html>

<script type="text/javascript">
    $("#csvform").trigger("reset");
    $("#csvform").change(function() {

        if ($('#file1').val()) {
            console.log("Changed")
            $('#btn1').removeClass('btn');
            $('#btn1').addClass('btn-selected');
        }

        if ($('#file2').val()) {
            $('#btn2').removeClass('btn');
            $('#btn2').addClass('btn-selected');
        }

        if ($('#file1').val() && $('#file2').val()) {
            $("#csvform").submit();
        }
    });
</script>