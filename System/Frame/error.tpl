<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{$header}</title>
    <style>
        * {
            margin: 0;
            padding: 0
        }

        html,
        body {
            width: 100%;
            height: 100%;

        }

        body {
            background: #fcfcfc;
            color: #555;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            font-size: 14px;
            line-height: 1.7;
        }

        h1,
        h2,
        h3 {
            margin-bottom: 20px;
            font-weight: 100;
            text-rendering: optimizeLegibility;
            font-family: "Montserrat", "Helvetica", "Tahoma", "Geneva", "Arial", sans-serif;
        }

        h1 {
            font-size: 70px;
            border-bottom: 4px solid #F0F2F4;
            letter-spacing: -3px;
            line-height: 1;
            padding: 5px 0 20px 0;
        }

        h3 {
            font-size: 24px;
            letter-spacing: 1px;
        }

        .errorBox {
            height: 100%;
            display: -webkit-box;
            display: -moz-box;
            display: -ms-flexbox;
            display: -webkit-flex;
            display: flex;
            -moz-justify-content: center;
            -ms-flex-pack: center;
            -webkit-justify-content: center;
            justify-content: center;
        }

        .errorBox>div {
            height: 100%;
            max-width: 1500px;
            display: -webkit-box;
            display: -moz-box;
            display: -ms-flexbox;
            display: -webkit-flex;
            display: flex;
            -moz-justify-content: center;
            -ms-flex-pack: center;
            -webkit-justify-content: center;
            justify-content: center;
            -moz-flex-direction: column;
            -ms-flex-direction: column;
            -webkit-flex-direction: column;
            flex-direction: column;
        }

        p {
            margin: 10px 0;
        }

        .tc {
            text-align: center;
        }

        .table {
            width: 100%;
            max-width: 100%;
            border-spacing: 0;
            border-collapse: collapse;
            background-color: #fff;
            border: 1px solid #EFF3F8;
            border-radius: 5px;
            margin-bottom: 30px;
            word-wrap: break-word;
            word-break: break-all;
        }

        .table>thead>tr>th,
        .table>tbody>tr>th,
        .table>thead>tr>td,
        .table>tbody>tr>td {
            border: 1px solid #EFF3F8;
            padding: 15px;
        }

        .table>thead>tr>th,
        .table>thead>tr>td {
            border: 0;
        }

        .table thead,
        .table th {
            font-weight: normal;
            background: #F6FAFF;
        }

        .icon-frown {
            display: inline-block;
            vertical-align: middle;
            position: relative;
            font-style: normal;
            color: currentColor;
            text-align: left;
            text-indent: -9999px;
            direction: ltr;
            border-radius: 50%;
            width: 100px;
            height: 100px;
            margin: 2px;
            border: 6px solid #555;
        }

        .icon-frown,
        .icon-frown * {
            box-sizing: border-box
        }

        .icon-frown:before {
            content: '';
            pointer-events: none;
            border-radius: 50%;
            box-shadow: 40px 0 0 0, 0 0 0 6px inset;
            height: 12px;
            width: 12px;
            left: 18px;
            position: absolute;
            top: 30%;
            color: #555
        }

        .icon-frown:after {
            content: '';
            pointer-events: none;
            border: 3px solid #555;
            border-radius: 50%;
            border-top-color: transparent;
            border-left-color: transparent;
            border-right-color: transparent;
            height: 50px;
            left: 50%;
            position: absolute;
            top: 25%;
            width: 50px;
            -webkit-transform: translateX(-50%) rotate(180deg);
            transform: translateX(-50%) rotate(180deg);
            -webkit-transform-origin: center 85%;
            transform-origin: center 85%
        }

        .code {
            background: #f9f2f4;
            color: #9c1d3d;
            padding: 3px 6px;
            border-radius: 3px;
        }
    </style>

</head>
<body>
    <div class="errorBox">
        <div>
            <h1 class="tc">
                <i class="icon-frown"></i>
            </h1>
            <h3 class="tc">{$title}{$message}</h3>
            
            {$info}
            {$table}
        </div>

</div>
</body>

</html>