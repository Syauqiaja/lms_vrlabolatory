<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <style>
        body {
            margin: 0;
            padding: 0;
            background: rgb(204, 204, 204);
        }

        @page {
            margin: 0;
            padding: 0;
        }

        @media print {

            body,
            page[size="A4"] {
                margin: 0;
                padding: 0;
                box-shadow: 0;
            }
        }

        .container {
            position: relative;
            width: 100%;
            height: 100%;
            text-align: center;
        }

        #name {
            position: absolute;
            top: 300px;
            left: 50%;
            transform: translateX(-50%);
            font-size: 45px;
            font-weight: bold;
            text-align: center;
        }

        #desc {
            position: absolute;
            top: 450px;
            left: 50%;
            transform: translateX(-50%);
            font-size: 24px;
            width: 800px;
            text-align: center;
        }

        img {
            height: 21cm;
            width: 29.7cm;
            position: absolute;
            left: 0;
            top: 0;
        }
    </style>
    <title>VRLaboratory Certificate</title>
</head>

<body>
    @php
    $imagePath = public_path('images/certificate-template.png');
    $imageData = '';
    if (file_exists($imagePath)) {
    $imageData = 'data:image/png;base64,' . base64_encode(file_get_contents($imagePath));
    }
    @endphp
    
    <div>
        @if($imageData)
        <img src="{{ $imageData }}" alt="">
        @endif
        <div class="container">
            <div id="name">
                <p>{{ $name }}</p>
            </div>
            <div id="desc">
                {!! $description !!}
            </div>
        </div>
    </div>

</body>

</html>