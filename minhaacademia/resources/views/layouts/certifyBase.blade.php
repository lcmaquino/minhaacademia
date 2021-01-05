<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{ $certify->name }} • {{ $youtubeChannelTitle }}</title>
    <style type="text/css">
        @charset "utf-8";

        @font-face {
            font-family: 'Source Sans Pro', sans-serif;
            src: url("{{ storage_path('fonts/Source_Sans_Pro/SourceSansPro-Regular.ttf') }}");
            font-weight: 400;
            font-style: normal;
        }

        @font-face {
            font-family: 'Source Sans Pro', sans-serif;
            src: url("{{ storage_path('fonts/Source_Sans_Pro/SourceSansPro-BlackItalic.ttf') }}")
            font-weight: 900;
            font-style: italic;
        }

        @font-face {
            font-family: 'Source Sans Pro', sans-serif;
            src: url("{{ storage_path('fonts/Source_Sans_Pro/SourceSansPro-Bold.ttf') }}")
            font-weight: 900;
            font-style: normal;
        }

        body {
            font-family: 'Source Sans Pro', sans-serif;
            font-size: 1.0rem;
            font-weight: 400;
            font-style: normal;
            background-color: #fff;
            color: #333;
            line-height: 1.2rem;
        }

        h1, h2, h3, h4, h5, h6 {
            margin-top: 0;
            margin-bottom: 2rem;
            font-weight: 300;
        }

        h1 { 
            font-size: 4.0rem;
            line-height: 1.2;
        }

        h2 { 
            font-size: 3.6rem;
            line-height: 1.25;
        }

        h3 {
            font-size: 3.0rem;
            line-height: 1.3;
        }

        h4 {
            font-size: 2.4rem;
            line-height: 1.35;
        }

        h5 { 
            font-size: 1.8rem;
            line-height: 1.5;
        }

        h6 {
            font-size: 1.5rem;
            line-height: 1.6;
        }

        a, a:hover, a:visited, a:link{
            color: #333;
        }

        .certify-template {
            margin: auto;
            width: 842px;
            height: 595px;
            border: 1px solid #333;
            background-color: #fff;
            position: relative;
            overflow: hidden;
        }

        .bg-image {
           margin: auto;
        }

        .bg-image > img {
            width: 130%;
        }

        .text-center {
            text-align: center;
            position: absolute;
            top: 50%;
            left: 50%;
            width: 757px;
            height: 535px;
            transform: translate(-50%, -60%);
        }

        .text-left {
            text-align: left;
        }

        .signature {
            width: 200px;
        }

        .logo {
            height: 36px;
        }
    </style>
</head>
<body>
    <div class="certify-template">
        <div class="text-center">
            <h1>Certificado</h1>
            @yield('content')
            <p>
                <img class="signature" src="{{ resource_path('img/certify-signature.png') }}" alt="Assinatura">
            </p>
            <p>
                {{ $certifySignatureName }}
            </p>
            <p>
                Canal: {{ $youtubeChannelTitle }}.
            </p>
            <p>
                <small>
                    Código de autenticação: {{ $certify->code }} • <a href="{{ route('certifyVerify') }}">{{ route('certifyVerify') }}</a>
                </small>
            </p>
        </div>
        <div class="bg-image">
            <img src="{{ resource_path('img/certify-background.png') }}" alt="Certificado tema">
        </div>
    </div>
</body>
</html>